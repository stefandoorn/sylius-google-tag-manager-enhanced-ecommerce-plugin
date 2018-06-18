<?php

namespace Tests\GtmEnhancedEcommercePlugin\EventListener;

use GtmEnhancedEcommercePlugin\EventListener\ThankYouListener;
use GtmEnhancedEcommercePlugin\TagManager\AddTransaction;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Controller\OrderController;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

/**
 * Class ThankYouListenerTest
 * @package Tests\GtmEnhancedEcommercePlugin\EventListener
 * @covers \GtmEnhancedEcommercePlugin\EventListener\ThankYouListener
 */
final class ThankYouListenerTest extends TestCase
{

    public function testWrongController()
    {
        // Requirements
        $gtm = new GoogleTagManager(true, 'id1234');

        // Build base mocks
        $channelContext = $this->getMockBuilder(ChannelContextInterface::class)->getMock();
        $currencyContext = $this->getMockBuilder(CurrencyContextInterface::class)->getMock();
        $orderRepository = $this->getMockBuilder(OrderRepositoryInterface::class)->getMock();
        $event = $this->getMockBuilder(FilterControllerEvent::class)->disableOriginalConstructor()->getMock();
        $controller = $this->getMockBuilder(OrderController::class)->disableOriginalConstructor()->getMock();

        // Mock expectations
        $event->expects($this->once())->method('getController')->willReturn([
            $controller,
            'otherAction',
        ]);

        // Service and listener
        $service = new AddTransaction($gtm, $channelContext, $currencyContext);
        $listener = new ThankYouListener($service, $orderRepository);

        // Run listener
        $listener->onKernelController($event);

        // Check result
        $this->assertArrayNotHasKey('ecommerce', $gtm->getData());
    }
    /*
        public function testNoOrderFound()
        {
            // Requirements
            $gtm = new GoogleTagManager(true, 'id1234');
            $order = new Order();

            // Build base mocks
            $channelContext = $this->getMockBuilder(ChannelContextInterface::class)->getMock();
            $currencyContext = $this->getMockBuilder(CurrencyContextInterface::class)->getMock();
            $orderRepository = $this->getMockBuilder(OrderRepositoryInterface::class)->getMock();
            $event = $this->getMockBuilder(FilterControllerEvent::class)->disableOriginalConstructor()->getMock();
            $controller = $this->getMockBuilder(OrderController::class)->disableOriginalConstructor()->getMock();
            $session = $this->getMockBuilder(SessionInterface::class)->getMock();
            $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

            // Mock expectations
            $event->expects($this->once())->method('getController')->willReturn([
                $controller,
                'thankYouAction',
            ]);
            $event->expects($this->once())->method('getRequest')->willReturn($request);
            $request->expects($this->once())->method('getSession')->willReturn($session);
            $session->expects($this->once())->method('get')->with('sylius_order_id')->willReturn(88);
            $orderRepository->expects($this->once())->method('find')->with(88)->willReturn(null);

            // Service and listener
            $service = new AddTransaction($gtm, $channelContext, $currencyContext);
            $listener = new ThankYouListener($service,$orderRepository);

            // Run listener
            $listener->onKernelController($event);

            // Check result
            $this->assertArrayNotHasKey('ecommerce', $gtm->getData());
        }

        public function testEnvironmentIsAddedToGtmObject()
        {
            // Requirements
            $gtm = new GoogleTagManager(true, 'id1234');
            $order = new Order();

            // Build base mocks
            $channelContext = $this->getMockBuilder(ChannelContextInterface::class)->getMock();
            $currencyContext = $this->getMockBuilder(CurrencyContextInterface::class)->getMock();
            $orderRepository = $this->getMockBuilder(OrderRepositoryInterface::class)->getMock();
            $event = $this->getMockBuilder(FilterControllerEvent::class)->disableOriginalConstructor()->getMock();
            $controller = $this->getMockBuilder(OrderController::class)->disableOriginalConstructor()->getMock();
            $session = $this->getMockBuilder(SessionInterface::class)->getMock();
            $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

            // Mock expectations
            $event->expects($this->once())->method('getController')->willReturn([
                $controller,
                'thankYouAction',
            ]);
            $event->expects($this->once())->method('getRequest')->willReturn($request);
            $request->expects($this->once())->method('getSession')->willReturn($session);
            $session->expects($this->once())->method('get')->with('sylius_order_id')->willReturn(88);
            $orderRepository->expects($this->once())->method('find')->with(88)->willReturn($order);

            // Service and listener
            $service = new AddTransaction($gtm, $channelContext, $currencyContext);
            $listener = new ThankYouListener($service,$orderRepository);

            // Run listener
            $listener->onKernelController($event);

            // Check result
            $this->assertArrayHasKey('ecommerce', $gtm->getData());
        }*/
}
