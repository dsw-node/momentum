
define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote'
], function ($, _, urlBuilder, customer, quote) {
    'use strict';

    var oscStoreCode;
    if (!_.isUndefined(window.checkoutConfig.bssOsc.giftOptionsConfig)) {
        oscStoreCode = window.checkoutConfig.bssOsc.giftOptionsConfig.storeCode;
    } else {
        oscStoreCode = window.checkoutConfig.storeCode;
    }

    return $.extend(urlBuilder, {
        storeCode: oscStoreCode,

        /**
         * Get update item url for service.
         *
         * @return {String}
         */
        getUpdateDeliverydateUrl: function () {
            var serviceUrl;
            if (!customer.isLoggedIn()) {
                serviceUrl = this.createUrl('/deliverydate/guest-carts/:cartId/update-item', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = this.createUrl('/deliverydate/carts/mine/update-item', {});
            }
            return serviceUrl;
        }
    });
});