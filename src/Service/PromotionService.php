<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;

class PromotionService {

    public function __construct(
        private ConfigService $configService,
        private EntityRepository $promotionIndividualCodeRepository
    ) {
    }

    public function generateNewPromotionCodes($salesChannelContext): void
    {
        $promotionsActivated = $this->configService->getUsePromotionsAbandonedCart($salesChannelContext->getSalesChannel());
        $promotionId = $this->configService->getAbandonedCartPromotionId($salesChannelContext->getSalesChannel());

        if (!$promotionsActivated || !$promotionId) {
            return;
        }
        $newCode = $this->generateUniqueCode();
        // Adding individual promotion codes
        $this->promotionIndividualCodeRepository->create([
            [
                'promotionId' => $promotionId,
                'code' => $newCode,
                'payload' => null,
            ],
        ], Context::createDefaultContext());
    }

    private function generateUniqueCode(): string
    {
        $newCode = 'ABANDONED-' . substr(str_shuffle(implode(range('A', 'Z'))), 0, 5);

        // Check if the code already exists
        $criteria = new Criteria();
        $criteria->addFilter(new ContainsFilter('code', $newCode));
        $existingCode = $this->promotionIndividualCodeRepository->search($criteria, Context::createDefaultContext())->first();

        // If the code already exists, generate a new one until it's unique
        while ($existingCode !== null) {
            $newCode = 'ABANDONED-' . substr(str_shuffle(implode(range('A', 'Z'))), 0, 5);
            $criteria = new Criteria();
            $criteria->addFilter(new ContainsFilter('code', $newCode));
            $existingCode = $this->promotionIndividualCodeRepository->search($criteria, Context::createDefaultContext())->first();
        }

        return $newCode;
    }
}
