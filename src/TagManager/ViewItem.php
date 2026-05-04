<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\GtmProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class ViewItem implements ViewItemInterface
{
    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private ChannelContextInterface $channelContext,
        private CurrencyContextInterface $currencyContext,
        private GtmProviderInterface $viewItemProvider,
    ) {
    }

    public function add(ProductInterface $product): void
    {
        $this->addViewItemData($product);
    }

    private function addViewItemData(ProductInterface $product): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#view_item_details
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $context = [
            ContextInterface::CONTEXT_PRODUCT => $product,
            ContextInterface::CONTEXT_CHANNEL => $channel,
            ContextInterface::CONTEXT_CURRENCY_CODE => $this->currencyContext->getCurrencyCode(),
        ];

        $this->googleTagManager->addPush([
            'event' => $this->viewItemProvider->getEvent($context),
            'ecommerce' => $this->viewItemProvider->getEcommerce($context),
        ]);
    }
}
