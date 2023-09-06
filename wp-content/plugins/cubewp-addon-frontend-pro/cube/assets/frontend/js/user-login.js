(function ($) {
    'use strict';
    $.cubewp_user_login = $.cubewp_user_login || {};

    $(document).on('click', '.cubewp-forget-password-form-trigger,.cubewp-login-form-trigger' , function (event) {
        event.preventDefault();
        $.cubewp_user_login.toggle_form(this);
    });

    $(document).on('submit', '#login-form', function (event) {
        event.preventDefault();
        $.cubewp_user_login.login_form(this);
    });

    $(document).on('submit', '#forget-password-form', function (event) {
        event.preventDefault();
        $.cubewp_user_login.forget_form(this);
    });

    $.cubewp_user_login.toggle_form = function (t) {
        var $this = jQuery(t);
        if ($this.hasClass("cubewp-forget-password-form-trigger")) {
            jQuery('#login-form').hide();
            jQuery('#forget-password-form').show();
        }else if ($this.hasClass("cubewp-login-form-trigger")) {
            jQuery('#login-form').show();
            jQuery('#forget-password-form').hide();
        }
    };

    $.cubewp_user_login.login_form = function (t) {
        var $this = jQuery(t),
            is_valid = cubewp_frontend_form_validation($this);
        if (is_valid === true) {
            $this.find("input[type=submit]").addClass("cubewp-processing-ajax");
            var formData = new FormData($this[0]);
            formData.append('action', 'cubewp_ajax_login');
            jQuery.ajax({
                url: cwp_user_login_params.ajax_url,
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (response) {
                    cwp_notification_ui(response.type, response.msg);
                    $this.find("input[type=submit]").removeClass("cubewp-processing-ajax");
                    if (typeof response.redirectURL != 'undefined' && response.redirectURL !== '') {
                        setTimeout(function () {
                            if (response.redirectURL === 'self') {
                                location.reload();
                            }else {
                                window.location.href = response.redirectURL;
                            }
                        }, 3000);
                    }
                },
                error: function () {
                    cwp_notification_ui("error", cwp_user_login_params.error_msg);
                    $this.find("input[type=submit]").removeClass("cubewp-processing-ajax");
                }
            });
        }
    };

    $.cubewp_user_login.forget_form = function (t) {
        var $this = jQuery(t),
            is_valid = cubewp_frontend_form_validation($this);
        if (is_valid === true) {
            $this.find("input[type=submit]").addClass("cubewp-processing-ajax");
            var formData = new FormData($this[0]);
            formData.append('action', 'cubewp_ajax_forget_password');
            jQuery.ajax({
                url: cwp_user_login_params.ajax_url,
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (response) {
                    cwp_notification_ui(response.type, response.msg);
                    $this.find("input[type=submit]").removeClass("cubewp-processing-ajax");
                    if (response.type === 'success') {
                        $this[0].reset();
                        jQuery(".cubewp-login-form-trigger").trigger("click");
                    }
                    if (typeof response.redirectURL != 'undefined' && response.redirectURL !== '') {
                        setTimeout(function () {
                            window.location.href = response.redirectURL;
                        }, 3000);
                    }
                },
                error: function () {
                    cwp_notification_ui("error", cwp_user_login_params.error_msg);
                    $this.find("input[type=submit]").removeClass("cubewp-processing-ajax");
                }
            });
        }
    };
})(jQuery);