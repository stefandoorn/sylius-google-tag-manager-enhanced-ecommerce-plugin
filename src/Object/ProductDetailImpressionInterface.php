<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

/**
 * Interface ProductDetailImpressionDataInterface
 */
interface ProductDetailImpressionInterface
{
    public function add(ProductDetailInterface $productDetail): void;

    public function toArray(): array;
}
