<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\DependencyInjection;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_gtm_enhanced_ecommerce');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->enumNode('product_identifier')
                    ->info(\sprintf('Choose which product identifier you want to use between %s and %s', ProductDetailInterface::ID_IDENTIFIER, ProductDetailInterface::CODE_IDENTIFIER))
                    ->values(ProductDetailInterface::IDENTIFIERS)
                    ->defaultValue(ProductDetailInterface::ID_IDENTIFIER)
                ->end()
                ->arrayNode('features')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('add_payment_info')->defaultTrue()->end()
                        ->booleanNode('add_shipping_info')->defaultTrue()->end()
                        ->booleanNode('add_to_cart')->defaultTrue()->end()
                        ->booleanNode('begin_checkout')->defaultTrue()->end()
                        ->booleanNode('purchase')->defaultTrue()->end()
                        ->booleanNode('remove_from_cart')->defaultTrue()->end()
                        ->booleanNode('view_cart')->defaultTrue()->end()
                        ->booleanNode('view_item')->defaultTrue()->end()
                        ->booleanNode('view_item_list')->defaultTrue()->end()
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
