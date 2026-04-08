<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class GtmItemFactory implements GtmItemFactoryInterface
{
    public function __construct(
        private ProductIdentifierHelperInterface $productIdentifierHelper,
    ) {
    }

    public function createNewFromProduct(ProductInterface $product): array
    {
        $data = [
            'item_id' => $this->productIdentifierHelper->getProductIdentifier($product),
            'index' => 0,
        ];

        // item_name is required
        $productName = $product->getName();
        if (null !== $productName) {
            $data['item_name'] = $productName;
        } else {
            $data['item_name'] = $data['item_id'];
        }

        $taxon = $product->getMainTaxon();
        if (null !== $taxon && null !== $taxon->getName()) {
            $data['item_category'] = $taxon->getName();
        }

        return $data;
    }

    public function createNewFromProductVariant(ProductVariantInterface $productVariant): array
    {
        /** @var ProductInterface|null $product */
        $product = $productVariant->getProduct();
        Assert::notNull($product, 'Product variant must have a product');

        $data = $this->createNewFromProduct($product);

        $variantName = $productVariant->getName();
        $variantCode = $productVariant->getCode();
        if (null !== $variantName || null !== $variantCode) {
            $data['item_variant'] = $variantName ?? $variantCode;
        }

        return $data;
    }

    public function createNewFromOrderItem(OrderItemInterface $orderItem, OrderInterface $order): array
    {
        $productVariant = $orderItem->getVariant();
        Assert::notNull($productVariant, 'Order item must have a variant');

        $index = $order->getItems()->indexOf($orderItem);
        $index = $index === false ? 0 : $index;

        $data = $this->createNewFromProductVariant($productVariant);

        $data['index'] = $index;
        $data['price'] = $orderItem->getFullDiscountedUnitPrice() / 100;
        $data['quantity'] = $orderItem->getQuantity();

        if (null !== $order->getChannel() && null !== $order->getChannel()->getName()) {
            $data['affiliation'] = $order->getChannel()->getName();
        }

        return $data;
    }
}
