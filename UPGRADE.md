# Upgrade

Upgrade 1.2.0 -> 2.0.0
----------------------

Implemented the base set-up for GA4. 

Unfortunately, to keep UA & GA4 working at the same time, some breaking changes had to be made. 

Mostly an extra service had to be injected, to verify whether UA or GA4 (or both) are being enabled. This service is `GoogleImplementationEnabled`.

In addition, a new configuration setting is available to enable/disable UA/GA4 as you wish:

```yaml
sylius_gtm_enhanced_ecommerce:
    ua: true
    ga4: true
```

On the JS side, two variables have been added which are regularly used to check whether UA/GA4 is enabled:

```javascript
    var gtmEnhancedEcommerceUAEnabled = '{{ sylius_gtm_enhanced_ecommerce_google_ua }}';
    var gtmEnhancedEcommerceGA4Enabled = '{{ sylius_gtm_enhanced_ecommerce_google_ga4 }}';
```

As well these global Twig variables are available that you see being used above, they are added in a `prepend` method call in the plugin Extension class.

At several locations extra services had to be injected, mainly contexts. Best is to review these if you've been overriding classes.

Upgrade v1.1.0 -> 1.2.0
-----------------------

AddToCart and RemoveFromCart are now tracked automatically from backend without needs of javascript overrides
The services `sylius.google_tag_manager_enhanced_ecommerce.cart.block_event_listener.sylius.shop.product.show.before_add_to_cart` is removed as also the twig templates and javascript associated
The `checkout` event is not fired anymore on the success page because it conflicts with the 'purchase' event.
The constructor of `AddTransaction` and `CheckoutStep` have been modified to pass the new `productIdentifierHelper` service  


Upgrade v1.0.0 -> 1.1.0
-----------------------

As we need to track only the first variant on product show page, ProductDetail twig directory has been refactored
* `ProductDetail/variants.html.twig` renamed to `ProductDetail/variant.html.twig`
* `ProductDetail/_variant.html.twig` is removed

Upgrade v0.7.0 -> v0.8.0
------------------------

Adjusted PSR namespacing to follow Sylius plugin naming conventions.

* Rename namespace from `SyliusGtmEnhancedEcommercePlugin` to `StefanDoorn\SyliusGtmEnhancedEcommercePlugin`

Upgrade v0.6.1 -> v0.7.0
------------------------

The plugin has been renamed to follow Sylius plugin naming conventions.

* Rename namespace references from `GtmEnhancedEcommercePlugin` to `SyliusGtmEnhancedEcommercePlugin`
* Rename configuration reference from `gtm_enhanced_ecommerce` to `sylius_gtm_enhanced_ecommerce`
* Adjust name in `composer.json` from `stefandoorn/google-tag-manager-enhanced-ecommerce-plugin` to `stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin`
* Change parameter references starting with `gtm_enhanced_ecommerce` to start with `sylius_gtm_enhanced_ecommerce`
