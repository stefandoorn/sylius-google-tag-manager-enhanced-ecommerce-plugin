<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmItemFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\ViewItemProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

#[CoversClass(ViewItemProvider::class)]
final class ViewItemProviderTest extends TestCase
{
    private MockObject&ProductVariantResolverInterface $productVariantResolver;

    private MockObject&ProductVariantPricesCalculatorInterface $productVariantPricesCalculator;

    private MockObject&GtmItemFactoryInterface $gtmItemFactory;

    private ViewItemProvider $provider;

    protected function setUp(): void
    {
        $this->productVariantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->productVariantPricesCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->gtmItemFactory = $this->createMock(GtmItemFactoryInterface::class);
        $this->provider = new ViewItemProvider(
            $this->productVariantResolver,
            $this->productVariantPricesCalculator,
            $this->gtmItemFactory,
        );
    }

    public function testGetEventReturnsViewItem(): void
    {
        self::assertEquals('view_item', $this->provider->getEvent([]));
    }

    public function testGetEcommerceCallsParentWithProductInProductsArray(): void
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
            ->willReturn(1500);

        $this->gtmItemFactory->expects($this->once())
            ->method('createNewFromProductVariant')
            ->with($productVariant)
            ->willReturn([
                'item_id' => 'product123',
                'item_name' => 'Test Product',
                'item_variant' => 'Test Variant',
                'item_category' => 'Category A',
            ]);

        $channel->method('getName')->willReturn('My Store');

        $context = [
            ContextInterface::CONTEXT_PRODUCT => $product,
            ContextInterface::CONTEXT_PRODUCTS => [$product],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'EUR',
            ContextInterface::CONTEXT_CHANNEL => $channel,
        ];

        $result = $this->provider->getEcommerce($context);

        self::assertNotNull($result);
        self::assertEquals('EUR', $result['currency']);
        self::assertCount(1, $result['items']);
        self::assertEquals(15.0, $result['items'][0]['price']);
        self::assertEquals('My Store', $result['items'][0]['affiliation']);
        self::assertEquals(0, $result['items'][0]['index']);
        self::assertEquals('product123', $result['items'][0]['item_id']);
        self::assertEquals('Test Product', $result['items'][0]['item_name']);
        self::assertEquals('Test Variant', $result['items'][0]['item_variant']);
        self::assertEquals(15.0, $result['value']);
    }

    public function testGetEcommerceThrowsWhenProductMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCTS => [],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'EUR',
            ContextInterface::CONTEXT_CHANNEL => $this->createMock(ChannelInterface::class),
        ]);
    }

    public function testGetEcommerceReturnsNullWhenNoVariantResolved(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->productVariantResolver->method('getVariant')
            ->with($product)
            ->willReturn(null);

        $context = [
            ContextInterface::CONTEXT_PRODUCT => $product,
            ContextInterface::CONTEXT_PRODUCTS => [$product],
            ContextInterface::CONTEXT_CURRENCY_CODE => 'EUR',
            ContextInterface::CONTEXT_CHANNEL => $channel,
        ];

        self::assertNull($this->provider->getEcommerce($context));
    }
}
