<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Checkout\AbandonedCart;

use DateInterval;
use DateTimeInterface;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\Currency\CurrencyEntity;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

/**
 * Class AbandonedCart
 *
 * @package Zeobv\AbandonedCart\Checkout\AbandonedCart
 */
class AbandonedCart extends Entity
{
    use EntityIdTrait;

    protected string $cartToken;
    protected string $email;
    protected array $lineItems;
    protected string $salesChannelDomainId;
    protected string $customerId;
    protected ?string $employeeId = null;
    protected string $currencyId;
    protected string $paymentMethodId;
    protected string $shippingMethodId;
    protected string $countryId;
    protected string $salesChannelId;
    protected int $scheduleIndex;
    protected ?bool $isRecovered = null;
    protected ?DateTimeInterface $lastMailSendAt = null;
    protected ?CustomerEntity $customer = null;
    protected ?Entity $employee = null;
    protected ?SalesChannelEntity $salesChannel = null;
    protected ?SalesChannelDomainEntity $salesChannelDomain = null;
    protected CurrencyEntity $currency;
    protected PaymentMethodEntity $paymentMethod;
    protected ShippingMethodEntity $shippingMethod;

    public function getCartToken(): string
    {
        return $this->cartToken;
    }

    public function setCartToken(string $cartToken): void
    {
        $this->cartToken = $cartToken;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getLineItems(): array
    {
        return $this->lineItems;
    }

    public function setLineItems(array $lineItems): void
    {
        $this->lineItems = $lineItems;
    }

    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    public function setCurrencyId(string $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    public function getPaymentMethodId(): string
    {
        return $this->paymentMethodId;
    }

    public function setPaymentMethodId(string $paymentMethodId): void
    {
        $this->paymentMethodId = $paymentMethodId;
    }

    public function getShippingMethodId(): string
    {
        return $this->shippingMethodId;
    }

    public function setShippingMethodId(string $shippingMethodId): void
    {
        $this->shippingMethodId = $shippingMethodId;
    }

    public function getCountryId(): string
    {
        return $this->countryId;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    public function setEmployeeId(?string $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    public function getSalesChannelDomainId(): string
    {
        return $this->salesChannelDomainId;
    }

    public function setSalesChannelDomainId(string $salesChannelDomainId): void
    {
        $this->salesChannelDomainId = $salesChannelDomainId;
    }

    public function setCountryId(string $countryId): void
    {
        $this->countryId = $countryId;
    }

    public function getScheduleIndex(): int
    {
        return $this->scheduleIndex;
    }

    public function setScheduleIndex(int $scheduleIndex): void
    {
        $this->scheduleIndex = $scheduleIndex;
    }

    public function getLastMailSendAt(): ?DateTimeInterface
    {
        return $this->lastMailSendAt;
    }

    public function setLastMailSendAt(DateTimeInterface $lastMailSendAt): void
    {
        $this->lastMailSendAt = $lastMailSendAt;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getIsRecovered(): ?bool
    {
        return $this->isRecovered;
    }

    public function setIsRecovered(bool $isRecovered): void
    {
        $this->isRecovered = $isRecovered;
    }

    public function getAge(): ?DateInterval
    {
        $date = $this->getUpdatedAt() !== null
            ? $this->getUpdatedAt()
            : $this->getCreatedAt();
        try {
            # throws \Exception when the interval_spec cannot be parsed as an interval.
            return $date->diff(new \DateTime());
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function getEmployee(): ?Entity
    {
        return $this->employee;
    }

    public function setEmployee(?Entity $employee): void
    {
        $this->employee = $employee;
    }

    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(SalesChannelEntity $salesChannelEntity): void
    {
        $this->salesChannel = $salesChannelEntity;
    }

    public function getSalesChannelDomain(): ?SalesChannelDomainEntity
    {
        return $this->salesChannelDomain;
    }

    public function setSalesChannelDomain(SalesChannelDomainEntity $salesChannelDomain): void
    {
        $this->salesChannelDomain = $salesChannelDomain;
    }

    public function getCurrency(): CurrencyEntity
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyEntity $currency): void
    {
        $this->currency = $currency;
    }

    public function getPaymentMethod(): PaymentMethodEntity
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethodEntity $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getShippingMethod(): ShippingMethodEntity
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod(ShippingMethodEntity $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
    }
}
