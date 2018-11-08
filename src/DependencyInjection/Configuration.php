<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('sylius_gtm_enhanced_ecommerce');

        $rootNode
            ->children()
                ->arrayNode('features')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('purchases')->defaultTrue()->end()
                        ->booleanNode('product_impressions')->defaultTrue()->end()
                        ->booleanNode('product_detail_impressions')->defaultTrue()->end()
                        ->booleanNode('product_clicks')->defaultTrue()->end()
                        ->booleanNode('cart')->defaultTrue()->end()
                        ->booleanNode('checkout')->defaultTrue()->end()
                    ->end()
                ->end()

                ->arrayNode('cache_resolvers')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('ttl')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('product_detail_impressions')->defaultValue(3600)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
