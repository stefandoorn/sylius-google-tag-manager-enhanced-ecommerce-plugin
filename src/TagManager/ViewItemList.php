<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Doctrine\Common\Collections\ArrayCollection;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductVariantPriceHelperInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class ViewItemList implements ViewItemListInterface
{
    /**
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private ProductRepositoryInterface $productRepository,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private ProductIdentifierHelperInterface $productIdentifierHelper,
        private ProductVariantResolverInterface $productVariantResolver,
        private ProductVariantPriceHelperInterface $productVariantPriceHelper,
    ) {
    }

    public function add(TaxonInterface $taxon, ?string $listId = null): void
    {
        $this->addViewItemListData($taxon, $listId);
    }

    private function addViewItemListData(TaxonInterface $taxon, ?string $listId = null): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $products = new ArrayCollection(
            $this->productRepository->createShopListQueryBuilder(
                $channel,
                $taxon,
                $this->localeContext->getLocaleCode(),
            )->getQuery()->getResult(),
        );

        if (0 === $products->count()) {
            return;
        }

        $index = 0;

        $data = [
            'item_list_id' => $listId,
            'item_list_name' => $taxon->getName(),
            'items' => $products->map(function (ProductInterface $product) use ($taxon, &$index): array {
                $productData = [
                    'item_id' => $this->productIdentifierHelper->getProductIdentifier($product),
                    'item_name' => $product->getName(),
                    'affiliation' => $this->channelContext->getChannel()->getName(),
                    'item_category' => $taxon->getName(),
                    'index' => $index++,
                ];

                /** @var ProductVariantInterface|null $productVariant */
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
