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

    public function createNewFromOrder(OrderInterface $order): array
    {
        $items = [];
        foreach ($order->getItems() as $orderItem) {
            $items[] = $this->gtmItemFactory->createNewFromOrderItem($orderItem, $order);
        }

        return [
            'currency' => (string) $order->getCurrencyCode(),
            'value' => $order->getTotal() / 100,
            'items' => $items,
        ];
    }

    public function createNewFromSingleOrderItem(OrderItemInterface $orderItem, OrderInterface $order): array
    {
        $item = $this->gtmItemFactory->createNewFromOrderItem($orderItem, $order);

        return [
            'currency' => (string) $order->getCurrencyCode(),
            'value' => ($item['price'] ?? 0) * ($item['quantity'] ?? 0),
            'items' => [$item],
        ];
    }

    public function createNewFromProduct(ProductInterface $product): array
    {
        $item = $this->gtmItemFactory->createNewFromProduct($product);

        return [
            'items' => [$item],
        ];
    }
}
