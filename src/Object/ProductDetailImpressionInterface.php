<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\Object;

/**
 * Interface ProductDetailImpressionDataInterface
 * @package GtmEnhancedEcommercePlugin\Object
 */
interface ProductDetailImpressionInterface
{
    /**
     * @param ProductDetailInterface $productDetail
     */
    public function add(ProductDetailInterface $productDetail): void;

    /**
     * @return array
     */
    public function toArray(): array;
}
