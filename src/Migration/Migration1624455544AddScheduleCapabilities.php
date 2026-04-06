<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1624455544AddScheduleCapabilities extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1624455544;
    }

    public function update(Connection $connection): void
    {
        $query = 'ALTER TABLE zeo_abandoned_cart DROP COLUMN mail_send;';
        $query .= 'ALTER TABLE zeo_abandoned_cart ADD COLUMN schedule_index TINYINT default 0 after email;';
        $query .= 'ALTER TABLE zeo_abandoned_cart ADD COLUMN last_mail_send_at datetime(3) NULL after email;';

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
