<?php

namespace Tests\GtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use GtmEnhancedEcommercePlugin\TagManager\AddTransaction;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

/**
 * Class AddTransactionTest
 * @package Tests\GtmEnhancedEcommercePlugin\TagManager
 * @covers \GtmEnhancedEcommercePlugin\TagManager\AddTransaction
 */
class AddTransactionTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleTransaction()
    {
        // Requirements
        $gtm = new GoogleTagManager(true, 'id1234');
        $order = new Order();

        // Mocks
        $currencyContext = $this->getMockBuilder(CurrencyContextInterface::class)->getMock();
        $channelContext = $this->getMockBuilder(ChannelContextInterface::class)->getMock();

        // Exceptations
        $channelContext->expects($this->once())->method('getChannel')->willReturn(new Channel());

        // Build object
        $service = new AddTransaction($gtm, $channelContext, $currencyContext);

        // Run add
        $service->addTransaction($order);

        // Test result
        $this->assertArrayHasKey('ecommerce', $gtm->getData());
        $this->assertArrayHasKey('currencyCode', $gtm->getData());
        $this->assertArrayHasKey('purchase', $gtm->getData()['ecommerce']);
        $this->assertArrayHasKey('actionField', $gtm->getData()['ecommerce']['purchase']);
        $this->assertArrayHasKey('products', $gtm->getData()['ecommerce']['purchase']);
    }
}
