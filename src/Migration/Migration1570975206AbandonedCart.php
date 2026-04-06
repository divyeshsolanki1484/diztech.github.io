<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1570975206AbandonedCart extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1570975206;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS `zeo_abandoned_cart` (
            `id` BINARY(16) NOT NULL,
            `cart_token` VARCHAR(50) NOT NULL,
            `email` VARCHAR(100) NULL,
            `mail_send` BOOL NULL,
            `line_items` JSON NULL,
            `currency_id` BINARY(16),
            `shipping_method_id` BINARY(16),
            `payment_method_id` BINARY(16),
            `country_id` BINARY(16),
            `customer_id` BINARY(16),
            `sales_channel_id` BINARY(16),
            `sales_channel_domain_id` BINARY(16),
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3),
            PRIMARY KEY (`id`),
            CONSTRAINT `fk.zeo_abandoned_cart.currency_id` FOREIGN KEY (`currency_id`)
                REFERENCES `currency` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `fk.zeo_abandoned_cart.shipping_method_id` FOREIGN KEY (`shipping_method_id`)
                REFERENCES `shipping_method` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `fk.zeo_abandoned_cart.payment_method_id` FOREIGN KEY (`payment_method_id`)
                REFERENCES `payment_method` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `fk.zeo_abandoned_cart.country_id` FOREIGN KEY (`country_id`)
                REFERENCES `country` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `fk.zeo_abandoned_cart.sales_channel_id` FOREIGN KEY (`sales_channel_id`)
                REFERENCES `sales_channel` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `fk.zeo_abandoned_cart.sales_channel_domain_id` FOREIGN KEY (`sales_channel_domain_id`)
                REFERENCES `sales_channel_domain` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        )
        ENGINE = InnoDB
        DEFAULT CHARSET = utf8mb4
        COLLATE = utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
