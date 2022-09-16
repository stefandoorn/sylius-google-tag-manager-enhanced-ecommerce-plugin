<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

trait CreateProductTrait
{
    private function createProductUA(OrderItemInterface $item): array
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

    private function createProductGA4(OrderItemInterface $item, ?int $index = null): array
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
            'currency' => $this->currencyContext->getCurrencyCode(),
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
