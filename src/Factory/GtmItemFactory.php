<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
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

    public function createNewFromOrderItem(OrderItemInterface $orderItem, OrderInterface $order): array
    {
        $index = 0;
        foreach ($order->getItems() as $i => $item) {
            if ($item === $orderItem) {
                $index = $i;

                break;
            }
        }

        /** @var ChannelInterface|null $channel */
        $channel = $order->getChannel();

        /** @var ProductVariantInterface|null $variant */
        $variant = $orderItem->getVariant();
        if (null === $variant) {
            $data = [
                'item_id' => (string) $orderItem->getId(),
                'item_name' => (string) $orderItem->getProductName(),
            ];
        } else {
            $data = $this->createNewFromProductVariant($variant);
        }

        $data['affiliation'] = null !== $channel ? (string) $channel->getName() : '';
        $data['index'] = $index;
        $data['price'] = $orderItem->getFullDiscountedUnitPrice() / 100;
        $data['quantity'] = $orderItem->getQuantity();

        return $data;
    }

    public function createNewFromProduct(ProductInterface $product): array
    {
        /** @var TaxonInterface|null $mainTaxon */
        $mainTaxon = $product->getMainTaxon();

        return [
            'item_id' => $this->productIdentifierHelper->getProductIdentifier($product),
            'item_name' => (string) $product->getName(),
            'item_category' => null !== $mainTaxon ? (string) $mainTaxon->getName() : '',
        ];
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
