<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\ScheduledTasks\Handlers\AbandonedCart;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;
use Zeobv\AbandonedCart\Checkout\AbandonedCart\AbandonedCart;
use Zeobv\AbandonedCart\ScheduledTasks\Tasks\AbandonedCart\CollectTask;
use Zeobv\AbandonedCart\Service\AbandonedCartService;
use Zeobv\AbandonedCart\Service\ConfigService;

#[AsMessageHandler(handles: CollectTask::class)]
class CollectTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        private string $environment,
        private EntityRepository $abandonedCartRepository,
        private EntityRepository $salesChannelRepository,
        private AbandonedCartService $abandonedCartService,
        private ConfigService $configService,
        private LoggerInterface $logger
    )
    {
        parent::__construct($scheduledTaskRepository, $logger);
    }

    public static function getHandledMessages(): iterable
    {
        return [CollectTask::class];
    }

    /**
     * Collects all abandoned carts and starts the abandoned cart process
     */
    public function run(): void
    {

        try {
            $salesChannelCollection = $this->getSalesChannels();

            if (is_null($salesChannelCollection)) {
                return;
            }

            $context = $this->getContext();
            $this->configService->setLastRunTimestamp();

            foreach ($salesChannelCollection as $salesChannelEntity) {
                $cartsProcessedForSalesChannel = 0;
                $notificationBatchSize = $this->configService->getNotificationBatchSize($salesChannelEntity);

                if (!$this->configService->getIsActive($salesChannelEntity)) {
                    continue;
                }

                $this->configService->setLastRunTimestamp($salesChannelEntity);

                $result = $this->getAbandonedCartsForSalesChannel($salesChannelEntity, $context);

                if (is_null($result) || $result->getTotal() <= 0) {
                    continue;
                }

                /** @var AbandonedCart $abandonedCart */
                foreach ($result->getElements() as $abandonedCart) {

                    if ($abandonedCart->getCustomer() === null) {
                        continue;
                    }

                    try {
                        if ($this->processAbandonedCart($abandonedCart, $context)) {
                            $cartsProcessedForSalesChannel++;
                        }
                    } catch (Throwable $e) {
                        $this->logOrThrowException($e);
                    }

                    if ($cartsProcessedForSalesChannel >= $notificationBatchSize) {
                        # If the batch size is reached, stop sending mails for this Sales Channel until the next run
                        break;
                    }
                }
            }
        } catch (Throwable $e) {
            $this->logOrThrowException($e);
        }
    }

    protected function processAbandonedCart(AbandonedCart $abandonedCart, Context $context): bool
    {
        try {
            # Check if abandoned cart should be processed
            if ($this->abandonedCartService->shouldProcessAbandonedCart($abandonedCart)) {
                # Process abandoned cart
                return $this->abandonedCartService->processAbandonedCart($abandonedCart);
            }
            if ($this->abandonedCartService->shouldTrashAbandonedCart($abandonedCart)) {
                # Trash abandoned cart
                $this->abandonedCartService->trashAbandonedCart($abandonedCart, $context);

                return false;
            }
        } catch (Throwable $e) {
            return false;
        }

        return false;
    }

    protected function getSalesChannels(): ?EntityCollection
    {
        $result = $this->salesChannelRepository->search(
            new Criteria(),
            $this->getContext()
        );

        if ($result->count() <= 0) {
            return null;
        }

        return $result->getEntities();
    }

    protected function logOrThrowException(Throwable $exception): bool
    {
        if ($this->isTestEnv()) {
            throw $exception;
        }

        $this->logger->critical('Failed sending Abandoned Cart mail:' . $exception->getMessage());

        return false;
    }

    private function getAbandonedCartsForSalesChannel(SalesChannelEntity $salesChannelEntity, Context $context): ?EntitySearchResult
    {
        try {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelEntity->getId()));
            $criteria->addAssociations([
                'customer', 
                'customer.defaultBillingAddress',
                'customer.defaultShippingAddress',
                'customer.group',
                'salesChannel', 
                'salesChannel.domains', 
                'salesChannel.mailHeaderFooter'
            ]);

            return $this->abandonedCartRepository->search($criteria, $context);
        } catch (Throwable $e) {
            $this->logOrThrowException($e);
        }

        return null;
    }

    private function isTestEnv(): bool
    {
        return $this->environment === 'test' || $this->environment === 'dev';
    }

    private function getContext(): Context
    {
        return Context::createDefaultContext();
    }
}
