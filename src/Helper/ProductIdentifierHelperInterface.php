<?php

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper;

use Sylius\Component\Core\Model\ProductInterface;
interface ProductIdentifierHelperInterface
{
    public function getProductIdentifier(ProductInterface $product): string;

}