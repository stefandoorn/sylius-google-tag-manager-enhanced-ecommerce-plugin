<?php declare(strict_types=1);

namespace SyliusGtmEnhancedEcommercePlugin\Object;

/**
 * Interface ProductDetailImpressionDataInterface
 * @package SyliusGtmEnhancedEcommercePlugin\Object
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
