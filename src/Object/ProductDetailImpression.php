<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

/**
 * Class ProductDetailImpressionData
 */
final class ProductDetailImpression implements ProductDetailImpressionInterface
{
    /** @var array|ProductDetailInterface[] */
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
        return \array_map(static function (ProductDetailInterface $productDetail): array {
            return $productDetail->toArray();
        }, $this->variants);
    }
}
