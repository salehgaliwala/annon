jQuery(document).ready(function(){
    if ( jQuery('a[rel^=\'prettyPhoto\']').length > 0 ) {
        jQuery("a[rel^='prettyPhoto']").prettyPhoto({
            theme: 'facebook',
            social_tools: false,
            show_title: false,
        });
    }
});
jQuery(document).on('click', '.cwp-pay-publish-btn', function (e) {
    var thisObj = jQuery(this);
    var pid = thisObj.data('pid');
    jQuery.ajax({
        url: cwp_single_params.ajax_url,
        type: 'POST',
        data : 'action=cubewp_pay_post&post_id='+ pid,
        dataType: "json",
        success: function (response) {
            cwp_notification_ui(response.type, response.msg);
            if( typeof response.redirectURL != 'undefined' && response.redirectURL != '' ){
                setTimeout(function() {
                    window.location.href = response.redirectURL;
                }, 1000);
            }
        }
    });
});

jQuery(document).on('click', '.cwp-publish-btn', function (e) {
    var thisObj = jQuery(this);
    var pid = thisObj.data('pid');
    jQuery.ajax({
        url: cwp_single_params.ajax_url,
        type: 'POST',
        data : 'action=cubewp_publish_post&post_id='+ pid,
        dataType: "json",
        success: function (response) {
            cwp_notification_ui(response.type, response.msg);
            if( typeof response.redirectURL != 'undefined' && response.redirectURL != '' ){
                setTimeout(function() {
                    window.location.href = response.redirectURL;
                }, 1000);
            }
        }
    });
});
