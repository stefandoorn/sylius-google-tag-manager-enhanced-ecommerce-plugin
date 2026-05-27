<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface GtmEcommerceFactoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function createNewFromOrder(OrderInterface $order): array;

    /**
     * @return array<string, mixed>
     */
    public function createNewFromSingleOrderItem(OrderItemInterface $orderItem, OrderInterface $order): array;

    /**
     * @return array<string, mixed>
     */
    public function createNewFromProduct(ProductInterface $product): array;
}
