<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpression;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

/**
 * Class ProductDetailImpressionFactory
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory
 */
final class ProductDetailImpressionFactory implements ProductDetailImpressionFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(): ProductDetailImpressionInterface
    {
        return new ProductDetailImpression();
    }
}
