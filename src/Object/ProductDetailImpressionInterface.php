<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

interface ProductDetailImpressionInterface
{
    public function add(ProductDetailInterface $productDetail): void;

    public function toArray(): array;
}
