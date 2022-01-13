<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory\ProductDetailFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\Factory\ProductDetailImpressionFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailImpressionInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductDetailImpressionDataResolver implements ProductDetailImpressionDataResolverInterface
{
    private ProductVariantPriceCalculatorInterface $productVariantPriceCalculator;

    private ChannelContextInterface $channelContext;

    private ProductDetailImpressionFactoryInterface $productDetailImpressionFactory;

    private ProductDetailFactoryInterface $productDetailFactory;

    private ProductIdentifierHelper $productIdentifierHelper;

    public function __construct(
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator,
        ChannelContextInterface $channelContext,
        ProductDetailImpressionFactoryInterface $productDetailImpressionFactory,
        ProductDetailFactoryInterface $productDetailFactory,
        ProductIdentifierHelper $productIdentifierHelper
    ) {
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
        $this->channelContext = $channelContext;
        $this->productDetailImpressionFactory = $productDetailImpressionFactory;
        $this->productDetailFactory = $productDetailFactory;
        $this->productIdentifierHelper = $productIdentifierHelper;
    }

    public function get(ProductInterface $product): ProductDetailImpressionInterface
    {
        $vo = $this->productDetailImpressionFactory->create();

        foreach ($this->prepare($product) as $item) {
            $vo->add($item);
        }

        return $vo;
    }

    /**
     * @return \Generator|ProductDetailInterface[]
     */
    private function prepare(ProductInterface $product): \Generator
    {
        foreach ($product->getEnabledVariants() as $variant) {
            yield $this->createProductVariant($variant);
        }
    }

    private function createProductVariant(ProductVariantInterface $productVariant): ProductDetailInterface
    {
        /** @var ProductInterface $product */
        $product = $productVariant->getProduct();

        $vo = $this->productDetailFactory->create();

        $vo->setId($this->productIdentifierHelper->getProductIdentifier($product));
        $vo->setName($product->getName());
        $vo->setPrice($this->getPrice($productVariant));
        $vo->setCategory(null !== $product->getMainTaxon() ? $product->getMainTaxon()->getName() : null);
        $vo->setVariant($productVariant->getCode());

        return $vo;
    }

    private function getPrice(ProductVariantInterface $productVariant): float
    {
        return \round($this->productVariantPriceCalculator->calculate($productVariant, [
                'channel' => $this->channelContext->getChannel(),
            ]) / 100, 2);
    }
}
