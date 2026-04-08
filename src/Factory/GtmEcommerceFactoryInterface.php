<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface GtmEcommerceFactoryInterface
{
    /**
     * @return mixed[]|null
     */
    public function createNewFromOrder(OrderInterface $order): ?array;

    /**
     * @return mixed[]|null
     */
    public function createNewFromSingleOrderItem(OrderItemInterface $orderItem, OrderInterface $order): ?array;

    /**
     * @return mixed[]|null
     */
    public function createNewFromProduct(ProductInterface $product): ?array;
}
