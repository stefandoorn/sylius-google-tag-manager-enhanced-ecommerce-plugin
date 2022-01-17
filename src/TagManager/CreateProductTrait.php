<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

trait CreateProductTrait
{
    private function createProduct(OrderItemInterface $item): array
    {
        /** @var ProductVariantInterface $variant */
        $variant = $item->getVariant();

        /** @var ProductInterface $product */
        $product = $variant->getProduct();

        /** @var TaxonInterface|null $mainTaxon */
        $mainTaxon = $product->getMainTaxon();

        $data = [
            'name' => $product->getName(),
            'quantity' => $item->getQuantity(),
            'variant' => $variant->getName() ?? $variant->getCode(),
            'category' => null !== $mainTaxon ? $mainTaxon->getName() : '',
            'price' => $item->getUnitPrice() / 100,
        ];

        $data['id'] = $this->productIdentifierHelper->getProductIdentifier($product);

        return $data;
    }
}
