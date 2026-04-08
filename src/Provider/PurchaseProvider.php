<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

class PurchaseProvider implements GtmProviderInterface
{
    public function __construct(
        protected GtmEcommerceFactoryInterface $gtmEcommerceFactory,
    ) {
    }

    public function getEvent(array $context): ?string
    {
        return 'purchase';
    }

    public function getEcommerce(array $context): ?array
    {
        Assert::keyExists($context, ContextInterface::CONTEXT_ORDER);

        /** @var OrderInterface|null $order */
        $order = $context[ContextInterface::CONTEXT_ORDER];
        Assert::isInstanceOf($order, OrderInterface::class);

        $ecommerce = $this->gtmEcommerceFactory->createNewFromOrder($order) ?? [];

        /** https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm&hl=fr#purchase-gtm */
        $ecommerce['transaction_id'] = $order->getNumber();

        return $ecommerce;
    }
}
