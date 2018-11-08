<?php declare(strict_types=1);

namespace SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;

/**
 * Interface ProductDetailFactoryInterface
 * @package SyliusGtmEnhancedEcommercePlugin\Object\Factory
 */
interface ProductDetailFactoryInterface
{
    /**
     * @return ProductDetailInterface
     */
    public function create(): ProductDetailInterface;
}
