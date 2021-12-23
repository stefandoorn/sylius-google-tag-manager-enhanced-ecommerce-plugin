<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

final class ProductDetailImpression implements ProductDetailImpressionInterface
{
    /** @var ProductDetailInterface[] */
    private array $variants = [];

    public function add(ProductDetailInterface $productDetail): void
    {
        $this->variants[] = $productDetail;
    }

    public function toArray(): array
    {
        return \array_map(static function (ProductDetailInterface $productDetail): array {
            return $productDetail->toArray();
        }, $this->variants);
    }
}
