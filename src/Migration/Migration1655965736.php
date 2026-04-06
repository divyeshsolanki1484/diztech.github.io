<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1655965736 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1655965736;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("
            ALTER TABLE zeo_abandoned_cart
            ADD `is_recovered` TINYINT(1) NULL DEFAULT '0';
        ");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
