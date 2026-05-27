<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

interface CartInterface
{
    public function view(OrderInterface $order): void;

    public function add(OrderItemInterface $orderItem, OrderInterface $order): void;

    public function remove(OrderItemInterface $orderItem, OrderInterface $order): void;
}
