/**
 * Mageants Store Pickup Magento2 Extension 
 */  
define(
    [
    'jquery',
    'ko',
    'uiComponent',
    'Mageants_StorePickup/js/action/update-pickup',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote'
    ], function ($, ko, Component,updatePickupAction,customerData,quote) {
    'use strict';

    return Component.extend(
        {
        defaults: {
        template: 'Mageants_StorePickup/pickup-store-block'
        },
        updateDeliverydate: function (item) {
            var pickup=$('#pickup_store').val();
            var pickup_date=$('#pickup_date').val();
            if(pickup!='' &&  pickup_date!='') {
                updatePickupAction(pickup, pickup_date).done(
                    function (response) {
                        var totals = response.totals,
                            data = JSON.parse(this.data),
                            quoteItemData = window.checkoutConfig.quoteItemData;
                        if (!response.status) {
                        } else {
                            customerData.reload('cart');
                        }
                    }
                );
            }
        },
        getDatePickerValue: function ($parent) {
            var pickup_store=window.checkoutConfig.shipping.store_pickup.pickup_store;
            if (typeof(pickup_store) != 'undefined' && pickup_store != null){
                // $("#pickup_store option:contains('"+pickup_store+"')").attr('selected', 'selected');
                $("#pickup_store").val(pickup_store);
            }
            var deliverydate=window.checkoutConfig.shipping.store_pickup.pickup_date;
            if (typeof(deliverydate) != 'undefined' && deliverydate != null  && deliverydate !="0000-00-00" && deliverydate!="0000-00-00 00:00:00"){
               var ret = deliverydate.split(" ");
               var deliverydate = ret[0];
               return deliverydate;
            }

        },
        initialize: function () {
        this._super();
        var enableextension = window.checkoutConfig.shipping.store_pickup.enableextension;
        var disabled = window.checkoutConfig.shipping.store_pickup.disabled;
        var noday = window.checkoutConfig.shipping.store_pickup.noday;
        var hourMin = parseInt(window.checkoutConfig.shipping.store_pickup.hourMin);
        var hourMax = parseInt(window.checkoutConfig.shipping.store_pickup.hourMax);
        var format = 'dd-mm-yy'//window.checkoutConfig.shipping.store_pickup.format;
        var disableDays = window.checkoutConfig.shipping.store_pickup.disableDays;
       
        this.stores = window.checkoutConfig.shipping.store_pickup.stores;
        if($.cookie("selected-val") && $.cookie("selected-val") != null && $.cookie("selected-val") == 'storepickup' && enableextension == 1)
            {
                this.enabled = "display:block";
            }
            else
            {
                this.enabled = "display:none";
            }
            
            if(!format) {
                format = 'yy-mm-dd';
            }
            var disabledDay = disabled.split(",").map(
                function(item) {
                return parseInt(item, 10);
                }
            );
        ko.bindingHandlers.datetimepicker = {
                init: function (element, valueAccessor, allBindingsAccessor) {
                    var $el = $(element);
                    //initialize datetimepicker
                    if(noday) {
                        var options = {
                            minDate: disableDays,
                            dateFormat:format,
                            hourMin: hourMin,
                            hourMax: hourMax
                        };
                    } else {
                        var options = {
                            minDate: disableDays,
                            dateFormat:format,
                            hourMin: hourMin,
                            hourMax: hourMax,
                            beforeShowDay: function(date) {
                                var day = date.getDay();
                                if(disabledDay.indexOf(day) > -1) {
                                    return [false];
                                } else {
                                    return [true];
                                }
                            }
                        };
                    }
                    //$el.datetimepicker(options);//comment by magento360
                    $el.datepicker(options);

                    var writable = valueAccessor();
                    if (!ko.isObservable(writable)) {
                        var propWriters = allBindingsAccessor()._ko_property_writers;
                        if (propWriters && propWriters.datetimepicker) {
                            //writable = propWriters.datetimepicker;//comment by magento360
                            writable = propWriters.datepicker;//comment by magento360
                        } else {
                            return;
                        }
                    }

                    // writable($(element).datetimepicker("getDate"));//commnet by magento 360
                    writable($(element).datepicker("getDate"));//commnet by magento 360
                },
                update: function (element, valueAccessor) {
                    var widget = $(element).data("DateTimePicker");
                    //when the view model is updated, update the widget
                    if (widget) {
                        var date = ko.utils.unwrapObservable(valueAccessor());
                        widget.date(date);
                    }
                }
            };
        return this;
        }
        }
    );
    }
);
