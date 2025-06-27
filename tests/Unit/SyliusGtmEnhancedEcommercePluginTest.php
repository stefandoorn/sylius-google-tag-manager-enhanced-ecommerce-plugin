<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit;

use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\SyliusGtmEnhancedEcommercePlugin;

final class SyliusGtmEnhancedEcommercePluginTest extends TestCase
{
    public function testGetPath(): void
    {
        $bundle = new SyliusGtmEnhancedEcommercePlugin();

        self::assertEquals(dirname(__DIR__, 2), $bundle->getPath());
    }
}
