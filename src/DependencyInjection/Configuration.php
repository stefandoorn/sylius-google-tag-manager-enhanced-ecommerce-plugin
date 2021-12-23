<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): void
    {
        $treeBuilder = new TreeBuilder('sylius_gtm_enhanced_ecommerce');
        $rootNode = $treeBuilder->getRootNode();

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
                        ->arrayNode('checkout')
                            ->canBeDisabled()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('steps')
                                    ->defaultValue([
                                        1 => [
                                            [
                                                'event' => 'click',
                                                'selector' => 'a[href$=\'/checkout/\']',
                                            ],
                                        ],
                                        2 => [
                                            [
                                                'event' => 'submit',
                                                'selector' => 'form[name=sylius_checkout_address]',
                                            ],
                                        ],
                                        3 => [
                                            [
                                                'event' => 'submit',
                                                'selector' => 'form[name=sylius_checkout_select_shipping]',
                                                'option' => 'enhancedEcommerceCheckoutGetChoiceValue',
                                            ],
                                        ],
                                        4 => [
                                            [
                                                'event' => 'submit',
                                                'selector' => 'form[name=sylius_checkout_select_payment]',
                                                'option' => 'enhancedEcommerceCheckoutGetChoiceValue',
                                            ],
                                        ],
                                    ])
                                    ->arrayPrototype()
                                        ->arrayPrototype()
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('event')->defaultValue('submit')->end()
                                                ->scalarNode('selector')->isRequired()->end()
                                                ->scalarNode('option')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
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
