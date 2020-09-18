'use strict';
(function ($) {

  if (typeof checkoutStepsConfiguration === "undefined") return;
  if (typeof checkoutStepsConfiguration !== "object") return;
  if (!checkoutStepsConfiguration.hasOwnProperty('enabled')) return;
  if (checkoutStepsConfiguration.enabled === false) return;
  if (!checkoutStepsConfiguration.hasOwnProperty('steps')) return;

  for (var stepId in checkoutStepsConfiguration.steps) {
    if (!checkoutStepsConfiguration.steps.hasOwnProperty(stepId)) continue;

    checkoutStepsConfiguration.steps[stepId].forEach(function BindStep(conf) {
      conf.stepId = stepId;
      $(conf.selector).on(conf.event, function () {
        var option = null;

        if (
          typeof conf.option !== "undefined"
          && typeof window[conf.option] === 'function'
        ) {
          option = window[conf.option].call(this);
        }

        enhancedEcommerceTrackCheckoutOption(conf.stepId, option);
      });
    });
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
function enhancedEcommerceCheckoutGetChoiceValue() {
  return $('input[type=radio]:checked', this).val();
}

/**
 * @param {integer} stepId
 * @param {string} checkoutOption
 */
function enhancedEcommerceTrackCheckoutOption(stepId, checkoutOption) {
  var obj = {
    'event': 'checkoutOption',
    'ecommerce': {
      'checkout_option': {
        'actionField': {
          'step': stepId,
          'option': checkoutOption
        }
      }
    }
  };

  /** global: dataLayer */
  dataLayer.push(obj);
}
