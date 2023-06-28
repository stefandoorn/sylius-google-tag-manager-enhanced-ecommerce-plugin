# Google Tag Manager Enhanced Ecommerce plugin for Sylius eCommerce platform

[![License](https://img.shields.io/packagist/l/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin.svg)](https://packagist.org/packages/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin)
[![Version](https://img.shields.io/packagist/v/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin.svg)](https://packagist.org/packages/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin)
[![Build](https://github.com/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin/actions/workflows/build.yml/badge.svg)](https://github.com/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin/actions/workflows/build.yml)

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-approved-by-sylius.png" width="200"></a></p>

## Installation

### 1. Composer

`composer require stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin`

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

By default all features are enabled.

## Features

References + examples of how to set-up your GTM container: https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm

Supported events:

* `view_item`
* `view_item_list`
* `add_to_cart`
* `remove_from_cart`
* `view_cart`
* `begin_checkout`
* `add_shipping_info`
* `add_payment_info`
* `purchase`

Make sure to check that the required 'sonata_block_render_events' template events are available. Check the
`src/Resources/config/features/*.yml` & `src/Resources/config/services.yml` for the definitions.

This is only to be checked if you've been overriding templates yourselves.

## Cache Resolvers

It might be that your data resolvers give a performance hit, e.g. on the product show page.
There are decorators available that allow you to cache the results for a set time in order. Take a look
at the service definitions in `cache_services.yml` & the default configuration on how to enable this setting.
