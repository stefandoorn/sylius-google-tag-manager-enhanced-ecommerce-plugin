<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ParameterExtension extends AbstractExtension
{
    private array $parameters;

    public function __construct(
        bool $add_payment_info,
        bool $add_shipping_info,
        bool $add_to_cart,
        bool $begin_checkout,
        bool $purchase,
        bool $remove_from_cart,
        bool $view_cart,
        bool $view_item,
        bool $view_item_list
    ) {
        $this->parameters = [
            'add_payment_info' => $add_payment_info,
            'add_shipping_info' => $add_shipping_info,
            'add_to_cart' => $add_to_cart,
            'begin_checkout' => $begin_checkout,
            'purchase' => $purchase,
            'remove_from_cart' => $remove_from_cart,
            'view_cart' => $view_cart,
            'view_item' => $view_item,
            'view_item_list' => $view_item_list,
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_gtm_enhanced_ecommerce_parameter', [$this, 'getParameter']),
        ];
    }

    public function getParameter(string $name): ?bool
    {
        return $this->hasParameter($name) ? $this->parameters[$name] : null;
    }

    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }
}
