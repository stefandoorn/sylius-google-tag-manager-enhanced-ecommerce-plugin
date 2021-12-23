<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

/**
 * Interface ProductDetailImpressionFactoryInterface
 */
interface ProductDetailImpressionFactoryInterface
{
    public function create(): ProductDetailImpressionInterface;
}
