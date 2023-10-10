/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
	'jquery',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder'
], function ($,storage, urlBuilder) {
    'use strict';

    return function (deferred, email) {
        return storage.post(
            urlBuilder.createUrl('/../../../agtechmail/index/check', {}),
            JSON.stringify({
                customerEmail: email
            }),
            false
        ).done(function (isEmailAvailable) {
		function checkisaval(){
			if($('.field[name="shippingAddress.extension_attributes.createaccountcust"] input').is(":checked")){
				$('.field[name="shippingAddress.extension_attributes.confpassword"],.field[name="shippingAddress.extension_attributes.password"]').show();
				$("button.action.primary.checkout").attr("disabled","disabled");
			}else{
			$('.field[name="shippingAddress.extension_attributes.confpassword"],.field[name="shippingAddress.extension_attributes.password"]').hide();
			$("button.action.primary.checkout").removeAttr("disabled");
				}
			if($('.field[name="billingAddress.extension_attributes.createaccountcust"] input').is(":checked")){
				$('.field[name="billingAddress.extension_attributes.confpassword"],.field[name="billingAddress.extension_attributes.password"]').show();
				$("button.action.primary.checkout").attr("disabled","disabled");
			}else{
				$('.field[name="billingAddress.extension_attributes.confpassword"],.field[name="billingAddress.extension_attributes.password"]').hide();
				$("button.action.primary.checkout").removeAttr("disabled");
			}

		}

        if (isEmailAvailable == "0") {
          deferred.resolve();
                $('.field[name="shippingAddress.extension_attributes.createaccountcust"]').show();
                $('.field[name="billingAddress.extension_attributes.createaccountcust"]').show();
				$('.field[name="shippingAddress.extension_attributes.createaccountcust"],.field[name="shippingAddress.extension_attributes.confpassword"],.field[name="shippingAddress.extension_attributes.password"]').removeClass("disable");
				$('.field[name="shippingAddress.extension_attributes.createaccountcust"] input,.field[name="shippingAddress.extension_attributes.confpassword"] input,.field[name="shippingAddress.extension_attributes.password"] input').removeAttr("disabled");
				$('.field[name="billingAddress.extension_attributes.createaccountcust"],.field[name="billingAddress.extension_attributes.confpassword"],.field[name="billingAddress.extension_attributes.password"]').removeClass("disable");
				$('.field[name="billingAddress.extension_attributes.createaccountcust"] input,.field[name="billingAddress.extension_attributes.confpassword"] input,.field[name="billingAddress.extension_attributes.password"] input').removeAttr("disabled");
				checkisaval();
            } else if(isEmailAvailable == "1"){
				$('.field[name="shippingAddress.extension_attributes.createaccountcust"] input').prop("checked",false);
				$('.field[name="shippingAddress.extension_attributes.createaccountcust"],.field[name="shippingAddress.extension_attributes.confpassword"],.field[name="shippingAddress.extension_attributes.password"]').addClass("disable");
				$('.field[name="shippingAddress.extension_attributes.createaccountcust"] input,.field[name="shippingAddress.extension_attributes.confpassword"] input,.field[name="shippingAddress.extension_attributes.password"] input').attr("disabled","disabled");
				$('.field[name="billingAddress.extension_attributes.createaccountcust"] input').prop("checked",false);
				$('.field[name="billingAddress.extension_attributes.createaccountcust"],.field[name="billingAddress.extension_attributes.confpassword"],.field[name="billingAddress.extension_attributes.password"]').addClass("disable");
				$('.field[name="billingAddress.extension_attributes.createaccountcust"] input,.field[name="billingAddress.extension_attributes.confpassword"] input,.field[name="billingAddress.extension_attributes.password"] input').attr("disabled","disabled");
                $('.field[name="shippingAddress.extension_attributes.createaccountcust"]').hide();
                $('.field[name="billingAddress.extension_attributes.createaccountcust"]').hide();
				  deferred.resolve();
				  checkisaval();
			}else if(isEmailAvailable == "2"){
                $('.field[name="shippingAddress.extension_attributes.createaccountcust"]').show();
                $('.field[name="billingAddress.extension_attributes.createaccountcust"]').show();
				$('.field[name="shippingAddress.extension_attributes.createaccountcust"] input').prop("checked",true);
				$('.field[name="shippingAddress.extension_attributes.createaccountcust"]').addClass("disable");
				$('.field[name="shippingAddress.extension_attributes.createaccountcust"] input').attr("disabled","disabled");
				$('.field[name="shippingAddress.extension_attributes.confpassword"],.field[name="shippingAddress.extension_attributes.password"]').removeClass("disable");
				$('.field[name="shippingAddress.extension_attributes.confpassword"] input,.field[name="shippingAddress.extension_attributes.password"] input').removeAttr("disabled");
				$('.field[name="billingAddress.extension_attributes.createaccountcust"] input').prop("checked",true);
				$('.field[name="billingAddress.extension_attributes.createaccountcust"]').addClass("disable");
				$('.field[name="billingAddress.extension_attributes.createaccountcust"] input').attr("disabled","disabled");
				$('.field[name="billingAddress.extension_attributes.confpassword"],.field[name="billingAddress.extension_attributes.password"]').removeClass("disable");
				$('.field[name="billingAddress.extension_attributes.confpassword"] input,.field[name="billingAddress.extension_attributes.password"] input').removeAttr("disabled");
				  deferred.resolve();
				  checkisaval();
			}
			else {
                deferred.reject();
            }
        }).fail(function () {
            deferred.reject();
        });
    };
});
