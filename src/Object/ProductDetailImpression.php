<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\Object;

/**
 * Class ProductDetailImpressionData
 * @package GtmEnhancedEcommercePlugin\Object
 */
final class ProductDetailImpression implements ProductDetailImpressionInterface
{
    /**
     * @var array|ProductDetailInterface[]
     */
    private $variants = [];

    /**
     * @inheritDoc
     */
    public function add(ProductDetailInterface $productDetail): void
    {
        $this->variants[] = $productDetail;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_map(function(ProductDetailInterface $productDetail) {
            return $productDetail->toArray();
        }, $this->variants);
    }
}
