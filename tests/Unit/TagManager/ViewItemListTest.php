<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\TagManager;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelperInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductVariantPriceHelperInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemList;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

#[CoversClass(ViewItemList::class)]
final class ViewItemListTest extends TestCase
{
    private GoogleTagManager $gtm;

    private MockObject&ProductRepositoryInterface $productRepository;

    private MockObject&ChannelContextInterface $channelContext;

    private MockObject&LocaleContextInterface $localeContext;

    private MockObject&ProductIdentifierHelperInterface $productIdentifierHelper;

    private MockObject&ProductVariantResolverInterface $productVariantResolver;

    private MockObject&ProductVariantPriceHelperInterface $productVariantPriceHelper;

    private ViewItemList $viewItemList;

    protected function setUp(): void
    {
        $this->gtm = new GoogleTagManager(true, 'id1234');
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->productIdentifierHelper = $this->createMock(ProductIdentifierHelperInterface::class);
        $this->productVariantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->productVariantPriceHelper = $this->createMock(ProductVariantPriceHelperInterface::class);

        $this->viewItemList = new ViewItemList(
            $this->gtm,
            $this->productRepository,
            $this->channelContext,
            $this->localeContext,
            $this->productIdentifierHelper,
            $this->productVariantResolver,
            $this->productVariantPriceHelper,
        );
    }

    public function testAddPushesViewItemListEvent(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $taxon->method('getName')->willReturn('Electronics');

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getName')->willReturn('My Store');

        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->localeContext->method('getLocaleCode')->willReturn('en_US');

        $product = $this->createMock(ProductInterface::class);
        $product->method('getName')->willReturn('Test Product');

        $variant = $this->createMock(ProductVariantInterface::class);

        $this->productIdentifierHelper
            ->method('getProductIdentifier')
            ->with($product)
            ->willReturn('PROD-1');

        $this->productVariantResolver
            ->method('getVariant')
            ->with($product)
            ->willReturn($variant);

        $this->productVariantPriceHelper
            ->method('getProductVariantPrice')
            ->with($variant)
            ->willReturn(1500);

        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn([$product]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getQuery')->willReturn($query);

        $this->productRepository
            ->method('createShopListQueryBuilder')
            ->with($channel, $taxon, 'en_US')
            ->willReturn($queryBuilder);

        $this->viewItemList->add($taxon, 'sylius_shop_product_index');

        $push = $this->gtm->getPush();

        // First push: ecommerce reset
        self::assertArrayHasKey('ecommerce', $push[0]);
        self::assertNull($push[0]['ecommerce']);

        // Second push: view_item_list event
        self::assertEquals('view_item_list', $push[1]['event']);
        self::assertEquals('sylius_shop_product_index', $push[1]['ecommerce']['item_list_id']);
        self::assertEquals('Electronics', $push[1]['ecommerce']['item_list_name']);

        $items = $push[1]['ecommerce']['items'];
        self::assertCount(1, $items);
        self::assertEquals('PROD-1', $items[0]['item_id']);
        self::assertEquals('Test Product', $items[0]['item_name']);
        self::assertEquals('My Store', $items[0]['affiliation']);
        self::assertEquals('Electronics', $items[0]['item_category']);
        self::assertEquals(0, $items[0]['index']);
        self::assertEquals(15.0, $items[0]['price']);
    }

    public function testAddDoesNothingWhenNoProducts(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->localeContext->method('getLocaleCode')->willReturn('en_US');

        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn([]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getQuery')->willReturn($query);

        $this->productRepository
            ->method('createShopListQueryBuilder')
            ->willReturn($queryBuilder);

        $this->viewItemList->add($taxon);

        self::assertEmpty($this->gtm->getPush());
    }
}
