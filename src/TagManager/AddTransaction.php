<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;

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
        $products = [];
        foreach ($order->getItems() as $item) {
            /** @var OrderItemInterface $item */
            $products[] = $this->createProduct($item);
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
}
