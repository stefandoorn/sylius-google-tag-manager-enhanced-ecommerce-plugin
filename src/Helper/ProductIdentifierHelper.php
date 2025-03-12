<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper;

use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ProductIdentifierHelper implements ProductIdentifierHelperInterface
{
    public const ID_IDENTIFIER = 'id';

    public const CODE_IDENTIFIER = 'code';

    public const IDENTIFIERS = [self::ID_IDENTIFIER, self::CODE_IDENTIFIER];

    public function __construct(
        private string $productIdentifier,
    ) {
    }

    public function getProductIdentifier(ProductInterface $product): string
    {
        switch ($this->productIdentifier) {
            case self::ID_IDENTIFIER:
                return (string) $product->getId();
            case self::CODE_IDENTIFIER:
                $code = $product->getCode();
                Assert::notNull($code, 'The product code cannot be null.');

                return $code;
        }

        throw new \RuntimeException(\sprintf('Invalid productIdentifier parameter value: %s', $this->productIdentifier));
    }
}
