<?php

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\SyliusGtmEnhancedEcommercePlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusGtmEnhancedEcommercePluginTest extends TestCase
{
    public function test__construct(): void
    {
        $bundle = new SyliusGtmEnhancedEcommercePlugin();

        self::assertInstanceOf(Bundle::class, $bundle);
    }

    public function testGetPath(): void
    {
        $bundle = new SyliusGtmEnhancedEcommercePlugin();

        self::assertEquals(dirname(__DIR__, 2), $bundle->getPath());
    }
}
