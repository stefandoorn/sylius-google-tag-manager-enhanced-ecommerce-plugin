<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductVariantPriceHelperInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class ViewItem implements ViewItemInterface
{
    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private ChannelContextInterface $channelContext,
        private CurrencyContextInterface $currencyContext,
        private ProductIdentifierHelperInterface $productIdentifierHelper,
        private ProductVariantResolverInterface $productVariantResolver,
        private ProductVariantPriceHelperInterface $productVariantPriceHelper,
    ) {
    }

    public function add(ProductInterface $product): void
    {
        $this->addViewItemData($product);
    }

    private function addViewItemData(ProductInterface $product): void
    {
        /** @var TaxonInterface|null $mainTaxon */
        $mainTaxon = $product->getMainTaxon();

        $data = [
            'items' => [
                [
                    'item_id' => $this->productIdentifierHelper->getProductIdentifier($product),
                    'item_name' => $product->getName(),
                    'affiliation' => $this->channelContext->getChannel()->getName(),
                    'item_category' => null !== $mainTaxon ? $mainTaxon->getName() : '',
                    'index' => 0,
                ],
            ],
        ];

        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantResolver->getVariant($product);
        if (null !== $productVariant) {
            $data['value'] = $this->productVariantPriceHelper->getProductVariantPrice($productVariant) / 100;
            $data['currency'] = $this->currencyContext->getCurrencyCode();

            $data['items'][0]['price'] = $data['value'];
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
