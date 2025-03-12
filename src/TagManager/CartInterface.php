<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderItemInterface;

interface CartInterface
{
    /**
     * @param array<string, mixed> $productData
     */
    public function add(array $productData): void;

    /**
     * @param array<string, mixed> $productData
     */
    public function remove(array $productData): void;

    /**
     * @return array<string, mixed>
     */
    public function getOrderItem(OrderItemInterface $orderItem): array;
}
