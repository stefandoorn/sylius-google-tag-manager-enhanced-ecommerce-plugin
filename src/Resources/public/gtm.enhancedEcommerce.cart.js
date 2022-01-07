function enhancedEcommerceAddToCart(productObj, callbackOrRedirectUrl) {
    var obj = {
        'event': 'addToCart',
        'ecommerce': {
            /** global: gtmEnhancedEcommerceCurrencyCode */
            'currencyCode': window.gtmEnhancedEcommerceCurrencyCode || '',
            'add': {
                'products': [productObj]
            }
        }
    };

    if (typeof callbackOrRedirectUrl !== 'undefined') {
        if (typeof callbackOrRedirectUrl === 'string') {
            obj.eventCallback = function () {
                document.location = callbackOrRedirectUrl
            };
        } else if (typeof callbackOrRedirectUrl === 'function') {
            obj.eventCallback = callbackOrRedirectUrl;
        }
    }

    /** global: dataLayer */
    dataLayer.push({ ecommerce: null });
    dataLayer.push(obj);
}

/**
 *
 * @param {Object} productObj
 * @param {function|string} callbackOrRedirectUrl
 */
function enhancedEcommerceRemoveFromCart(productObj, callbackOrRedirectUrl) {
    var obj = {
        'event': 'removeFromCart',
        'ecommerce': {
            /** global: gtmEnhancedEcommerceCurrencyCode */
            'currencyCode': window.gtmEnhancedEcommerceCurrencyCode || '',
            'remove': {
                'products': [productObj]
            }
        }
    };

    if (typeof callbackOrRedirectUrl !== 'undefined') {
        if (typeof callbackOrRedirectUrl === 'string') {
            obj.eventCallback = function () {
                document.location = callbackOrRedirectUrl
            };
        } else if (typeof callbackOrRedirectUrl === 'function') {
            obj.eventCallback = callbackOrRedirectUrl;
        }
    }

    /** global: dataLayer */
    dataLayer.push({ ecommerce: null });
    dataLayer.push(obj);
}
