<?php

namespace Tests\GtmEnhancedEcommercePlugin\TagManager;

use Doctrine\Common\Collections\ArrayCollection;
use GtmEnhancedEcommercePlugin\Object\Factory\ProductDetailFactoryInterface;
use GtmEnhancedEcommercePlugin\Object\Factory\ProductDetailImpressionFactoryInterface;
use GtmEnhancedEcommercePlugin\Resolver\ProductDetailImpressionDataResolver;
use GtmEnhancedEcommercePlugin\TagManager\ProductDetailImpression;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\Taxon;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

class ProductDetailImpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleTransaction()
    {
        // Requirements
        $gtm = new GoogleTagManager(true, 'id1234');

        // Mocks
        $channelContext = $this->getMockBuilder(ChannelContextInterface::class)->getMock();
        $channelContext->expects($this->exactly(2))->method('getChannel')->willReturn(new Channel());

        // Product variant price calculator
        $productVariantPriceCalculator = $this->getMockBuilder(ProductVariantPriceCalculatorInterface::class)->getMock();
        $productVariantPriceCalculator->expects($this->exactly(2))->method('calculate')->willReturnOnConsecutiveCalls(
            12210,
            13380
        );

        // Factories
        $productDetailFactory = $this->getMockBuilder(ProductDetailFactoryInterface::class)->getMock();
        $productDetailFactory->expects($this->exactly(2))->method('create')->willReturnCallback(function () {
            return new \GtmEnhancedEcommercePlugin\Object\ProductDetail();
        });
        $productDetailImpressionFactory = $this->getMockBuilder(ProductDetailImpressionFactoryInterface::class)->getMock();
        $productDetailImpressionFactory->expects($this->exactly(1))->method('create')->willReturnCallback(function () {
            return new \GtmEnhancedEcommercePlugin\Object\ProductDetailImpression();
        });

        // Resolver
        $resolver = new ProductDetailImpressionDataResolver(
            $productVariantPriceCalculator,
            $channelContext,
            $productDetailImpressionFactory,
            $productDetailFactory
        );

        // Product data
        $product = $this->getMockBuilder(ProductInterface::class)->getMock();
        $product->expects($this->exactly(2))->method('getId')->willReturnOnConsecutiveCalls(1, 2);
        $product->expects($this->exactly(2))->method('getName')->willReturnOnConsecutiveCalls('Test 1', 'Test 2');
        $product->expects($this->exactly(1))->method('getVariants')->willReturnCallback(function () use ($product) {
            $collection = new ArrayCollection();

            $variant = new ProductVariant();
            $variant->setCode('code-1');
            $variant->setProduct($product);
            $collection->add($variant);

            $variant = new ProductVariant();
            $variant->setCode('code-2');
            $variant->setProduct($product);
            $collection->add($variant);

            return $collection;
        });
        $product->expects($this->exactly(4))->method('getMainTaxon')->willReturnCallback(function () {
            $taxon = new Taxon();
            $taxon->setCurrentLocale('en_US');
            $taxon->setName('Test Taxon');
            return $taxon;
        });

        // Build object
        $service = new ProductDetailImpression($gtm, $resolver);

        // Run add
        $service->add($product);

        // Test result
        $this->assertArrayHasKey('ecommerce', $gtm->getData());
        $this->assertArrayHasKey('detail', $gtm->getData()['ecommerce']);
        $this->assertArrayHasKey('products', $gtm->getData()['ecommerce']['detail']);

        $data = $gtm->getData()['ecommerce']['detail']['products'];
        $this->assertInternalType('array', $data);
        $this->assertCount(2, $data);

        $this->assertSame([
            'name' => 'Test 1',
            'id' => 1,
            'price' => 122.10,
            'category' => 'Test Taxon',
            'variant' => 'code-1',
        ], $data[0]);

        $this->assertSame([
            'name' => 'Test 2',
            'id' => 2,
            'price' => 133.80,
            'category' => 'Test Taxon',
            'variant' => 'code-2',
        ], $data[1]);
    }
}
