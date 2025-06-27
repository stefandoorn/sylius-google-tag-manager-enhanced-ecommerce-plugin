<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CartInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CartListener
{
    public function __construct(
        private CartContextInterface $cartContext,
        private CartInterface $cart,
    ) {
    }

    public function onCartSummary(GenericEvent $event): void
    {
        /** @var OrderInterface|null $order */
        $order = $event->getSubject();
        Assert::notNull($order);

        $this->cart->view($order);
    }

    public function onAddToCart(GenericEvent $event): void
    {
        /** @var AddToCartCommandInterface|mixed $addToCartCommand */
        $addToCartCommand = $event->getSubject();
        Assert::isInstanceOf($addToCartCommand, AddToCartCommandInterface::class);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $addToCartCommand->getCartItem();

        /** @var OrderInterface $order */
        $order = $addToCartCommand->getCart();

        $this->cart->add($orderItem, $order);
    }

    public function onRemoveFromCart(GenericEvent $event): void
    {
        /** @var OrderItemInterface|mixed $orderItem */
        $orderItem = $event->getSubject();
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();

        $this->cart->remove($orderItem, $order);
    }
}
