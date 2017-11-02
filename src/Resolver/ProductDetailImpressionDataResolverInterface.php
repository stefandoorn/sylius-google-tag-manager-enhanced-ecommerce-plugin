<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\Resolver;

use GtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * Interface ProductDetailImpressionDataResolverInterface
 * @package GtmEnhancedEcommercePlugin\Resolver
 */
interface ProductDetailImpressionDataResolverInterface
{
    /**
     * @return ProductDetailImpressionInterface
     */
    public function get(ProductInterface $product): ProductDetailImpressionInterface;
}
