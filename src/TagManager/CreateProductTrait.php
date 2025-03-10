<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

trait CreateProductTrait
{
    /**
     * @return array<string, mixed>
     */
    private function createProduct(OrderItemInterface $item, ?int $index = null): array
    {
        /** @var ProductVariantInterface $variant */
        $variant = $item->getVariant();

        /** @var ProductInterface $product */
        $product = $variant->getProduct();

        /** @var TaxonInterface|null $mainTaxon */
        $mainTaxon = $product->getMainTaxon();

        $data = [
            'item_id' => $this->productIdentifierHelper->getProductIdentifier($product),
            'item_name' => $product->getName(),
            'affiliation' => $this->channelContext->getChannel()->getName(),
            'item_category' => null !== $mainTaxon ? $mainTaxon->getName() : '',
            'item_variant' => $variant->getName() ?? $variant->getCode(),
            'price' => $item->getUnitPrice() / 100,
            'quantity' => $item->getQuantity(),
        ];

        if (null !== $index) {
            $data['index'] = $index;
        }

        return $data;
    }
}
