<?php

namespace GtmEnhancedEcommercePlugin\EventListener;

use GtmEnhancedEcommercePlugin\Resolver\CheckoutStepResolverInterface;
use GtmEnhancedEcommercePlugin\TagManager\CheckoutStepInterface;
use Sylius\Bundle\CoreBundle\Controller\OrderController;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class CheckoutCartSummaryListener
 * @package GtmEnhancedEcommerce\EventListener
 */
class CheckoutStepListener
{
    /**
     * @var CheckoutStepInterface
     */
    private $checkoutStep;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var CheckoutStepResolverInterface
     */
    private $checkoutStepResolver;

    /**
     * CheckoutCartSummaryListener constructor.
     * @param CheckoutStepInterface $checkoutStep
     * @param CartContextInterface $cartContext
     * @param CheckoutStepResolverInterface $checkoutStepResolver
     */
    public function __construct(
        CheckoutStepInterface $checkoutStep,
        CartContextInterface $cartContext,
        CheckoutStepResolverInterface $checkoutStepResolver
    ) {
        $this->checkoutStep = $checkoutStep;
        $this->cartContext = $cartContext;
        $this->checkoutStepResolver = $checkoutStepResolver;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
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

        // Add E-Commerce data
        $this->checkoutStep->addStep($this->cartContext->getCart(), $step);
    }
}
