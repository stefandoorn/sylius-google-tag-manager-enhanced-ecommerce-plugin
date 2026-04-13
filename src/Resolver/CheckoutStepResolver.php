<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver;

use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\HttpFoundation\Request;

final class CheckoutStepResolver implements CheckoutStepResolverInterface
{
    public function resolve(string $method, Request $request): ?string
    {
        return match ($method) {
            'summaryAction' => OrderCheckoutStates::STATE_CART,
            'updateAction' => $this->updateAction($request),
            default => null,
        };
    }

    private function updateAction(Request $request): ?string
    {
        $route = $request->get('_route');

        return match ($route) {
            'sylius_shop_checkout_address' => OrderCheckoutStates::STATE_ADDRESSED,
            'sylius_shop_checkout_select_shipping' => OrderCheckoutStates::STATE_SHIPPING_SELECTED,
            'sylius_shop_checkout_select_payment' => OrderCheckoutStates::STATE_PAYMENT_SELECTED,
            'sylius_shop_checkout_complete' => OrderCheckoutStates::STATE_COMPLETED,
            default => null,
        };
    }
}
