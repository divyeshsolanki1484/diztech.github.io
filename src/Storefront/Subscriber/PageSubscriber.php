<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Storefront\Subscriber;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemAddedEvent;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemQuantityChangedEvent;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemRemovedEvent;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Account\Overview\AccountOverviewPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Register\CheckoutRegisterPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Zeobv\AbandonedCart\Checkout\AbandonedCart\AbandonedCart;
use Zeobv\AbandonedCart\Pagelet\AbandonedCartReminder\Account\AbandonedCartReminderAccountPageletLoader;
use Zeobv\AbandonedCart\Pagelet\AbandonedCartReminder\Account\AbandonedCartReminderPagelet;
use Zeobv\AbandonedCart\Service\AbandonedCartService;
use Zeobv\AbandonedCart\Service\ConfigService;

class PageSubscriber implements EventSubscriberInterface
{
    protected ?Request $currentRequest;

    public function __construct(
        RequestStack $requestStack,
        private EntityRepository $abandonedCartRepository,
        private AbandonedCartService $abandonedCartService,
        private AbandonedCartReminderAccountPageletLoader $abandonedCartReminderAccountPageletLoader,
        private ConfigService $configService,
    )
    {
        $this->currentRequest = $requestStack->getCurrentRequest();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AccountOverviewPageLoadedEvent::class => 'onAccountOverviewLoaded',
            AfterLineItemAddedEvent::class => 'onLineItemAdded',
            AfterLineItemQuantityChangedEvent::class => 'onLineItemQtyChanged',
            AfterLineItemRemovedEvent::class => 'onLineItemRemoved',
            OffcanvasCartPageLoadedEvent::class => 'onOffcanvasCartPageLoaded',
            CheckoutRegisterPageLoadedEvent::class => 'onCheckoutRegisterPageLoaded',
            CheckoutConfirmPageLoadedEvent::class => 'onCheckoutConfirmPageLoaded',
        ];
    }

    public function onAccountOverviewLoaded(AccountOverviewPageLoadedEvent $event): void
    {
        $event->getPage()->addExtension(
            AbandonedCartReminderPagelet::EXTENSION_NAME,
            $this->abandonedCartReminderAccountPageletLoader->load(
                $event->getPage()->getCustomer()
            )
        );
    }

    public function onLineItemAdded(AfterLineItemAddedEvent $event): void
    {
        $cart = $event->getCart();

        $salesChannelContext = $event->getSalesChannelContext();

        if ($this->currentRequest->attributes->get('sw-domain-id')) {
            $this->upsertAbandonedCart($cart, $salesChannelContext, true);
        }
    }

    public function onLineItemRemoved(AfterLineItemRemovedEvent $event): void
    {
        $cart = $event->getCart();

        $salesChannelContext = $event->getSalesChannelContext();

        if ($this->currentRequest->attributes->get('sw-domain-id')) {
            $this->upsertAbandonedCart($cart, $salesChannelContext, true);
        }
    }

    public function onLineItemQtyChanged(AfterLineItemQuantityChangedEvent $event): void
    {
        $cart = $event->getCart();

        $salesChannelContext = $event->getSalesChannelContext();

        if ($this->currentRequest->attributes->get('sw-domain-id')) {
            $this->upsertAbandonedCart($cart, $salesChannelContext, true);
        }
    }

    public function onOffcanvasCartPageLoaded(OffcanvasCartPageLoadedEvent $event): void
    {
        $cart = $event->getPage()->getCart();
        $this->abandonedCartService->saveCart($cart, $event->getSalesChannelContext());
        $this->upsertAbandonedCart($cart, $event->getSalesChannelContext());
    }

    public function onCheckoutConfirmPageLoaded(CheckoutConfirmPageLoadedEvent $event): void
    {
        $cart = $event->getPage()->getCart();
        $this->abandonedCartService->saveCart($cart, $event->getSalesChannelContext());
        $this->upsertAbandonedCart($cart, $event->getSalesChannelContext());
    }

    public function onCheckoutRegisterPageLoaded(CheckoutRegisterPageLoadedEvent $event): void
    {
        $cart = $event->getPage()->getCart();
        $this->abandonedCartService->saveCart($cart, $event->getSalesChannelContext());
        $this->upsertAbandonedCart($cart, $event->getSalesChannelContext());
    }

    protected function upsertAbandonedCart(Cart $cart, SalesChannelContext $context, bool $cartShouldExist = false): void
    {

        $active = $this->configService->getIsActive($context->getSalesChannel());
        $allowGuestEmails = $this->configService->getAllowGuestEmails($context->getSalesChannel());

        if (!$active) {
            return;
        }

        $customer = $context->getCustomer();
        
        if (is_null($customer)) {
            return;
        }

        $customerEmail = $this->resolveCustomerEmail($customer);
        if ($customerEmail === null) {
            return;
        }
        
        $employeeCustomerId = $this->resolveEmployeeCustomerId($customer, $customerEmail);

        // Exclude guest customers unless explicitly allowed
        if ($customer->getGuest() && !$allowGuestEmails) {
            return;
        }

        $salesChannelDomainId = $this->currentRequest->attributes->get('sw-domain-id');

        if (is_null($salesChannelDomainId)) {
            return;
        }

        $criteria = new Criteria();

        # Get abandoned cart by customer email to avoid abandoned cart duplication.
        $criteria->addFilter(new EqualsFilter('email', $customerEmail));

        $cartResult = $this->abandonedCartRepository->search($criteria, $context->getContext());

        if ($cartShouldExist && $cartResult->count() <= 0) {
            return;
        }

        $lineItems = json_encode($cart->getLineItems()->getElements());

        if ($lineItems === false) {
            return;
        }

        $lineItems = json_decode($lineItems, true);

        /** @var AbandonedCart|null $abandonedCart */
        $abandonedCart = $cartResult->first();

        if (empty($lineItems)) {
            $id = $abandonedCart ? $abandonedCart->getId() : null;
            if ($id !== null) {
                $this->abandonedCartRepository->delete([
                    [
                        'id' => $id,
                    ],
                ], $context->getContext());
            }
            return;
        }

        $data = [
            'id' => $abandonedCart ? $abandonedCart->getId() : Uuid::randomHex(),
            'cartToken' => $cart->getToken(),
            'lineItems' => $lineItems,
            'currencyId' => $context->getCurrency()->getId(),
            'paymentMethodId' => $context->getPaymentMethod()->getId(),
            'shippingMethodId' => $context->getShippingMethod()->getId(),
            'countryId' => $context->getShippingLocation()->getCountry()->getId(),
            'salesChannelId' => $context->getSalesChannel()->getId(),
            'salesChannelDomainId' => $salesChannelDomainId,
            'customerId' => $customer->getId(),
            'employeeId' => $employeeCustomerId,
            'email' => $customerEmail,
        ];

        $this->abandonedCartRepository->upsert([$data], $context->getContext());
    }

    private function resolveCustomerEmail(CustomerEntity $customer): ?string
    {
        $email = $customer->getEmail();

        if ($customer->hasExtension('b2bEmployee')) {
            $b2bEmployee = $customer->getExtension('b2bEmployee');

            if (\is_object($b2bEmployee) && method_exists($b2bEmployee, 'getEmail')) {
                $employeeEmail = $b2bEmployee->getEmail();

                if (\is_string($employeeEmail) && $employeeEmail !== '') {
                    $email = $employeeEmail;
                }
            }
        }

        if (!\is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return null;
        }

        return $email;
    }

    private function resolveEmployeeCustomerId(CustomerEntity $customer, string $resolvedEmail): ?string
    {
        if (!$customer->hasExtension('b2bEmployee')) {
            return null;
        }

        $b2bEmployee = $customer->getExtension('b2bEmployee');
        if (!\is_object($b2bEmployee)) {
            return null;
        }

        // Get the employee ID from b2b_employee table
        if (method_exists($b2bEmployee, 'getId')) {
            $employeeId = $b2bEmployee->getId();
            if (\is_string($employeeId) && Uuid::isValid($employeeId)) {
                return $employeeId;
            }
        }

        return null;
    }
}
