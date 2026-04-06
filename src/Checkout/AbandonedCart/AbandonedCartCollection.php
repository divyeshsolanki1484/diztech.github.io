<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Checkout\AbandonedCart;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(AbandonedCart $entity)
 * @method void                set(string $key, AbandonedCart $entity)
 * @method array<AbandonedCart> getIterator()
 * @method array<AbandonedCart> getElements()
 * @method AbandonedCart|null get(string $key)
 * @method AbandonedCart|null first()
 * @method AbandonedCart|null last()
 */
class AbandonedCartCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return AbandonedCart::class;
    }
}
