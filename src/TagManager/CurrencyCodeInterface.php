<?php

namespace GtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Interface AddTransactionInterface
 * @package GtmEnhancedEcommercePlugin\TagManager
 */
interface CurrencyCodeInterface
{
    /**
     *
     */
    public function addCurrencyCode(): void;
}
