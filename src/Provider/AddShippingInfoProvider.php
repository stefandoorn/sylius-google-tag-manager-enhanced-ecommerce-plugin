<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Webmozart\Assert\Assert;

class AddShippingInfoProvider implements GtmProviderInterface
{
    public function __construct(
        protected GtmEcommerceFactoryInterface $gtmEcommerceFactory,
    ) {
    }

    public function getEvent(array $context): ?string
    {
        return 'add_shipping_info';
    }

    public function getEcommerce(array $context): ?array
    {
        Assert::keyExists($context, ContextInterface::CONTEXT_ORDER);

        /** @var OrderInterface|null $order */
        $order = $context[ContextInterface::CONTEXT_ORDER];
        Assert::isInstanceOf($order, OrderInterface::class);

        $ecommerce = $this->gtmEcommerceFactory->createNewFromOrder($order) ?? [];

        $ecommerce['shipping_tier'] = implode(
            ', ',
            $order->getShipments()->map(function (ShipmentInterface $shipment) {
                return $shipment->getMethod()?->getName();
            })->toArray(),
        );

        return $ecommerce;
    }
}
