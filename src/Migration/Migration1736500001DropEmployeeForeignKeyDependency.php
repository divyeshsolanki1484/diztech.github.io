<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1736500001DropEmployeeForeignKeyDependency extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1736500001;
    }

    public function update(Connection $connection): void
    {
        $tableExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = 'zeo_abandoned_cart'"
        );

        if ((int) $tableExists <= 0) {
            return;
        }

        $foreignKeys = $connection->fetchFirstColumn(
            "SELECT tc.constraint_name
             FROM information_schema.table_constraints tc
             INNER JOIN information_schema.key_column_usage kcu
                ON kcu.constraint_schema = tc.constraint_schema
                AND kcu.table_name = tc.table_name
                AND kcu.constraint_name = tc.constraint_name
             WHERE tc.constraint_schema = DATABASE()
               AND tc.table_name = 'zeo_abandoned_cart'
               AND tc.constraint_type = 'FOREIGN KEY'
               AND kcu.column_name = 'employee_id'"
        );

        foreach ($foreignKeys as $foreignKey) {
            if (!\is_string($foreignKey) || $foreignKey === '') {
                continue;
            }

            $connection->executeStatement(
                sprintf(
                    'ALTER TABLE `zeo_abandoned_cart` DROP FOREIGN KEY `%s`',
                    str_replace('`', '``', $foreignKey)
                )
            );
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
