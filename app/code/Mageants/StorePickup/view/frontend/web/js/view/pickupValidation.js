define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Mageants_StorePickup/js/model/pickupValidation'
    ],
    function (Component, additionalValidators, pickupValidation) {
        'use strict';
        additionalValidators.registerValidator(pickupValidation);
        return Component.extend({});
    }
);