<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\AddPaymentInfoProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class AddPaymentInfoProviderTest extends TestCase
{
    private MockObject&GtmEcommerceFactoryInterface $gtmEcommerceFactory;

    private AddPaymentInfoProvider $provider;

    protected function setUp(): void
    {
        $this->gtmEcommerceFactory = $this->createMock(GtmEcommerceFactoryInterface::class);
        $this->provider = new AddPaymentInfoProvider($this->gtmEcommerceFactory);
    }

    public function testEventReturnsCorrectConstant(): void
    {
        self::assertSame('add_payment_info', $this->provider->getEvent([]));
    }

    public function testGetEcommerceReturnsDataWithPaymentType(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $payment1 = $this->createMock(PaymentInterface::class);
        $payment2 = $this->createMock(PaymentInterface::class);
        $method1 = $this->createMock(PaymentMethodInterface::class);
        $method2 = $this->createMock(PaymentMethodInterface::class);

        $method1->expects($this->once())
            ->method('getName')
            ->willReturn('Credit Card');

        $method2->expects($this->once())
            ->method('getName')
            ->willReturn('PayPal');

        $payment1->expects($this->once())
            ->method('getMethod')
            ->willReturn($method1);

        $payment2->expects($this->once())
            ->method('getMethod')
            ->willReturn($method2);

        $order->expects($this->once())
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$payment1, $payment2]));

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
            'payment_type' => 'Credit Card, PayPal',
        ], $result);
    }

    public function testGetEcommerceWithOrderWithoutPaymentsReturnsEmptyPaymentType(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())
            ->method('getPayments')
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
            'payment_type' => '',
        ], $result);
    }

    public function testGetEcommerceWithPaymentWithoutMethodHandledGracefully(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $payment = $this->createMock(PaymentInterface::class);

        $payment->expects($this->once())
            ->method('getMethod')
            ->willReturn(null);

        $order->expects($this->once())
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$payment]));

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn(['currency' => 'GBP']);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame([
            'currency' => 'GBP',
            'payment_type' => '',
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
            ->method('getPayments')
            ->willReturn(new ArrayCollection([]));

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromOrder')
            ->with($order)
            ->willReturn(null);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER => $order,
        ]);

        self::assertSame([
            'payment_type' => '',
        ], $result);
    }
}
