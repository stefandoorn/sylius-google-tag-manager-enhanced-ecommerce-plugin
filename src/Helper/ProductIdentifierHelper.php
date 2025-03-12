<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper;

use Sylius\Component\Core\Model\ProductInterface;

final class ProductIdentifierHelper implements ProductIdentifierHelperInterface
{
    public const ID_IDENTIFIER = 'id';

    public const CODE_IDENTIFIER = 'code';

    public const IDENTIFIERS = [self::ID_IDENTIFIER, self::CODE_IDENTIFIER];

    private string $productIdentifier;

    public function __construct(string $productIdentifier)
    {
        $this->productIdentifier = $productIdentifier;
    }

    public function getProductIdentifier(ProductInterface $product): string
    {
        switch ($this->productIdentifier) {
            case self::ID_IDENTIFIER:
                return (string) $product->getId();
            case self::CODE_IDENTIFIER:
                return $product->getCode();
        }

        throw new \RuntimeException(\sprintf('Invalid productIdentifier parameter value: %s', $this->productIdentifier));
    }
}
