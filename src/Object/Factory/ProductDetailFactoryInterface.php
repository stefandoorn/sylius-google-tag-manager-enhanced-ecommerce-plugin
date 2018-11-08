<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;

/**
 * Interface ProductDetailFactoryInterface
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory
 */
interface ProductDetailFactoryInterface
{
    /**
     * @return ProductDetailInterface
     */
    public function create(): ProductDetailInterface;
}
