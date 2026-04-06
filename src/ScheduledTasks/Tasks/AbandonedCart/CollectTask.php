<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\ScheduledTasks\Tasks\AbandonedCart;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CollectTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'zeo_abandoned_cart.abandoned_cart_collect_task';
    }

    public static function getDefaultInterval(): int
    {
        return 60; # 1 minute
    }
}
