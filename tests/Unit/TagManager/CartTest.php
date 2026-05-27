<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\TagManager;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\Cart;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

#[CoversClass(Cart::class)]
final class CartTest extends TestCase
{
    private GoogleTagManager $gtm;

    private MockObject&ChannelContextInterface $channelContext;

    private MockObject&CurrencyContextInterface $currencyContext;

    private MockObject&ProductIdentifierHelperInterface $productIdentifierHelper;

    private Cart $cart;

    protected function setUp(): void
    {
        $this->gtm = new GoogleTagManager(true, 'id1234');
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->currencyContext = $this->createMock(CurrencyContextInterface::class);
        $this->productIdentifierHelper = $this->createMock(ProductIdentifierHelperInterface::class);

        $this->cart = new Cart(
            $this->gtm,
            $this->channelContext,
            $this->currencyContext,
            $this->productIdentifierHelper,
        );
    }

    public function testGetOrderItemBuildsProductDataFromOrderItem(): void
    {
        $orderItem = $this->createOrderItemMock(
            product: $product = $this->createMock(ProductInterface::class),
            variant: $variant = $this->createMock(ProductVariantInterface::class),
            mainTaxon: $taxon = $this->createMock(TaxonInterface::class),
            unitPrice: 1500,
            quantity: 2,
        );

        $product->method('getName')->willReturn('Test Product');
        $variant->method('getName')->willReturn('Variant A');
        $taxon->method('getName')->willReturn('Category A');

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getName')->willReturn('My Store');
        $this->channelContext->method('getChannel')->willReturn($channel);

        $this->productIdentifierHelper
            ->method('getProductIdentifier')
            ->with($product)
            ->willReturn('PROD-123');

        $data = $this->cart->getOrderItem($orderItem);

        self::assertSame([
            'item_id' => 'PROD-123',
            'item_name' => 'Test Product',
            'affiliation' => 'My Store',
            'item_category' => 'Category A',
            'item_variant' => 'Variant A',
            'price' => 15,
            'quantity' => 2,
        ], $data);
    }

    public function testAddPushesAddToCartEventWithEcommerceReset(): void
    {
        $this->currencyContext->method('getCurrencyCode')->willReturn('EUR');

        $productData = [
            'item_id' => 'PROD-123',
            'item_name' => 'Test Product',
            'price' => 15.0,
            'quantity' => 2,
        ];

        $this->cart->add($productData);

        $push = $this->gtm->getPush();

        self::assertCount(2, $push);
        self::assertArrayHasKey('ecommerce', $push[0]);
        self::assertNull($push[0]['ecommerce']);

        self::assertSame('add_to_cart', $push[1]['event']);
        self::assertSame('EUR', $push[1]['ecommerce']['currency']);
        self::assertSame(30.0, $push[1]['ecommerce']['value']);
        self::assertSame([$productData], $push[1]['ecommerce']['items']);
    }

    public function testRemovePushesRemoveFromCartEventWithEcommerceReset(): void
    {
        $this->currencyContext->method('getCurrencyCode')->willReturn('USD');

        $productData = [
            'item_id' => 'PROD-321',
            'item_name' => 'Another Product',
            'price' => 10.0,
            'quantity' => 3,
        ];

        $this->cart->remove($productData);

        $push = $this->gtm->getPush();

        self::assertCount(2, $push);
        self::assertArrayHasKey('ecommerce', $push[0]);
        self::assertNull($push[0]['ecommerce']);

        self::assertSame('remove_from_cart', $push[1]['event']);
        self::assertSame('USD', $push[1]['ecommerce']['currency']);
        self::assertSame(30.0, $push[1]['ecommerce']['value']);
        self::assertSame([$productData], $push[1]['ecommerce']['items']);
    }

    private function createOrderItemMock(
        ProductInterface&MockObject $product,
        ProductVariantInterface&MockObject $variant,
        ?TaxonInterface $mainTaxon,
        int $unitPrice,
        int $quantity,
    ): OrderItemInterface&MockObject {
        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem->method('getVariant')->willReturn($variant);
        $orderItem->method('getUnitPrice')->willReturn($unitPrice);
        $orderItem->method('getQuantity')->willReturn($quantity);

        $variant->method('getProduct')->willReturn($product);
        $product->method('getMainTaxon')->willReturn($mainTaxon);

        return $orderItem;
    }
}
