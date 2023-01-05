<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\Cart;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class CartListener
{
    public const POST_ADD_ORDER_ITEM_UA = 'post_add_order_item_ua';

    public const POST_REMOVE_ORDER_ITEM_UA = 'post_remove_order_item_ua';

    public const POST_ADD_ORDER_ITEM_GA4 = 'post_add_order_item_ga4';

    public const POST_REMOVE_ORDER_ITEM_GA4 = 'post_remove_order_item_ga4';

    private RequestStack $requestStack;

    private Cart $cart;

    private GoogleImplementationEnabled $googleImplementationEnabled;

    public function __construct(
        RequestStack $requestStack,
        Cart $cart,
        GoogleImplementationEnabled $googleImplementationEnabled,
    ) {
        $this->requestStack = $requestStack;
        $this->cart = $cart;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
    }

    public function onAddToCart(ResourceControllerEvent $event): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->requestStack->getSession()->set(
                self::POST_ADD_ORDER_ITEM_UA,
                $this->cart->getOrderItemUA($event->getSubject()),
            );
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            $this->requestStack->getSession()->set(
                self::POST_ADD_ORDER_ITEM_GA4,
                $this->cart->getOrderItemGA4($event->getSubject()),
            );
        }
    }

    public function onRemoveFromCart(ResourceControllerEvent $event): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->requestStack->getSession()->set(
                self::POST_REMOVE_ORDER_ITEM_UA,
                $this->cart->getOrderItemUA($event->getSubject()),
            );
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            $this->requestStack->getSession()->set(
                self::POST_REMOVE_ORDER_ITEM_GA4,
                $this->cart->getOrderItemGA4($event->getSubject()),
            );
        }
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            if ($this->requestStack->getSession()->has(self::POST_ADD_ORDER_ITEM_UA)) {
                $orderItem = $this->requestStack->getSession()->get(self::POST_ADD_ORDER_ITEM_UA);
                $this->requestStack->getSession()->remove(self::POST_ADD_ORDER_ITEM_UA);
                $this->cart->addUA($orderItem);
            }

            if ($this->requestStack->getSession()->has(self::POST_REMOVE_ORDER_ITEM_UA)) {
                $orderItem = $this->requestStack->getSession()->get(self::POST_REMOVE_ORDER_ITEM_UA);
                $this->requestStack->getSession()->remove(self::POST_REMOVE_ORDER_ITEM_UA);
                $this->cart->removeUA($orderItem);
            }
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            if ($this->requestStack->getSession()->has(self::POST_ADD_ORDER_ITEM_GA4)) {
                $orderItem = $this->requestStack->getSession()->get(self::POST_ADD_ORDER_ITEM_GA4);
                $this->requestStack->getSession()->remove(self::POST_ADD_ORDER_ITEM_GA4);
                $this->cart->addGA4($orderItem);
            }

            if ($this->requestStack->getSession()->has(self::POST_REMOVE_ORDER_ITEM_GA4)) {
                $orderItem = $this->requestStack->getSession()->get(self::POST_REMOVE_ORDER_ITEM_GA4);
                $this->requestStack->getSession()->remove(self::POST_REMOVE_ORDER_ITEM_GA4);
                $this->cart->removeGA4($orderItem);
            }
        }
    }
}
