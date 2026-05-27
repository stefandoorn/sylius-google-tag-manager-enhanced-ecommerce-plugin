<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\AddToCartProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

#[CoversClass(AddToCartProvider::class)]
final class AddToCartProviderTest extends TestCase
{
    private MockObject&GtmEcommerceFactoryInterface $ecommerceFactory;

    private AddToCartProvider $provider;

    protected function setUp(): void
    {
        $this->ecommerceFactory = $this->createMock(GtmEcommerceFactoryInterface::class);
        $this->provider = new AddToCartProvider($this->ecommerceFactory);
    }

    public function testGetEventReturnsAddToCart(): void
    {
        self::assertSame('add_to_cart', $this->provider->getEvent([]));
    }

    public function testGetEcommerceDelegatesToFactory(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $expected = ['currency' => 'EUR', 'value' => 30.0, 'items' => [['item_id' => 'PROD-1']]];

        $this->ecommerceFactory
            ->expects($this->once())
            ->method('createNewFromSingleOrderItem')
            ->with($orderItem, $order)
            ->willReturn($expected);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame($expected, $result);
    }

    public function testGetEcommerceThrowsWhenOrderItemMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $this->createMock(OrderInterface::class),
        ]);
    }

    public function testGetEcommerceThrowsWhenOrderMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER_ITEM => $this->createMock(OrderItemInterface::class),
        ]);
    }
}
