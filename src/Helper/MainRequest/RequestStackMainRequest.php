<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\MainRequest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestStackMainRequest
{
    public static function getMainRequest(RequestStack $requestStack): Request
    {
        return $requestStack->getMainRequest();
    }

    public static function isMainRequest(RequestStack $requestStack): bool
    {
        return $requestStack->getCurrentRequest() === self::getMainRequest($requestStack);
    }
}
