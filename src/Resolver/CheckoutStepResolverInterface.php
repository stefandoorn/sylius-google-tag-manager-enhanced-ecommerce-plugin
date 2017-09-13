<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\Resolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface CheckoutStepResolverInterface
 * @package GtmEnhancedEcommercePlugin\Resolver
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
