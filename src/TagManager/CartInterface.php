<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

interface CartInterface
{
    public function add(array $productData): void;

    public function remove(array $productData): void;
}
