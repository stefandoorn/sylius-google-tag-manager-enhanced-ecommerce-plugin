<?php declare(strict_types=1);

namespace GtmEnhancedEcommercePlugin\EventListener;

use GtmEnhancedEcommercePlugin\Resolver\ProductDetailImpressionDataResolverInterface;
use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductShowBlockAfterProductHeaderListener
{
    /**
     * @var string
     */
    private $template;

    /**
     * @var ProductDetailImpressionDataResolverInterface
     */
    private $resolver;

    public function __construct(string $template, ProductDetailImpressionDataResolverInterface $productDetailImpressionDataResolver)
    {
        $this->template = $template;
        $this->resolver = $productDetailImpressionDataResolver;
    }

    public function onProductShow(BlockEvent $event): void
    {
        /** @var \AppBundle\Entity\Interfaces\ProductInterface $product */
        $product = $event->getSetting('product');

        $block = new Block();
        $block->setId(uniqid('', true));
        $block->setSettings(array_replace($event->getSettings(), [
            'template' => $this->template,
            'resources' => $this->resolver->get($product)
        ]));

        $block->setType('sonata.block.service.template');

        $event->addBlock($block);
    }
}
