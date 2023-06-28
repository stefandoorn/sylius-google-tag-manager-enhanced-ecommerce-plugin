<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

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

    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        ProductIdentifierHelper $productIdentifierHelper
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
        $this->productIdentifierHelper = $productIdentifierHelper;
    }

    public function addTransaction(OrderInterface $order): void
    {
        $this->addTransactionGA4($order);
    }

    private function addTransactionGA4(OrderInterface $order): void
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
}
