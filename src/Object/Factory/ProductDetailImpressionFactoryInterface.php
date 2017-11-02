<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\Object\Factory;

use GtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

/**
 * Interface ProductDetailImpressionFactoryInterface
 * @package GtmEnhancedEcommercePlugin\Object\Factory
 */
interface ProductDetailImpressionFactoryInterface
{
    /**
     * @return ProductDetailImpressionInterface
     */
    public function create(): ProductDetailImpressionInterface;
}
