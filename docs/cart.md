# Cart

## Google Documentation

https://developers.google.com/tag-manager/enhanced-ecommerce#cart

## Introduction

### Add to Cart

In case you enable this feature, a JS method called 'enhancedEcommerceAddToCart' will be available on the product show page. Make sure this gets
fired after adding something to the cart. As it requires changes to templates, we only provide the JS method
and let you handle the templating and triggers, as every webshop probably differs and it's hard to maintain.

The final triggering could be done by overriding the default `sylius-add-to-cart.js` and add in the `onSuccess` handler:

```javascript
enhancedEcommerceAddToCart(gtmAddToCartProductInfo);
```

The `gtmAddToCartProductInfo` is set on the product page and can be extended with additional information that's missing
by default; e.g. the price, variant, dimensions and metrics. The quantity defaults to 1.

### Remove from Cart

Same goes for remove from cart. As it needs customisation to templates which are not easy to do as every theme is different,
a JS method `enhancedEcommerceRemoveFromCart` is available.

Make sure to inject an object as from the GTM documentation, and you are good to go. You can override the default `sylius-remove-from-cart.js`
and trigger this method in the `onSuccess` method.
## Configuration in GTM

### Trigger (Add to Cart)

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `addToCart`

### Tag (Add to Cart)

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Add to Cart`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.

### Trigger (Remove from Cart)

Add a trigger:

* Type: Custom Event
* Activate on: Event equals `removeFromCart`

### Tag (Remove from Cart)

Add a tag:

* Type: Universal Analytics
* Trackingtype: Event
* Category: `Ecommerce`
* Action: `Remove from Cart`
* Enable Enhanced Ecommerce
* Use dataLayer
* Trigger on: see trigger above.