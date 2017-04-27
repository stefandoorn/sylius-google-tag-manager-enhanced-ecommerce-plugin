<?php

namespace GtmEnhancedEcommercePlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class GtmEnhancedEcommerceExtension
 * @package GtmEnhancedEcommercePlugin\DependencyInjection
 */
final class GtmEnhancedEcommerceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach($config['features'] as $feature => $setting) {
            $parameter = sprintf('gtm_enhanced_ecommerce.features.%s', $feature);

            $container->setParameter($parameter, $setting);
        }
    }
}
