<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Pagelet\AbandonedCartReminder\Account;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\AbandonedCart\Service\AbandonedCartService;

class AbandonedCartReminderAccountPageletLoader
{
    public function __construct(
        private EntityRepository $customerRepository,
        private Translator $translator,
        private LoggerInterface $logger
    ) {
    }

    public function load(CustomerEntity $customer): AbandonedCartReminderPagelet
    {
        $pagelet = $this->getBasePagelet($customer);

        $pagelet->setSubscriptionStatus(
            $this->getCustomerReminderSubscriptionStatus($customer)
        );

        return $pagelet;
    }

    public function subscribe(CustomerEntity $customer, SalesChannelContext $context, AbandonedCartReminderPagelet $pagelet): AbandonedCartReminderPagelet
    {
        try {
            $customFields = $customer->getCustomFields();
            $customFields[AbandonedCartService::REMINDER_STATUS_FIELD_NAME] = AbandonedCartService::REMINDER_MAIL_STATUS_SUBSCRIBED;

            $this->customerRepository->update([
                [
                    'id' => $customer->getId(),
                    'customFields' => $customFields,
                ],
            ], $context->getContext());

            $pagelet->setSubscriptionStatus(AbandonedCartService::REMINDER_MAIL_STATUS_SUBSCRIBED);
            $pagelet->setSuccess(true);
            $pagelet->setMessages(
                [
                    [
                        'type' => 'success',
                        'text' => $this->translator->trans('zeobv-abandoned-cart.subscription.subscriptionPersistedSuccess'),
                    ],
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->error('Abandoned cart subscription failed', ['exception' => $exception]);
            $pagelet->setSuccess(false);
            $pagelet->setMessages(
                [
                    [
                        'type' => 'danger',
                        'text' => $this->translator->trans('zeobv-abandoned-cart.subscription.subscriptionConfirmationFailed'),
                    ],
                ]
            );
        }

        return $pagelet;
    }

    public function unsubscribe(CustomerEntity $customer, SalesChannelContext $context, AbandonedCartReminderPagelet $pagelet): AbandonedCartReminderPagelet
    {
        try {
            $customFields = $customer->getCustomFields();
            $customFields[AbandonedCartService::REMINDER_STATUS_FIELD_NAME] = AbandonedCartService::REMINDER_MAIL_STATUS_UNSUBSCRIBED;

            $this->customerRepository->update([
                [
                    'id' => $customer->getId(),
                    'customFields' => $customFields,
                ],
            ], $context->getContext());

            $pagelet->setSubscriptionStatus(AbandonedCartService::REMINDER_MAIL_STATUS_UNSUBSCRIBED);
            $pagelet->setSuccess(true);
            $pagelet->setMessages(
                [
                    [
                        'type' => 'success',
                        'text' => $this->translator->trans('zeobv-abandoned-cart.subscription.subscriptionRevokeSuccess'),
                    ],
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->error('Abandoned cart unsubscription failed', ['exception' => $exception]);
            $pagelet->setSuccess(false);
            $pagelet->setMessages(
                [
                    [
                        'type' => 'danger',
                        'text' => $this->translator->trans('error.message-default'),
                    ],
                ]
            );
        }

        return $pagelet;
    }

    protected function getCustomerReminderSubscriptionStatus(CustomerEntity $customer): string
    {
        $customFields = $customer->getCustomFields();

        if ($customFields === null || ! key_exists(AbandonedCartService::REMINDER_STATUS_FIELD_NAME, $customFields)) {
            return AbandonedCartService::REMINDER_MAIL_STATUS_SUBSCRIBED;
        }

        return $customFields[AbandonedCartService::REMINDER_STATUS_FIELD_NAME];
    }

    protected function getBasePagelet(CustomerEntity $customer): AbandonedCartReminderPagelet
    {
        $pagelet = new AbandonedCartReminderPagelet();
        $pagelet->setCustomer($customer);

        return $pagelet;
    }
}
