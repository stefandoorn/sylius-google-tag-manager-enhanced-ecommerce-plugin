<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\MainRequest\ControllerEventMainRequest;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\MainRequest\RequestStackMainRequest;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ViewItemListener
{
    private RequestStack $requestStack;

    private ViewItemInterface $viewItem;

    public function __construct(
        RequestStack $requestStack,
        ViewItemInterface $viewItem
    ) {
        $this->requestStack = $requestStack;
        $this->viewItem = $viewItem;
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        if ($this->requestStack->getCurrentRequest() !== RequestStackMainRequest::getMainRequest(
                $this->requestStack
            )) {
            return;
        }

        /** @var ProductInterface $product */
        $product = $event->getSubject();

        $this->viewItem->add($product);
    }
}
