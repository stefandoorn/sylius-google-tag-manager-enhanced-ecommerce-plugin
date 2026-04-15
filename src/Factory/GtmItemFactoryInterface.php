<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface GtmItemFactoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function createNewFromProductVariant(ProductVariantInterface $productVariant): array;

    /**
     * @return array<string, mixed>
     */
    public function createNewFromOrderItem(OrderItemInterface $orderItem): array;
}
