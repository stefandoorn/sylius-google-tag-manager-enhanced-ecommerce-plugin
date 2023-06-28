<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\Cart;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class CartListener
{
    public const POST_ADD_ORDER_ITEM = 'post_add_order_item';

    public const POST_REMOVE_ORDER_ITEM = 'post_remove_order_item';

    private RequestStack $requestStack;

    private Cart $cart;

    private FirewallMap $firewallMap;

    public function __construct(
        RequestStack $requestStack,
        Cart $cart,
        FirewallMap $firewallMap
    ) {
        $this->requestStack = $requestStack;
        $this->cart = $cart;
        $this->firewallMap = $firewallMap;
    }

    public function onAddToCart(ResourceControllerEvent $event): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        $session->set(
            self::POST_ADD_ORDER_ITEM,
            $this->cart->getOrderItem($event->getSubject()),
        );
    }

    public function onRemoveFromCart(ResourceControllerEvent $event): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        $session->set(
            self::POST_REMOVE_ORDER_ITEM,
            $this->cart->getOrderItem($event->getSubject()),
        );
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $firewallConfig = $this->firewallMap->getFirewallConfig($event->getRequest());
        if (null !== $firewallConfig && 'shop' !== $firewallConfig->getName()) {
            return;
        }

        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        if ($session->has(self::POST_ADD_ORDER_ITEM)) {
            $orderItem = $session->get(self::POST_ADD_ORDER_ITEM);
            $session->remove(self::POST_ADD_ORDER_ITEM);
            $this->cart->addGA4($orderItem);
        }

        if ($session->has(self::POST_REMOVE_ORDER_ITEM)) {
            $orderItem = $session->get(self::POST_REMOVE_ORDER_ITEM);
            $session->remove(self::POST_REMOVE_ORDER_ITEM);
            $this->cart->removeGA4($orderItem);
        }
    }

    private function getSession(): ?SessionInterface
    {
        $request = null;
        if (method_exists($this->requestStack, 'getMasterRequest')) {
            $request = $this->requestStack->getMasterRequest();
        }
        if (method_exists($this->requestStack, 'getMainRequest')) {
            $request = $this->requestStack->getMainRequest();
        }

        if (null === $request) {
            return null;
        }

        return $request->hasSession() ? $request->getSession() : null;
    }
}
