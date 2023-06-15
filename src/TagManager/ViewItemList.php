<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Doctrine\Common\Collections\ArrayCollection;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\GoogleImplementationEnabled;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductVariantPriceHelper;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class ViewItemList implements ViewItemListInterface
{
    private GoogleTagManagerInterface $googleTagManager;

    private ProductRepositoryInterface $productRepository;

    private ChannelContextInterface $channelContext;

    private LocaleContextInterface $localeContext;

    private ProductIdentifierHelper $productIdentifierHelper;

    private ProductVariantResolverInterface $productVariantResolver;

    private GoogleImplementationEnabled $googleImplementationEnabled;

    private ProductVariantPriceHelper $productVariantPriceHelper;

    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ProductRepositoryInterface $productRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductIdentifierHelper $productIdentifierHelper,
        ProductVariantResolverInterface $productVariantResolver,
        ProductVariantPriceHelper $productVariantPriceHelper,
        GoogleImplementationEnabled $googleImplementationEnabled
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->productRepository = $productRepository;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->productIdentifierHelper = $productIdentifierHelper;
        $this->productVariantResolver = $productVariantResolver;
        $this->googleImplementationEnabled = $googleImplementationEnabled;
        $this->productVariantPriceHelper = $productVariantPriceHelper;
    }

    public function add(TaxonInterface $taxon, ?string $listId = null): void
    {
        if (!$this->googleImplementationEnabled->isGA4Enabled()) {
            return;
        }

        $this->addViewItemListData($taxon, $listId);
    }

    private function addViewItemListData(TaxonInterface $taxon, ?string $listId = null): void
    {
        $products = new ArrayCollection(
            $this->productRepository->createShopListQueryBuilder(
                $this->channelContext->getChannel(),
                $taxon,
                $this->localeContext->getLocaleCode(),
                [],
                false,
            )->getQuery()->getResult()
        );

        if (0 === $products->count()) {
            return;
        }

        $index = 0;
        $taxonName = null !== $taxon ? $taxon->getName() : '';

        $data = [
            'item_list_id' => $listId,
            'item_list_name' => $taxon->getName(),
            'items' => $products->map(function (ProductInterface $product) use ($taxonName, &$index): array {
                $productData = [
                    'item_id' => $this->productIdentifierHelper->getProductIdentifier($product),
                    'item_name' => $product->getName(),
                    'affiliation' => $this->channelContext->getChannel()->getName(),
                    'item_category' => $taxonName,
                    'index' => $index++,
                ];

                $productVariant = $this->productVariantResolver->getVariant($product);
                if (null !== $productVariant) {
                    $productData['price'] = $this->productVariantPriceHelper->getProductVariantPrice($productVariant) / 100;
                }

                return $productData;
            })->toArray(),
        ];

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#view_item_details
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $this->googleTagManager->addPush([
            'event' => 'view_item_list',
            'ecommerce' => \array_filter($data),
        ]);
    }
}
