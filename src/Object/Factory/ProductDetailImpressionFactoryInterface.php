<?php declare(strict_types=1);

namespace SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

/**
 * Interface ProductDetailImpressionFactoryInterface
 * @package SyliusGtmEnhancedEcommercePlugin\Object\Factory
 */
interface ProductDetailImpressionFactoryInterface
{
    /**
     * @return ProductDetailImpressionInterface
     */
    public function create(): ProductDetailImpressionInterface;
}
