<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider;

interface GtmProviderInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function getEvent(array $context): ?string;

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>|null
     */
    public function getEcommerce(array $context): ?array;
}
