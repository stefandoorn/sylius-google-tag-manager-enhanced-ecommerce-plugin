<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\Cart;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class CartListener
{
    public const POST_ADD_ORDER_ITEM_UA = 'post_add_order_item_ua';

    public const POST_REMOVE_ORDER_ITEM_UA = 'post_remove_order_item_ua';

    public const POST_ADD_ORDER_ITEM_GA4 = 'post_add_order_item_ga4';

    public const POST_REMOVE_ORDER_ITEM_GA4 = 'post_remove_order_item_ga4';

    private RequestStack $requestStack;

    private Cart $cart;

    private FirewallMap $firewallMap;

    private GoogleImplementationEnabled $googleImplementationEnabled;

    public function __construct(
        RequestStack $requestStack,
        Cart $cart,
        FirewallMap $firewallMap,
        GoogleImplementationEnabled $googleImplementationEnabled
    ) {
        $this->requestStack = $requestStack;
        $this->cart = $cart;
        $this->firewallMap = $firewallMap;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
    }

    public function onAddToCart(ResourceControllerEvent $event): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $session->set(
                self::POST_ADD_ORDER_ITEM_UA,
                $this->cart->getOrderItemUA($event->getSubject()),
            );
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            $session->set(
                self::POST_ADD_ORDER_ITEM_GA4,
                $this->cart->getOrderItemGA4($event->getSubject()),
            );
        }
    }

    public function onRemoveFromCart(ResourceControllerEvent $event): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $session->set(
                self::POST_REMOVE_ORDER_ITEM_UA,
                $this->cart->getOrderItemUA($event->getSubject()),
            );
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            $session->set(
                self::POST_REMOVE_ORDER_ITEM_GA4,
                $this->cart->getOrderItemGA4($event->getSubject()),
            );
        }
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

        if ($this->googleImplementationEnabled->isUAEnabled()) {
            if ($session->has(self::POST_ADD_ORDER_ITEM_UA)) {
                $orderItem = $session->get(self::POST_ADD_ORDER_ITEM_UA);
                $session->remove(self::POST_ADD_ORDER_ITEM_UA);
                $this->cart->addUA($orderItem);
            }

            if ($session->has(self::POST_REMOVE_ORDER_ITEM_UA)) {
                $orderItem = $session->get(self::POST_REMOVE_ORDER_ITEM_UA);
                $session->remove(self::POST_REMOVE_ORDER_ITEM_UA);
                $this->cart->removeUA($orderItem);
            }
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            if ($session->has(self::POST_ADD_ORDER_ITEM_GA4)) {
                $orderItem = $session->get(self::POST_ADD_ORDER_ITEM_GA4);
                $session->remove(self::POST_ADD_ORDER_ITEM_GA4);
                $this->cart->addGA4($orderItem);
            }

            if ($session->has(self::POST_REMOVE_ORDER_ITEM_GA4)) {
                $orderItem = $session->get(self::POST_REMOVE_ORDER_ITEM_GA4);
                $session->remove(self::POST_REMOVE_ORDER_ITEM_GA4);
                $this->cart->removeGA4($orderItem);
            }
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
