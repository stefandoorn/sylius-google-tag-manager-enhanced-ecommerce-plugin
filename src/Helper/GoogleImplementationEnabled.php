<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper;

final class GoogleImplementationEnabled
{
    private bool $uaEnabled;

    private bool $ga4Enabled;

    public function __construct(
        bool $uaEnabled,
        bool $ga4Enabled
    ) {
        $this->uaEnabled = $uaEnabled;
        $this->ga4Enabled = $ga4Enabled;
    }

    public function isUAEnabled(): bool
    {
        return $this->uaEnabled;
    }

    public function isGA4Enabled(): bool
    {
        return $this->ga4Enabled;
    }
}
