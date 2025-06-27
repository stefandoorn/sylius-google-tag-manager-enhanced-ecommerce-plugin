<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Webmozart\Assert\Assert;

class AddToCartProvider implements GtmProviderInterface
{
    public function __construct(
        protected GtmEcommerceFactoryInterface $gtmEcommerceFactory,
    ) {
    }

    public function getEvent(array $context): ?string
    {
        return 'add_to_cart';
    }

    public function getEcommerce(array $context): ?array
    {
        Assert::keyExists($context, ContextInterface::CONTEXT_ORDER_ITEM);
        Assert::keyExists($context, ContextInterface::CONTEXT_ORDER);

        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $context[ContextInterface::CONTEXT_ORDER_ITEM] ?? null;
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);

        /** @var OrderInterface|null $order */
        $order = $context[ContextInterface::CONTEXT_ORDER] ?? null;
        Assert::isInstanceOf($order, OrderInterface::class);

        return $this->gtmEcommerceFactory->createNewFromSingleOrderItem($orderItem, $order);
    }
}
