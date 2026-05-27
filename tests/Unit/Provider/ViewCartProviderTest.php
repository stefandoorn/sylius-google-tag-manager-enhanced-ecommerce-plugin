<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\ViewCartProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;

#[CoversClass(ViewCartProvider::class)]
final class ViewCartProviderTest extends TestCase
{
    private MockObject&GtmEcommerceFactoryInterface $ecommerceFactory;

    private ViewCartProvider $provider;

    protected function setUp(): void
    {
        $this->ecommerceFactory = $this->createMock(GtmEcommerceFactoryInterface::class);
        $this->provider = new ViewCartProvider($this->ecommerceFactory);
    }

    public function testGetEventReturnsViewCart(): void
    {
        self::assertSame('view_cart', $this->provider->getEvent([]));
    }

    public function testGetEcommerceDelegatesToFactory(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $expected = ['currency' => 'EUR', 'value' => 30.0, 'items' => []];

        $this->ecommerceFactory
            ->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn($expected);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame($expected, $result);
    }

    public function testGetEcommerceThrowsWhenOrderMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([]);
    }
}
