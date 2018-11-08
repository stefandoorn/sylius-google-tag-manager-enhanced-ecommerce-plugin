<?php declare(strict_types=1);

namespace SyliusGtmEnhancedEcommercePlugin\Resolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface CheckoutStepResolverInterface
 * @package SyliusGtmEnhancedEcommercePlugin\Resolver
 */
interface CheckoutStepResolverInterface
{
    /**
     * @param string $method
     * @param Request $request
     * @return int|null
     */
    public function resolve(string $method, Request $request): ?int;
}
