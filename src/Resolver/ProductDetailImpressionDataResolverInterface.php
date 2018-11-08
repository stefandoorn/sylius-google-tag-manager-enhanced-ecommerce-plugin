<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * Interface ProductDetailImpressionDataResolverInterface
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver
 */
interface ProductDetailImpressionDataResolverInterface
{
    /**
     * @return ProductDetailImpressionInterface
     */
    public function get(ProductInterface $product): ProductDetailImpressionInterface;
}
