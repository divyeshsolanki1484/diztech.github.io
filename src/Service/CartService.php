<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart\Service;

use Shopware\Core\Checkout\Cart\AbstractCartPersister;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartCalculator;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService as SwCartService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\AbandonedCart\Checkout\AbandonedCart\AbandonedCart;

class CartService
{
    public function __construct(
        private SwCartService $cartService,
        private AbstractCartPersister $cartPersister,
        private CartCalculator $calculator
    )
    {
    }

    public function createNewFromAbandonedCart(SalesChannelContext $salesChannelContext): Cart
    {
        return $this->cartService->createNew($salesChannelContext->getToken());
    }

    public function populateCartFromAbandonedCart(Cart &$cart, AbandonedCart $abandonedCart): Cart
    {
        $lineItems = array_map(function (array $lineItemData) {
            return $this->createLineItemFromData($lineItemData);
        }, $abandonedCart->getLineItems());

        $lineItems = new LineItemCollection($lineItems);

        $cart->addLineItems($lineItems);

        return $cart;
    }

    public function setCartToSession(Cart &$cart): void
    {
        $this->cartService->setCart($cart);
    }

    public function deleteRelatedCart(AbandonedCart $abandonedCart, SalesChannelContext $salesChannelContext): void
    {
        $this->cartPersister->delete($abandonedCart->getCartToken(), $salesChannelContext);
    }

    public function saveNewCart(Cart $cart, SalesChannelContext $salesChannelContext): void
    {
        $this->cartPersister->save($cart, $salesChannelContext);
    }

    /**
     * @param array $lineItemData
     */
    private function createLineItemFromData(array $lineItemData): LineItem
    {
        $lineItem = new LineItem(
            $lineItemData['id'],
            $lineItemData['type'],
            $lineItemData['referencedId'],
            $lineItemData['quantity']
        );

        foreach ($lineItemData as $key => $value) {
            if (
                in_array($key, [
                    'id',
                    'type',
                    'referencedId',
                    'uniqueIdentifier',
                    'quantity',
                    'modified',
                    'cover',
                    'price',
                    'dataTimestamp',
                    'priceDefinition',
                    'deliveryInformation',
                    'quantityInformation',
                    'requirement',
                    'modifiedByApp',
                    'extensions',
                ]) || is_null($value)
            ) {
                continue;
            }

            $setMethod = 'set' . ucfirst($key);

            try {
                switch ($key) {
                    case 'children':
                        $collection = new LineItemCollection();
                        foreach ($value as $lineItemChildData) {
                            $collection->add(
                                $this->createLineItemFromData($lineItemChildData)
                            );
                        }
                        $lineItem->setChildren($collection);
                        break;
                    default:
                        $lineItem->$setMethod($value);
                }
            } catch (\Exception) {
                // Skip properties that cannot be set on this line item
                continue;
            }
        }

        return $lineItem;
    }

    public function recalculate(Cart $cart, SalesChannelContext $context): Cart
    {
        $cart = $this->calculator->calculate($cart, $context);
        $this->cartPersister->save($cart, $context);

        return $cart;
    }

    public function add(Cart $cart, LineItem|array $items, SalesChannelContext $context): Cart
    {
        return $this->cartService->add($cart, $items, $context);
    }
    public function deleteCart(Cart $cart, SalesChannelContext $salesChannelContext): void
    {
        $this->cartPersister->delete($cart->getToken(), $salesChannelContext);
    }
}
