<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CheckoutStepInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CheckoutStepListener
{
    public function __construct(
        private CheckoutStepInterface $checkoutStep,
        private CartContextInterface $cartContext,
        private string $state,
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();

        if ($this->state !== $order->getCheckoutState()) {
            return;
        }

        $this->checkoutStep->addStep($order, $this->state);
    }
}
