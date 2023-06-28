<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
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

    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ProductIdentifierHelper $productIdentifierHelper,
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->productIdentifierHelper = $productIdentifierHelper;
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
    }

    public function addStep(OrderInterface $order, int $step): void
    {
        // Triggers allowed:
        // -----------------
        // 1. Cart -> view_cart
        // 2. Address -> begin_checkout (customer moved past the cart)
        // 4. Payment -> add_shipping_info (customer moved past the shipping step)
        // 5. Confirm -> add_payment_info (customer moved past the payment step)

        $additionalData = [];
        switch ($step) {
            case self::STEP_CART:
                $event = 'view_cart';

                break;
            case self::STEP_ADDRESS:
                $event = 'begin_checkout';

                break;
            case self::STEP_PAYMENT:
                $event = 'add_shipping_info';
                $additionalData['shipping_tier'] = implode(
                    ', ',
                    $order->getShipments()->map(function (ShipmentInterface $shipment) {
                        return $shipment->getMethod()->getName();
                    })->toArray()
                );

                break;
            case self::STEP_CONFIRM:
                $event = 'add_payment_info';
                $additionalData['payment_type'] = implode(
                    ', ',
                    $order->getPayments()->map(function (PaymentInterface $payment) {
                        return $payment->getMethod()->getName();
                    })->toArray()
                );

                break;
            default:
                return;
        }

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#initiate_the_checkout_process
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $cart = [
            'currency' => $this->currencyContext->getCurrencyCode(),
            'value' => $order->getTotal() / 100,
            'items' => $this->getProducts($order),
        ];
        if ($order->getPromotionCoupon() !== null) {
            $cart['coupon'] = $order->getPromotionCoupon()->getCode();
        }

        $this->googleTagManager->addPush([
            'event' => $event,
            'ecommerce' => \array_merge(
                $additionalData,
                $cart,
            ),
        ]);
    }

    private function getProducts(OrderInterface $order): array
    {
        $products = [];

        foreach ($order->getItems() as $index => $item) {
            $products[] = $this->createProduct($item, $index);
        }

        return $products;
    }
}
