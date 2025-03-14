<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\MainRequest\ControllerEventMainRequest;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver\CheckoutStepResolverInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CheckoutStepInterface;
use Sylius\Bundle\CoreBundle\Controller\OrderController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class CheckoutStepListener
{
    public function __construct(
        private CheckoutStepInterface $checkoutStep,
        private CartContextInterface $cartContext,
        private CheckoutStepResolverInterface $checkoutStepResolver,
        private int $step,
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // Only perform on the main request, not on sub-requests
        if (!ControllerEventMainRequest::isMainRequest($event)) {
            return;
        }

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony, but it may happen.
         * If it is a class, it comes in array format
         */
        if (!\is_array($controller)) {
            return;
        }

        // Should be order controller, else we are for sure not in the checkout
        if (!$controller[0] instanceof OrderController) {
            return;
        }

        // Resolve step
        $step = $this->checkoutStepResolver->resolve($controller[1], $event->getRequest());
        if ($step === null) {
            return;
        }

        // Only allow happening on certain steps, due to service configuration (feature toggles)
        if ($step !== $this->step) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();

        // Add E-Commerce data
        $this->checkoutStep->addStep($order, $step);
    }
}
