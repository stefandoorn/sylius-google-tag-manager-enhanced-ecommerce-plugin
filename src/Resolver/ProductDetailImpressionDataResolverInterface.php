<?php declare(strict_types=1);

namespace SyliusGtmEnhancedEcommercePlugin\Resolver;

use SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * Interface ProductDetailImpressionDataResolverInterface
 * @package SyliusGtmEnhancedEcommercePlugin\Resolver
 */
interface ProductDetailImpressionDataResolverInterface
{
    /**
     * @return ProductDetailImpressionInterface
     */
    public function get(ProductInterface $product): ProductDetailImpressionInterface;
}
