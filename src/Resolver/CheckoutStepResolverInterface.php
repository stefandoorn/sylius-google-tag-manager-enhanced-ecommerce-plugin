<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver;

use Symfony\Component\HttpFoundation\Request;

interface CheckoutStepResolverInterface
{
    public function resolve(string $method, Request $request): ?int;
}
