<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Twig;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use Sylius\Component\Core\Model\ProductInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductIdentifierExtension extends AbstractExtension
{
    private ProductIdentifierHelper $productIdentifierHelper;

    public function __construct(ProductIdentifierHelper $productIdentifierHelper)
    {
        $this->productIdentifierHelper = $productIdentifierHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_gtm_enhanced_ecommerce_product_identifier', [$this, 'getProductIdentifier']),
        ];
    }

    public function getProductIdentifier(ProductInterface $product): string
    {
        return $this->productIdentifierHelper->getProductIdentifier($product);
    }
}
