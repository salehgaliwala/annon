jQuery( document ).ready(function() {
    cwp_conditional_fields('[name="cwp_custom_form[fields]');

    jQuery(document).on('submit', 'form.cwp-user-form-submit', function (e) {
        var is_valid = cubewp_frontend_form_validation(jQuery(this));
        if( is_valid == true ){
            var submit_button = jQuery(this).find('[type=submit]');
            submit_button.addClass("cubewp-processing-ajax").prop("disabled", 1);
            var formData = new FormData(jQuery(this)[0]);
            jQuery('.cwp-user-form-submit').find(".cwp-field-wysiwyg_editor").each(function() {
                var editor_id = jQuery(this).data('id');
                var editor_name = jQuery('textarea#'+ editor_id).attr('name');
                var postContent = tinymce.get(editor_id).getContent();
                formData.append(editor_name, postContent);
            });
            formData.append('action', 'cubewp_submit_custom_form');
            formData.append('security_nonce', cubewp_custom_form_submit_params.security_nonce);

            jQuery.ajax({
                url: cubewp_custom_form_submit_params.ajax_url,
                type: 'POST',
                data : formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    submit_button.removeClass("cubewp-processing-ajax").removeAttr("disabled");
                    cwp_notification_ui(response.type, response.msg);
                    if( typeof response.redirectURL != 'undefined' && response.redirectURL != '' ){
                        setTimeout(function() {
                            window.location.href = response.redirectURL;
                        }, 2000);
                    }
                }
            });
            
        }
        return false;
    });

});