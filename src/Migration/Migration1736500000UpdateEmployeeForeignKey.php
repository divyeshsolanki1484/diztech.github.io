<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1736500000UpdateEmployeeForeignKey extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1736500000;
    }

    public function update(Connection $connection): void
    {
        // Check if the old foreign key exists
        $fkExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.table_constraints 
             WHERE constraint_schema = DATABASE() 
             AND table_name = 'zeo_abandoned_cart'
             AND constraint_name = 'fk.zeo_abandoned_cart.employee_id'"
        );

        if ($fkExists) {
            // Drop the old foreign key constraint
            $connection->executeStatement('
                ALTER TABLE `zeo_abandoned_cart`
                DROP FOREIGN KEY `fk.zeo_abandoned_cart.employee_id`
            ');
        }

        // Change employee_id column to BINARY(16) to store UUID
        $connection->executeStatement('
            ALTER TABLE `zeo_abandoned_cart`
            MODIFY COLUMN `employee_id` BINARY(16) NULL
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // Nothing to do
    }
}
