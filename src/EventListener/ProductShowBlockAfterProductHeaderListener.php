<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver\ProductDetailImpressionDataResolverInterface;

final class ProductShowBlockAfterProductHeaderListener
{
    private string $template;

    private ProductDetailImpressionDataResolverInterface $resolver;

    public function __construct(string $template, ProductDetailImpressionDataResolverInterface $productDetailImpressionDataResolver)
    {
        $this->template = $template;
        $this->resolver = $productDetailImpressionDataResolver;
    }

    public function onProductShow(BlockEvent $event): void
    {
        $product = $event->getSetting('product');

        $block = new Block();
        $block->setId(\uniqid('', true));
        $block->setSettings(\array_replace($event->getSettings(), [
            'template' => $this->template,
            'resources' => $this->resolver->get($product),
        ]));

        $block->setType('sonata.block.service.template');

        $event->addBlock($block);
    }
}
