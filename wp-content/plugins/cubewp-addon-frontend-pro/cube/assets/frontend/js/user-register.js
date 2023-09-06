jQuery( document ).ready(function() {
    cwp_conditional_fields('[name="cwp_user_register[custom_fields]');
});
(function( $ ) {
    'use strict';
    $.cubewp_user_register = $.cubewp_user_register || {};
    
    $( document ).on(
        'click',
        '.cwp-user-register .submit-btn',
        function() {
            $.cubewp_user_register.submit_form( this );
            return false;
        }
    );
    
    $.cubewp_user_register.submit_form = function( t ) {
 
        $(t).closest( ".cwp-frontend-form-container" ).find('.cwp-alert').slideUp(500, function(){
            jQuery(this).remove();
        });
        var is_valid = cubewp_frontend_form_validation(jQuery('.cwp-user-register'));
        
        if( is_valid == true ){
            var formData = new FormData(jQuery('.cwp-user-register')[0]);
            jQuery('.cwp-user-register').find(".cwp-field-wysiwyg_editor").each(function() {
                var editor_id = jQuery(this).data('id');
                var editor_name = jQuery('textarea#'+ editor_id).attr('name');
                var postContent = tinymce.get(editor_id).getContent();
                formData.append(editor_name, postContent);
            });
            formData.append('action', 'cubewp_submit_user_register');
            formData.append('security_nonce', cwp_user_register_params.security_nonce);
            var submit_button = $(t);
            submit_button.addClass("cubewp-processing-ajax").prop("disabled", 1);

            jQuery.ajax({
                url: cwp_user_register_params.ajax_url,
                type: 'POST',
                data : formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (response) {
                    submit_button.removeClass("cubewp-processing-ajax").removeAttr("disabled");
                    cwp_notification_ui(response.type, response.msg);
                    if( typeof response.redirectURL != 'undefined' && response.redirectURL != '' ){
                        setTimeout(function() {
                            window.location.href = response.redirectURL;
                        }, 3000);
                    }
                }
            });
        }
    };
    
})( jQuery );    
    