<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderInterface;

interface AddTransactionInterface
{
    public function addTransaction(OrderInterface $order): void;
}
