<?php declare(strict_types=1);

namespace Zeobv\AbandonedCart\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1736000001AddEmployeeId extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1736000001;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("
            ALTER TABLE `zeo_abandoned_cart` 
            ADD COLUMN `employee_id` BINARY(16) NULL AFTER `customer_id`,
            ADD CONSTRAINT `fk.zeo_abandoned_cart.employee_id` 
                FOREIGN KEY (`employee_id`) 
                REFERENCES `customer` (`id`) 
                ON DELETE SET NULL 
                ON UPDATE CASCADE
        ");
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
