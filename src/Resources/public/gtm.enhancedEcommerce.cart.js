function enhancedEcommerceAddToCart(productObj, callbackOrRedirectUrl) {
    var obj = {
        'event': 'addToCart',
        'ecommerce': {
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

    dataLayer.push(obj);
}

function enhancedEcommerceRemoveFromCart(productObj, callbackOrRedirectUrl) {
    var obj = {
        'event': 'removeFromCart',
        'ecommerce': {
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

    dataLayer.push(obj);
}
