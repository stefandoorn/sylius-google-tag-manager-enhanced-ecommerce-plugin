<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * Interface CheckoutStepInterface
 */
interface CheckoutStepInterface
{
    public function addStep(OrderInterface $order, int $step): void;
}
