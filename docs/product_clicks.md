# Product Clicks

## Google Documentation

https://developers.google.com/tag-manager/enhanced-ecommerce#product-clicks

## Introduction

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

## Configuration in GTM

### Trigger

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `productClick`

### Tag

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Product Click`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.
