<?php

namespace GtmEnhancedEcommercePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package GtmEnhancedEcommercePlugin\DependencyInjection
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('gtm_enhanced_ecommerce');

        $rootNode
            ->children()
                ->arrayNode('features')
                    ->children()
                        ->booleanNode('purchases')->defaultTrue()->end()
                        ->booleanNode('product-detail-impressions')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
