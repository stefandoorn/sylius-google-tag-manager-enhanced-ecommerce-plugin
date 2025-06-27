<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class ViewItemListener
{
    public function __construct(
        private ViewItemInterface $viewItem,
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        /** @var ProductInterface $product */
        $product = $event->getSubject();

        $this->viewItem->add($product);
    }
}
