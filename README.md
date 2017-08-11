# Google Tag Manager Enhanced Ecommerce plugin for Sylius 

[![License](https://img.shields.io/packagist/l/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin.svg)](https://packagist.org/packages/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin) [![Version](https://img.shields.io/packagist/v/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin.svg)](https://packagist.org/packages/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin) [![Build status on Linux](https://img.shields.io/travis/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin/master.svg)](http://travis-ci.org/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin) [![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin.svg)](https://scrutinizer-ci.com/g/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin/) [![Code Coverage](https://scrutinizer-ci.com/g/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/stefandoorn/google-tag-manager-enhanced-ecommerce-plugin/?branch=master)

Google Tag Manager Enhanced Ecommerce plugin for Sylius eCommerce Platform

## Installation

### 1. Composer

`composer require stefandoorn/google-tag-manager-enhanced-ecommerce-plugin`

### 2. Follow installation instructions of required sub bundle

https://github.com/stefandoorn/google-tag-manager-plugin

### 3. Load bundle

Add to the bundle list in `app/AppKernel.php`:

```php
new GtmEnhancedEcommercePlugin\GtmEnhancedEcommercePlugin(),
```

### 4. Adjust configurations

Configure the features you would like to use/not. Find a base configuration reference by running:

```
bin/console config:dump-reference GtmEnhancedEcommercePlugin
```

By default all features are enabled.

## Features

* `purchases`: Send purchases to GTM (https://developers.google.com/tag-manager/enhanced-ecommerce#purchases)
* `product-detail-impressions`: Send impression on product detail pages to GTM (https://developers.google.com/tag-manager/enhanced-ecommerce#details)
