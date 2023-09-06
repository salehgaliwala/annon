(function ($) {
    'use strict';
    $.cubewp_payments = $.cubewp_payments || {};
    $.scripts_params = cubewp_payments_dashboard_scripts_params || {};
    $(document).on('submit', '.cubewp-make-dispute', function (event) {
        event.preventDefault();
        $.cubewp_payments.make_dispute($(this));
    });

    $.cubewp_payments.make_dispute = function ($this) {
        var order_id = $this.attr('data-cubewp-dispute-id'),
            message = $this.find('*[name=cubewp_dispute_message]').val(), submit_btn = $this.find('input[type=submit]');
        if (message === '') {
            cwp_notification_ui('error', $.scripts_params.empty_error_msg);
            return false;
        }
        if (!submit_btn.hasClass('cubewp-processing-ajax')) {
            submit_btn.addClass('cubewp-processing-ajax')
            jQuery.ajax({
                url: $.scripts_params.ajax_url, type: 'POST', data: {
                    'action': 'cubewp_payments_make_dispute',
                    'order_id': order_id,
                    'message': message,
                    'nonce': $.scripts_params.dispute_nonce,
                }, dataType: "json", success: function (response) {
                    cwp_notification_ui(response.type, response.msg);
                    submit_btn.removeClass("cubewp-processing-ajax");
                    if (typeof response.redirectURL != 'undefined' && response.redirectURL !== '') {
                        setTimeout(function () {
                            if (response.redirectURL === 'self') {
                                location.reload();
                            } else {
                                window.location.href = response.redirectURL;
                            }
                        }, 3000);
                    }
                }, error: function () {
                    cwp_notification_ui("error", $.scripts_params.error_msg);
                    submit_btn.removeClass("cubewp-processing-ajax");
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                }
            });
        }
    };
})(jQuery);