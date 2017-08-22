<?php

namespace GtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

/**
 * Class AddTransaction
 * @package SyliusGoogleAnalyticsEnhancedEcommerceTrackingBundle\TagManager
 */
class AddTransaction implements AddTransactionInterface
{

    /**
     * @var GoogleTagManagerInterface
     */
    private $googleTagManager;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * AddTransaction constructor.
     * @param GoogleTagManagerInterface $googleTagManager
     * @param ChannelContextInterface $channelContext
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
    }

    /**
     * @param OrderInterface $order
     */
    public function addTransaction(OrderInterface $order): void
    {
        $products = [];
        foreach ($order->getItems() as $item) {
            /** @var OrderItemInterface $item */
            $products[] = [
                'name' => $item->getProduct()->getName(),
                'id' => $item->getProduct()->getId(),
                'quantity' => $item->getQuantity(),
                'variant' => $item->getVariant()->getName() ?? $item->getVariant()->getCode(),
                'category' => $item->getProduct()->getMainTaxon()->getName(),
                'price' => $item->getTotal() / 100,
            ];
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
            'purchase' => $purchase,
        ]);
    }
}
