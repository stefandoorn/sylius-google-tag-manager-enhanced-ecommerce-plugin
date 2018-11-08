<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * Interface CheckoutStepInterface
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager
 */
interface CheckoutStepInterface
{
    /**
     * @param OrderInterface $order
     * @param int $step
     */
    public function addStep(OrderInterface $order, int $step): void;
}
