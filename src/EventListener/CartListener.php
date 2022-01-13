<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\Cart;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class CartListener
{
    public const POST_ADD_ORDER_ITEM = 'post_add_order_item';

    public const POST_REMOVE_ORDER_ITEM = 'post_remove_order_item';

    private SessionInterface $session;

    private Cart $cart;

    public function __construct(SessionInterface $session, Cart $cart)
    {
        $this->session = $session;
        $this->cart = $cart;
    }

    public function onAddToCart(ResourceControllerEvent $event)
    {
        $this->session->set(self::POST_ADD_ORDER_ITEM, $this->cart->getOrderItem($event->getSubject()));
    }

    public function onRemoveFromCart(ResourceControllerEvent $event)
    {
        $this->session->set(self::POST_REMOVE_ORDER_ITEM, $this->cart->getOrderItem($event->getSubject()));
    }

    public function onKernelController(ControllerEvent $event)
    {
        if ($this->session->has(self::POST_ADD_ORDER_ITEM)) {
            $orderItem = $this->session->get(self::POST_ADD_ORDER_ITEM);
            $this->session->remove(self::POST_ADD_ORDER_ITEM);
            $this->cart->add($orderItem);
        }

        if ($this->session->has(self::POST_REMOVE_ORDER_ITEM)) {
            $orderItem = $this->session->get(self::POST_REMOVE_ORDER_ITEM);
            $this->session->remove(self::POST_REMOVE_ORDER_ITEM);
            $this->cart->remove($orderItem);
        }
    }
}
