<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactory;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class GtmEcommerceFactoryTest extends TestCase
{
    private MockObject&GtmItemFactoryInterface $gtmItemFactory;

    private GtmEcommerceFactory $factory;

    protected function setUp(): void
    {
        $this->gtmItemFactory = $this->createMock(GtmItemFactoryInterface::class);
        $this->factory = new GtmEcommerceFactory($this->gtmItemFactory);
    }

    public function testCreateNewFromOrder(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $coupon = $this->createMock(PromotionCouponInterface::class);

        // Set up order expectations
        $order->expects($this->once())
            ->method('getCurrencyCode')
            ->willReturn('USD');

        $order->expects($this->once())
            ->method('getItemsSubtotal')
            ->willReturn(8000);

        $order->expects($this->once())
            ->method('getTaxTotal')
            ->willReturn(2000);

        $order->expects($this->once())
            ->method('getShippingTotal')
            ->willReturn(500);

        $order->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn(new ArrayCollection([$orderItem]));

        $order->expects($this->atLeastOnce())
            ->method('getPromotionCoupon')
            ->willReturn($coupon);

        $coupon->expects($this->once())
            ->method('getCode')
            ->willReturn('DISCOUNT10');

        // Set up GTM item factory expectations
        $this->gtmItemFactory->expects($this->once())
            ->method('createNewFromOrderItem')
            ->with($orderItem)
            ->willReturn([
                'item_id' => 'product-123',
                'price' => 80.00,
                'quantity' => 1,
                'item_name' => 'Test Product',
            ]);

        $result = $this->factory->createNewFromOrder($order);

        $expected = [
            'currency' => 'USD',
            'value' => 80.00,
            'tax' => 20.00,
            'shipping' => 5.00,
            'coupon' => 'DISCOUNT10',
            'items' => [
                [
                    'item_id' => 'product-123',
                    'price' => 80.00,
                    'quantity' => 1,
                    'item_name' => 'Test Product',
                ],
            ],
        ];

        self::assertEquals($expected, $result);
    }

    public function testCreateNewFromOrderWithoutCoupon(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);

        $order->expects($this->once())->method('getCurrencyCode')->willReturn('EUR');
        $order->expects($this->once())->method('getItemsSubtotal')->willReturn(4200);
        $order->expects($this->once())->method('getTaxTotal')->willReturn(800);
        $order->expects($this->once())->method('getShippingTotal')->willReturn(300);
        $order->expects($this->atLeastOnce())->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $order->expects($this->atLeastOnce())->method('getPromotionCoupon')->willReturn(null);

        $this->gtmItemFactory->expects($this->once())
            ->method('createNewFromOrderItem')
            ->willReturn(['item_id' => 'product-456', 'price' => 42.00]);

        $result = $this->factory->createNewFromOrder($order);

        $expected = [
            'currency' => 'EUR',
            'value' => 42.00,
            'tax' => 8.00,
            'shipping' => 3.00,
            'items' => [['item_id' => 'product-456', 'price' => 42.00]],
        ];

        self::assertEquals($expected, $result);
    }

    public function testCreateNewFromSingleOrderItem(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $coupon = $this->createMock(PromotionCouponInterface::class);

        $orderItem->expects($this->once())->method('getTotal')->willReturn(3000);
        $orderItem->expects($this->once())->method('getTaxTotal')->willReturn(500);

        $order->expects($this->once())->method('getCurrencyCode')->willReturn('GBP');
        $order->expects($this->atLeastOnce())->method('getPromotionCoupon')->willReturn($coupon);
        $coupon->expects($this->once())->method('getCode')->willReturn('SALE20');

        $this->gtmItemFactory->expects($this->once())
            ->method('createNewFromOrderItem')
            ->with($orderItem)
            ->willReturn(['item_id' => 'product-789', 'item_name' => 'Special Item']);

        $result = $this->factory->createNewFromSingleOrderItem($orderItem, $order);

        $expected = [
            'currency' => 'GBP',
            'value' => 30.00,
            'tax' => 5.00,
            'coupon' => 'SALE20',
            'items' => [['item_id' => 'product-789', 'item_name' => 'Special Item']],
        ];

        self::assertEquals($expected, $result);
    }

    public function testCreateNewFromProduct(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->gtmItemFactory->expects($this->once())
            ->method('createNewFromProduct')
            ->with($product)
            ->willReturn(['item_id' => 'product-999', 'item_name' => 'Featured Product']);

        $result = $this->factory->createNewFromProduct($product);

        $expected = [
            'items' => [['item_id' => 'product-999', 'item_name' => 'Featured Product']],
        ];

        self::assertEquals($expected, $result);
    }
}
