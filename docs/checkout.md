# Checkout

## Configuration

You can customize the steps of checkout by creating your own. This is the default one:

```$yaml
# config/packages/sylius_gtm_enhanced_ecommerce.yaml

sylius_gtm_enhanced_ecommerce:
    features:
        checkout:
            steps:
                1:
                    -
                        event: "click"
                        selector: "a[href$='/checkout/']"
                2:
                    -
                        selector: "form[name=sylius_checkout_address]"
                3:
                    -
                        option: "enhancedEcommerceCheckoutGetChoiceValue"
                        selector: "form[name=sylius_checkout_select_shipping]"
                4:
                    -
                        option: "enhancedEcommerceCheckoutGetChoiceValue"
                        selector: "form[name=sylius_checkout_select_payment]"
```

The configuration allow you to add or remove steps, choice a specific js event to listen,
a specific selector to put this event and finally allow you to give a global function which
give you the possibility to add additional information to GA.

## Google Documentation

https://developers.google.com/analytics/devguides/collection/ua/gtm/enhanced-ecommerce#checkout

## Introduction

The checkout steps are registered from the backend code. Based on the controller / method a certain step will be triggered and sent to GTM.

The checkout options are tracked through JS. The payment & shipping forms are being listened for submits, and at that moment the
selected option will be sent to GTM. Keep this in mind when adjusting the checkout forms.

## Configuration in GTM

Note that this configuration has to be added next to the [standard Google Tag Manager / Google Analytics implementation](https://support.google.com/tagmanager/answer/6107124?hl=en).

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
