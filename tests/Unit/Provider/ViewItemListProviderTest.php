<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmItemFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\ViewItemListProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class ViewItemListProviderTest extends TestCase
{
    private MockObject&ProductVariantResolverInterface $productVariantResolver;

    private MockObject&ProductVariantPricesCalculatorInterface $productVariantPricesCalculator;

    private MockObject&GtmItemFactoryInterface $gtmItemFactory;

    private ViewItemListProvider $provider;

    protected function setUp(): void
    {
        $this->productVariantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->productVariantPricesCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->gtmItemFactory = $this->createMock(GtmItemFactoryInterface::class);
        $this->provider = new ViewItemListProvider(
            $this->productVariantResolver,
            $this->productVariantPricesCalculator,
            $this->gtmItemFactory,
        );
    }

    public function testEventReturnsCorrectConstant(): void
    {
        self::assertSame('view_item_list', $this->provider->getEvent([]));
    }

    public function testGetEcommerceReturnsDataWithProducts(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->productVariantResolver->expects($this->once())
            ->method('getVariant')
            ->with($product)
            ->willReturn($productVariant);

        $this->productVariantPricesCalculator->expects($this->once())
            ->method('calculate')
            ->with($productVariant, ['channel' => $channel])
            ->willReturn(1999);

        $this->gtmItemFactory->expects($this->once())
            ->method('createNewFromProductVariant')
            ->with($productVariant)
            ->willReturn(['id' => 'product123', 'name' => 'Product Name']);

        $channel->expects($this->once())
            ->method('getName')
            ->willReturn('Web Store');

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCTS => [$product],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'USD',
            ContextInterface::CONTEXT_CHANNEL => $channel,
        ]);

        $expected = [
            'currency' => 'USD',
            'items' => [
                [
                    'id' => 'product123',
                    'name' => 'Product Name',
                    'price' => 19.99,
                    'affiliation' => 'Web Store',
                    'index' => 0,
                ],
            ],
        ];

        self::assertEquals($expected, $result);
    }

    public function testGetEcommerceIncludesTaxonDataWhenAvailable(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $this->productVariantResolver->expects($this->once())
            ->method('getVariant')
            ->willReturn($productVariant);

        $this->productVariantPricesCalculator->expects($this->once())
            ->method('calculate')
            ->willReturn(2500);

        $this->gtmItemFactory->expects($this->once())
            ->method('createNewFromProductVariant')
            ->willReturn(['id' => 'product456']);

        $channel->method('getName')->willReturn('Web Store');
        $taxon->method('getCode')->willReturn('category-code');
        $taxon->method('getName')->willReturn('Category Name');

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCTS => [$product],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'EUR',
            ContextInterface::CONTEXT_CHANNEL => $channel,
            ContextInterface::CONTEXT_TAXON => $taxon,
        ]);

        $expected = [
            'currency' => 'EUR',
            'item_list_id' => 'category-code',
            'item_list_name' => 'Category Name',
            'items' => [
                [
                    'id' => 'product456',
                    'price' => 25,
                    'affiliation' => 'Web Store',
                    'index' => 0,
                ],
            ],
        ];

        self::assertEquals($expected, $result);
    }

    public function testGetEcommerceWithEmptyProductsArray(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCTS => [],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'USD',
            ContextInterface::CONTEXT_CHANNEL => $channel,
        ]);

        $expected = [
            'currency' => 'USD',
            'items' => [],
        ];

        self::assertEquals($expected, $result);
    }

    public function testGetEcommerceSkipsProductsWithNoVariant(): void
    {
        $product1 = $this->createMock(ProductInterface::class);
        $product2 = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->productVariantResolver->expects($this->exactly(2))
            ->method('getVariant')
            ->willReturnOnConsecutiveCalls(null, $productVariant);

        $this->productVariantPricesCalculator->expects($this->once())
            ->method('calculate')
            ->willReturn(1000);

        $this->gtmItemFactory->expects($this->once())
            ->method('createNewFromProductVariant')
            ->willReturn(['id' => 'product789']);

        $channel->method('getName')->willReturn('Web Store');

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCTS => [$product1, $product2],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'USD',
            ContextInterface::CONTEXT_CHANNEL => $channel,
        ]);

        $expected = [
            'currency' => 'USD',
            'items' => [
                [
                    'id' => 'product789',
                    'price' => 10,
                    'affiliation' => 'Web Store',
                    'index' => 0,
                ],
            ],
        ];

        self::assertEquals($expected, $result);
    }

    public function testGetEcommerceThrowsExceptionWhenRequiredKeysAreMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([]);
    }

    public function testGetEcommerceThrowsExceptionWhenInvalidChannelProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCTS => [],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'USD',
            ContextInterface::CONTEXT_CHANNEL => new \stdClass(),
        ]);
    }

    public function testGetEcommerceThrowsExceptionWhenInvalidTaxonProvided(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCTS => [],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'USD',
            ContextInterface::CONTEXT_CHANNEL => $channel,
            ContextInterface::CONTEXT_TAXON => new \stdClass(),
        ]);
    }
}
