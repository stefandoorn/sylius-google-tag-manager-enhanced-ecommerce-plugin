<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CheckoutStepInterface;
use Symfony\Component\HttpFoundation\Request;

final class CheckoutStepResolver implements CheckoutStepResolverInterface
{
    public function resolve(string $method, Request $request): ?int
    {
        return match ($method) {
            'summaryAction' => CheckoutStepInterface::STEP_CART,
            'updateAction' => $this->updateAction($request),
            default => null,
        };
    }

    private function updateAction(Request $request): ?int
    {
        $route = $request->get('_route');

        return match ($route) {
            'sylius_shop_checkout_address' => CheckoutStepInterface::STEP_ADDRESS,
            'sylius_shop_checkout_select_shipping' => CheckoutStepInterface::STEP_SHIPPING,
            'sylius_shop_checkout_select_payment' => CheckoutStepInterface::STEP_PAYMENT,
            'sylius_shop_checkout_complete' => CheckoutStepInterface::STEP_CONFIRM,
            default => null,
        };
    }
}
