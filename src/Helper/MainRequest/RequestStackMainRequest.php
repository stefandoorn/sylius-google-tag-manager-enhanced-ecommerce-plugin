<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\MainRequest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestStackMainRequest
{
    public static function getMainRequest(RequestStack $requestStack): Request
    {
        if (\method_exists($requestStack, 'getMainRequest')) {
            return $requestStack->getMainRequest();
        }

        if (\method_exists($requestStack, 'getMasterRequest')) {
            return $requestStack->getMasterRequest();
        }

        throw new \Exception('Neither "getMainRequest" or "getMasterRequest" exists');
    }
}