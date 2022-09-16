<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

interface CartInterface
{
    public function addUA(array $productData): void;

    public function addGA4(array $productData): void;

    public function removeUA(array $productData): void;

    public function removeGA4(array $productData): void;
}
