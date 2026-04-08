<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class GtmEcommerceFactory implements GtmEcommerceFactoryInterface
{
    public function __construct(
        private GtmItemFactoryInterface $gtmItemFactory,
    ) {
    }

    public function createNewFromOrder(OrderInterface $order): ?array
    {
        $ecommerce = [
            'currency' => $order->getCurrencyCode(),
            'value' => $order->getItemsSubtotal() / 100,
            'tax' => $order->getTaxTotal() / 100,
            'shipping' => $order->getShippingTotal() / 100,
            'items' => $this->getProducts($order),
        ];

        if ($order->getPromotionCoupon() !== null) {
            $ecommerce['coupon'] = $order->getPromotionCoupon()->getCode();
        }

        return $ecommerce;
    }

    public function createNewFromSingleOrderItem(OrderItemInterface $orderItem, OrderInterface $order): ?array
    {
        $ecommerce = [
            'currency' => $order->getCurrencyCode(),
            'value' => $orderItem->getTotal() / 100,
            'tax' => $orderItem->getTaxTotal() / 100,
            'items' => [
                $this->gtmItemFactory->createNewFromOrderItem($orderItem, $order),
            ],
        ];

        if ($order->getPromotionCoupon() !== null) {
            $ecommerce['coupon'] = $order->getPromotionCoupon()->getCode();
        }

        return $ecommerce;
    }

    public function createNewFromProduct(ProductInterface $product): ?array
    {
        return [
            'items' => [
                $this->gtmItemFactory->createNewFromProduct($product),
            ],
        ];
    }

    /**
     * @return array<array<string, mixed>>
     */
    private function getProducts(OrderInterface $order): array
    {
        $products = [];

        foreach ($order->getItems() as $orderItem) {
            $products[] = $this->gtmItemFactory->createNewFromOrderItem($orderItem, $order);
        }

        return $products;
    }
}
