<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\BeginCheckoutProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class BeginCheckoutProviderTest extends TestCase
{
    private MockObject&GtmEcommerceFactoryInterface $gtmEcommerceFactory;

    private BeginCheckoutProvider $provider;

    protected function setUp(): void
    {
        $this->gtmEcommerceFactory = $this->createMock(GtmEcommerceFactoryInterface::class);
        $this->provider = new BeginCheckoutProvider($this->gtmEcommerceFactory);
    }

    public function testEventReturnsCorrectConstant(): void
    {
        self::assertSame('begin_checkout', $this->provider->getEvent([]));
    }

    public function testGetEcommerceReturnsDataFromFactory(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $expected = [
            'currency' => 'EUR',
            'value' => 99.99,
            'items' => [
                    [
                        'id' => 'product123',
                    ],
                ],
        ];

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn($expected);

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

    public function testGetEcommerceReturnsNullWhenFactoryReturnsNull(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn(null);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertNull($result);
    }
}
