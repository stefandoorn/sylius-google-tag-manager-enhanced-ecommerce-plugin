<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CheckoutStepInterface;
use Symfony\Component\HttpFoundation\Request;

final class CheckoutStepResolver implements CheckoutStepResolverInterface
{
    public function resolve(string $method, Request $request): ?int
    {
        switch ($method) {
            case 'summaryAction':
                return CheckoutStepInterface::STEP_CART;
            case 'updateAction':
                return $this->updateAction($request);
        }

        return null;
    }

    private function updateAction(Request $request): ?int
    {
        $route = $request->get('_route');

        switch ($route) {
            case 'sylius_shop_checkout_address':
                return CheckoutStepInterface::STEP_ADDRESS;
            case 'sylius_shop_checkout_select_shipping':
                return CheckoutStepInterface::STEP_SHIPPING;
            case 'sylius_shop_checkout_select_payment':
                return CheckoutStepInterface::STEP_PAYMENT;
            case 'sylius_shop_checkout_complete':
                return CheckoutStepInterface::STEP_CONFIRM;
        }

        return null;
    }
}
