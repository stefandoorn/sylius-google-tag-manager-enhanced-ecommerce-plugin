<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CheckoutStepInterface;
use Sylius\Bundle\CoreBundle\Controller\OrderController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class ThankYouListener
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CheckoutStepInterface $checkoutStep,
    ) {
    }

    public function __invoke(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if (!$event->isMainRequest()) {
            return;
        }

        if (!\is_array($controller)) {
            return;
        }

        if (!$controller[0] instanceof OrderController) {
            return;
        }

        if ($controller[1] !== 'thankYouAction') {
            return;
        }

        // Find Order ID
        $orderId = $event->getRequest()->getSession()->get('sylius_order_id');
        if (null === $orderId) {
            return;
        }

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        if (null === $order) {
            return;
        }

        $this->checkoutStep->addStep($order, OrderCheckoutStates::STATE_COMPLETED);
    }
}
