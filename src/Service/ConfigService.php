<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Service;

use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigService
{
    public function __construct(
        private SystemConfigService $systemConfigService
    ) {
    }

    public function getIsActive(SalesChannelEntity $salesChannelEntity): bool
    {
        return $this->systemConfigService->getBool('ZeobvAbandonedCart.config.active', $salesChannelEntity->getId());
    }

    public function setLastRunTimestamp(?SalesChannelEntity $salesChannelEntity = null): void
    {
        $this->systemConfigService->set(
            'ZeobvAbandonedCart.config.lastRunTimestamp',
            date('d/m/Y H:i:s'),
            $salesChannelEntity ? $salesChannelEntity->getId() : null
        );
    }

    /**
     * @deprecated
     */
    public function getProcessStartDelay(SalesChannelEntity $salesChannelEntity): int
    {
        /** @var int|null $mailSendDelay */
        $mailSendDelay = $this->systemConfigService->get('ZeobvAbandonedCart.config.mailSendDelay', $salesChannelEntity->getId());
        return $mailSendDelay !== null ? $mailSendDelay : 1;
    }

    public function getTrashDelay(SalesChannelEntity $salesChannelEntity): int
    {
        /** @var int|null $decayTime */
        $decayTime = $this->systemConfigService->get('ZeobvAbandonedCart.config.abandonedCartDecay', $salesChannelEntity->getId());
        return $decayTime !== null ? $decayTime : 24;
    }

    public function getNotificationBatchSize(?SalesChannelEntity $salesChannel = null): int
    {
        return $this->systemConfigService->getInt(
            'ZeobvAbandonedCart.config.notificationBatchSize',
            $salesChannel ? $salesChannel->getId() : null
        );
    }

    public function getIsCartAnonymous(SalesChannelEntity $salesChannelEntity): bool
    {
        return $this->systemConfigService->getBool(
            'ZeobvAbandonedCart.config.isCartAnonymous',
            $salesChannelEntity->getId()
        );
    }

    public function getAllowGuestEmails(SalesChannelEntity $salesChannelEntity): bool
    {
        return $this->systemConfigService->getBool(
            'ZeobvAbandonedCart.config.allowGuestEmails',
            $salesChannelEntity->getId()
        );
    }

    public function getUsePromotionsAbandonedCart(SalesChannelEntity $salesChannelEntity): bool
    {
        return $this->systemConfigService->getBool(
            'ZeobvAbandonedCart.config.usePromotionsAbandonedCart',
            $salesChannelEntity->getId()
        );
    }

    public function getAbandonedCartPromotionId(SalesChannelEntity $salesChannelEntity): string {
        return $this->systemConfigService->getString(
            'ZeobvAbandonedCart.config.abandonedCartPromotionId',
            $salesChannelEntity->getId()
        );
    }

    public function getExcludedCustomerGroupId(SalesChannelEntity $salesChannelEntity): array
    {
        $excludedCustomerGroupId = $this->systemConfigService->get(
            'ZeobvAbandonedCart.config.excludedCustomerGroups',
            $salesChannelEntity->getId());

        if (! is_array($excludedCustomerGroupId)) {
            return [];
        }

        return $excludedCustomerGroupId;
    }

    public function getAbandonedCartCCEmails(SalesChannelEntity $salesChannelEntity): array
    {
        $abandonedCartCCEmails = $this->systemConfigService->get(
            'ZeobvAbandonedCart.config.abandonedCartCCEmails',
            $salesChannelEntity->getId()
        );

        if (!isset($abandonedCartCCEmails) || !is_string($abandonedCartCCEmails)) {
            return [];
        }

        // Split the string by commas and trim each email
        $ccEmails = array_map('trim', explode(',', $abandonedCartCCEmails));

        $validCCEmails = array_filter($ccEmails, function ($filterCCEmails) {
            return filter_var($filterCCEmails, FILTER_VALIDATE_EMAIL);
        });

        return $validCCEmails;
    }

    public function getSchedule(SalesChannelEntity $salesChannelEntity): array
    {
        /** @var array|false $schedule */
        $schedule = json_decode(
            $this->systemConfigService->getString(
                'ZeobvAbandonedCart.config.schedule',
                $salesChannelEntity->getId()
            ),
            true
        );

        return is_array($schedule) ? $schedule : [];
    }

    /**
     * Used for abandoned cart conversion metrics
     */
    public function getMetricMailsSent(SalesChannelEntity $salesChannelEntity): int
    {
        /** @var int|null $metricMailsSent */
        $metricMailsSent = $this->systemConfigService->get(
            'ZeobvAbandonedCart.config.metricMailsSent',
            $salesChannelEntity->getId()
        );

        return $metricMailsSent !== null ? $metricMailsSent : 0;
    }

    /**
     * Used for abandoned cart conversion metrics
     */
    public function setMetricMailsSent(int $incrementMailSent): void
    {
        $this->systemConfigService->set(
            'ZeobvAbandonedCart.config.metricMailsSent',
            $incrementMailSent,
        );
    }
}
