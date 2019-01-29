/**
 * Mageants Store Pickup Magento2 Extension 
 */ 
/*global define,alert*/

var objAddress;

define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/select-billing-address'
    ],
    function (
        $,
        ko,
        quote,
        resourceUrlManager,
        storage,
        paymentService,
        methodConverter,
        errorProcessor,
        fullScreenLoader,
        selectBillingAddressAction
    ) {
        'use strict';
        var stores = window.checkoutConfig.shipping.store_pickup.pickupaddress;
        return {
            saveShippingInformation: function () {
                var payload;
                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }
                var extensionAttributes = "";
                 if (jQuery.cookie("pickupAddress")) 
                    {
                        objAddress = jQuery.parseJSON(jQuery.cookie("pickupAddress"));
                    }
                else
                    {
                        objAddress = quote.shippingAddress();
                    }
                if(quote.shippingMethod().carrier_code == 'storepickup')
                {
                    if (jQuery.cookie("pickupAddress") == null)
                     {
                        jQuery.cookie("pickupAddress",  JSON.stringify(quote.shippingAddress()), { path: '/' });
                     }
                    if (jQuery.cookie("pickupAddress")) 
                    {
                        objAddress = jQuery.parseJSON(jQuery.cookie("pickupAddress"));
                    }
                    var str = $('[name="pickup_store"]').val();
                    var storeName = stores[str].name.split(' ');
                    var streetAddress = [stores[str].address];
                    var firstname = '';
                    var lastname = '';
                    if (storeName.length == 1)
                    {
                        firstname = 'Store';
                        lastname = storeName[0];
                    }
                    else if (storeName.length > 2) 
                    {
                        firstname = storeName[0];
                        for (var i = 1; i < storeName.length; i++) 
                        {
                            lastname = lastname+" "+storeName[i];
                        }
                    }
                    else{
                        firstname = storeName[0];
                        lastname = storeName[1]; 
                    }
                    
                    quote.shippingAddress().firstname = firstname;
                    quote.shippingAddress().lastname = lastname;
                    quote.shippingAddress().street = streetAddress;
                    quote.shippingAddress().city = stores[str].city;
                    quote.shippingAddress().postcode = jQuery.trim(stores[str].postcode);
                    quote.shippingAddress().region = jQuery.trim(stores[str].region);
                    quote.shippingAddress().countryId = jQuery.trim(stores[str].country).toUpperCase();
                    quote.shippingAddress().telephone = jQuery.trim(stores[str].phone);
                    var extensionAttributes = {
                        pickup_date: $('[name="pickup_date"]').val(),
                        pickup_store_val: $('[name="pickup_store"]').val(),
                        pickup_store: $('[name="pickup_store"] option:selected').text()
                    }
                } 
                else{
                    if(objAddress != ""){
                        quote.shippingAddress().firstname = objAddress.firstname;
                        quote.shippingAddress().lastname = objAddress.lastname;
                        quote.shippingAddress().street = objAddress.street;
                        quote.shippingAddress().city = objAddress.city;
                        quote.shippingAddress().telephone = objAddress.telephone;
                        quote.shippingAddress().postcode = objAddress.postcode;
                        quote.shippingAddress().region = objAddress.region;
                        quote.shippingAddress().countryId = objAddress.countryId;
                    }
                }

                if (extensionAttributes == "") {
                    payload = {
                        addressInformation: {
                            shipping_address: quote.shippingAddress(),
                            billing_address: quote.billingAddress(),
                            shipping_method_code: quote.shippingMethod().method_code,
                            shipping_carrier_code: quote.shippingMethod().carrier_code
                        }
                    };                    
                }
                else{
                    payload = {
                        addressInformation: {
                            shipping_address: quote.shippingAddress(),
                            billing_address: quote.billingAddress(),
                            shipping_method_code: quote.shippingMethod().method_code,
                            shipping_carrier_code: quote.shippingMethod().carrier_code,
                            extension_attributes: extensionAttributes
                        }
                    };                    
                }

                fullScreenLoader.startLoader();

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        console.log(response);
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
                        
                        console.log(response);
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );
            }
        };
    }
);
