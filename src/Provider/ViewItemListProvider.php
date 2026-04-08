<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmItemFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Webmozart\Assert\Assert;

class ViewItemListProvider implements GtmProviderInterface
{
    public function __construct(
        protected ProductVariantResolverInterface $productVariantResolver,
        protected ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        protected GtmItemFactoryInterface $gtmItemFactory,
    ) {
    }

    public function getEvent(array $context): ?string
    {
        return 'view_item_list';
    }

    public function getEcommerce(array $context): ?array
    {
        Assert::keyExists($context, ContextInterface::CONTEXT_PRODUCTS);
        Assert::keyExists($context, ContextInterface::CONTEXT_CURRENCY_CODE);
        Assert::keyExists($context, ContextInterface::CONTEXT_CHANNEL);

        /** @var TaxonInterface|mixed $taxon */
        $taxon = $context[ContextInterface::CONTEXT_TAXON] ?? null;
        Assert::nullOrIsInstanceOf($taxon, TaxonInterface::class);

        /** @var array<ProductInterface|mixed> $products */
        $products = $context[ContextInterface::CONTEXT_PRODUCTS];
        Assert::allIsInstanceOf($products, ProductInterface::class);

        /** @var string|mixed $currencyCode */
        $currencyCode = $context[ContextInterface::CONTEXT_CURRENCY_CODE];
        Assert::notNull($currencyCode, 'Currency code should not be null');

        /** @var ChannelInterface|mixed $channel */
        $channel = $context[ContextInterface::CONTEXT_CHANNEL];
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $index = 0;
        $ecommerce = [
            'currency' => $currencyCode,
            'items' => array_values(array_filter(array_map(function (ProductInterface $product) use ($channel, &$index): ?array {
                /** @var ProductVariantInterface|null $productVariant */
                $productVariant = $this->productVariantResolver->getVariant($product);
                if (null === $productVariant) {
                    return null;
                }

                $item = $this->gtmItemFactory->createNewFromProductVariant($productVariant);

                $item['price'] = $this->productVariantPricesCalculator->calculate($productVariant, ['channel' => $channel]) / 100;
                $item['affiliation'] = $channel->getName();
                $item['index'] = $index++;

                return $item;
            }, $products))),
        ];

        if (null !== $taxon) {
            $ecommerce['item_list_id'] = $taxon->getCode();
            $ecommerce['item_list_name'] = $taxon->getName();
        }

        return $ecommerce;
    }
}
