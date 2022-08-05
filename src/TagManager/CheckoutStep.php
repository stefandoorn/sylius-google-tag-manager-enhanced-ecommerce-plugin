<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use Sylius\Component\Order\Model\OrderInterface;
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

    private GoogleImplementationEnabled $googleImplementationEnabled;

    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ProductIdentifierHelper $productIdentifierHelper,
        GoogleImplementationEnabled $googleImplementationEnabled
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->productIdentifierHelper = $productIdentifierHelper;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
    }

    public function addStep(OrderInterface $order, int $step): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->addStepUA($order, $step);
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
            $checkout['products'] = $this->getProducts($order);
        }

        $this->googleTagManager->addPush([
            'event' => 'checkout',
            'ecommerce' => [
                'checkout' => $checkout,
            ],
        ]);
    }

    private function getProducts(OrderInterface $order): array
    {
        $products = [];

        foreach ($order->getItems() as $item) {
            $products[] = $this->createProduct($item);
        }

        return $products;
    }
}
