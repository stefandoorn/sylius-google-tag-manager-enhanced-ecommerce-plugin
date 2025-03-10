<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\DependencyInjection\SyliusGtmEnhancedEcommerceExtension;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;

final class SyliusGtmEnhancedEcommerceExtensionTest extends AbstractExtensionTestCase
{
    public function testProductIdentifierSetAsCode(): void
    {
        $this->load([
            'product_identifier' => ProductIdentifierHelper::CODE_IDENTIFIER,
        ]);

        $this->assertContainerBuilderHasParameter('sylius_gtm_enhanced_ecommerce.product_identifier', ProductIdentifierHelper::CODE_IDENTIFIER);
    }

    #[DataProvider('dataProviderConfigFeatures')]
    public function testMinimalConfigFeatures(string $feature): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(sprintf('sylius_gtm_enhanced_ecommerce.features.%s', $feature), true);
        $this->assertContainerBuilderHasParameter('sylius_gtm_enhanced_ecommerce.product_identifier', ProductIdentifierHelper::ID_IDENTIFIER);
    }

    #[DataProvider('dataProviderConfigFeatures')]
    public function testDisabledFeatures(string $feature): void
    {
        $this->load([
            'features' => [
                $feature => false,
            ]
        ]);

        $this->assertContainerBuilderHasParameter(sprintf('sylius_gtm_enhanced_ecommerce.features.%s', $feature), false);
    }

    /**
     * @return iterable<array<string>>
     */
    public static function dataProviderConfigFeatures(): iterable
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

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusGtmEnhancedEcommerceExtension(),
        ];
    }
}
