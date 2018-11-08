<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

/**
 * Interface ProductDetailImpressionDataInterface
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object
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
