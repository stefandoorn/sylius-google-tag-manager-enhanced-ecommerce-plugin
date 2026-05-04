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

        /** @var ProductInterface[] $products */
        $products = $context[ContextInterface::CONTEXT_PRODUCTS];

        /** @var string $currencyCode */
        $currencyCode = $context[ContextInterface::CONTEXT_CURRENCY_CODE];

        /** @var ChannelInterface $channel */
        $channel = $context[ContextInterface::CONTEXT_CHANNEL];

        /** @var TaxonInterface|null $taxon */
        $taxon = $context[ContextInterface::CONTEXT_TAXON] ?? null;

        $items = [];
        foreach ($products as $index => $product) {
            /** @var ProductVariantInterface|null $productVariant */
            $productVariant = $this->productVariantResolver->getVariant($product);
            if (null === $productVariant) {
                continue;
            }

            $price = $this->productVariantPricesCalculator->calculate(
                $productVariant,
                ['channel' => $channel],
            );

            $item = $this->gtmItemFactory->createNewFromProductVariant($productVariant);
            $item['price'] = $price / 100;
            $item['affiliation'] = $channel->getName();
            $item['index'] = $index;

            if (null !== $taxon) {
                $item['item_list_id'] = (string) $taxon->getCode();
                $item['item_list_name'] = (string) $taxon->getName();
            }

            $items[] = $item;
        }

        if (0 === count($items)) {
            return null;
        }

        return [
            'currency' => $currencyCode,
            'items' => $items,
        ];
    }
}
