define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_CheckoutAgreements/js/model/agreements-assigner',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/url-builder',
    'mage/url',
    'Magento_Checkout/js/model/error-processor',
    'uiRegistry'
], function (
    $, 
    wrapper, 
    agreementsAssigner,
    quote,
    customer,
    urlBuilder, 
    urlFormatter, 
    errorProcessor,
    registry
) {
    'use strict';

    return function (placeOrderAction) {

        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            agreementsAssigner(paymentData);
            var isCustomer = customer.isLoggedIn();
            var quoteId = quote.getQuoteId();

            var url = urlFormatter.build('checkauto/quote/save');

            var createaccountcust = $('.field[name="billingAddress.extension_attributes.createaccountcust"] input').is(":checked");
            var password = $('[name="extension_attributes[password]"]').val();
            var confpassword = $('[name="extension_attributes[confpassword]"]').val();
            
            if(password != confpassword){
                alert("please enter correct password");
                return false;
            }
            if (createaccountcust || password || confpassword) {
                var payload = {
                    'cartId': quoteId,
                    'createaccountcust': createaccountcust,
                    'password': password,
                    'confpassword':confpassword,
                    'is_customer': isCustomer
                };
                return true;

                if (!payload.createaccountcust && !payload.password && !payload.confpassword) {
                    return true;
                }

                var result = true;

                $.ajax({
                    url: url,
                    data: payload,
                    dataType: 'text',
                    type: 'POST',
                }).done(
                    function (response) {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );
            }
            return originalAction(paymentData, messageContainer);
        });
    };
});