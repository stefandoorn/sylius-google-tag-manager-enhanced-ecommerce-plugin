<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Factory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactory;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

#[CoversClass(GtmEcommerceFactory::class)]
final class GtmEcommerceFactoryTest extends TestCase
{
    private MockObject&GtmItemFactoryInterface $gtmItemFactory;

    private GtmEcommerceFactory $factory;

    protected function setUp(): void
    {
        $this->gtmItemFactory = $this->createMock(GtmItemFactoryInterface::class);
        $this->factory = new GtmEcommerceFactory($this->gtmItemFactory);
    }

    public function testCreateNewFromOrderBuildsEcommerceFromAllItems(): void
    {
        $orderItem1 = $this->createMock(OrderItemInterface::class);
        $orderItem2 = $this->createMock(OrderItemInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $order->method('getItems')->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$orderItem1, $orderItem2]));
        $order->method('getCurrencyCode')->willReturn('EUR');
        $order->method('getTotal')->willReturn(3000);

        $this->gtmItemFactory
            ->method('createNewFromOrderItem')
            ->willReturnMap([
                [$orderItem1, $order, ['item_id' => 'PROD-1', 'price' => 10.0, 'quantity' => 2]],
                [$orderItem2, $order, ['item_id' => 'PROD-2', 'price' => 5.0, 'quantity' => 2]],
            ]);

        $result = $this->factory->createNewFromOrder($order);

        self::assertSame('EUR', $result['currency']);
        self::assertEqualsWithDelta(30.0, $result['value'], 0.001);
        self::assertCount(2, $result['items']);
        self::assertSame('PROD-1', $result['items'][0]['item_id']);
        self::assertSame('PROD-2', $result['items'][1]['item_id']);
    }

    public function testCreateNewFromSingleOrderItemBuildsEcommerce(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $order->method('getCurrencyCode')->willReturn('USD');

        $this->gtmItemFactory
            ->method('createNewFromOrderItem')
            ->with($orderItem, $order)
            ->willReturn(['item_id' => 'PROD-1', 'price' => 12.5, 'quantity' => 4]);

        $result = $this->factory->createNewFromSingleOrderItem($orderItem, $order);

        self::assertSame('USD', $result['currency']);
        self::assertSame(50.0, $result['value']);
        self::assertCount(1, $result['items']);
        self::assertSame('PROD-1', $result['items'][0]['item_id']);
    }

    public function testCreateNewFromProductBuildsEcommerce(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->gtmItemFactory
            ->method('createNewFromProduct')
            ->with($product)
            ->willReturn(['item_id' => 'PROD-1', 'item_name' => 'Test']);

        $result = $this->factory->createNewFromProduct($product);

        self::assertCount(1, $result['items']);
        self::assertSame('PROD-1', $result['items'][0]['item_id']);
    }
}
