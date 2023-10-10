define([
    'Magento_Ui/js/lib/view/utils/async',
    'uiRegistry',
    'mage/utils/wrapper',
    'Swissup_Checkout/js/scroll-to-error',
    'Swissup_Firecheckout/js/model/layout',
    'underscore',
    'Swissup_Firecheckout/js/utils/form-field/dependency',
    'mage/translate',
    'mage/validation',
    'passwordStrengthIndicator'
], function ($, registry, wrapper, scrollToError, layout, _, dependency, translate) {
    'use strict';


    function validateLoginForm() {
        return $('form[data-role=email-with-possible-login]').validation('isValid');
    }


    var inputs = [
        '[name="shippingAddress.extension_attributes.createaccountcust"]',
        '[name="shippingAddress.extension_attributes.password"]',
        '[name="shippingAddress.extension_attributes.confpassword"]',
        '[name="billingAddress.extension_attributes.createaccountcust"]',
        '[name="billingAddress.extension_attributes.password"]',
        '[name="billingAddress.extension_attributes.confpassword"]'
    ];

    $.async({
        selector: inputs.join(','),
        ctx: $('.checkout-container').get(0)
    }, function (el) {
        setTimeout(function() {
            $(el).detach().appendTo('#customer-email-fieldset');

            if ($(el).find('input').attr('name') === 'extension_attributes[password]') {
                $(el).find('input').attr(
                    'data-validate',
                    JSON.stringify({
                        'validate-customer-password': true
                    })
                );

                $(el).find('input').attr('data-password-min-length', '8');
                $(el).find('input').attr('data-password-min-character-sets', '3');

                $(el).find('input').after(
                    `<div id="password-strength-meter-container" data-role="password-strength-meter" aria-live="polite">
                        <div id="password-strength-meter" class="password-strength-meter">
                            ${translate('Password Strength')}:
                            <span id="password-strength-meter-label" data-role="password-strength-meter-label">
                                ${translate('No Password')}
                             </span>
                        </div>
                    </div>`
                );

                $(el).passwordStrengthIndicator({
                    'passwordSelector': '[name="extension_attributes[password]"]',
                    'passwordStrengthMeterSelector':'[data-role=password-strength-meter]',
                    'passwordStrengthMeterLabelSelector':'[data-role=password-strength-meter-label]',
                    'formSelector': '[data-role=email-with-possible-login]'
                });
            }

            if ($(el).find('input').attr('name') === 'extension_attributes[confpassword]') {
                $(el).find('input').attr(
                    'data-validate',
                    JSON.stringify({
                        'equalTo': '#' + $('[name="extension_attributes[password]"]').attr('id')
                    })
                );
            }

            $('[name="extension_attributes[password]"]').on('change', validateLoginForm);
            $('[name="extension_attributes[confpassword]"]').on('change', validateLoginForm);

            function checkIsAval() {
                if ($('.field[name="shippingAddress.extension_attributes.createaccountcust"] input').is(":checked")) {
                    $('.field[name="shippingAddress.extension_attributes.confpassword"],.field[name="shippingAddress.extension_attributes.password"]').show();
                } else {
                    $('.field[name="shippingAddress.extension_attributes.confpassword"],.field[name="shippingAddress.extension_attributes.password"]').hide();
                }
            }

            $('.field[name="shippingAddress.extension_attributes.createaccountcust"]').click(function () {
                if (!$('.field[name="shippingAddress.extension_attributes.createaccountcust"]').hasClass('disable')) {
                    checkIsAval();
                } else {
                    $('.field[name="shippingAddress.extension_attributes.createaccountcust"] input').prop("checked",true);
                    $('.field[name="shippingAddress.extension_attributes.createaccountcust"] input').prop("disabled", true);
                }
            });

            checkIsAval();

            function checkIsAvalVirtual() {
                if ($('.field[name="billingAddress.extension_attributes.createaccountcust"] input').is(":checked")) {
                    $('.field[name="billingAddress.extension_attributes.confpassword"],.field[name="billingAddress.extension_attributes.password"]').show();
                } else {
                    $('.field[name="billingAddress.extension_attributes.confpassword"],.field[name="billingAddress.extension_attributes.password"]').hide();
                }
            }

            $('.field[name="billingAddress.extension_attributes.createaccountcust"]').click(function () {
                if (!$('.field[name="billingAddress.extension_attributes.createaccountcust"]').hasClass('disable')) {
                    checkIsAvalVirtual();
                } else {
                    $('.field[name="billingAddress.extension_attributes.createaccountcust"] input').prop("checked",true);
                    $('.field[name="billingAddress.extension_attributes.createaccountcust"] input').prop("disabled", true);
                }
            });

            checkIsAvalVirtual();

            if (!layout.isMultistep()) {
                $('body').on('fc:validate', function (e) {
                    e.valid = validateLoginForm();
                });
            } else {
                registry.get('checkout.steps.shipping-step.shippingAddress', function (shipping) {
                    shipping.validateShippingInformation = wrapper.wrap(
                        shipping.validateShippingInformation,
                        function (o) {
                            if (validateLoginForm()) {
                                return scrollToError();
                            }

                            return o();
                        }
                    );
                });
            }
        }, 1000);
    });
});
