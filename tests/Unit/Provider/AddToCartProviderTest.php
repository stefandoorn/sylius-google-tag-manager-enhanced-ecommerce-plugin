<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\AddToCartProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class AddToCartProviderTest extends TestCase
{
    private MockObject&GtmEcommerceFactoryInterface $gtmEcommerceFactory;

    private AddToCartProvider $provider;

    protected function setUp(): void
    {
        $this->gtmEcommerceFactory = $this->createMock(GtmEcommerceFactoryInterface::class);
        $this->provider = new AddToCartProvider($this->gtmEcommerceFactory);
    }

    public function testEventReturnsCorrectConstant(): void
    {
        self::assertSame('add_to_cart', $this->provider->getEvent([]));
    }

    public function testGetEcommerceReturnsDataFromFactory(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $expected = [
            'currency' => 'USD',
            'items' => [
                    [
                        'id' => 'product123',
                    ],
                ],
        ];

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromSingleOrderItem')
            ->with($orderItem)
            ->willReturn($expected);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame($expected, $result);
    }

    public function testGetEcommerceThrowsExceptionWhenOrderItemKeyMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([]);
    }

    public function testGetEcommerceThrowsExceptionWhenOrderItemIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER_ITEM => new \stdClass(),
        ]);
    }

    public function testGetEcommerceReturnsNullWhenFactoryReturnsNull(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromSingleOrderItem')
            ->with($orderItem)
            ->willReturn(null);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertNull($result);
    }
}
