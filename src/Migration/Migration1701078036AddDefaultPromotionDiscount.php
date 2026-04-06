<?php 

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Migration\Traits\ImportTranslationsTrait;
use Shopware\Core\Migration\Traits\Translations;
use Zeobv\AbandonedCart\ZeobvAbandonedCart;

class Migration1701078036AddDefaultPromotionDiscount extends MigrationStep {
    use ImportTranslationsTrait;

    public function getCreationTimestamp(): int {
        return 1701078036;
    }

    public function update(Connection $connection): void {
        //Insert will only execute if default promotion is not exists
        $defaultPromotionId = $connection->fetchFirstColumn(
            'SELECT `id` FROM `promotion` WHERE `id` = :defaultPromotion',
            ['defaultPromotion' => Uuid::fromHexToBytes(ZeobvAbandonedCart::DEFAULT_PROMOTION_ID)]
        );

        if (count($defaultPromotionId) === 0) {
            // Adding default promotion
            $connection->insert('promotion', [
                'id' => Uuid::fromHexToBytes(ZeobvAbandonedCart::DEFAULT_PROMOTION_ID),
                'active' => true,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'use_codes' => true,
                'use_individual_codes' => true,
                'individual_code_pattern' => 'ABANDONED-%s%s%s%s%s',
            ]);

            // Adding default promotion translation
            $promotionTranslations = new Translations(
                [
                    'promotion_id' => Uuid::fromHexToBytes(ZeobvAbandonedCart::DEFAULT_PROMOTION_ID),
                    'name' => 'Werbung für verlassene Einkaufswagen',
                ],
                [
                    'promotion_id' => Uuid::fromHexToBytes(ZeobvAbandonedCart::DEFAULT_PROMOTION_ID),
                    'name' => 'Abandoned cart promotion',
                ],
            );

            $this->importTranslation('promotion_translation', $promotionTranslations, $connection);

            //Adding all available sales channels
            $salesChannelIds = $connection->executeQuery('SELECT `sales_channel`.id FROM `sales_channel`')->fetchFirstColumn();

            foreach ($salesChannelIds as $salesChannelId) {
                $connection->insert('promotion_sales_channel', [
                    'id' => Uuid::randomBytes(),
                    'promotion_id' => Uuid::fromHexToBytes(ZeobvAbandonedCart::DEFAULT_PROMOTION_ID),
                    'sales_channel_id' => $salesChannelId,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }

            //Adding promotion discount
            $connection->insert('promotion_discount', [
                'id' => Uuid::randomBytes(),
                'promotion_id' => Uuid::fromHexToBytes(ZeobvAbandonedCart::DEFAULT_PROMOTION_ID),
                'scope' => 'cart',
                'type' => 'percentage',
                'value' => 5,
                'consider_advanced_rules' => 0,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        }
    }

    public function updateDestructive(Connection $connection): void {
        // implement update destructive
    }
}
