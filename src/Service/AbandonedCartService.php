<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Service;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Promotion\Cart\PromotionItemBuilder;
use Shopware\Core\Content\Mail\Service\AbstractMailService as MailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\RouterInterface;
use Zeobv\AbandonedCart\Checkout\AbandonedCart\AbandonedCart;

class AbandonedCartService {
    public const REMINDER_MAIL_STATUS_SUBSCRIBED = 'subscribed';
    public const REMINDER_MAIL_STATUS_UNSUBSCRIBED = 'unsubscribed';
    public const REMINDER_STATUS_FIELD_NAME = 'zeobvAbandonedCartSubscriptionStatus';

    public function __construct(
        private ConfigService $configService,
        private EntityRepository $abandonedCartRepository,
        private EntityRepository $mailTemplateRepository,
        private EntityRepository $customerRepository,
        private MailService $mailService,
        private CartService $cartService,
        private SalesChannelContextPersister $contextPersister,
        private CachedSalesChannelContextFactory $salesChannelContextFactory,
        private RouterInterface $router,
        private PromotionItemBuilder $promotionItemBuilder,
        private EntityRepository $promotionIndividualCodeRepository,
        private PromotionService $promotionService
    ) {
    }

    public function shouldProcessAbandonedCart(AbandonedCart $abandonedCart): bool {
        $salesChannel = $abandonedCart->getSalesChannel();
        $lastMailWasSendAt = $abandonedCart->getLastMailSendAt() ?? (!is_null($abandonedCart->getUpdatedAt()) ? $abandonedCart->getUpdatedAt() : $abandonedCart->getCreatedAt());
        $minutesSinceLastMailWasSend = (time() - $lastMailWasSendAt->getTimestamp()) / 60;

        $schedule = $this->configService->getSchedule($salesChannel);

        if (!empty($schedule)) {
            if (!key_exists($abandonedCart->getScheduleIndex(), $schedule)) {
                return false;
            }

            $scheduledDelaySinceLastMail = $schedule[$abandonedCart->getScheduleIndex()]['delay'];
        } else {
            $scheduledDelaySinceLastMail = $this->configService->getProcessStartDelay($salesChannel) * 60;
        }

        return $this->configService->getIsActive($salesChannel) &&
            $this->canProcessAbandonedCart($abandonedCart) &&
            $minutesSinceLastMailWasSend >= $scheduledDelaySinceLastMail;
    }

    public function canProcessAbandonedCart(AbandonedCart $abandonedCart): bool
    {
        // Check if customer exists
        if (!$abandonedCart->getCustomer() instanceof CustomerEntity) {
            return false;
        }

        // Check if guest emails are allowed
        $allowGuestEmails = $this->configService->getAllowGuestEmails($abandonedCart->getSalesChannel());

        // Exclude guest customers unless explicitly allowed
        if ($abandonedCart->getCustomer()->getGuest() && !$allowGuestEmails) {
            return false;
        }

        $customFields = $abandonedCart->getCustomer()->getCustomFields();
        return !is_array($customFields)
            || !key_exists(self::REMINDER_STATUS_FIELD_NAME, $customFields)
            || $customFields[self::REMINDER_STATUS_FIELD_NAME] === self::REMINDER_MAIL_STATUS_SUBSCRIBED;
    }

    /**
     * Bool returns if cart was successfully processed.
     */
    public function processAbandonedCart(AbandonedCart $abandonedCart): bool {
        // Load the SalesChannel context of the abandoned cart
        $salesChannelContext = $this->salesChannelContextFactory->create(
            Uuid::randomHex(),
            $abandonedCart->getSalesChannelId(),
            [
                SalesChannelContextService::LANGUAGE_ID => $abandonedCart->getSalesChannelDomain()?->getLanguageId(),
            ]
        );

        // Use the context of the Abandoned Cart SalesChannel
        $context = $salesChannelContext->getContext();

        // Create New cart
        $newCart = $this->cartService->createNewFromAbandonedCart($salesChannelContext);
        $this->cartService->populateCartFromAbandonedCart($newCart, $abandonedCart);
        $cart = $this->cartService->recalculate($newCart, $salesChannelContext);

        // Setup the Symfony router context to simulate the Abandoned Cart SalesChannelDomain.
        // In console context the router context is not set and urls in email would fallback to http://localhost
        $originalRouterContext = clone $this->router->getContext();
        $salesChannelDomain = $abandonedCart->getSalesChannelDomain();
        if ($salesChannelDomain === null) {
            return false;
        }
        $this->switchRouterContext($salesChannelDomain->getUrl());

        /** @var MailTemplateEntity|null $mailTemplate */
        $mailTemplate = $this->getMailTemplate($abandonedCart, $context);

        if (is_null($mailTemplate) || is_null($mailTemplate->getTranslation('senderName'))) {
            $this->router->setContext($originalRouterContext);
            return false;
        }

        $abandonedCartCCEmails = $this->configService->getAbandonedCartCCEmails($salesChannelContext->getSalesChannel());

        # Add CC emails to recipients if available
        $abandonedCartCCEmailData = [];
        if (isset($abandonedCartCCEmails)) {
            $abandonedCartCCEmailData = array_combine($abandonedCartCCEmails, $abandonedCartCCEmails);
        }

        # Map information of customer to template
        $customer = $abandonedCart->getCustomer();
        if ($customer === null) {
            $this->router->setContext($originalRouterContext);
            return false;
        }
        $recipientEmail = $abandonedCart->getEmail() ?: $customer->getEmail();
        $recipients = [
            $recipientEmail => $customer->getFirstName(),
        ];
        
        $mailData = [
            'recipients' => $recipients,
            'recipientsCc' => $abandonedCartCCEmailData,
            'salesChannelId' => $abandonedCart->getSalesChannelId(),
            'subject' => $mailTemplate->getTranslation('subject'),
            'senderName' => $mailTemplate->getTranslation('senderName'),
            'contentPlain' => $mailTemplate->getTranslation('contentPlain'),
            'contentHtml' => $mailTemplate->getTranslation('contentHtml'),
            'mediaIds' => [],
        ];

        $recoveryCustomer = $this->resolveRecoveryCustomer($abandonedCart, $salesChannelContext);

        $templateData = $mailTemplate->jsonSerialize();
        $templateData['zeoAbandonedCart'] = $abandonedCart;
        $templateData['cart'] = $cart;
        $templateData['customer'] = $recoveryCustomer ?? $abandonedCart->getCustomer();

        # Create email
        $mailMessage = $this->mailService->send(
            $mailData,
            $context,
            $templateData
        );

        /* Here we are fetching the value of mail sent from config and increment the value every time on mail is sent. */
        if ($mailMessage !== null) {
            if ($abandonedCart->getScheduleIndex() === 0) {
                $metricMailsSent = $this->configService->getMetricMailsSent($abandonedCart->getSalesChannel());
                $incrementMailSent = $metricMailsSent + 1;
                $this->configService->setMetricMailsSent($incrementMailSent);
            }

            $this->abandonedCartRepository->update([
                [
                    'id' => $abandonedCart->getId(),
                    'scheduleIndex' => $abandonedCart->getScheduleIndex() + 1,
                    'lastMailSendAt' => new \DateTime(),
                ],
            ], $context);

            $this->promotionService->generateNewPromotionCodes($salesChannelContext);
            $this->cartService->deleteCart($cart, $salesChannelContext);

            $this->router->setContext($originalRouterContext);
            return true;
        }

        $this->router->setContext($originalRouterContext);
        return false;
    }

    public function resolveAbandonedCart(AbandonedCart $abandonedCart, Context $context): void {
        # Cart is resolved to an order. The abandoned cart is no longer abandoned. Resolve the abandoned cart.
        $this->abandonedCartRepository->delete([
            ['id' => $abandonedCart->getId()],
        ], $context);
    }

    /**
     * An AbandonedCart should be trashed when it has been abandoned longer than the configured trash delay
     */
    public function shouldTrashAbandonedCart(AbandonedCart $abandonedCart): bool {
        $age = $abandonedCart->getAge();
        $ageInHours = ($age->d * 24) + $age->h + ($age->i / 60);

        return !$this->canProcessAbandonedCart($abandonedCart) ||
            $ageInHours > $this->configService->getTrashDelay($abandonedCart->getSalesChannel());
    }

    public function trashAbandonedCart(AbandonedCart $abandonedCart, Context $context): void {
        $this->abandonedCartRepository->delete([
            ['id' => $abandonedCart->getId()],
        ], $context);
    }

    public function recoverAbandonedCart(AbandonedCart $abandonedCart, SalesChannelContext $salesChannelContext): Cart {
        $newCart = $this->cartService->createNewFromAbandonedCart($salesChannelContext);
        $this->cartService->populateCartFromAbandonedCart($newCart, $abandonedCart);

        $this->setNewCart($abandonedCart, $newCart, $salesChannelContext);

        $recoveryCustomer = $this->resolveRecoveryCustomer($abandonedCart, $salesChannelContext);

        if (!$this->configService->getIsCartAnonymous($salesChannelContext->getSalesChannel()) && $recoveryCustomer) {
            $contextData = [
                'customerId' => $recoveryCustomer->getId(),
                'billingAddressId' => $recoveryCustomer->getDefaultBillingAddress() ?
                    $recoveryCustomer->getDefaultBillingAddress()->getId() : null,
                'shippingAddressId' => $recoveryCustomer->getDefaultShippingAddress() ?
                    $recoveryCustomer->getDefaultShippingAddress()->getId() : null,
            ];

            // If there's an employee, add employeeId to context so B2B extension gets loaded
            if ($abandonedCart->getEmployeeId()) {
                $contextData['employeeId'] = $abandonedCart->getEmployeeId();
            }

            $this->contextPersister->save(
                $salesChannelContext->getToken(),
                $contextData,
                $salesChannelContext->getSalesChannelId()
            );
        }

        $usePromotionsIsActivated = $this->configService->getUsePromotionsAbandonedCart($salesChannelContext->getSalesChannel());
        $promotionId = $this->configService->getAbandonedCartPromotionId($salesChannelContext->getSalesChannel());
        $excludedCustomerGroupId = $this->configService->getExcludedCustomerGroupId($salesChannelContext->getSalesChannel());
        $customerGroupId = $recoveryCustomer?->getGroupId();

        if (!in_array($customerGroupId, $excludedCustomerGroupId) && $usePromotionsIsActivated && $promotionId !== '') {
            // Getting promotion individuals codes here
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('promotionId', $promotionId));
            $criteria->addFilter(new EqualsFilter('payload', null));
            $criteria->addFilter(new ContainsFilter('code', 'ABANDONED-'));

            $promotions = $this->promotionIndividualCodeRepository->search($criteria, Context::createDefaultContext())->getElements();

            if ($promotions === null) {
                return $newCart;
            }

            // Adding promotion in cart
            $this->addPromotion($promotions, $newCart, $salesChannelContext);
        }
        return $newCart;
    }

    public function addPromotion(array $promotions, Cart $promotionCart, SalesChannelContext $salesChannelContext): void {
        //Getting first promotion code key and value
        $firstPromotionCodeKey = array_key_first($promotions);
        $firstPromotionCodeValue = $promotions[$firstPromotionCodeKey];

        $code = $firstPromotionCodeValue->getCode();

        if ($code === '') {
            throw RoutingException::missingRequestParameter('code');
        }

        //adding promotion to abandoned cart user
        $lineItem = $this->promotionItemBuilder->buildPlaceholderItem($code);
        $this->cartService->add($promotionCart, $lineItem, $salesChannelContext);
    }

    public function setNewCart(AbandonedCart $abandonedCart, Cart $newCart, SalesChannelContext $salesChannelContext): void {
        $this->cartService->setCartToSession($newCart);
        $this->cartService->deleteRelatedCart($abandonedCart, $salesChannelContext);
        $this->cartService->saveNewCart($newCart, $salesChannelContext);
    }

    public function saveCart(Cart $cart, SalesChannelContext $salesChannelContext): void {
        $this->cartService->saveNewCart($cart, $salesChannelContext);
    }

    /**
     * Switches the Symfony router context to another url
     * https://symfony.com/doc/4.4/routing.html#generating-urls-in-commands
     *
     *
     * Copied from Shopware:
     * Shopware\Storefront\Controller\ContextController@switchLanguage
     */
    protected function switchRouterContext(string $url): void {
        $parsedUrl = parse_url($url);

        $routerContext = $this->router->getContext();
        $routerContext->setHttpPort($parsedUrl['port'] ?? 80);
        $routerContext->setMethod('GET');
        $routerContext->setHost($parsedUrl['host']);
        $routerContext->setBaseUrl(rtrim($parsedUrl['path'] ?? '', '/'));
    }

    private function getMailTemplate(AbandonedCart $abandonedCart, Context $context): ?MailTemplateEntity {
        $schedule = $this->configService->getSchedule($abandonedCart->getSalesChannel());

        if (!key_exists($abandonedCart->getScheduleIndex(), $schedule)) {
            return null;
        }

        $scheduleConfig = $schedule[$abandonedCart->getScheduleIndex()];

        $criteria = new Criteria([$scheduleConfig['templateId']]);
        $criteria->addAssociation('mailTemplateType');

        /** @var MailtemplateEntity $mailtemplateEntity */
        $mailtemplateEntity = $this->mailTemplateRepository->search($criteria, $context)->first();
        return $mailtemplateEntity;
    }

    private function resolveRecoveryCustomer(AbandonedCart $abandonedCart, SalesChannelContext $salesChannelContext): ?CustomerEntity
    {
        // For B2B employees, we need to return the business partner customer
        // The employee context will be added via employeeId in the session
        
        // Always try to find customer by email first (works for both B2B and regular customers)
        $email = $abandonedCart->getEmail();
        if (\is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            $criteria = new Criteria();
            $criteria->setLimit(1);
            $criteria->addAssociations(['defaultBillingAddress', 'defaultShippingAddress', 'group']);
            $criteria->addFilter(new EqualsFilter('email', $email));
            $criteria->addFilter(new EqualsFilter('salesChannelId', $abandonedCart->getSalesChannelId()));

            /** @var CustomerEntity|null $customerByEmail */
            $customerByEmail = $this->customerRepository->search($criteria, $salesChannelContext->getContext())->first();
            if ($customerByEmail instanceof CustomerEntity) {
                return $customerByEmail;
            }
        }

        // Fallback to the customer stored in abandoned cart
        return $abandonedCart->getCustomer();
    }
}
