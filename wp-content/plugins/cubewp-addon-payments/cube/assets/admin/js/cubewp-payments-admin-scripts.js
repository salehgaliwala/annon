jQuery(document).ready(function () {
    if (jQuery('.form-table #plan_type').length > 0) {
        cwp_hide_show_plan_fields();
        jQuery(document).on('change', '.form-table #plan_type', function () {
            cwp_hide_show_plan_fields();
        });
    }

    if (jQuery('.form-table #plan_duration_type').length > 0) {
        cwp_hide_show_plan_duration_field();
        jQuery(document).on('change', '.form-table #plan_duration_type', function (e) {
            cwp_hide_show_plan_duration_field();
        });
    }
});

function cwp_hide_show_plan_fields() {
    var plan_type = jQuery('.form-table #plan_type').val();
    if (plan_type === 'package') {
        jQuery('.form-table #no_of_posts').closest('tr').show();
    } else {
        jQuery('.form-table #no_of_posts').closest('tr').hide();
    }
}

function cwp_hide_show_plan_duration_field() {
    var plan_duration_type = jQuery('.form-table #plan_duration_type').val();
    if (plan_duration_type === 'per_days') {
        jQuery('.form-table #plan_duration').closest('tr').show();
    } else {
        jQuery('.form-table #plan_duration').closest('tr').hide();
    }
}