<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class CheckoutStep implements CheckoutStepInterface
{
    use CreateProductTrait;

    public const STEP_CART = 1;

    public const STEP_ADDRESS = 2;

    public const STEP_SHIPPING = 3;

    public const STEP_PAYMENT = 4;

    public const STEP_CONFIRM = 5;

    private GoogleTagManagerInterface $googleTagManager;

    private ProductIdentifierHelper $productIdentifierHelper;

    private CurrencyContextInterface $currencyContext;

    private ChannelContextInterface $channelContext;

    private GoogleImplementationEnabled $googleImplementationEnabled;

    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ProductIdentifierHelper $productIdentifierHelper,
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        GoogleImplementationEnabled $googleImplementationEnabled
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->productIdentifierHelper = $productIdentifierHelper;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
    }

    public function addStep(OrderInterface $order, int $step): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->addStepUA($order, $step);
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            $this->addStepGA4($order, $step);
        }
    }

    private function addStepUA(OrderInterface $order, int $step): void
    {
        $checkout = [
            'actionField' => [
                'step' => $step,
            ],
        ];

        if ($step <= self::STEP_CONFIRM) {
            $checkout['products'] = $this->getProductsUA($order);
        }

        $this->googleTagManager->addPush([
            'event' => 'checkout',
            'ecommerce' => [
                'checkout' => $checkout,
            ],
        ]);
    }

    private function getProductsUA(OrderInterface $order): array
    {
        $products = [];

        foreach ($order->getItems() as $item) {
            $products[] = $this->createProductUA($item);
        }

        return $products;
    }

    private function getProductsGA4(OrderInterface $order): array
    {
        $products = [];

        foreach ($order->getItems() as $item) {
            $products[] = $this->createProductGA4($item);
        }

        return $products;
    }

    private function addStepGA4(OrderInterface $order, int $step): void
    {
        if (self::STEP_CART !== $step) { // In GA4 only 'begin_checkout' is recorded
            return;
        }

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#initiate_the_checkout_process
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $cart = [
            'currency' => $this->currencyContext->getCurrencyCode(),
            'value' => $order->getTotal() / 100,
            'items' => $this->getProductsGA4($order),
        ];
        if ($order->getPromotionCoupon() !== null) {
            $cart['coupon'] = $order->getPromotionCoupon()->getCode();
        }

        $this->googleTagManager->addPush([
            'event' => 'begin_checkout',
            'ecommerce' => $cart,
        ]);
    }
}
