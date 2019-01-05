(function ($) {
  'use strict';

  if (typeof checkoutStepsConfiguration !== "undefined" && checkoutStepsConfiguration.enabled === true) {
    for (var stepId in checkoutStepsConfiguration.steps) {
      if (checkoutStepsConfiguration.steps.hasOwnProperty(stepId)) {
        checkoutStepsConfiguration.steps[stepId].forEach(function (conf) {
          $(conf.selector).on(conf.event, function (e) {
            var option = null;
            if (typeof conf.option !== "undefined" && typeof window[conf.option] === 'function') {
              option = window[conf.option].call(this);
            }
            enhancedEcommerceTrackCheckoutOption(stepId, option);
          });
        });
      }
    }
  }
})(jQuery);

/**
 * This function will be called above if the configuration of
 * %sylius_gtm_enhanced_ecommerce.features.checkout.steps.*.*.option%
 * contains a function accessible into the window var.
 *
 * The 'this' context is the context of
 * %sylius_gtm_enhanced_ecommerce.features.checkout.steps.*.*.selector%
 * So if 'this' represent the current checkout form you can use it
 * to restrict what you want to be stored into GA
 *
 * @returns {string}
 */
function getCheckoutChoiceVal() {
  return $('input[type=radio]:checked', this).val();
}

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
