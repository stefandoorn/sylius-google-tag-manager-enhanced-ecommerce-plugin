<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ParameterExtension extends AbstractExtension
{
    private array $parameters;

    public function __construct(
        bool $purchases,
        bool $product_impressions,
        bool $product_detail_impressions,
        bool $product_clicks,
        bool $cart,
        array $checkout
    ) {
        $this->parameters = [
            'purchases' => $purchases,
            'product_impressions' => $product_impressions,
            'product_detail_impressions' => $product_detail_impressions,
            'product_clicks' => $product_clicks,
            'cart' => $cart,
            'checkout' => $checkout
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_gtm_enhanced_ecommerce_parameter', [$this, 'getParameter']),
        ];
    }

    /**
     * @return bool|array|null
     */
    public function getParameter(string $name)
    {
        return $this->hasParameter($name) ? $this->parameters[$name] : null;
    }

    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }
}
