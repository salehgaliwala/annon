jQuery( document ).ready(function() {
    
    cwp_conditional_fields('[name="cwp_user_form[cwp_meta]');

    if (jQuery('.cwp-taxonomy-field').length > 0) {
        cwp_display_groups_meta_by_terms_onLoad();
        jQuery(document).on('change', '.cwp-taxonomy-field', function () {
            cwp_display_groups_meta_by_terms_onLoad();
     });
    }
});

function cwp_display_groups_meta_by_terms_onLoad(){
    jQuery(".cwp-conditional-by-term").hide();
    jQuery(".cwp-taxonomy-field").each(function() {
        var thisObj  = jQuery(this),
            type     = thisObj.attr('type'),
            id       = thisObj.attr('id'),
            val = '';
        if (type == 'checkbox'){
            if (thisObj.is(':checked')){
                var val = jQuery(this).val();
            }
        }else {
            var val = jQuery(this).val();
        }
        if(val != ''){
            cwp_display_groups_meta_by_terms(val,id);
        }
        
    });
}

function cwp_display_groups_meta_by_terms(objectVal,objectID){
    jQuery(".cwp-conditional-by-term").each(function() {
        var thisObj     = jQuery(this);
        var group_terms = thisObj.data('terms');
        if( typeof group_terms !== 'undefined' && group_terms !== '' ){
            var group_terms_arr = group_terms.toString().split(",");
            if(Array.isArray(objectVal) && objectVal.length != 0){
                jQuery.each(objectVal, function(index, item) {
                    if( jQuery.inArray(item, group_terms_arr) != '-1' ){
                        thisObj.show();
                    }
                }.bind(this));
            }else if( jQuery.inArray(objectVal, group_terms_arr) != '-1' ){
                thisObj.show();
            }
            cwp_conditional_fields('[name="cwp_user_form[cwp_meta]');
        }
    });
}

jQuery( document ).ready(function() {
    
    jQuery(document).on('submit', 'form.cwp-user-form-submit', function (e) {
        var is_valid = cubewp_frontend_form_validation(jQuery(this));
        if( is_valid == true ){
            var submit_button = jQuery(this).find('[type=submit]');
            submit_button.addClass("cubewp-processing-ajax").prop("disabled", 1);
            var formData = new FormData(jQuery(this)[0]);
            if( typeof tinymce != "undefined" ) {
                jQuery('.cwp-user-form-submit').find(".cwp-field-wysiwyg_editor").each(function() {
                    var editor_id = jQuery(this).data('id');
                    var editor_name = jQuery('textarea#'+ editor_id).attr('name');
                    var postContent = tinymce.get(editor_id).getContent();
                    formData.append(editor_name, postContent);
                });
            }
            formData.append('action', 'cubewp_submit_post_form');
            formData.append('security_nonce', cwp_submit_post_params.security_nonce);

            jQuery.ajax({
                url: cwp_submit_post_params.ajax_url,
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
                        }, 2000);
                    }
                }
            });
            
        }
        return false;
    });
});

jQuery(document).ready(function() {
	if( jQuery(".cwp_quick_sign_in_container").length > 0 ){
		jQuery("#cwp_quick_checkbox_field").change(function() {
			if (jQuery(this).is(":checked")) {
			   jQuery(".cwp-quick-sign-up-container").slideUp(200);
				jQuery(".cwp_quick_sign_in_container").slideDown(200);
			} else {
				
				 jQuery(".cwp-quick-sign-up-container").slideDown(200);
				jQuery(".cwp_quick_sign_in_container").slideUp(200);
			}
		});
		
		jQuery(document).on('click', '.cwp_email_verification', function (e) {
			if( jQuery('#cwp-quick-signup-email').val() == 0 || jQuery('#cwp-quick-signup-email').val() == null){
				alert('Please enter your email');
				return false;
			}
			button = jQuery(this);
			button.addClass('cubewp-processing-ajax');
			jQuery.ajax({
				url: cwp_submit_post_params.ajax_url, 
				type: 'POST', 
				dataType: "json", 
				data: {
					'action': 'cwp_send_verification_email',
					'process': 'send_token',
					'email': jQuery('#cwp-quick-signup-email').val(),
					'nonce': cwp_submit_post_params.security_nonce
				}, success: function (response) {
					button.removeClass('cubewp-processing-ajax');
					if (response.success === true) {
						cwp_notification_ui('success', response.data);
						if( jQuery('#cwp-quick-signup-otp-verification').length == 0 ){
							button.before('<div class="cwp-field-container cwp-field-text cwp-otp-container"><label for="cwp-quick-signup-otp-verification">Enter OTP here<span class="cwp-required">*</span></label><input type="text" id="cwp-quick-signup-otp-verification" name="cwp-quick-signup-otp-verification" class="form-control form-control required" value=""></div><a class="cwp_email_verification">Resend OTP</a><a class="cwp_otp_verification">Verify</a>');
							button.remove();
						}
					} else {
						cwp_notification_ui('error', response.data);
					}
				}
			});
		});
		
		jQuery(document).on('click', '.cwp_otp_verification', function (e) {
			if( jQuery('#cwp-quick-signup-otp-verification').val() == 0 || jQuery('#cwp-quick-signup-otp-verification').val() == null){
				alert('Please enter your otp for verification');
				return false;
			}
			button = jQuery(this);
			button.addClass('cubewp-processing-ajax');
			jQuery.ajax({
				url: cwp_submit_post_params.ajax_url, 
				type: 'POST', 
				dataType: "json", 
				data: {
					'action': 'cwp_send_verification_email',
					'process': 'verify_token',
					'otp': jQuery('#cwp-quick-signup-otp-verification').val(),
					'nonce': cwp_submit_post_params.security_nonce
				}, success: function (response) {
					button.removeClass('cubewp-processing-ajax');
					if (response.success === true) {
						cwp_notification_ui('success', response.data);
						button.remove();
						jQuery('.cwp_email_verification').remove();
						jQuery('.cwp-otp-container').css( 'display', 'none');
					} else {
						cwp_notification_ui('error', response.data);
					}
				}
			});
		});
	}
});
