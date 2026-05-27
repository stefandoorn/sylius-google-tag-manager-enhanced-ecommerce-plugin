<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CartInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartListener
{
    public function __construct(
        private CartContextInterface $cartContext,
        private CartInterface $cart,
    ) {
    }

    public function onCartSummary(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof OrderInterface) {
            return;
        }

        $this->cart->view($subject);
    }

    public function onAddToCart(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof AddToCartCommandInterface) {
            return;
        }

        $cart = $subject->getCart();
        $orderItem = $subject->getCartItem();

        if (!$cart instanceof OrderInterface || !$orderItem instanceof OrderItemInterface) {
            return;
        }

        $this->cart->add($orderItem, $cart);
    }

    public function onRemoveFromCart(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof OrderItemInterface) {
            return;
        }

        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException) {
            return;
        }

        if (!$cart instanceof OrderInterface) {
            return;
        }

        $this->cart->remove($subject, $cart);
    }
}
