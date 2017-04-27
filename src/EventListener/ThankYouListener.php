<?php

namespace GtmEnhancedEcommercePlugin\EventListener;

use GtmEnhancedEcommercePlugin\TagManager\AddTransactionInterface;
use Sylius\Bundle\CoreBundle\Controller\OrderController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ThankYouListener
 * @package GtmEnhancedEcommerce\EventListener
 */
class ThankYouListener
{

    /**
     * @var AddTransactionInterface
     */
    private $transactionService;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * ThankYouListener constructor.
     * @param bool $enabled
     * @param AddTransactionInterface $transactionService
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        bool $enabled,
        AddTransactionInterface $transactionService,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->transactionService = $transactionService;
        $this->orderRepository = $orderRepository;
        $this->enabled = $enabled;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        // We only want the SyliusOrderController
        if (!$controller[0] instanceof OrderController) {
            return;
        }

        // Now check the method, should be
        if (!$controller[1] === 'thankYouAction') {
            return;
        }

        // Find Order ID
        $orderId = $event->getRequest()->getSession()->get('sylius_order_id');
        if ($orderId === null) {
            return;
        }

        // Find Order
        $order = $this->orderRepository->find($orderId);
        if (!$order instanceof OrderInterface) {
            return;
        }

        // Add E-Commerce data
        $this->transactionService->addTransaction($order);
    }
}
