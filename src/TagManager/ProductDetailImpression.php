<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\TagManager;

use GtmEnhancedEcommercePlugin\Resolver\ProductDetailImpressionDataResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

/**
 * Class ProductDetailImpression
 * @package SyliusGoogleAnalyticsEnhancedEcommerceTrackingBundle\TagManager
 */
final class ProductDetailImpression implements ProductDetailImpressionInterface
{
    /**
     * @var GoogleTagManagerInterface
     */
    private $googleTagManager;

    /**
     * @var ProductDetailImpressionDataResolverInterface
     */
    private $productDetailImpressionDataResolver;

    /**
     * ProductDetailImpression constructor.
     * @param GoogleTagManagerInterface $googleTagManager
     * @param ProductDetailImpressionDataResolverInterface $productDetailImpressionDataResolver
     */
    public function __construct(
        GoogleTagManagerInterface $googleTagManager,
        ProductDetailImpressionDataResolverInterface $productDetailImpressionDataResolver
    ) {
        $this->googleTagManager = $googleTagManager;
        $this->productDetailImpressionDataResolver = $productDetailImpressionDataResolver;
    }

    /**
     * @inheritdoc
     */
    public function add(ProductInterface $product): void
    {
        $this->googleTagManager->mergeData('ecommerce', [
            'detail' => [
                'products' => $this->productDetailImpressionDataResolver->get($product)->toArray(),
            ],
        ]);
    }
}
