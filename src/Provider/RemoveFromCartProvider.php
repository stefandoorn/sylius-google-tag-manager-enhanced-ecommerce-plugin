<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider;

class RemoveFromCartProvider extends AddToCartProvider
{
    public function getEvent(array $context): ?string
    {
        return 'remove_from_cart';
    }
}
