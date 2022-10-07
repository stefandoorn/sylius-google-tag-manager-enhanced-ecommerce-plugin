(function ( $ ) {
    'use strict';

    $.fn.extend({
        enhancedEcommerceProductClickTrigger: function () {
            $(this).on('click', function (event) {
                var obj = {
                    'name': $(this).attr('data-name'),
                    'id': $(this).attr('data-id')
                };

                var price = $(this).attr('data-price');
                if (typeof price !== 'undefined') {
                    obj.price = price;
                }

                var brand = $(this).attr('data-brand');
                if (typeof brand !== 'undefined') {
                    obj.brand = brand;
                }

                var category = $(this).attr('data-category');
                if (typeof category !== 'undefined') {
                    obj.category = category;
                }

                var variant = $(this).attr('data-variant');
                if (typeof variant !== 'undefined') {
                    obj.variant = variant;
                }

                var position = $(this).attr('data-position');
                if (typeof position !== 'undefined') {
                    obj.position = position;
                }

                enhancedEcommerceTrackProductClick(obj, $(this).attr('href'), $(this).attr('data-action-field-list'));
            });
        }
    });

    $('a.gtm-eh-track-product-click').enhancedEcommerceProductClickTrigger();
})( jQuery );

/**
 * Call this function when a user clicks on a product link. This function uses the event
 * callback datalayer variable to handle navigation after the ecommerce data has been sent
 * to Google Analytics.
 * @param {Object} productObj An object representing a product.
 * @param {string} clickedUrl URL clicked, browser need to direct there
 * @param {string} actionFieldList Optional to use as action field list
 */
function enhancedEcommerceTrackProductClick(productObj, clickedUrl, actionFieldList) {
    if (typeof actionFieldList === 'undefined') {
        actionFieldList = window.actionFieldList || (window.actionFieldList = 'Product List');
    }

    if (typeof gtmEnhancedEcommerceUAEnabled !== 'undefined' && gtmEnhancedEcommerceUAEnabled) {
        var obj = {
            'event': 'productClick',
            'currencyCode': window.gtmEnhancedEcommerceCurrencyCode || '',
            'ecommerce': {
                'click': {
                    'products': [productObj]
                }
            },
            'eventCallback': function () {
                document.location = clickedUrl
            }
        };

        if (actionFieldList) {
            obj.ecommerce.click.actionField = {'list': actionFieldList}; // Optional list property.
        }

        /** global: dataLayer */
        dataLayer.push({ecommerce: null});
        dataLayer.push(obj);
    }
}
