<?php

namespace GtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * Interface CheckoutStepInterface
 * @package GtmEnhancedEcommercePlugin\TagManager
 */
interface CheckoutStepInterface
{
    /**
     * @param OrderInterface $order
     * @param int $step
     */
    public function addStep(OrderInterface $order, int $step): void;
}
