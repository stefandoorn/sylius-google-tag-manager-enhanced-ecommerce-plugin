# Product Impressions

## Google Documentation

https://developers.google.com/tag-manager/enhanced-ecommerce#product-impressions

## Introduction

A 'productListType' variable is used to distinguish certain pages on which the products have been shown. From Sylius RC1 it
defaults to the category name with a string prefix ('Category List'). Feel free to set your own naming in `window.productListType`
after the default is set.

Because Sylius doesn't fire the `sylius.<resource>.index` yet (https://github.com/Sylius/Sylius/issues/7305), make sure to listen in GTM on the event
'productImpressions' to get your data registered (use event action = 'impression' in GTM config).

## Configuration in GTM

Note that this configuration has to be added next to the [standard Google Tag Manager / Google Analytics implementation](https://support.google.com/analytics/answer/6163791).

### Trigger

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `productImpressions`

### Tag

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Impression`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.
