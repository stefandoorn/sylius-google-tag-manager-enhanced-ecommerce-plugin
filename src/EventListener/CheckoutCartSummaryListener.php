<?php

namespace GtmEnhancedEcommercePlugin\EventListener;

use GtmEnhancedEcommercePlugin\Resolver\CheckoutStepResolver;
use GtmEnhancedEcommercePlugin\TagManager\AddTransactionInterface;
use GtmEnhancedEcommercePlugin\TagManager\CheckoutStep;
use Sylius\Bundle\CoreBundle\Controller\OrderController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class CheckoutCartSummaryListener
 * @package GtmEnhancedEcommerce\EventListener
 */
class CheckoutCartSummaryListener
{
    /**
     * @var CheckoutStep
     */
    private $checkoutStep;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var CheckoutStepResolver
     */
    private $checkoutStepResolver;

    /**
     * CheckoutCartSummaryListener constructor.
     * @param CheckoutStep $checkoutStep
     * @param CartContextInterface $cartContext
     */
    public function __construct(
        CheckoutStep $checkoutStep,
        CartContextInterface $cartContext,
        CheckoutStepResolver $checkoutStepResolver
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
        $step = $this->checkoutStepResolver->resolve($controller[0], $controller[1], $event->getRequest());
        if ($step === null) {
            return;
        }

        // Add E-Commerce data
        $this->checkoutStep->addStep($this->cartContext->getCart(), $step);
    }
}
