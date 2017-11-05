<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\Object\Factory;

use GtmEnhancedEcommercePlugin\Object\ProductDetailImpression;
use GtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

/**
 * Class ProductDetailImpressionFactory
 * @package GtmEnhancedEcommercePlugin\Object\Factory
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
