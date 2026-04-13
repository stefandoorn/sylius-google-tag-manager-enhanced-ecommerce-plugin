<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CartInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class CartListener
{
    public function __construct(
        private CartInterface $cart,
        private FirewallMap $firewallMap,
        private CartContextInterface $cartContext,
    ) {
    }

    public function onAddToCart(ResourceControllerEvent $event): void
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $event->getSubject();

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();

        $this->cart->add($orderItem, $order);
    }

    public function onRemoveFromCart(ResourceControllerEvent $event): void
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $event->getSubject();

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();

        $this->cart->remove($orderItem, $order);
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $firewallConfig = $this->firewallMap->getFirewallConfig($event->getRequest());
        if (null === $firewallConfig) {
            return;
        }

        if ('shop' !== $firewallConfig->getName()) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();

        $this->cart->view($order);
    }
}
