<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class GtmItemFactory implements GtmItemFactoryInterface
{
    public function __construct(
        private ProductIdentifierHelperInterface $productIdentifierHelper,
    ) {
    }

    public function createNewFromProductVariant(ProductVariantInterface $productVariant): array
    {
        /** @var ProductInterface|null $product */
        $product = $productVariant->getProduct();

        return [
            'item_id' => null !== $product
                ? $this->productIdentifierHelper->getProductIdentifier($product)
                : (string) $productVariant->getCode(),
            'item_name' => null !== $product ? (string) $product->getName() : $productVariant->getDescriptor(),
            'item_variant' => $productVariant->getName() ?? $productVariant->getCode(),
            'item_category' => $this->getMainTaxonName($productVariant),
        ];
    }

    public function createNewFromOrderItem(OrderItemInterface $orderItem): array
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $orderItem->getVariant();
        if (null === $variant) {
            return [
                'item_id' => (string) $orderItem->getId(),
                'item_name' => (string) $orderItem->getProductName(),
            ];
        }

        $data = $this->createNewFromProductVariant($variant);
        $data['quantity'] = $orderItem->getQuantity();

        return $data;
    }

    private function getMainTaxonName(ProductVariantInterface $productVariant): string
    {
        /** @var ProductInterface|null $product */
        $product = $productVariant->getProduct();
        if (null === $product) {
            return '';
        }

        /** @var TaxonInterface|null $mainTaxon */
        $mainTaxon = $product->getMainTaxon();

        return null !== $mainTaxon ? (string) $mainTaxon->getName() : '';
    }
}
