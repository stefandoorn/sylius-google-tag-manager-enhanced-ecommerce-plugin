<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface GtmItemFactoryInterface
{
    /**
     * @return mixed[]
     */
    public function createNewFromProduct(ProductInterface $product): array;

    /**
     * @return mixed[]
     */
    public function createNewFromProductVariant(ProductVariantInterface $productVariant): array;

    /**
     * @return mixed[]
     */
    public function createNewFromOrderItem(OrderItemInterface $orderItem, OrderInterface $order): array;
}
