<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class Cart implements CartInterface
{
    use CreateProductTrait;

    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private ChannelContextInterface $channelContext,
        private CurrencyContextInterface $currencyContext,
        private ProductIdentifierHelperInterface $productIdentifierHelper,
    ) {
    }

    public function getOrderItem(OrderItemInterface $orderItem): array
    {
        return $this->createProduct($orderItem);
    }

    public function add(array $productData): void
    {
        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#add_or_remove_an_item_from_a_shopping_cart
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $this->googleTagManager->addPush([
            'event' => 'add_to_cart',
            'ecommerce' => [
                'currency' => $this->currencyContext->getCurrencyCode(),
                'value' => $productData['price'] * $productData['quantity'],
                'items' => [$productData],
            ],
        ]);
    }

    public function remove(array $productData): void
    {
        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#add_or_remove_an_item_from_a_shopping_cart
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $this->googleTagManager->addPush([
            'event' => 'remove_from_cart',
            'ecommerce' => [
                'currency' => $this->currencyContext->getCurrencyCode(),
                'value' => $productData['price'] * $productData['quantity'],
                'items' => [$productData],
            ],
        ]);
    }
}
