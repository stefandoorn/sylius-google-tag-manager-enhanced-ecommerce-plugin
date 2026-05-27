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
        $this->push($this->viewCartProvider, [
            ContextInterface::CONTEXT_ORDER => $order,
        ]);
    }

    public function add(OrderItemInterface $orderItem, OrderInterface $order): void
    {
        $this->push($this->addToCartProvider, [
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
            ContextInterface::CONTEXT_ORDER => $order,
        ]);
    }

    public function remove(OrderItemInterface $orderItem, OrderInterface $order): void
    {
        $this->push($this->removeFromCartProvider, [
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
            ContextInterface::CONTEXT_ORDER => $order,
        ]);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function push(GtmProviderInterface $provider, array $context): void
    {
        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $this->googleTagManager->addPush([
            'event' => $provider->getEvent($context),
            'ecommerce' => $provider->getEcommerce($context),
        ]);
    }
}
