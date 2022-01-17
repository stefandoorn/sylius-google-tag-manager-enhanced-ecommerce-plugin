<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductIdentifierHelper
{
    private string $productIdentifier;

    public function __construct(string $productIdentifier)
    {
        $this->productIdentifier = $productIdentifier;
    }

    public function getProductIdentifier(ProductInterface $product): string
    {
        switch ($this->productIdentifier) {
            case ProductDetailInterface::ID_IDENTIFIER:
                return (string) $product->getId();
            case ProductDetailInterface::CODE_IDENTIFIER:
                return $product->getCode();
        }

        throw new \RuntimeException(\sprintf('Invalid productIdentifier parameter value: %s', $this->productIdentifier));
    }
}
