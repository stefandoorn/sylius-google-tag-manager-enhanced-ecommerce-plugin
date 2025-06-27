<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\AddShippingInfoProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class AddShippingInfoProviderTest extends TestCase
{
    private MockObject&GtmEcommerceFactoryInterface $gtmEcommerceFactory;

    private AddShippingInfoProvider $provider;

    protected function setUp(): void
    {
        $this->gtmEcommerceFactory = $this->createMock(GtmEcommerceFactoryInterface::class);
        $this->provider = new AddShippingInfoProvider($this->gtmEcommerceFactory);
    }

    public function testEventReturnsCorrectConstant(): void
    {
        self::assertSame('add_shipping_info', $this->provider->getEvent([]));
    }

    public function testGetEcommerceReturnsDataWithShippingTier(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $shipment1 = $this->createMock(ShipmentInterface::class);
        $shipment2 = $this->createMock(ShipmentInterface::class);
        $method1 = $this->createMock(ShippingMethodInterface::class);
        $method2 = $this->createMock(ShippingMethodInterface::class);

        $method1->expects($this->once())
            ->method('getName')
            ->willReturn('DHL Express');

        $method2->expects($this->once())
            ->method('getName')
            ->willReturn('UPS Standard');

        $shipment1->expects($this->once())
            ->method('getMethod')
            ->willReturn($method1);

        $shipment2->expects($this->once())
            ->method('getMethod')
            ->willReturn($method2);

        $order->expects($this->once())
            ->method('getShipments')
            ->willReturn(new ArrayCollection([$shipment1, $shipment2]));

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn(['currency' => 'EUR', 'value' => 99.99]);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame([
            'currency' => 'EUR',
            'value' => 99.99,
            'shipping_tier' => 'DHL Express, UPS Standard',
        ], $result);
    }

    public function testGetEcommerceWithOrderWithoutShipmentsReturnsEmptyShippingTier(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())
            ->method('getShipments')
            ->willReturn(new ArrayCollection([]));

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn(['currency' => 'USD', 'value' => 50.00]);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame([
            'currency' => 'USD',
            'value' => 50.00,
            'shipping_tier' => '',
        ], $result);
    }

    public function testGetEcommerceWithShipmentWithoutMethodHandledGracefully(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);

        $shipment->expects($this->once())
            ->method('getMethod')
            ->willReturn(null);

        $order->expects($this->once())
            ->method('getShipments')
            ->willReturn(new ArrayCollection([$shipment]));

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn(['currency' => 'GBP']);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame([
            'currency' => 'GBP',
            'shipping_tier' => '',
        ], $result);
    }

    public function testGetEcommerceThrowsExceptionWhenOrderMissingInContext(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([]);
    }

    public function testGetEcommerceThrowsExceptionWhenOrderIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => new \stdClass(),
        ]);
    }

    public function testGetEcommerceUsesEmptyArrayWhenFactoryReturnsNull(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order->expects($this->once())
            ->method('getShipments')
            ->willReturn(new ArrayCollection([]));

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn(null);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame([
            'shipping_tier' => '',
        ], $result);
    }
}
