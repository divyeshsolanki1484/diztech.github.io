<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Checkout\AbandonedCart;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Checkout\Payment\PaymentMethodDefinition;
use Shopware\Core\Checkout\Shipping\ShippingMethodDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Country\CountryDefinition;
use Shopware\Core\System\Currency\CurrencyDefinition;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class AbandonedCartDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'zeo_abandoned_cart';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return AbandonedCartCollection::class;
    }

    public function getEntityClass(): string
    {
        return AbandonedCart::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('cart_token', 'cartToken'))->addFlags(new Required()),
            (new StringField('email', 'email')),
            (new JsonField('line_items', 'lineItems')),
            (new FkField('sales_channel_domain_id', 'salesChannelDomainId', SalesChannelDomainDefinition::class)),
            (new FkField('currency_id', 'currencyId', CurrencyDefinition::class)),
            (new FkField('payment_method_id', 'paymentMethodId', PaymentMethodDefinition::class)),
            (new FkField('shipping_method_id', 'shippingMethodId', ShippingMethodDefinition::class)),
            (new FkField('country_id', 'countryId', CountryDefinition::class)),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class)),
            (new IdField('employee_id', 'employeeId')),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class)),
            (new IntField('schedule_index', 'scheduleIndex')),
            (new DateTimeField('last_mail_send_at', 'lastMailSendAt')),
            (new BoolField('is_recovered', 'isRecovered')),
            (new CreatedAtField()),
            (new UpdatedAtField()),
            (new OneToOneAssociationField('currency', 'currency_id', 'id', CurrencyDefinition::class)),
            (new OneToOneAssociationField('paymentMethod', 'payment_method_id', 'id', PaymentMethodDefinition::class)),
            (new OneToOneAssociationField('shippingMethod', 'shipping_method_id', 'id', ShippingMethodDefinition::class)),
            (new OneToOneAssociationField('customer', 'customer_id', 'id', CustomerDefinition::class)),
            (new OneToOneAssociationField('country', 'country_id', 'id', CountryDefinition::class)),
            (new OneToOneAssociationField('salesChannel', 'sales_channel_id', 'id', SalesChannelDefinition::class)),
            (new OneToOneAssociationField('salesChannelDomain', 'sales_channel_domain_id', 'id', SalesChannelDomainDefinition::class)),
        ]);
    }
}
