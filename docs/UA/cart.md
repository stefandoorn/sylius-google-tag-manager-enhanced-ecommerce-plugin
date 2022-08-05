# Cart

## Google Documentation

https://developers.google.com/analytics/devguides/collection/ua/gtm/enhanced-ecommerce#cart

## Introduction

### Add to Cart

`Add to Cart` event are registered from the backend code. Based on the default sylius event `sylius.order_item.post_add` and sent to GTM.

### Remove from Cart

`Remove from Cart` event are registered from the backend code. Based on the default sylius event `sylius.order_item.post_remove` and sent to GTM.

## Configuration in GTM

Note that this configuration has to be added next to the [standard Google Tag Manager / Google Analytics implementation](https://support.google.com/tagmanager/answer/6107124?hl=en).

### Add to Cart (1/2)

#### Trigger (Add to Cart)

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `addToCart`

#### Tag (Add to Cart)

Note: this trigger is different than the

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Add to Cart`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.

### Remove from Cart (2/2)

#### Trigger (Remove from Cart)

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `removeFromCart`

#### Tag (Remove from Cart)

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Remove from Cart`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.
