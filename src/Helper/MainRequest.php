<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper;

use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class MainRequest
{
    public static function isMainRequest(ControllerEvent $event): bool
    {
        if (\method_exists($event, 'isMainRequest')) {
            return $event->isMainRequest();
        }

        if (\method_exists($event, 'isMasterRequest')) {
            return $event->isMasterRequest();
        }

        throw new \Exception('Neither "isMainRequest" or "isMasterRequest" exists');
    }
}
