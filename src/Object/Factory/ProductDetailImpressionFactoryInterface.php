<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

interface ProductDetailImpressionFactoryInterface
{
    public function create(): ProductDetailImpressionInterface;
}
