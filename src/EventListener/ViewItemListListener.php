<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemListInterface;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\BreadcrumbComponent;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class ViewItemListListener
{
    public function __construct(
        private ViewItemListInterface $viewItemList,
        private BreadcrumbComponent $breadcrumbComponent,
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        try {
            $taxon = $this->breadcrumbComponent->taxon();
        } catch (\InvalidArgumentException) {
            return;
        }

        /** @var GridViewInterface $gridView */
        $gridView = $event->getSubject();

        $products = [];
        /** @var iterable<ProductInterface> $data */
        $data = $gridView->getData();
        foreach ($data as $product) {
            $products[] = $product;
        }

        $this->viewItemList->add($taxon, $products);
    }
}
