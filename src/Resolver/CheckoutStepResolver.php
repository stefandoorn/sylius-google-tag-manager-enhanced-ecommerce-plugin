<?php

namespace GtmEnhancedEcommercePlugin\Resolver;

use GtmEnhancedEcommercePlugin\TagManager\CheckoutStep;
use Sylius\Bundle\CoreBundle\Controller\OrderController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CheckoutStepResolver
 * @package GtmEnhancedEcommercePlugin\Resolver
 */
class CheckoutStepResolver
{
    /**
     * @param string $method
     * @param Request $request
     * @return int|null
     */
    public function resolve(string $method, Request $request): ?int
    {
        if ($method === 'summaryAction') {
            return CheckoutStep::STEP_CART;
        } elseif ($method === 'updateAction') {
            $route = $request->get('_route');
            if ($route === 'sylius_shop_checkout_address') {
                return CheckoutStep::STEP_ADDRESS;
            } elseif ($route === 'sylius_shop_checkout_select_shipping') {
                return CheckoutStep::STEP_SHIPPING;
            } elseif ($route === 'sylius_shop_checkout_select_payment') {
                return CheckoutStep::STEP_PAYMENT;
            } elseif ($route === 'sylius_shop_checkout_complete') {
                return CheckoutStep::STEP_CONFIRM;
            }
        } elseif ($method === 'thankYouAction') {
            return CheckoutStep::STEP_THANKYOU;
        }

        return null;
    }
}
