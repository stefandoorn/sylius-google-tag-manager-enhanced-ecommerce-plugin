<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;

/**
 * Interface ProductDetailImpressionFactoryInterface
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory
 */
interface ProductDetailImpressionFactoryInterface
{
    /**
     * @return ProductDetailImpressionInterface
     */
    public function create(): ProductDetailImpressionInterface;
}
