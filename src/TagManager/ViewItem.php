<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class ViewItem implements ViewItemInterface
{
    private GoogleTagManagerInterface $googleTagManager;

    private ChannelContextInterface $channelContext;

    private CurrencyContextInterface $currencyContext;

    private ProductIdentifierHelper $productIdentifierHelper;

    private ProductVariantResolverInterface $productVariantResolver;

    private ProductVariantPriceCalculatorInterface $productVariantPriceCalculator;

    private GoogleImplementationEnabled $googleImplementationEnabled;

    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        ProductIdentifierHelper $productIdentifierHelper,
        ProductVariantResolverInterface $productVariantResolver,
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator,
        GoogleImplementationEnabled $googleImplementationEnabled
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
        $this->productIdentifierHelper = $productIdentifierHelper;
        $this->productVariantResolver = $productVariantResolver;
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
    }

    public function add(ProductInterface $product): void
    {
        if (!$this->googleImplementationEnabled->isGA4Enabled()) {
            return;
        }

        $this->addViewItemData($product);
    }

    private function addViewItemData(ProductInterface $product): void
    {
        /** @var TaxonInterface|null $mainTaxon */
        $mainTaxon = $product->getMainTaxon();

        $data = [
            'items' => [
                'item_id' => $this->productIdentifierHelper->getProductIdentifier($product),
                'item_name' => $product->getName(),
                'affiliation' => $this->channelContext->getChannel()->getName(),
                'item_category' => null !== $mainTaxon ? $mainTaxon->getName() : '',
                'index' => 0,
            ],
        ];

        $productVariant = $this->productVariantResolver->getVariant($product);

        if (null !== $productVariant) {
            $productVariantPrice = $this->productVariantPriceCalculator->calculate(
                $productVariant,
                [
                    'channel' => $this->channelContext->getChannel(),
                ]
            );

            $data['items'][0]['price'] = $productVariantPrice / 100;

            $data['currency'] = $this->currencyContext->getCurrencyCode();
            $data['value'] = $productVariantPrice / 100;
        }

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#view_item_details
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $this->googleTagManager->addPush([
            'event' => 'view_item',
            'ecommerce' => $data,
        ]);
    }
}
