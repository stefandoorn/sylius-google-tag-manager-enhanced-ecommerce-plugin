# Google Tag Manager Enhanced Ecommerce plugin for Sylius eCommerce platform

[![License](https://img.shields.io/packagist/l/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin.svg)](https://packagist.org/packages/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin)
[![Version](https://img.shields.io/packagist/v/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin.svg)](https://packagist.org/packages/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin)
[![Build](https://github.com/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin/actions/workflows/build.yml/badge.svg)](https://github.com/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin/actions/workflows/build.yml)

<p align="center">
    <a href="https://sylius.com/plugins/" target="_blank">
        <img src="https://sylius.com/assets/badge-approved-by-sylius.png" width="200">
    </a>
</p>

## Installation

### 1. Composer

```shell
composer require stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin`
```

### 2. Follow installation instructions of required sub bundle

https://github.com/stefandoorn/google-tag-manager-plugin

### 3. Load bundle

Add to `bundles.php`:

```php
StefanDoorn\SyliusGtmEnhancedEcommercePlugin\SyliusGtmEnhancedEcommercePlugin::class => ['all' => true],
```

### 4. Adjust configurations

Configure the features you would like to use/not. Find a base configuration reference by running:

```
bin/console config:dump-reference SyliusGtmEnhancedEcommercePlugin
```

By default, all features are enabled.

## Features

References + examples of how to set up your GTM container: https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm

Supported events:

Product catalogue actions:
* `view_item` trigger on the event `sylius.product.show`.
* `view_item_list` trigger on the event `sylius.product.index`.

Cart actions:
* `add_to_cart` trigger on the event `Sylius\Component\Order\SyliusCartEvents::CART_ITEM_ADD`.
* `remove_from_cart` trigger on the event `Sylius\Component\Order\SyliusCartEvents::CART_ITEM_REMOVE`.
* `view_cart` trigger on the event `Sylius\Component\Order\SyliusCartEvents::CART_SUMMARY`.

Checkout steps:

> Note: The targeted events `sylius.order.post_*` are triggered when the checkout step form is processed.
> After the event is triggered, a redirection happens, making it impossible to track the event in the same request.
> This plugin is saving the pushed GTM event and display it on the next available page.

* `begin_checkout` trigger on the event `sylius.order.post_address`.
* `add_shipping_info` trigger on the event `sylius.order.post_select_shipping`.
* `add_payment_info` trigger on the event `sylius.order.post_payment`.
* `purchase` trigger just before the `sylius.controller.order::thankYouAction` is called.
