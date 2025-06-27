<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\PurchaseProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class PurchaseProviderTest extends TestCase
{
    private MockObject&GtmEcommerceFactoryInterface $gtmEcommerceFactory;

    private PurchaseProvider $provider;

    protected function setUp(): void
    {
        $this->gtmEcommerceFactory = $this->createMock(GtmEcommerceFactoryInterface::class);
        $this->provider = new PurchaseProvider($this->gtmEcommerceFactory);
    }

    public function testEventReturnsCorrectConstant(): void
    {
        self::assertSame('purchase', $this->provider->getEvent([]));
    }

    public function testGetEcommerceReturnsDataWithTransactionId(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $ecommerceData = [
            'currency' => 'EUR',
            'value' => 99.99,
            'items' => [
                    [
                        'id' => 'product123',
                    ],
                ],
        ];

        $expected = array_merge($ecommerceData, [
            'transaction_id' => 'ORDER12345',
        ]);

        $order->expects($this->once())
            ->method('getNumber')
            ->willReturn('ORDER12345');

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn($ecommerceData);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame($expected, $result);
    }

    public function testGetEcommerceThrowsExceptionWhenOrderKeyMissing(): void
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
            ->method('getNumber')
            ->willReturn('ORDER12345');

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn(null);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame(['transaction_id' => 'ORDER12345'], $result);
    }
}
