<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\Object\Factory;

use GtmEnhancedEcommercePlugin\Object\ProductDetailInterface;

/**
 * Interface ProductDetailFactoryInterface
 * @package GtmEnhancedEcommercePlugin\Object\Factory
 */
interface ProductDetailFactoryInterface
{
    /**
     * @return ProductDetailInterface
     */
    public function create(): ProductDetailInterface;
}
