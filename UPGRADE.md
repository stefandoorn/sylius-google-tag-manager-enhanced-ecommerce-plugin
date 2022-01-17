# Upgrade

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
