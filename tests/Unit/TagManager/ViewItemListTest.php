<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\TagManager;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\GtmProviderInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemList;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

#[CoversClass(ViewItemList::class)]
final class ViewItemListTest extends TestCase
{
    private GoogleTagManager $gtm;

    private MockObject&ChannelContextInterface $channelContext;

    private MockObject&CurrencyContextInterface $currencyContext;

    private MockObject&GtmProviderInterface $viewItemListProvider;

    private ViewItemList $viewItemList;

    protected function setUp(): void
    {
        $this->gtm = new GoogleTagManager(true, 'id1234');
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->currencyContext = $this->createMock(CurrencyContextInterface::class);
        $this->viewItemListProvider = $this->createMock(GtmProviderInterface::class);

        $this->viewItemList = new ViewItemList(
            $this->gtm,
            $this->channelContext,
            $this->currencyContext,
            $this->viewItemListProvider,
        );
    }

    public function testAddPushesViewItemListEvent(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $product = $this->createMock(ProductInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->currencyContext->method('getCurrencyCode')->willReturn('EUR');

        $expectedEcommerce = [
            'items' => [
                [
                    'item_id' => 'PROD-1',
                    'item_name' => 'Test Product',
                    'affiliation' => 'My Store',
                    'item_category' => 'Electronics',
                    'item_list_id' => 'ELECTRONICS',
                    'item_list_name' => 'Electronics',
                    'index' => 0,
                    'price' => 15.0,
                ],
            ],
        ];

        $this->viewItemListProvider
            ->method('getEvent')
            ->willReturn('view_item_list');

        $this->viewItemListProvider
            ->method('getEcommerce')
            ->with($this->callback(function (array $context) use ($product, $taxon, $channel): bool {
                return $context[ContextInterface::CONTEXT_PRODUCTS] === [$product] &&
                    $context[ContextInterface::CONTEXT_TAXON] === $taxon &&
                    $context[ContextInterface::CONTEXT_CHANNEL] === $channel &&
                    $context[ContextInterface::CONTEXT_CURRENCY_CODE] === 'EUR';
            }))
            ->willReturn($expectedEcommerce);

        $this->viewItemList->add($taxon, [$product]);

        $push = $this->gtm->getPush();

        // First push: ecommerce reset
        self::assertArrayHasKey('ecommerce', $push[0]);
        self::assertNull($push[0]['ecommerce']);

        // Second push: view_item_list event
        self::assertEquals('view_item_list', $push[1]['event']);
        self::assertEquals($expectedEcommerce, $push[1]['ecommerce']);
    }

    public function testAddDoesNothingWhenNoProducts(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);

        $this->viewItemList->add($taxon, []);

        self::assertEmpty($this->gtm->getPush());
    }
}
