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

    public function getOrderItem(OrderItemInterface $orderItem): array
    {
        return $this->createProduct($orderItem);
    }

    public function add(array $productData): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->addUA($productData);
        }
    }

    private function addUA(array $productData): void
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

    public function remove(array $productData): void
    {
        if ($this->googleImplementationEnabled->isUAEnabled()) {
            $this->removeUA($productData);
        }
    }

    private function removeUA(array $productData): void
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
}
