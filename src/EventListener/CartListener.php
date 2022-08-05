<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\Cart;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class CartListener
{
    public const POST_ADD_ORDER_ITEM_UA = 'post_add_order_item_ua';

    public const POST_REMOVE_ORDER_ITEM_UA = 'post_remove_order_item_ua';

    public const POST_ADD_ORDER_ITEM_GA4 = 'post_add_order_item_ga4';

    public const POST_REMOVE_ORDER_ITEM_GA4 = 'post_remove_order_item_ga4';

    private SessionInterface $session;

    private Cart $cart;

    private GoogleImplementationEnabled $googleImplementationEnabled;

    public function __construct(
        SessionInterface $session,
        Cart $cart,
        GoogleImplementationEnabled $googleImplementationEnabled
    ) {
        $this->session = $session;
        $this->cart = $cart;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
    }

    public function onAddToCart(ResourceControllerEvent $event): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->session->set(
                self::POST_ADD_ORDER_ITEM_UA,
                $this->cart->getOrderItemUA($event->getSubject())
            );
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            $this->session->set(
                self::POST_ADD_ORDER_ITEM_GA4,
                $this->cart->getOrderItemGA4($event->getSubject())
            );
        }
    }

    public function onRemoveFromCart(ResourceControllerEvent $event): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->session->set(
                self::POST_REMOVE_ORDER_ITEM_UA,
                $this->cart->getOrderItemUA($event->getSubject())
            );
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            $this->session->set(
                self::POST_REMOVE_ORDER_ITEM_GA4,
                $this->cart->getOrderItemGA4($event->getSubject())
            );
        }
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            if ($this->session->has(self::POST_ADD_ORDER_ITEM_UA)) {
                $orderItem = $this->session->get(self::POST_ADD_ORDER_ITEM_UA);
                $this->session->remove(self::POST_ADD_ORDER_ITEM_UA);
                $this->cart->addUA($orderItem);
            }

            if ($this->session->has(self::POST_REMOVE_ORDER_ITEM_UA)) {
                $orderItem = $this->session->get(self::POST_REMOVE_ORDER_ITEM_UA);
                $this->session->remove(self::POST_REMOVE_ORDER_ITEM_UA);
                $this->cart->removeUA($orderItem);
            }
        }

        if ($this->googleImplementationEnabled->isGA4Enabled()) {
            if ($this->session->has(self::POST_ADD_ORDER_ITEM_GA4)) {
                $orderItem = $this->session->get(self::POST_ADD_ORDER_ITEM_GA4);
                $this->session->remove(self::POST_ADD_ORDER_ITEM_GA4);
                $this->cart->addGA4($orderItem);
            }

            if ($this->session->has(self::POST_REMOVE_ORDER_ITEM_GA4)) {
                $orderItem = $this->session->get(self::POST_REMOVE_ORDER_ITEM_GA4);
                $this->session->remove(self::POST_REMOVE_ORDER_ITEM_GA4);
                $this->cart->removeGA4($orderItem);
            }
        }
    }
}
