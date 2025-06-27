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

final class GtmItemFactoryFromProductVariantTest extends TestCase
{
    private MockObject&ProductIdentifierHelperInterface $productIdentifierHelper;

    private GtmItemFactory $gtmItemFactory;

    protected function setUp(): void
    {
        $this->productIdentifierHelper = $this->createMock(ProductIdentifierHelperInterface::class);
        $this->gtmItemFactory = new GtmItemFactory($this->productIdentifierHelper);
    }

    public function testsCestCreateNewFromProductVariantIncludesProductData(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getName')->willReturn('Test Product');
        $product->method('getMainTaxon')->willReturn(null);

        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->method('getProduct')->willReturn($product);
        $productVariant->method('getName')->willReturn('Size L');
        $productVariant->method('getCode')->willReturn('test-product-l');

        $this->productIdentifierHelper->method('getProductIdentifier')
            ->with($product)
            ->willReturn('product123');

        $result = $this->gtmItemFactory->createNewFromProductVariant($productVariant);

        self::assertSame([
            'item_id' => 'product123',
            'index' => 0,
            'item_name' => 'Test Product',
            'item_variant' => 'Size L',
        ], $result);
    }

    public function testsCreateNewFromProductVariantUsesCodeWhenNameIsNull(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getName')->willReturn('Test Product');
        $product->method('getMainTaxon')->willReturn(null);

        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->method('getProduct')->willReturn($product);
        $productVariant->method('getName')->willReturn(null);
        $productVariant->method('getCode')->willReturn('test-product-m');

        $this->productIdentifierHelper->method('getProductIdentifier')
            ->with($product)
            ->willReturn('product456');

        $result = $this->gtmItemFactory->createNewFromProductVariant($productVariant);

        self::assertSame([
            'item_id' => 'product456',
            'index' => 0,
            'item_name' => 'Test Product',
            'item_variant' => 'test-product-m',
        ], $result);
    }

    public function testsCreateNewFromProductVariantWithoutVariantInfoOmitsItemVariant(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getName')->willReturn('Test Product');
        $product->method('getMainTaxon')->willReturn(null);

        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->method('getProduct')->willReturn($product);
        $productVariant->method('getName')->willReturn(null);
        $productVariant->method('getCode')->willReturn(null);

        $this->productIdentifierHelper->method('getProductIdentifier')
            ->with($product)
            ->willReturn('product789');

        $result = $this->gtmItemFactory->createNewFromProductVariant($productVariant);

        self::assertSame([
            'item_id' => 'product789',
            'index' => 0,
            'item_name' => 'Test Product',
        ], $result);
    }

    public function testsCreateNewFromProductVariantIncludesTaxonData(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $taxon->method('getName')->willReturn('Electronics');

        $product = $this->createMock(ProductInterface::class);
        $product->method('getName')->willReturn('Test Product');
        $product->method('getMainTaxon')->willReturn($taxon);

        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->method('getProduct')->willReturn($product);
        $productVariant->method('getName')->willReturn('Variant Name');

        $this->productIdentifierHelper->method('getProductIdentifier')
            ->with($product)
            ->willReturn('product999');

        $result = $this->gtmItemFactory->createNewFromProductVariant($productVariant);

        self::assertSame([
            'item_id' => 'product999',
            'index' => 0,
            'item_name' => 'Test Product',
            'item_category' => 'Electronics',
            'item_variant' => 'Variant Name',
        ], $result);
    }
}
