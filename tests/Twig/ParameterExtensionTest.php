<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Twig;

use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Twig\ParameterExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class ParameterExtensionTest extends TestCase
{
    private function initTwigForTemplateParameter(string $template): Environment
    {
        $twig = new Environment(
            new ArrayLoader([
                'template' => $template,
            ]),
            [
                'debug' => true,
                'cache' => false,
                'autoescape' => 'html',
                'optimizations' => 0,
            ]
        );

        $twig->addExtension(new ParameterExtension(
            true,
            true,
            true,
            true,
            true,
            []
        ));

        return $twig;
    }

    public function testParameterCreationForArrayResult(): void
    {
        $template = "{{ sylius_gtm_enhanced_ecommerce_parameter('checkout')|json_encode }}";
        $output = $this->initTwigForTemplateParameter($template)->render('template');
        $this->assertEquals('[]', $output);
    }

    public function testParameterCreationForBoolResult(): void
    {
        $template = "{{ sylius_gtm_enhanced_ecommerce_parameter('purchases') }}";
        $output = $this->initTwigForTemplateParameter($template)->render('template');
        $this->assertEquals('1', $output);
    }

    public function testParameterCreationForNotExistingParameter(): void
    {
        $template = "{{ sylius_gtm_enhanced_ecommerce_parameter('not_existing_parameter') }}";
        $output = $this->initTwigForTemplateParameter($template)->render('template');
        $this->assertEmpty($output);
    }
}
