(function ( $ ) {
    'use strict';

    var CHECKOUT_STEP_SHIPPING = 3;
    var CHECKOUT_STEP_PAYMENT = 4;

    $.fn.extend({
        enhancedEcommerceCheckoutShipping: function () {
            $(this).on('submit', function () {
                enhancedEcommerceTrackCheckoutOption(CHECKOUT_STEP_SHIPPING, $('input[type=radio]:checked', $(this)).val());
            });
        },

        enhancedEcommerceCheckoutPayment: function () {
            $(this).on('submit', function () {
                enhancedEcommerceTrackCheckoutOption(CHECKOUT_STEP_PAYMENT, $('input[type=radio]:checked', $(this)).val());
            });
        }
    });

    $('form[name=sylius_checkout_select_shipping]').enhancedEcommerceCheckoutShipping();
    $('form[name=sylius_checkout_select_payment]').enhancedEcommerceCheckoutPayment();
})( jQuery );

/**
 * @param {integer} step
 * @param {string} checkoutOption
 */
function enhancedEcommerceTrackCheckoutOption(step, checkoutOption) {
    var obj = {
        'event': 'checkoutOption',
        'ecommerce': {
            'checkout_option': {
                'actionField': {
                    'step': step,
                    'option': checkoutOption
                }
            }
        }
    };

    /** global: dataLayer */
    dataLayer.push(obj);
}
