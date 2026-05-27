<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\TagManager;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\GtmProviderInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\Cart;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

#[CoversClass(Cart::class)]
final class CartTest extends TestCase
{
    private GoogleTagManager $gtm;

    private MockObject&GtmProviderInterface $viewCartProvider;

    private MockObject&GtmProviderInterface $addToCartProvider;

    private MockObject&GtmProviderInterface $removeFromCartProvider;

    private Cart $cart;

    protected function setUp(): void
    {
        $this->gtm = new GoogleTagManager(true, 'id1234');
        $this->viewCartProvider = $this->createMock(GtmProviderInterface::class);
        $this->addToCartProvider = $this->createMock(GtmProviderInterface::class);
        $this->removeFromCartProvider = $this->createMock(GtmProviderInterface::class);

        $this->cart = new Cart(
            $this->gtm,
            $this->viewCartProvider,
            $this->addToCartProvider,
            $this->removeFromCartProvider,
        );
    }

    public function testViewPushesViewCartEvent(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $expectedEcommerce = [
            'currency' => 'EUR',
            'value' => 30.0,
            'items' => [['item_id' => 'PROD-1']],
        ];

        $this->viewCartProvider->method('getEvent')->willReturn('view_cart');
        $this->viewCartProvider
            ->method('getEcommerce')
            ->with($this->callback(static fn (array $context): bool => $context[ContextInterface::CONTEXT_ORDER] === $order))
            ->willReturn($expectedEcommerce);

        $this->cart->view($order);

        $push = $this->gtm->getPush();
        self::assertNull($push[0]['ecommerce']);
        self::assertSame('view_cart', $push[1]['event']);
        self::assertSame($expectedEcommerce, $push[1]['ecommerce']);
    }

    public function testAddPushesAddToCartEvent(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $expectedEcommerce = [
            'currency' => 'EUR',
            'value' => 30.0,
            'items' => [['item_id' => 'PROD-1']],
        ];

        $this->addToCartProvider->method('getEvent')->willReturn('add_to_cart');
        $this->addToCartProvider
            ->method('getEcommerce')
            ->with($this->callback(static fn (array $context): bool => $context[ContextInterface::CONTEXT_ORDER_ITEM] === $orderItem &&
                $context[ContextInterface::CONTEXT_ORDER] === $order))
            ->willReturn($expectedEcommerce);

        $this->cart->add($orderItem, $order);

        $push = $this->gtm->getPush();
        self::assertNull($push[0]['ecommerce']);
        self::assertSame('add_to_cart', $push[1]['event']);
        self::assertSame($expectedEcommerce, $push[1]['ecommerce']);
    }

    public function testRemovePushesRemoveFromCartEvent(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $expectedEcommerce = [
            'currency' => 'EUR',
            'value' => 30.0,
            'items' => [['item_id' => 'PROD-1']],
        ];

        $this->removeFromCartProvider->method('getEvent')->willReturn('remove_from_cart');
        $this->removeFromCartProvider
            ->method('getEcommerce')
            ->with($this->callback(static fn (array $context): bool => $context[ContextInterface::CONTEXT_ORDER_ITEM] === $orderItem &&
                $context[ContextInterface::CONTEXT_ORDER] === $order))
            ->willReturn($expectedEcommerce);

        $this->cart->remove($orderItem, $order);

        $push = $this->gtm->getPush();
        self::assertNull($push[0]['ecommerce']);
        self::assertSame('remove_from_cart', $push[1]['event']);
        self::assertSame($expectedEcommerce, $push[1]['ecommerce']);
    }
}
