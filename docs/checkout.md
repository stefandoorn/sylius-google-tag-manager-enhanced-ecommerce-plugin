# Checkout

## Google Documentation

https://developers.google.com/tag-manager/enhanced-ecommerce#checkout

## Introduction

The checkout steps are registered from the backend code. Based on the controller / method a certain step will be triggered and sent to GTM.

The checkout options are tracked through JS. The payment & shipping forms are being listened for submits, and at that moment the
selected option will be sent to GTM. Keep this in mind when adjusting the checkout forms.

## Configuration in GTM

### Trigger (Checkout)

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `checkout`

### Tag (Checkout)

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Checkout`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.

### Trigger (Checkout Option)

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `checkoutOption`

### Tag (Checkout Option)

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Checkout Option`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.