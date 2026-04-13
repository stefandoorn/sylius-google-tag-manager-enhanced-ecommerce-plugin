<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class AddTransaction implements AddTransactionInterface
{
    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private ChannelContextInterface $channelContext,
        private CurrencyContextInterface $currencyContext,
        private ProductIdentifierHelperInterface $productIdentifierHelper,
    ) {
    }

    public function addTransaction(OrderInterface $order): void
    {
        $products = [];
        foreach ($order->getItems() as $index => $item) {
            /** @var OrderItemInterface $item */
            $products[] = $this->createProduct($item, $index);
        }

        $purchase = [
            'transaction_id' => $order->getNumber(),
            'value' => $order->getTotal() / 100,
            'tax' => $order->getTaxTotal() / 100,
            'shipping' => $order->getShippingTotal() / 100,
            'currency' => $this->currencyContext->getCurrencyCode(),
            'items' => $products,
        ];
        if ($order->getPromotionCoupon() !== null) {
            $purchase['coupon'] = $order->getPromotionCoupon()->getCode();
        }

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#make_a_purchase_or_issue_a_refund
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $this->googleTagManager->addPush([
            'event' => 'purchase',
            'ecommerce' => $purchase,
        ]);
    }

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
