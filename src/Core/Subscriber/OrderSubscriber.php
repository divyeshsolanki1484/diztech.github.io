<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Core\Subscriber;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zeobv\AbandonedCart\Checkout\AbandonedCart\AbandonedCart;
use Zeobv\AbandonedCart\Service\AbandonedCartService;

class OrderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AbandonedCartService $abandonedCartService,
        private EntityRepository $orderRepository,
        private EntityRepository $abandonedCartRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderEvents::ORDER_WRITTEN_EVENT => 'onOrderWritten',
        ];
    }

    public function onOrderWritten(EntityWrittenEvent $event): void
    {
        foreach ($event->getWriteResults() as $writeResult) {
            if (
                $writeResult->getOperation() !== EntityWriteResult::OPERATION_INSERT
                || $writeResult->getEntityName() !== OrderDefinition::ENTITY_NAME
            ) {
                continue;
            }

            if (is_array($writeResult->getPrimaryKey())) {
                foreach ($writeResult->getPrimaryKey() as $id) {
                    $this->resolveAbandonedCartForOrderId($id, $event->getContext());
                }
            } else {
                $this->resolveAbandonedCartForOrderId($writeResult->getPrimaryKey(), $event->getContext());
            }
        }
    }

    protected function resolveAbandonedCartForOrderId(string $orderId, Context $context): void
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociation('orderCustomer');

        /** @var OrderEntity|null $order */
        $order = $this->orderRepository->search($criteria, $context)->first();

        if ($order === null || $order->getOrderCustomer() === null) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $order->getOrderCustomer()->getEmail()));
        /** @var AbandonedCart|null $abandonedCart */
        $abandonedCart = $this->abandonedCartRepository->search($criteria, $context)->first();

        if ($abandonedCart === null) {
            return;
        }

        /* Here we are upsert the data on order table for displaying the recovered orders in dashboard */
        if ($abandonedCart->getIsRecovered() === true) {
            $writeData = [
                'id' => $orderId,
                'customFields' => ['ZeobvAbandonedCartMail' => 1],
            ];
            $this->orderRepository->upsert([$writeData], Context::createDefaultContext());
        }

        $this->abandonedCartService->resolveAbandonedCart($abandonedCart, $context);
    }
}
