<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;

/**
 * Interface ProductDetailFactoryInterface
 */
interface ProductDetailFactoryInterface
{
    public function create(): ProductDetailInterface;
}
