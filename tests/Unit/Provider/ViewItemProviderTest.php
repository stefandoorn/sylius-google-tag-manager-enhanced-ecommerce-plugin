<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
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

    public function testEventReturnsCorrectConstant(): void
    {
        self::assertSame('view_item', $this->provider->getEvent([]));
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
            ->willReturn(['id' => 'product123', 'name' => 'Test Product']);

        $channel->expects($this->once())
            ->method('getName')
            ->willReturn('Web Store');

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCT => $product,
            ContextInterface::CONTEXT_CURRENCY_CODE => 'EUR',
            ContextInterface::CONTEXT_CHANNEL => $channel,
        ]);

        $expected = [
            'currency' => 'EUR',
            'value' => 15,
            'items' => [
                [
                    'id' => 'product123',
                    'name' => 'Test Product',
                    'price' => 15,
                    'affiliation' => 'Web Store',
                    'index' => 0,
                ],
            ],
        ];

        self::assertEquals($expected, $result);
    }

    public function testGetEcommerceThrowsExceptionWhenProductKeyIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_CURRENCY_CODE => 'USD',
            ContextInterface::CONTEXT_CHANNEL => $this->createMock(ChannelInterface::class),
        ]);
    }

    public function testGetEcommerceThrowsExceptionWhenProductIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_PRODUCT => new \stdClass(),
            ContextInterface::CONTEXT_CURRENCY_CODE => 'USD',
            ContextInterface::CONTEXT_CHANNEL => $this->createMock(ChannelInterface::class),
        ]);
    }
}
