<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\ProductInterface;

interface ViewItemInterface
{
    public function add(ProductInterface $product): void;
}
