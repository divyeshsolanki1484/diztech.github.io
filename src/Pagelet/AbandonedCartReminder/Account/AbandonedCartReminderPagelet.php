<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Pagelet\AbandonedCartReminder\Account;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Storefront\Pagelet\Pagelet;

class AbandonedCartReminderPagelet extends Pagelet
{
    public const EXTENSION_NAME = 'zeobvAbandonedCartAccountPagelet';

    protected CustomerEntity $customer;
    protected ?bool $success = null;
    protected ?array $messages = null;
    protected ?string $subscriptionStatus = null;

    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function isSuccess(): ?bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    public function addMessages(array $messages): void
    {
        if (! \is_array($this->messages)) {
            $this->messages = $messages;
        } else {
            $this->messages = array_merge($this->messages, $messages);
        }
    }

    public function getSubscriptionStatus(): ?string
    {
        return $this->subscriptionStatus;
    }

    public function setSubscriptionStatus(?string $subscriptionStatus): void
    {
        $this->subscriptionStatus = $subscriptionStatus;
    }
}
