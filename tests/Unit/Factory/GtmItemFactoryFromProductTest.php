<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmItemFactory;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class GtmItemFactoryFromProductTest extends TestCase
{
    private MockObject&ProductIdentifierHelperInterface $productIdentifierHelper;

    private GtmItemFactory $factory;

    protected function setUp(): void
    {
        $this->productIdentifierHelper = $this->createMock(ProductIdentifierHelperInterface::class);
        $this->factory = new GtmItemFactory($this->productIdentifierHelper);
    }

    public function testCreateNewFromProductVariantWithFullData(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $variant->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);

        $this->productIdentifierHelper->expects($this->once())
            ->method('getProductIdentifier')
            ->with($product)
            ->willReturn('product-123');

        $product->expects($this->once())
            ->method('getName')
            ->willReturn('Test Product');

        $product->expects($this->once())
            ->method('getMainTaxon')
            ->willReturn($taxon);

        $taxon->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('Test Category');

        $variant->expects($this->once())
            ->method('getName')
            ->willReturn('Test Variant');

        $variant->expects($this->once())
            ->method('getCode');

        $result = $this->factory->createNewFromProductVariant($variant);

        $expected = [
            'item_id' => 'product-123',
            'index' => 0,
            'item_name' => 'Test Product',
            'item_category' => 'Test Category',
            'item_variant' => 'Test Variant',
        ];

        self::assertEquals($expected, $result);
    }

    public function testCreateNewFromProductVariantWithNoName(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $variant->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);

        $this->productIdentifierHelper->expects($this->once())
            ->method('getProductIdentifier')
            ->with($product)
            ->willReturn('product-123');

        $product->expects($this->once())
            ->method('getName')
            ->willReturn('Test Product');

        $product->expects($this->once())
            ->method('getMainTaxon')
            ->willReturn(null);

        $variant->expects($this->once())
            ->method('getName')
            ->willReturn(null);

        $variant->expects($this->once())
            ->method('getCode')
            ->willReturn('VAR-123');

        $result = $this->factory->createNewFromProductVariant($variant);

        $expected = [
            'item_id' => 'product-123',
            'index' => 0,
            'item_name' => 'Test Product',
            'item_variant' => 'VAR-123',
        ];

        self::assertEquals($expected, $result);
    }

    public function testCreateNewFromProductVariantWithMinimalData(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $variant->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);

        $this->productIdentifierHelper->expects($this->once())
            ->method('getProductIdentifier')
            ->with($product)
            ->willReturn('product-123');

        $product->expects($this->once())
            ->method('getName')
            ->willReturn(null);

        $product->expects($this->once())
            ->method('getMainTaxon')
            ->willReturn(null);

        $variant->expects($this->once())
            ->method('getName')
            ->willReturn(null);

        $variant->expects($this->once())
            ->method('getCode')
            ->willReturn(null);

        $result = $this->factory->createNewFromProductVariant($variant);

        $expected = [
            'item_id' => 'product-123',
            'index' => 0,
            'item_name' => 'product-123',
        ];

        self::assertEquals($expected, $result);
    }
}
