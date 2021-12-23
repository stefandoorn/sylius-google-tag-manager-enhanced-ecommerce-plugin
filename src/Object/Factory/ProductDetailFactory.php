<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetail;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;

final class ProductDetailFactory implements ProductDetailFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(): ProductDetailInterface
    {
        return new ProductDetail();
    }
}
