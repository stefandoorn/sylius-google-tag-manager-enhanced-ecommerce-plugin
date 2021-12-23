# Google Tag Manager Enhanced Ecommerce plugin for Sylius eCommerce platform

[![License](https://img.shields.io/packagist/l/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin.svg)](https://packagist.org/packages/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin)
[![Version](https://img.shields.io/packagist/v/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin.svg)](https://packagist.org/packages/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin)
[![Build status on Linux](https://img.shields.io/travis/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin/master.svg)](http://travis-ci.org/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin.svg)](https://scrutinizer-ci.com/g/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin/)
[![Coverage Status](https://coveralls.io/repos/github/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin/badge.svg?branch=master)](https://coveralls.io/github/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin?branch=master)

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-approved-by-sylius.png" width="200"></a></p>

## Installation

### 1. Composer

`composer require stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin`

### 2. Follow installation instructions of required sub bundle

https://github.com/stefandoorn/google-tag-manager-plugin

### 3. Load bundle

Add to the bundle list:

```php
new StefanDoorn\SyliusGtmEnhancedEcommercePlugin\SyliusGtmEnhancedEcommercePlugin(),
```

### 4. Adjust configurations

Configure the features you would like to use/not. Find a base configuration reference by running:

```
bin/console config:dump-reference SyliusGtmEnhancedEcommercePlugin
```

### 5. Install assets

```
bin/console assets:install
bin/console sylius:install:assets
bin/console sylius:theme:assets:install
```

By default all features are enabled.

## Features

Each feature has it's own specific documentation.

* [purchases](docs/purchases.md): Send purchases to GTM.
* [product_impressions](docs/product_impressions.md): Send impressions on product listings to GTM
* [product_detail_impressions](docs/product_detail_impressions.md): Send impression on product detail pages to GTM
* [product_clicks](docs/product_clicks.md): Send click events on product links to GTM
* [cart](docs/cart.md): Send add to cart / remove from cart events to GTM
* [checkout](docs/checkout.md): Send checkout steps & selected options to GTM

Make sure to check that the required 'sonata_block_render_events' template events are available. Check the
`src/Resources/config/features/*.yml` & `src/Resources/config/services.yml` for the definitions.

This is only to be checked if you've been overriding templates yourselves.

## Features not supported (yet):

* `promotion_impressions`: https://developers.google.com/tag-manager/enhanced-ecommerce#promo-impressions
* `promotion_clicks`: https://developers.google.com/tag-manager/enhanced-ecommerce#promo-clicks
* `refunds`: https://developers.google.com/tag-manager/enhanced-ecommerce#refunds

## Cache Resolvers

It might be that your data resolvers give a performance hit, e.g. on the product show page.
There are decorators available that allow you to cache the results for a set time in order. Take a look
at the service definitions in `cache_services.yml` & the default configuration on how to enable this setting.
