/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'jquery',
    'Magento_Checkout/js/view/summary/item/details',
    'mage/translate',
    'ko',
    'underscore',
    'Magento_Customer/js/customer-data',
    'Bss_OneStepCheckout/js/action/update-item',
    'Magento_Checkout/js/model/quote'
], function ($, Component, $t, ko, _, customerData, updateItemAction, quote) {
    'use strict';
    var quoteItemData = window.checkoutConfig.quoteItemData;
    return Component.extend({
        defaults: {
            template: 'Bss_OneStepCheckout/summary/item/details'
        },
        quoteItemData: quoteItemData,
        titleQtyBox: ko.observable($t('Qty')),

        /**
         * @param {Object} item
         * @returns void
         */
        updateQty: function (item) {
            updateItemAction(item).done(
                function (response) {
                    var totals = response.totals,
                        data = JSON.parse(this.data),
                        itemId = data.itemId,
                        itemsOrigin = [],
                        quoteItemData = window.checkoutConfig.quoteItemData;
                    if (!response.status) {
                        var originItem = _.find(quoteItemData, function (index) {
                            return index.item_id == itemId;
                        });
                        $.each(totals.items, function(index) {
                            if (this.item_id == originItem.item_id) {
                                this.qty = originItem.qty;
                            }
                            itemsOrigin[index] = this;
                        });
                        totals.items = itemsOrigin;
                    } else {
                        customerData.reload('cart');
                    }
                    quote.setTotals(totals);
                }
            );
        },

        /**
         * @param {*} itemId
         * @returns {String}
         */
        getProductUrl: function (itemId) {
            if (_.isUndefined(customerData.get('cart')())) {
                customerData.reload('cart');
            }
            var productUrl = 'javascript:void(0)',
                cartData = customerData.get('cart')(),
                items = cartData.items;

            var item = _.find(items, function (item) {
                return item.item_id == itemId;
            });

            if (!_.isUndefined(item) && item.product_has_url) {
                productUrl = item.product_url;
            }
            return productUrl;
        },
        isDateshow: function () {
            if(window.checkoutConfig.shipping.deliverydate.isshow == 0)
            {
                return 'none';
            }
            else
            {
                return 'block';
            }
        },
        getDatePickerId: function (quoteItem) {
            var item = this.getItem(quoteItem.item_id);
            return "item_date_picker_"+item.product_id;
        },
        getDateTitle: function () {
            return window.checkoutConfig.shipping.deliverydate.datelabel;
        },
        initialize: function () {
            this._super();
            var disabled = window.checkoutConfig.shipping.deliverydate.disabled;
            var noday = window.checkoutConfig.shipping.deliverydate.noday;
            var hourstart = parseInt(window.checkoutConfig.shipping.deliverydate.hourstart);
            var hourend = parseInt(window.checkoutConfig.shipping.deliverydate.hourend);
            var issameday = parseInt(window.checkoutConfig.shipping.deliverydate.issameday);
            var spcdate = window.checkoutConfig.shipping.deliverydate.spcdate;
            var displaytype = window.checkoutConfig.shipping.deliverydate.displaytype;
            var format = window.checkoutConfig.shipping.deliverydate.dateformatetype;
            var spcdatearr = [];
            if(spcdate){
                spcdatearr = spcdate.split(",");
            }
            var disabledDay = disabled.split(",").map(function (item) {
                return parseInt(item, 10);
            });

            ko.bindingHandlers.datetimepicker = {
                init: function (element, valueAccessor, allBindingsAccessor) {
                    var $el = $(element);
                    if(noday)
                    {
                        var options = {
                            minDate: issameday,
                            dateFormat:format,
                            hourMin: hourstart,
                            hourMax: hourend,
                            beforeShowDay: function (date) {
                                // Check Date is Special Date Or Not
                                var cmonth = parseInt(date.getMonth() + 1);
                                cmonth = String("0" + cmonth).slice(-2);
                                var dayofdate = String("0" + date.getDate()).slice(-2);
                                var sdate = date.getFullYear()+'-'+cmonth+'-'+dayofdate;
                                if(spcdatearr.indexOf(sdate) > -1)
                                {
                                    return [false];
                                }

                                return [true];
                            }
                        };
                    }
                    else
                    {
                        var options = {
                            minDate: issameday,
                            dateFormat:format,
                            hourMin: hourstart,
                            hourMax: hourend,
                            beforeShowDay: function (date) {
                                // Check Date is Special Date Or Not
                                var cmonth = parseInt(date.getMonth() + 1);
                                cmonth = String("0" + cmonth).slice(-2);
                                var dayofdate = String("0" + date.getDate()).slice(-2);
                                var sdate = date.getFullYear()+'-'+cmonth+'-'+dayofdate;
                                if(spcdatearr.indexOf(sdate) > -1)
                                {
                                    return [false];
                                }

                                // Disable Day Of Week
                                var day = date.getDay();
                                if(disabledDay.indexOf(day) > -1)
                                {
                                    return [false];
                                } else {
                                    return [true];
                                }
                            }
                        };
                    }
                    if(displaytype == 1) {
                        $el.datetimepicker(options);
                    }else{
                        $el.datepicker(options);
                    }

                    var writable = valueAccessor();
                    if (!ko.isObservable(writable)) {
                        var propWriters = allBindingsAccessor()._ko_property_writers;
                        if (propWriters && propWriters.datetimepicker) {

                            if(displaytype == 1) {
                                writable = propWriters.datetimepicker;
                            }else{
                                writable = propWriters.datepicker;
                            }
                        } else {
                            return;
                        }
                    }
                    if(displaytype == 1) {
                        writable($(element).datetimepicker("getDate"));
                    }else{
                        writable($(element).datepicker("getDate"));
                    }
                },
                update: function (element, valueAccessor) {
                    var widget = $(element).data("DateTimePicker");
                    //when the view model is updated, update the widget
                    if (widget) {
                        var date = ko.utils.unwrapObservable(valueAccessor());
                        widget.date(date);
                        $elem = $(element);
                        current = $elem.datepicker("getDate");
                        if (value - current !== 0) {
                            $elem.datepicker("setDate", value);
                        }
                    }
                }
            };

            return this;
        },
        getItem: function(item_id) {
            var itemElement = null;
            _.each(this.quoteItemData, function(element, index) {
                if (element.item_id == item_id) {
                    itemElement = element;
                }
            });
            return itemElement;
        },
        calenderclick: function (quoteItem) {
            document.getElementsByClassName('custom_deliverydate')[0].focus();
        }
    });
});
