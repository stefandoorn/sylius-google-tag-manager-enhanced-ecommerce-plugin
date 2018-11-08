<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Interface AddTransactionInterface
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager
 */
interface AddTransactionInterface
{
    /**
     * @param OrderInterface $order
     */
    public function addTransaction(OrderInterface $order): void;
}
