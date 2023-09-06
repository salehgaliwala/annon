jQuery(document).ready(function ($) {

    jQuery(document).on('click', '.cwpform-shortcode', function () {
        var $this = jQuery(this),
            temp_text = document.createElement("input");
        if ($this.find('.inner').hasClass('copy-to-clipboard')) {
            temp_text.value = $this.find('.inner').clone().children().remove().end().text();
            document.body.appendChild(temp_text);
            temp_text.select();
            document.execCommand("copy");
            document.body.removeChild(temp_text);
        }
    });
    
    function build_live_tab_preview(obj) {
        var title = obj.find('.tab-title-field').val(), user_role = obj.find('.tab-role-field').val(),
            content = obj.find('.tab-type-field').val(), icon = obj.find('.tab-icon-field').val();
        if (title !== '') {
            obj.find('.cubewp-user-dashboard-tab-title').text(title);
        } else {
            obj.find('.cubewp-user-dashboard-tab-title').text(jQuery('.cubewp-dashboard-builder-area-content-heading .cubewp-user-dashboard-tab-title').text());
        }
        if (user_role !== '') {
            obj.find('.cubewp-user-dashboard-tab-user-role').text(user_role);
        } else {
            obj.find('.cubewp-user-dashboard-tab-user-role').text(jQuery('.cubewp-dashboard-builder-area-content-heading .cubewp-user-dashboard-tab-user-role').text());
        }
        if (content !== '') {
            obj.find('.cubewp-user-dashboard-tab-content-type').text(content);
        } else {
            obj.find('.cubewp-user-dashboard-tab-content-type').text(jQuery('.cubewp-dashboard-builder-area-content-heading .cubewp-user-dashboard-tab-content-type').text());
        }
        if (icon !== '') {
            obj.find('.cubewp-user-dashboard-tab-icon').text(icon);
        } else {
            obj.find('.cubewp-user-dashboard-tab-icon').text(jQuery('.cubewp-dashboard-builder-area-content-heading .cubewp-user-dashboard-tab-icon').text());
        }
    }

    jQuery(document).on('change', '.cubewp-user-dashboard-tab-form select', function (e) {
        build_live_tab_preview(jQuery(this).closest('.cubewp-user-dashboard-tab'));
        if (jQuery(this).hasClass("tab-type-field")) {
            e.preventDefault();
            var thisObj = jQuery(this);
            var content_type = thisObj.val();
            if (content_type === '') {
                thisObj.closest('.cubewp-user-dashboard-tab-form').find('.cubewp-user-dashboard-tab-form-conditional-fields').html('');
            } else {
                var data = {
                    action: 'cwp_dashboard_content_type_fields',
                    content_id: jQuery(this).attr('id'),
                    content_type: content_type,
                    nonce: cwp_user_dashboard_params.nonce
                };
                jQuery.post(cwp_user_dashboard_params.url, data, function (response) {
                    thisObj.closest('.cubewp-user-dashboard-tab-form').find('.cubewp-user-dashboard-tab-form-conditional-fields').html(response.data);
                });
            }
        }
    });

    jQuery(document).on('input', '.cubewp-user-dashboard-tab-form input, .cubewp-user-dashboard-tab-form textarea', function (e) {
        build_live_tab_preview(jQuery(this).closest('.cubewp-user-dashboard-tab'));
    });

    jQuery('#cwp-add-new-tab-btn').click(function (e) {
        e.preventDefault();
        var data = {
            action: 'cwp_new_dashboard_tab_ajax', nonce: cwp_user_dashboard_params.nonce
        };
        jQuery.post(cwp_user_dashboard_params.url, data, function (response) {
            jQuery('.cubewp-dashboard-builder-area-content').append(response.data);
            var new_tab = jQuery('.cubewp-user-dashboard-tab');
            new_tab.last().find('.field-counter').text(new_tab.length);
        });
    });

    jQuery(document).on('click', '.cubewp-user-dashboard-tab-action-delete', function (e) {
        jQuery(this).closest('.cubewp-user-dashboard-tab').remove();
    });

    jQuery(document).on('click', '.cubewp-user-dashboard-tab-info', function (e) {
        var $this = jQuery(this),
            targetUl = $this.closest('.cubewp-user-dashboard-tab').find('.cubewp-user-dashboard-tab-form');
        targetUl.slideToggle();
        $this.closest('.cubewp-user-dashboard-tab').toggleClass("active-tab");
    });

    jQuery(document).on('submit', 'form#user-dashboard-form', function (e) {
        var is_valid = true;
        jQuery(".cwp-validation .required").each(function () {
            var _thisObj = jQuery(this),
                parent = _thisObj.closest('.cubewp-user-dashboard-tab').find('.cubewp-user-dashboard-tab-info');
            _thisObj.closest('.cubewp-user-dashboard-tab-form-field').find('.cwp-error-message').remove();
            parent.removeClass("have_error");
            if (_thisObj.val() === '') {
                is_valid = false;
                var validation_msg = _thisObj.data('validation_msg');
                if (validation_msg == '') {
                    var validation_msg = 'This field is required.';
                }
                _thisObj.closest('.cubewp-user-dashboard-tab-form-field').append('<div class="cwp-notice cwp-error-message"><p>' + validation_msg + '</p></div>');
                parent.addClass("have_error");
            }
        });

        return is_valid;
    });


    var sortable = jQuery('.cubewp-dashboard-builder-area-content');
    if (sortable.length > 0) {
        sortable.sortable({
            handle: ".cubewp-user-dashboard-tab-sorter",
            containment: ".cubewp-dashboard-builder-area-content",
            items: ".cubewp-user-dashboard-tab"
        }).disableSelection();
    }
    jQuery(document).on('click', '.cubewp-dashboard-icons span', function () {
        jQuery(this)
            .closest(".cubewp-user-dashboard-tab-form-field")
            .find(".tab-icon-field")
            .val(jQuery(this)
                .attr("class"));
    });
});