<?php

namespace GtmEnhancedEcommercePlugin\Resolver;

use GtmEnhancedEcommercePlugin\TagManager\CheckoutStep;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CheckoutStepResolver
 * @package GtmEnhancedEcommercePlugin\Resolver
 */
class CheckoutStepResolver implements CheckoutStepResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(string $method, Request $request): ?int
    {
        switch ($method) {
            case 'summaryAction':
                return CheckoutStep::STEP_CART;
            case 'updateAction':
                return $this->updateAction($request);
            case 'thankYouAction':
                return CheckoutStep::STEP_THANKYOU;
        }

        return null;
    }

    /**
     * @param Request $request
     * @return int|null
     */
    private static function updateAction(Request $request): ?int
    {
        $route = $request->get('_route');

        switch ($route) {
            case 'sylius_shop_checkout_address':
                return CheckoutStep::STEP_ADDRESS;
            case 'sylius_shop_checkout_select_shipping':
                return CheckoutStep::STEP_SHIPPING;
            case 'sylius_shop_checkout_select_payment':
                return CheckoutStep::STEP_PAYMENT;
            case 'sylius_shop_checkout_complete':
                return CheckoutStep::STEP_CONFIRM;
        }

        return null;
    }
}
