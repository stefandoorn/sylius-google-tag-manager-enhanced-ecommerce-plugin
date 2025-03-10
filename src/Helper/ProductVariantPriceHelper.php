<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantPriceHelper implements ProductVariantPriceHelperInterface
{
    public function __construct(
        private ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function getProductVariantPrice(
        ProductVariantInterface $productVariant,
    ): int {
        return $this->productVariantPricesCalculator->calculate(
            $productVariant,
            ['channel' => $this->channelContext->getChannel()],
        );
    }
}
