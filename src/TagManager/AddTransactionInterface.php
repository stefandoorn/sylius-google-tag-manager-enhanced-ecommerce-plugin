<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Interface AddTransactionInterface
 * @package GtmEnhancedEcommercePlugin\TagManager
 */
interface AddTransactionInterface
{
    /**
     * @param OrderInterface $order
     */
    public function addTransaction(OrderInterface $order): void;
}
