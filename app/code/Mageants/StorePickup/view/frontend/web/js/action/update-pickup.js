define([
    'underscore',
    'Mageants_StorePickup/js/model/url-builder',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/shipping-service',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/payment-service',
    'Mageants_StorePickup/js/model/update-pickup-service',
    'Magento_Ui/js/model/messageList'
],function(
    _,
    urlBuilder,
    storage,
    errorProcessor,
    shippingService,
    url,
    quote,
    methodConverter,
    paymentServiceDefault,
    updateItemService,
    globalMessageList
) {
    'use strict';
    return function (pickup,pickup_date) {
        var serviceUrl = urlBuilder.getUpdateDeliverydateUrl(),
            address = quote.shippingAddress();
        shippingService.isLoading(true);
        return storage.post(
            serviceUrl,
            JSON.stringify({
                address: {
                    'region_id': address.regionId,
                    'region': address.region,
                    'country_id': address.countryId,
                    'postcode': address.postcode
                },
                pickup: parseInt(pickup),
                pickup_date: pickup_date
            })
        ).done(function (response) {
            if (response.has_error && response.status) {
                globalMessageList.addSuccessMessage(response);
                window.location.replace(url.build('checkout/cart/'));
            } else {
                if (response.status) {
                    globalMessageList.addSuccessMessage(response);
                    updateItemService.hasUpdateResult(true);
                    updateItemService.hasUpdateResult(false);
                } else {
                    globalMessageList.addErrorMessage(response);
                }
            }
        }).fail(function (response) {
            errorProcessor.process(response);
        }).always(function () {
            shippingService.isLoading(false);
        });
    };
});
