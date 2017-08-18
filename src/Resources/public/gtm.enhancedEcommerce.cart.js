function enhancedEcommerceAddToCart(productObj, redirectUrl) {
    var obj = {
        'event': 'addToCart',
        'ecommerce': {
            'add': {
                'products': [productObj]
            }
        }
    };

    if (typeof redirectUrl !== 'undefined') {
        obj.eventCallback = function () {
            document.location = redirectUrl
        };
    }

    dataLayer.push(obj);
}

function enhancedEcommerceRemoveFromCart(productObj, redirectUrl) {
    var obj = {
        'event': 'removeFromCart',
        'ecommerce': {
            'remove': {
                'products': [productObj]
            }
        }
    };

    if (typeof redirectUrl !== 'undefined') {
        obj.eventCallback = function () {
            document.location = redirectUrl
        };
    }

    dataLayer.push(obj);
}
