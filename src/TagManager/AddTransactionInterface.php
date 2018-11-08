<?php declare(strict_types=1);

namespace SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Interface AddTransactionInterface
 * @package SyliusGtmEnhancedEcommercePlugin\TagManager
 */
interface AddTransactionInterface
{
    /**
     * @param OrderInterface $order
     */
    public function addTransaction(OrderInterface $order): void;
}
