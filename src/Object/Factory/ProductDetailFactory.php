<?php declare(strict_types=1);

namespace SyliusGtmEnhancedEcommercePlugin\Object\Factory;

use SyliusGtmEnhancedEcommercePlugin\Object\ProductDetail;
use SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;

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
