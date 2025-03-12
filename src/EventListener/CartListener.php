<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\MainRequest\RequestStackMainRequest;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CartInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class CartListener
{
    public const POST_ADD_ORDER_ITEM = 'post_add_order_item';

    public const POST_REMOVE_ORDER_ITEM = 'post_remove_order_item';

    public function __construct(
        private RequestStack $requestStack,
        private CartInterface $cart,
        private FirewallMap $firewallMap,
    ) {
    }

    public function onAddToCart(ResourceControllerEvent $event): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        /** @var OrderItemInterface $orderItem */
        $orderItem = $event->getSubject();

        $session->set(
            self::POST_ADD_ORDER_ITEM,
            $this->cart->getOrderItem($orderItem),
        );
    }

    public function onRemoveFromCart(ResourceControllerEvent $event): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        /** @var OrderItemInterface $orderItem */
        $orderItem = $event->getSubject();

        $session->set(
            self::POST_REMOVE_ORDER_ITEM,
            $this->cart->getOrderItem($orderItem),
        );
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

        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        if ($session->has(self::POST_ADD_ORDER_ITEM)) {
            /** @var array<string, mixed> $orderItem */
            $orderItem = $session->get(self::POST_ADD_ORDER_ITEM);
            $session->remove(self::POST_ADD_ORDER_ITEM);
            $this->cart->add($orderItem);
        }

        if ($session->has(self::POST_REMOVE_ORDER_ITEM)) {
            /** @var array<string, mixed> $orderItem */
            $orderItem = $session->get(self::POST_REMOVE_ORDER_ITEM);
            $session->remove(self::POST_REMOVE_ORDER_ITEM);
            $this->cart->remove($orderItem);
        }
    }

    private function getSession(): ?SessionInterface
    {
        $request = RequestStackMainRequest::getMainRequest($this->requestStack);
        if (null === $request) {
            return null;
        }

        return $request->hasSession() ? $request->getSession() : null;
    }
}
