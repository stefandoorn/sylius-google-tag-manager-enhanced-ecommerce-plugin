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

### 5. Install assets

```
bin/console assets:install
bin/console sylius:install:assets
bin/console sylius:theme:assets:install
```

By default all features are enabled.

## Features

* `purchases`: Send purchases to GTM (https://developers.google.com/tag-manager/enhanced-ecommerce#purchases)
* `product_impressions`: Send impressions on product listings to GTM (https://developers.google.com/tag-manager/enhanced-ecommerce#product-impressions)
* `product_detail_impressions`: Send impression on product detail pages to GTM (https://developers.google.com/tag-manager/enhanced-ecommerce#details)
* `product_clicks`: Send click events on product links to GTM (https://developers.google.com/tag-manager/enhanced-ecommerce#product-clicks)
* `cart`: Send add to cart / remove from cart events to GTM (https://developers.google.com/tag-manager/enhanced-ecommerce#cart)
* `checkout`: Send checkout steps & selected options to GTM (https://developers.google.com/tag-manager/enhanced-ecommerce#checkout)

## Feature specifics

### Product Impressions

A 'productListType' variable is used to distinguish certain pages on which the products have been shown. From Sylius RC1 it
defaults to the category name with a string prefix ('Category List'). Feel free to set your own naming in `window.productListType`
after the default is set.

Because Sylius doesn't fire the `sylius.<resource>.index` yet (https://github.com/Sylius/Sylius/issues/7305), make sure to listen in GTM on the event
'productImpressions' to get your data registered (use event action = 'impression' in GTM config).

### Product clicks

To make this work, make sure to install the assets so the JS file will get loaded. Next to this, perform the following steps:

* Add the following REQUIRED data attributes to the links you want to track:
** `data-id`: ID of the product
** `data-name`: name of the product
* Add the following class to the 'a' tags to be tracked:
** Class: `gtm-eh-track-product-click`

Optionally you can add additional data attributes which will get inserted:

* `price`
* `brand`
* `variant`
* `position`

In case you want to set a specific value for 'actionField.list' (see GTM documentation), set `window.actionFieldList` to a string value. Or add
`data-action-field-list` to the 'a' tag with a string value.

If none of the above suits your needs, just use the JS function yourself as defined in `src/Resources/public/gtm.enhancedEcommerce.productClicks.js`.

#### Example of the HTML edits:

Normal link to product page:

```
<a href="{{ path('sylius_shop_product_show', {'slug': product.slug, '_locale': product.translation.locale}) }}" class="header sylius-product-name">{{ product.name }}</a>
```

Becomes:

```
<a data-id="{{ product.id}}" data-name="{{ product.name }}" href="{{ path('sylius_shop_product_show', {'slug': product.slug, '_locale': product.translation.locale}) }}" class="header sylius-product-name gtm-eh-track-product-click">{{ product.name }}</a>
```

### Cart

#### Add to Cart

In case you enable this feature, a JS method called 'enhancedEcommerceAddToCart' will be available. Make sure this gets
fired after adding something to the cart. As it requires changes to templates, we only provide the JS method
and let you handle the templating and triggers, as every webshop probably differs and it's hard to maintain.

The final triggering could be done by overriding the default `sylius-add-to-cart.js` and add in the `onSuccess` handler:

```javascript
enhancedEcommerceAddToCart(gtmAddToCartProductInfo);
```

The `gtmAddToCartProductInfo` is set on the product page and can be extended with additional information that's missing
by default; e.g. the price, variant, dimensions and metrics. The quantity defaults to 1.

#### Remove from Cart

Same goes for remove from cart. As it needs customisation to templates which are not easy to do as every theme is different,
a JS method `enhancedEcommerceRemoveFromCart` is available.

Make sure to inject an object as from the GTM documentation, and you are good to go. You can override the default `sylius-remove-from-cart.js`
and trigger this method in the `onSuccess` method.

### Checkout

The checkout steps are registered from the backend code. Based on the controller / method a certain step will be triggered and sent to GTM.

The checkout options are tracked through JS. The payment & shipping forms are being listened for submits, and at that moment the
selected option will be sent to GTM. Keep this in mind when adjusting the checkout forms.
