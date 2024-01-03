<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\MainRequest;

use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class ControllerEventMainRequest
{
    public static function isMainRequest(ControllerEvent $event): bool
    {
        return $event->isMainRequest();
    }
}
