<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpression;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

final class ProductDetailImpressionFactory implements ProductDetailImpressionFactoryInterface
{
    public function create(): ProductDetailImpressionInterface
    {
        return new ProductDetailImpression();
    }
}
