<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Storefront\Controller;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Exception\InvalidUuidException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Zeobv\AbandonedCart\Checkout\AbandonedCart\AbandonedCart;
use Zeobv\AbandonedCart\Pagelet\AbandonedCartReminder\Account\AbandonedCartReminderAccountPageletLoader;
use Zeobv\AbandonedCart\Service\AbandonedCartService;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class RecoverController extends StorefrontController
{
    public function __construct(
        private EntityRepository $abandonedCartRepository,
        private AbandonedCartService $abandonedCartService,
        private AbandonedCartReminderAccountPageletLoader $abandonedCartReminderAccountPageletLoader
    )
    {
    }

    #[Route(path: '/zeo/abandonedcart/recover/{id}', name: 'frontend.zeo.abandonedcart.recover', options: ['seo' => false], methods: ['GET'])]
    public function recover(Request $request, Context $context, SalesChannelContext $salesChannelContext): RedirectResponse
    {
        $id = $request->attributes->get('id');

        if (empty($id)) {
            return $this->redirect('/');
        }

        $abandonedCart = $this->getAbandonedCartForId($id, $context);

        if (is_null($abandonedCart)) {
            return $this->redirect('/');
        }

        /* Here we upsert the data in the abandoned cart table field scheduleIndex to check the user is coming from the click on the mail because we can't get anything if their user coming from the mail or via cart page, so we create this logic. */
        $this->abandonedCartService->recoverAbandonedCart($abandonedCart, $salesChannelContext);

        $writeData = [
            'id' => $abandonedCart->getId(),
            'isRecovered' => true,
        ];

        $this->abandonedCartRepository->upsert([$writeData], $context);

        return $this->redirectToRoute('frontend.checkout.register.page');
    }

    #[Route(path: '/widget/zeo/abandonedcart/subscribe/customer', name: 'frontend.zeo.abandonedcart.account.subscription.update', defaults: ['XmlHttpRequest' => true, '_loginRequired' => true], methods: ['POST'])]
    public function subscribeCustomer(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        $pagelet = $this->abandonedCartReminderAccountPageletLoader->load($customer);

        switch ($request->request->get('option')) {
            case AbandonedCartService::REMINDER_MAIL_STATUS_SUBSCRIBED:
                $pagelet = $this->abandonedCartReminderAccountPageletLoader->subscribe($customer, $context, $pagelet);
                break;
            default:
                $pagelet = $this->abandonedCartReminderAccountPageletLoader->unsubscribe($customer, $context, $pagelet);
                break;
        }

        return $this->renderStorefront('@ZeobvAbandonedCart/storefront/page/account/abandoned-cart.html.twig', [
            'abandonedCartAccountPagelet' => $pagelet,
        ]);
    }

    protected function getAbandonedCartForId(string $id, Context $context): ?AbandonedCart
    {
        try {
            $criteria = new Criteria([strtolower($id)]);
            $criteria->addAssociations(['customer', 'customer.defaultBillingAddress', 'customer.defaultShippingAddress']);
            $result = $this->abandonedCartRepository->search($criteria, $context);
            /** @var AbandonedCart $abandonedCart */
            $abandonedCart = $result->first();
        } catch (InconsistentCriteriaIdsException $e) {
            $abandonedCart = null;
        } catch (InvalidUuidException $e) {
            $abandonedCart = null;
        }

        return $abandonedCart;
    }
}
