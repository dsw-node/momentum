define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magecomp_Deliverydate/deliverydate'
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

        getDateTitle: function () {
            return window.checkoutConfig.shipping.deliverydate.datelabel;
        },

        isCommentshow: function () {
            if(window.checkoutConfig.shipping.deliverydate.commentshow == 0)
            {
                return 'none';
            }
            else
            {
                return 'block';
            }
        },

        getCommentTitle: function () {
            return window.checkoutConfig.shipping.deliverydate.commentlabel;
        },

        calenderclick: function () {
            document.getElementById('deliverydate').focus();
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
            if(spcdate)
            {
                spcdatearr = spcdate.split(",");
            }

            var disabledDay = disabled.split(",").map(function (item) {
                return parseInt(item, 10);
            });

            ko.bindingHandlers.datetimepicker = {
                init: function (element, valueAccessor, allBindingsAccessor) {
                    var $el = $(element);
                    //initialize datetimepicker
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
                    }
                }
            };

            return this;
        }
    });
});
