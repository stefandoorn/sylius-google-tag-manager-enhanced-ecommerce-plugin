<?php declare(strict_types=1);

namespace SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpression;
use SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

/**
 * Class ProductDetailImpressionFactory
 * @package SyliusGtmEnhancedEcommercePlugin\Object\Factory
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
