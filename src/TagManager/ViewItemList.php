<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\GtmProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class ViewItemList implements ViewItemListInterface
{
    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private ChannelContextInterface $channelContext,
        private CurrencyContextInterface $currencyContext,
        private GtmProviderInterface $viewItemListProvider,
    ) {
    }

    public function add(TaxonInterface $taxon, array $products): void
    {
        $this->addViewItemListData($taxon, $products);
    }

    /** @param \Sylius\Component\Core\Model\ProductInterface[] $products */
    private function addViewItemListData(TaxonInterface $taxon, array $products): void
    {
        if (0 === \count($products)) {
            return;
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $context = [
            ContextInterface::CONTEXT_PRODUCTS => $products,
            ContextInterface::CONTEXT_TAXON => $taxon,
            ContextInterface::CONTEXT_CHANNEL => $channel,
            ContextInterface::CONTEXT_CURRENCY_CODE => $this->currencyContext->getCurrencyCode(),
        ];

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#view_item_details
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $this->googleTagManager->addPush([
            'event' => $this->viewItemListProvider->getEvent($context),
            'ecommerce' => $this->viewItemListProvider->getEcommerce($context),
        ]);
    }
}
