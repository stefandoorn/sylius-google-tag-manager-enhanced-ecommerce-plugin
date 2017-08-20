<?php

namespace GtmEnhancedEcommercePlugin\EventListener;

use GtmEnhancedEcommercePlugin\TagManager\ProductDetailImpressionInterface;
use Sonata\BlockBundle\Event\BlockEvent;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * Class ProductShowListener
 * @package GtmEnhancedEcommercePlugin\EventListener
 */
final class ProductShowListener
{
    /**
     * @var ProductDetailImpressionInterface
     */
    private $addProductDetail;

    /**
     * ProductShowListener constructor.
     * @param ProductDetailImpressionInterface $addProductDetail
     */
    public function __construct(ProductDetailImpressionInterface $addProductDetail)
    {
        $this->addProductDetail = $addProductDetail;
    }

    /**
     * @param BlockEvent $event
     */
    public function onProductShow(ResourceControllerEvent $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getSubject();

        $this->addProductDetail->add($product);
    }
}
