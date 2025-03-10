<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductVariantPriceHelperInterface
{
    public function getProductVariantPrice(
        ProductVariantInterface $productVariant,
    ): int;
}
