<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class AddTransaction implements AddTransactionInterface
{
    use CreateProductTrait;

    private GoogleTagManagerInterface $googleTagManager;

    private ChannelContextInterface $channelContext;

    private CurrencyContextInterface $currencyContext;

    private ProductIdentifierHelper $productIdentifierHelper;

    private GoogleImplementationEnabled $googleImplementationEnabled;

    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        ProductIdentifierHelper $productIdentifierHelper,
        GoogleImplementationEnabled $googleImplementationEnabled
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
        $this->productIdentifierHelper = $productIdentifierHelper;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
    }

    public function addTransaction(OrderInterface $order): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->addTransactionUA($order);
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            $this->addTransactionGA4($order);
        }
    }

    private function addTransactionUA(OrderInterface $order): void
    {
        $products = [];
        foreach ($order->getItems() as $item) {
            /** @var OrderItemInterface $item */
            $products[] = $this->createProductUA($item);
        }

        $actionField = [
            'id' => $order->getNumber(),
            'affiliation' => $this->channelContext->getChannel()->getName(),
            'tax' => $order->getTaxTotal() / 100,
            'revenue' => $order->getTotal() / 100,
            'shipping' => $order->getShippingTotal() / 100,
        ];

        if ($order->getPromotionCoupon() !== null) {
            $actionField['coupon'] = $order->getPromotionCoupon()->getCode();
        }

        $purchase = [
            'actionField' => $actionField,
            'products' => $products,
        ];

        $this->googleTagManager->mergeData('ecommerce', [
            'currencyCode' => $this->currencyContext->getCurrencyCode(),
            'purchase' => $purchase,
        ]);
    }

    private function addTransactionGA4(OrderInterface $order): void
    {
        $products = [];
        foreach ($order->getItems() as $index => $item) {
            /** @var OrderItemInterface $item */
            $products[] = $this->createProductGA4($item, $index);
        }

        $purchase = [
            'transaction_id' => $order->getNumber(),
            'affiliation' => $this->channelContext->getChannel()->getName(),
            'value' => $order->getTotal() / 100,
            'tax' => $order->getTaxTotal() / 100,
            'shipping' => $order->getShippingTotal() / 100,
            'currency' => $this->currencyContext->getCurrencyCode(),
        ];
        if ($order->getPromotionCoupon() !== null) {
            $purchase['coupon'] = $order->getPromotionCoupon()->getCode();
        }

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtag#make_a_purchase_or_issue_a_refund
        $this->googleTagManager->addPush(\array_merge([
            'event' => 'purchase',
            'items' => $products,
        ], $purchase));
    }
}
