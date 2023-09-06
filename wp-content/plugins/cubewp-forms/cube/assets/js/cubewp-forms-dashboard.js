jQuery(document).ready(function($) {

    jQuery('.cwp-form-action-view').click(function(e) {

        var e_this = jQuery(this);
        var lead_id = e_this.data('lead_id');
        jQuery('.cwp-form-sidebar .cwp-form-data-content').html('');
        jQuery('.cwp-form-sidebar .cwp-form-data-content').addClass('cwp-loading-msgs');
        var data = {
            action: 'cwp_forms_data',
            lead_id: lead_id,
            security_nonce: cubewp_forms_dashboard_params.security_nonce
        };
        jQuery.ajax({
            type: 'POST',
            url: cubewp_forms_dashboard_params.ajax_url,
            data: data,
            success: function(response) {
                jQuery('.cwp-form-sidebar .cwp-form-data-content').removeClass('cwp-loading-msgs');
                jQuery('.cwp-form-sidebar .cwp-form-data-content').html(response.output);
            }
        });
        setTimeout(function() {
            jQuery('.cwp-form-sidebar').css({
                'transition': '0.3s',
                'right': ' 100px',
                'opacity': '1',
                'width': '400px',
                'visibility': 'visible',


            });
            jQuery(document).on('click', '.cwp-close-sidebar', function(x) {
                jQuery('.cwp-form-sidebar').css({
                    'transition': '0.3s',
                    'right': ' 0px',
                    'opacity': '0',
                    'width': '0',
                    'visibility': 'hidden',

                });
            });
        }, 500);

    });

});