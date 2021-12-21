<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

/**
 * Class CheckoutStep
 */
final class CheckoutStep implements CheckoutStepInterface
{
    public const STEP_CART = 1;

    public const STEP_ADDRESS = 2;

    public const STEP_SHIPPING = 3;

    public const STEP_PAYMENT = 4;

    public const STEP_CONFIRM = 5;

    public const STEP_THANKYOU = 6;

    /** @var GoogleTagManagerInterface */
    private $googleTagManager;

    /**
     * CheckoutStep constructor.
     */
    public function __construct(GoogleTagManagerInterface $googleTagManager)
    {
        $this->googleTagManager = $googleTagManager;
    }

    /**
     * @inheritdoc
     */
    public function addStep(OrderInterface $order, int $step): void
    {
        $checkout = [
            'actionField' => [
                'step' => $step,
            ],
        ];

        if ($step < self::STEP_THANKYOU) {
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

    private function createProduct(OrderItemInterface $item): array
    {
        return [
            'name' => $item->getProduct()->getName(),
            'id' => $item->getProduct()->getId(),
            'quantity' => $item->getQuantity(),
            'variant' => $item->getVariant()->getName() ?? $item->getVariant()->getCode(),
            'category' => null !== $item->getProduct()->getMainTaxon() ? $item->getProduct()->getMainTaxon()->getName() : '',
            'price' => $item->getUnitPrice() / 100,
        ];
    }
}
