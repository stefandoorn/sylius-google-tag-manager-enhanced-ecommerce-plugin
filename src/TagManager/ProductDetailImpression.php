<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

/**
 * Class ProductDetailImpression
 * @package SyliusGoogleAnalyticsEnhancedEcommerceTrackingBundle\TagManager
 */
class ProductDetailImpression implements ProductDetailImpressionInterface
{
    /**
     * @var GoogleTagManagerInterface
     */
    private $googleTagManager;

    /**
     * @var ProductVariantPriceCalculatorInterface
     */
    private $productVariantPriceCalculator;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var array
     */
    private $variants = [];

    /**
     * ProductDetailImpression constructor.
     * @param GoogleTagManagerInterface $googleTagManager
     * @param ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator,
        ChannelContextInterface $channelContext
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
        $this->channelContext = $channelContext;
    }

    /**
     * @inheritdoc
     */
    public function add(ProductInterface $product): void
    {
        foreach ($product->getVariants() as $variant) {
            $this->addProductVariant($variant);
        }

        $this->googleTagManager->mergeData('ecommerce', [
            'detail' => [
                'products' => $this->variants,
            ],
        ]);
    }

    /**
     * @param ProductVariantInterface $productVariant
     */
    private function addProductVariant(ProductVariantInterface $productVariant): void
    {
        $product = $productVariant->getProduct();

        $this->variants[] = [
            'name' => $product->getName(),
            'id' => $product->getId(),
            'price' => $this->getPrice($productVariant),
            'category' => $product->getMainTaxon()->getName(),
            'variant' => $productVariant->getCode(),
        ];
    }

    /**
     * @param ProductVariantInterface $productVariant
     * @return float
     */
    private function getPrice(ProductVariantInterface $productVariant): float
    {
        return (float)round($this->productVariantPriceCalculator->calculate($productVariant, [
                'channel' => $this->channelContext->getChannel(),
            ]) / 100, 2);
    }
}
