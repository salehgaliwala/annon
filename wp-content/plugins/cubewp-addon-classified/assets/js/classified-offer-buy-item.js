(function ($) {
    'use strict';
    var classified_params = classified_offer_buy_item_scripts_params,
        sticky_sidebar = $('.classified-sticky-sidebar'),
        single_offers = $('.classified-make-an-offer-offers button'),
        offer_input = $('#classified-make-an-offer'),
        offer_form = $('.classified-make-an-offer-field'),
        open_offer = $('.classified-make-an-offer-btn'),
        buy_now = $('.classified-item-buy-now-btn'),
        chat_seller = $('.classified-seller-chat');

    if (chat_seller.length > 0) {
        chat_seller.on('click', function (e) {
            e.preventDefault();
            var $this = $(this),
                target = $this.closest('.classified-single-widget').find('.classified-seller-chat-form');
            if ($this.hasClass('expanded')) {
                $this.removeClass('expanded');
                target.slideUp(function () {
                    sticky_sidebar.trigger('sticky_kit:recalc');
                });
            }else {
                $this.addClass('expanded');
                target.slideDown(function () {
                    sticky_sidebar.trigger('sticky_kit:recalc');
                });
            }
        })
    }

    if (buy_now.length > 0) {
        buy_now.on('click', function (e) {
            e.preventDefault();
            var $this = $(this),
                item_id = $this.attr('data-item-id');
            if ( ! $this.hasClass('cubewp-processing-ajax')) {
                $this.addClass('cubewp-processing-ajax').prop('disabled', 1);
                $.ajax({
                    url: classified_params.classified_ajax_url,
                    type: 'POST',
                    dataType: "json",
                    data: {
                        'action': 'classified_buy_item',
                        'item_id': item_id,
                        'nonce': classified_params.classified_buy_item_nonce
                    },
                    success: function (response) {
                        $this.removeClass('cubewp-processing-ajax').prop('disabled', 0);
                        if (response.type === "success") {
                            cwp_notification_ui('success', response.msg);
                            setTimeout(function () {
                                if (typeof response.redirectURL != "undefined") {
                                    window.location.href = response.redirectURL;
                                }
                            }, 2000)
                        } else {
                            cwp_notification_ui('error', response.msg);
                        }
                    }
                });
            }
        });
    }

    if (open_offer.length > 0) {
        open_offer.on('click', function (e) {
            e.preventDefault();
            if ($(this).hasClass('expanded')) {
                $(this).removeClass('expanded');
                $('.classified-make-an-offer-container').slideUp(function () {
                    sticky_sidebar.trigger('sticky_kit:recalc');
                });
            }else {
                $(this).addClass('expanded');
                $('.classified-make-an-offer-container').slideDown(function () {
                    sticky_sidebar.trigger('sticky_kit:recalc');
                });
            }
        });
    }

    if (offer_form.length > 0) {
        offer_form.on('submit', function (e) {
            e.preventDefault();
            var $this = $(this),
                item_id = $this.attr('data-item-id'),
                submit_btn = $this.find('button[type=submit]'),
                offer_input = $this.find('#classified-make-an-offer');
            if (!submit_btn.hasClass('cubewp-processing-ajax')) {
                offer_input.removeClass('is-invalid');
                var value = offer_input.val() * 1,
                    min = offer_input.attr('min') * 1,
                    max = offer_input.attr('max') * 1;
                if (value > max) {
                    offer_input.addClass('is-invalid');
                    return false;
                }
                if (value < min) {
                    offer_input.addClass('is-invalid');
                    return false;
                }
                submit_btn.addClass('cubewp-processing-ajax').prop('disabled', 1);
                $.ajax({
                    url: classified_params.classified_ajax_url,
                    type: 'POST',
                    dataType: "json",
                    data: {
                        'action': 'classified_make_offer',
                        'offer': offer_input.val(),
                        'item_id': item_id,
                        'nonce': classified_params.classified_make_offer_nonce
                    },
                    success: function (response) {
                        submit_btn.removeClass('cubewp-processing-ajax').prop('disabled', 0);
                        if (response.success === true) {
                            cwp_notification_ui('success', response.data);
                        } else {
                            cwp_notification_ui('error', response.data);
                        }
                        /*
                        setTimeout(function () {
                            location.reload();
                        }, 2000)
                         */
                    }
                });
            }
        });
    }

    if (offer_input.length > 0) {
        offer_input.keypress(function (e) {
            var charCode = (e.which) ? e.which : event.keyCode
            if (String.fromCharCode(charCode).match(/[^0-9.]/g)) return false;
        });
    }

    if (single_offers.length > 0) {
        single_offers.on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.closest('.classified-make-an-offer-offers').find('button').removeAttr('disabled');
            $this.prop('disabled', 1);
            offer_input.val($this.text());
        })
    }

})(jQuery);