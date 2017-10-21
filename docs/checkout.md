# Checkout

## Google Documentation

https://developers.google.com/tag-manager/enhanced-ecommerce#checkout

## Introduction

The checkout steps are registered from the backend code. Based on the controller / method a certain step will be triggered and sent to GTM.

The checkout options are tracked through JS. The payment & shipping forms are being listened for submits, and at that moment the
selected option will be sent to GTM. Keep this in mind when adjusting the checkout forms.

## Configuration in GTM

Note that this configuration has to be added next to the [standard Google Tag Manager / Google Analytics implementation](https://support.google.com/analytics/answer/6163791).

### Checkout (1/2)

Checkout is used to track the steps customers follow throughout your checkout flow.

#### Trigger (Checkout)

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `checkout`

#### Tag (Checkout)

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Checkout`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.

### Checkout Option (2/2)

Checkout Option is used to track selected shipping method or payment method by the customer.

#### Trigger (Checkout Option)

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `checkoutOption`

#### Tag (Checkout Option)

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Checkout Option`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.