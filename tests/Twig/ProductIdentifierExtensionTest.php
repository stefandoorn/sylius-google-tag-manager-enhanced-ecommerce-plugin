<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Twig;

use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Twig\ProductIdentifierExtension;
use Sylius\Component\Core\Model\ProductInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class ProductIdentifierExtensionTest extends TestCase
{
    private function initTwigForTemplateProductIdentifier(string $productIdentifier): Environment
    {
        $twig = new Environment(
            new ArrayLoader([
                'template' => '{{ sylius_gtm_enhanced_ecommerce_product_identifier(product) }}',
            ]),
            [
                'debug' => true,
                'cache' => false,
                'autoescape' => 'html',
                'optimizations' => 0,
            ]
        );

        $twig->addExtension(new ProductIdentifierExtension(new ProductIdentifierHelper($productIdentifier)));

        return $twig;
    }

    public function testIdAsIdentifier(): void
    {
        $productIdentifier = ProductDetailInterface::ID_IDENTIFIER;

        $product = $this->getMockBuilder(ProductInterface::class)->getMock();

        // Exceptations
        $product->method('getId')->willReturn(123);
        $product->method('getCode')->willReturn('TEST');

        $output = $this->initTwigForTemplateProductIdentifier($productIdentifier)->render('template', ['product' => $product]);
        $this->assertEquals('123', $output);
    }

    public function testCodeAsIdentifier(): void
    {
        $productIdentifier = ProductDetailInterface::CODE_IDENTIFIER;

        $product = $this->getMockBuilder(ProductInterface::class)->getMock();

        // Exceptations
        $product->method('getId')->willReturn(123);
        $product->method('getCode')->willReturn('TEST');

        $output = $this->initTwigForTemplateProductIdentifier($productIdentifier)->render('template', ['product' => $product]);
        $this->assertEquals('TEST', $output);
    }
}
