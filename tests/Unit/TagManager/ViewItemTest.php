<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\TagManager;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductVariantPriceHelperInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItem;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

#[CoversClass(ViewItem::class)]
final class ViewItemTest extends TestCase
{
    private GoogleTagManager $gtm;

    private MockObject&ChannelContextInterface $channelContext;

    private MockObject&CurrencyContextInterface $currencyContext;

    private MockObject&ProductIdentifierHelperInterface $productIdentifierHelper;

    private MockObject&ProductVariantResolverInterface $productVariantResolver;

    private MockObject&ProductVariantPriceHelperInterface $productVariantPriceHelper;

    private ViewItem $viewItem;

    protected function setUp(): void
    {
        $this->gtm = new GoogleTagManager(true, 'id1234');
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->currencyContext = $this->createMock(CurrencyContextInterface::class);
        $this->productIdentifierHelper = $this->createMock(ProductIdentifierHelperInterface::class);
        $this->productVariantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->productVariantPriceHelper = $this->createMock(ProductVariantPriceHelperInterface::class);

        $this->viewItem = new ViewItem(
            $this->gtm,
            $this->channelContext,
            $this->currencyContext,
            $this->productIdentifierHelper,
            $this->productVariantResolver,
            $this->productVariantPriceHelper,
        );
    }

    public function testAddPushesViewItemEvent(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $mainTaxon = $this->createMock(TaxonInterface::class);

        $product->method('getName')->willReturn('Test Product');
        $product->method('getMainTaxon')->willReturn($mainTaxon);
        $mainTaxon->method('getName')->willReturn('Category A');

        $channel->method('getName')->willReturn('My Store');
        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->currencyContext->method('getCurrencyCode')->willReturn('EUR');

        $this->productIdentifierHelper
            ->method('getProductIdentifier')
            ->with($product)
            ->willReturn('PROD-123');

        $this->productVariantResolver
            ->method('getVariant')
            ->with($product)
            ->willReturn($productVariant);

        $this->productVariantPriceHelper
            ->method('getProductVariantPrice')
            ->with($productVariant)
            ->willReturn(1500);

        $this->viewItem->add($product);

        $push = $this->gtm->getPush();

        // First push: ecommerce reset
        self::assertArrayHasKey('ecommerce', $push[0]);
        self::assertNull($push[0]['ecommerce']);

        // Second push: view_item event
        self::assertEquals('view_item', $push[1]['event']);
        self::assertEquals([
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
        ], $push[1]['ecommerce']);
    }
}
