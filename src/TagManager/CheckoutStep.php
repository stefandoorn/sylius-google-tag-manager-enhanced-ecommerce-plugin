<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

/**
 * Class CheckoutStep
 * @package SyliusGoogleAnalyticsEnhancedEcommerceTrackingBundle\TagManager
 */
final class CheckoutStep implements CheckoutStepInterface
{
    const STEP_CART = 1;
    const STEP_ADDRESS = 2;
    const STEP_SHIPPING = 3;
    const STEP_PAYMENT = 4;
    const STEP_CONFIRM = 5;
    const STEP_THANKYOU = 6;

    /**
     * @var GoogleTagManagerInterface
     */
    private $googleTagManager;

    /**
     * CheckoutStep constructor.
     * @param GoogleTagManagerInterface $googleTagManager
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

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function getProducts(OrderInterface $order): array
    {
        $products = [];

        foreach ($order->getItems() as $item) {
            $products[] = $this->createProduct($item);
        }

        return $products;
    }

    /**
     * @param OrderItemInterface $item
     * @return array
     */
    private function createProduct(OrderItemInterface $item): array
    {
        return [
            'name' => $item->getProduct()->getName(),
            'id' => $item->getProduct()->getId(),
            'quantity' => $item->getQuantity(),
            'variant' => $item->getVariant()->getName() ?? $item->getVariant()->getCode(),
            'category' => $item->getProduct()->getMainTaxon() ? $item->getProduct()->getMainTaxon()->getName() : '',
            'price' => $item->getTotal() / 100,
        ];
    }
}
