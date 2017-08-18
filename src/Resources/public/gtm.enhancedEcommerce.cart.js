function enhancedEcommerceAddToCart(productObj) {
    var obj = {
        'event': 'addToCart',
        'ecommerce': {
            'add': {
                'products': [productObj]
            }
        }
    };

    dataLayer.push(obj);
}
