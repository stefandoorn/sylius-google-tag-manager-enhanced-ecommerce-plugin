<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;

final class ViewItemListener
{
    private ViewItemInterface $viewItem;

    public function __construct(
        ViewItemInterface $viewItem
    ) {
        $this->viewItem = $viewItem;
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        /** @var ProductInterface $product */
        $product = $event->getSubject();

        $this->viewItem->add($product);
    }
}
