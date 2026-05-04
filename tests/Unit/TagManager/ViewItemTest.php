<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\TagManager;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\GtmProviderInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItem;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

#[CoversClass(ViewItem::class)]
final class ViewItemTest extends TestCase
{
    private GoogleTagManager $gtm;

    private MockObject&ChannelContextInterface $channelContext;

    private MockObject&CurrencyContextInterface $currencyContext;

    private MockObject&GtmProviderInterface $viewItemProvider;

    private ViewItem $viewItem;

    protected function setUp(): void
    {
        $this->gtm = new GoogleTagManager(true, 'id1234');
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->currencyContext = $this->createMock(CurrencyContextInterface::class);
        $this->viewItemProvider = $this->createMock(GtmProviderInterface::class);

        $this->viewItem = new ViewItem(
            $this->gtm,
            $this->channelContext,
            $this->currencyContext,
            $this->viewItemProvider,
        );
    }

    public function testAddPushesViewItemEvent(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $channel->method('getName')->willReturn('My Store');
        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->currencyContext->method('getCurrencyCode')->willReturn('EUR');

        $expectedEcommerce = [
            'items' => [
                [
                    'item_id' => 'PROD-123',
                    'item_name' => 'Test Product',
                    'affiliation' => 'My Store',
                    'item_category' => 'Category A',
                    'index' => 0,
                    'price' => 15.0,
                ],
            ],
            'value' => 15.0,
            'currency' => 'EUR',
        ];

        $this->viewItemProvider
            ->method('getEvent')
            ->willReturn('view_item');

        $this->viewItemProvider
            ->method('getEcommerce')
            ->with($this->callback(function (array $context) use ($product, $channel): bool {
                return $context[ContextInterface::CONTEXT_PRODUCT] === $product &&
                    $context[ContextInterface::CONTEXT_CHANNEL] === $channel &&
                    $context[ContextInterface::CONTEXT_CURRENCY_CODE] === 'EUR';
            }))
            ->willReturn($expectedEcommerce);

        $this->viewItem->add($product);

        $push = $this->gtm->getPush();

        // First push: ecommerce reset
        self::assertArrayHasKey('ecommerce', $push[0]);
        self::assertNull($push[0]['ecommerce']);

        // Second push: view_item event
        self::assertEquals('view_item', $push[1]['event']);
        self::assertEquals($expectedEcommerce, $push[1]['ecommerce']);
    }
}
