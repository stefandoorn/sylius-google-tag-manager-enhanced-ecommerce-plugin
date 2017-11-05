<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\Object\Factory;

use GtmEnhancedEcommercePlugin\Object\ProductDetail;
use GtmEnhancedEcommercePlugin\Object\ProductDetailInterface;

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
