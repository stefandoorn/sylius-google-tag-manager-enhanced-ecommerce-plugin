<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\GtmProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class Cart implements CartInterface
{
    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private GtmProviderInterface $viewCartProvider,
        private GtmProviderInterface $addToCartProvider,
        private GtmProviderInterface $removeFromCartProvider,
    ) {
    }

    public function view(OrderInterface $order): void
    {
        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#add_or_remove_an_item_from_a_shopping_cart
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $context = [
            ContextInterface::CONTEXT_ORDER => $order,
        ];

        $this->googleTagManager->addPush([
            'event' => $this->viewCartProvider->getEvent($context),
            'ecommerce' => $this->viewCartProvider->getEcommerce($context),
        ]);
    }

    public function add(OrderItemInterface $orderItem, OrderInterface $order): void
    {
        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#add_or_remove_an_item_from_a_shopping_cart
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $context = [
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
            ContextInterface::CONTEXT_ORDER => $order,
        ];

        $this->googleTagManager->addPush([
            'event' => $this->addToCartProvider->getEvent($context),
            'ecommerce' => $this->addToCartProvider->getEcommerce($context),
        ]);
    }

    public function remove(OrderItemInterface $orderItem, OrderInterface $order): void
    {
        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#add_or_remove_an_item_from_a_shopping_cart
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $context = [
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
            ContextInterface::CONTEXT_ORDER => $order,
        ];

        $this->googleTagManager->addPush([
            'event' => $this->removeFromCartProvider->getEvent($context),
            'ecommerce' => $this->removeFromCartProvider->getEcommerce($context),
        ]);
    }
}
