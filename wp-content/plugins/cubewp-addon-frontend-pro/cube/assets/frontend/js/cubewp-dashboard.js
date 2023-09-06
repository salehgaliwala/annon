jQuery(document).ready(function () {
    jQuery(document).on('click', '.cwp-user-dashboard .cwp-user-dashboard-tabs li a', function(x) {
        var $this = jQuery(this);
        if ($this.hasClass('cwp-not-tab')) {
            return;
        }
        x.preventDefault();
        if ($this.hasClass('cwp-active-tab')) return false;
        var tabContent = $this.attr('href');

        var url = cubewp_set_arg_into_current_url('tab_id', tabContent.replace("#", ""));

        window.history.pushState({},"", url);

        jQuery('.cwp-user-dashboard-tab-content').removeClass('cwp-active-tab-content');
        jQuery('.cwp-user-dashboard .cwp-user-dashboard-tabs li').removeClass('cwp-active-tab');

        jQuery($this).closest('li').addClass('cwp-active-tab');
        jQuery(tabContent).addClass('cwp-active-tab-content');
    });
});

function cubewp_set_arg_into_current_url($option, $value) {
    var $url = window.location.origin + window.location.pathname,
        searchParams = new URLSearchParams(window.location.search);
    searchParams.set($option, $value);
    $url += "?" + searchParams.toString();

    return $url;
}

jQuery(document).on('click', '.cwp-post-action-delete', function (e) {
    e.preventDefault();
    var result = confirm(cwp_frontend_dashboard_params.warning);
    if(result){
        var thisObj = jQuery(this);
        var pid = thisObj.data('pid');
        jQuery.ajax({
            url: cwp_frontend_dashboard_params.ajax_url,
            type: 'POST',
            data : 'action=cubewp_delete_post&post_id='+ pid +'&security_nonce='+cwp_frontend_dashboard_params.security_nonce,
            dataType: "json",
            success: function (response) {
                cwp_notification_ui(response.type, response.msg);
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });
    }
});

