jQuery( document ).ready(function() {
    cwp_conditional_fields('[name="cwp_user_profile[custom_fields]');
    jQuery(document).on('click', '#cwp-delete-user', function () {
        var user_id = jQuery(this).data('user-id');
        var result = confirm(cwp_user_profile_params.warning);
        if(result){
            jQuery.ajax({
                url: cwp_user_profile_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'cwp_delete_user',
                    user_id: user_id,
                    nonce: cwp_user_profile_params.delete_nonce
                },
                success: function (response) {
                    if (response.type === "success") {
                        cwp_notification_ui('success', response.msg);
                        setTimeout(function () {
                            if (typeof response.redirectURL != "undefined") {
                                window.location.href = response.redirectURL;
                            }
                        }, 2000);
                    } else {
                        cwp_notification_ui('error', response.msg);
                    }
                
                }
            });
        }
    });
    jQuery(document).on('click', '#cwp-download-user', function () {
        var user_id = jQuery(this).data('user-id');
        var result = confirm(cwp_user_profile_params.warning);
        if(result){
            jQuery.ajax({
                type: 'POST',
                url: cwp_user_profile_params.ajax_url,
                data: {
                    action: 'cwp_download_user',
                    user_id: user_id,
                    nonce: cwp_user_profile_params.download_nonce
                },
                success: function (resp) {
                    var blob = new Blob([resp]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'cubewp-user-details.csv';
                    link.click();
                }
            });
        }
    });
});
(function ($) {
    'use strict';
    $.cubewp_user_profile = $.cubewp_user_profile || {};

    $(document).on('submit', '.cwp-user-profile', function () {
        $.cubewp_user_profile.submit_form(this);
        return false;
    });

    $.cubewp_user_profile.submit_form = function (t) {
        var is_valid = cubewp_frontend_form_validation(jQuery(t));
        if (is_valid === true) {
            var formData = new FormData(jQuery(t)[0]);
            jQuery(t).find(".cwp-field-wysiwyg_editor").each(function () {
                var editor_id = jQuery(this).data('id');
                var editor_name = jQuery('textarea#' + editor_id).attr('name');
                var postContent = tinymce.get(editor_id).getContent();
                formData.append(editor_name, postContent);
            });
            formData.append('action', 'cubewp_update_user_profile');
            formData.append('security_nonce', cwp_user_profile_params.security_nonce);
            var submit_button = $(t).find('.submit-btn');
            submit_button.addClass("cubewp-processing-ajax").prop("disabled", 1);
            jQuery.ajax({
                url: cwp_user_profile_params.ajax_url,
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (response) {
                    submit_button.removeClass("cubewp-processing-ajax").removeAttr("disabled");
                    cwp_notification_ui(response.type, response.msg);
                    if( typeof response.redirectURL != 'undefined' ){
                        setTimeout(function() {
                            if (response.redirectURL !== false) {
                                window.location.href = response.redirectURL;
                            }else {
                                location.reload();
                            }
                        }, 3000);
                    }
                }
            });
        }
    };
})(jQuery);