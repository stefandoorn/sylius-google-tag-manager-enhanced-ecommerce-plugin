<?php

use GtmPlugin\GtmPlugin;
use Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle;
use Sylius\Bundle\CoreBundle\Application\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

final class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        return array_merge(parent::registerBundles(), [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(),
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle(),

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(), // Required by SyliusApiBundle
            new SyliusAdminApiBundle(),

            new Xynnn\GoogleTagManagerBundle\GoogleTagManagerBundle(),
            new GtmPlugin(),
            new StefanDoorn\SyliusGtmEnhancedEcommercePlugin\SyliusGtmEnhancedEcommercePlugin(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getRootDir() . '/config/config.yml');
    }
}
