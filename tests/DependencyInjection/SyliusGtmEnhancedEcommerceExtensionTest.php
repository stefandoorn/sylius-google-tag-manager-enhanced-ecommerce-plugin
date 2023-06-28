<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\DependencyInjection;

use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\DependencyInjection\SyliusGtmEnhancedEcommerceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class SyliusGtmEnhancedEcommerceExtensionTest extends TestCase
{
    /**
     * @dataProvider dataProviderConfigFeatures
     */
    public function testMinimalConfigFeatures(string $feature): void
    {
        $container = $this->getContainer();
        $extension = new SyliusGtmEnhancedEcommerceExtension();

        $config = [];

        $extension->load(['sylius_gtm_enhanced_ecommerce' => $config], $container);

        $this->assertTrue(
            $container->getParameter(sprintf('sylius_gtm_enhanced_ecommerce.features.%s', $feature))
        );
    }

    public function dataProviderConfigFeatures(): array
    {
        return [
            ['add_payment_info'],
            ['add_shipping_info'],
            ['add_to_cart'],
            ['begin_checkout'],
            ['purchase'],
            ['remove_from_cart'],
            ['view_cart'],
            ['view_item'],
            ['view_item_list'],
        ];
    }

    public function testMinimalConfig(): void
    {
        $container = $this->getContainer();
        $extension = new SyliusGtmEnhancedEcommerceExtension();

        $config = [];

        $extension->load(['sylius_gtm_enhanced_ecommerce' => $config], $container);

        $this->assertFalse(
            $container->hasParameter('sylius_gtm_enhanced_ecommerce.cache_resolver.product_detail_impressions')
        );
    }

    public function testWithCacheResolver(): void
    {
        $container = $this->getContainer();
        $extension = new SyliusGtmEnhancedEcommerceExtension();

        $config = [
            'cache_resolvers' => true,
        ];
        $extension->load(['sylius_gtm_enhanced_ecommerce' => $config], $container);

        $this->assertEquals(
            3600,
            $container->getParameter('sylius_gtm_enhanced_ecommerce.cache_resolver.product_detail_impressions')
        );
    }

    private function getContainer(): ContainerBuilder
    {
        return new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.bundles' => [],
            'kernel.cache_dir' => \sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => __DIR__ . '/../../src/',
        ]));
    }
}
