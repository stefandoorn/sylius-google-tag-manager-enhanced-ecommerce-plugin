<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class Cart implements CartInterface
{
    use CreateProductTrait;

    private GoogleTagManagerInterface $googleTagManager;

    private ChannelContextInterface $channelContext;

    private CurrencyContextInterface $currencyContext;

    private ProductIdentifierHelper $productIdentifierHelper;

    private GoogleImplementationEnabled $googleImplementationEnabled;

    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        ProductIdentifierHelper $productIdentifierHelper,
        GoogleImplementationEnabled $googleImplementationEnabled
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
        $this->productIdentifierHelper = $productIdentifierHelper;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
    }

    public function getOrderItemUA(OrderItemInterface $orderItem): array
    {
        return $this->createProductUA($orderItem);
    }

    public function getOrderItemGA4(OrderItemInterface $orderItem): array
    {
        return $this->createProductGA4($orderItem);
    }

    public function addUA(array $productData): void
    {
        $this->googleTagManager->addPush([
            'event' => 'addToCart',
            'ecommerce' => [
                'currencyCode' => $this->currencyContext->getCurrencyCode(),
                'add' => [
                    'products' => [$productData],
                ],
            ],
        ]);
    }

    public function addGA4(array $productData): void
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

    public function removeUA(array $productData): void
    {
        $this->googleTagManager->addPush([
            'event' => 'removeFromCart',
            'ecommerce' => [
                'currencyCode' => $this->currencyContext->getCurrencyCode(),
                'remove' => [
                    'products' => [$productData],
                ],
            ],
        ]);
    }

    public function removeGA4(array $productData): void
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
