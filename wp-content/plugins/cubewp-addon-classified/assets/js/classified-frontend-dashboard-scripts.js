(function ($) {
    'use strict';
    var classified_params = classified_frontend_dashboard_scripts_params,
        classified_mark_item_sold_modal_texts = classified_params.classified_mark_item_sold_modal_texts,
        classified_dashboard_sidebar_tabs = $('.classified-dashboard-sidebar-tab'),
        classified_dashboard_tab_content = $('.cwp-user-dashboard-tab-content'),
        classified_dashboard_expand_tabs = $('.classified-dashboard-expand-tabs i'),
        classified_dashboard_home_tab = $('.classified-dashboard-expand-tabs a'),
        classified_see_categories = $('.classified-table-item-see-categories'),
        classified_complete_profile = $('.classified-complete-author-profile'),
        classified_item_remaining_time = $('.classified-item-remaining-time'),
        classified_mark_item_sold = $('.classified-mark-item-sold'),
        classified_boost_item = $('.classified-boost-item'),
        classified_order_action = $('.classified-make-order-action'),
        classified_ignore_rate = $('.classified-ignore-rate-request'),
        classified_dashboard_quick_profile = $('.classified-dashboard-open-edit-profile');

    if (classified_dashboard_quick_profile.length > 0) {
        var profile_dashboard_tab = $('.classified-dashboard-sidebar-tab.classified_user_profile');
        if (profile_dashboard_tab.length > 0) {

        }else {
            classified_dashboard_quick_profile.remove();
        }
        classified_dashboard_quick_profile.on('click', function () {
            profile_dashboard_tab.trigger('click');
            $('.classified-author-edit-profile:not(.classified-active-tab)').trigger('click');
        });
    }

    if (classified_ignore_rate.length > 0) {
        classified_ignore_rate.on('click', function (e) {
            e.preventDefault();
            var $this = $(this),
                request_id = $this.attr('data-request-id');
            if (!$this.hasClass('cubewp-processing-ajax')) {
                new Classified_confirm(classified_params.classified_ignore_rate_request, function (modal_element, $this) {
                    $this.addClass('cubewp-processing-ajax');
                    $.ajax({
                        url: classified_params.classified_ajax_url, type: 'POST', data: {
                            'action': 'classified_ignore_rate_request',
                            'request_id': request_id,
                            'nonce': classified_params.classified_ignore_rate_request_nonce
                        }, dataType: "json", success: function (response) {
                            $this.removeClass('cubewp-processing-ajax');
                            if (response.success === true) {
                                cwp_notification_ui('success', response.data);
                            } else {
                                cwp_notification_ui('error', response.data);
                            }
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                            modal_element.modal('hide');
                        }
                    });
                });
            }
        });
    }

    if (classified_order_action.length > 0) {
        classified_order_action.on('click', function (e) {
            e.preventDefault();
            var $this = $(this), order_id = $this.attr('data-order-id'), action_type = $this.attr('data-action-type');
            if (!$this.hasClass('cubewp-processing-ajax')) {
                var confirm_texts = [];
                if (action_type === 'processing') {
                    confirm_texts = classified_params.classified_make_order_processing_texts;
                }else if (action_type === 'shipped') {
                    confirm_texts = classified_params.classified_make_order_shipped_texts;
                }else if (action_type === 'dispute') {
                    confirm_texts = classified_params.classified_make_order_dispute_texts;
                }else {
                    confirm_texts = classified_params.classified_make_order_complete_texts;
                }
                new Classified_confirm(confirm_texts, function (modal, $_this) {
                    var $data = {
                        'action': 'classified_make_order_action',
                        'order_id': order_id,
                        'type': action_type,
                        'nonce': classified_params.classified_make_order_action_nonce
                    };
                    var shipping_form = modal.find('#classified-shipping-confirmation'),
                        dispute_form = modal.find('#classified-dispute-details');
                    if (dispute_form.length > 0) {
                        if (dispute_form.find('#classified-dispute').length > 0) {
                            dispute_form.find('#classified-dispute').removeClass('is-invalid');
                            if (dispute_form.find('#classified-dispute').val() === '') {
                                dispute_form.find('#classified-dispute').addClass('is-invalid');
                                return false;
                            }else {
                                $data.dispute_details = dispute_form.find('#classified-dispute').val();
                            }
                        }
                    }
                    if (shipping_form.length > 0) {
                        if (shipping_form.find('#classified-tracking').length > 0) {
                            shipping_form.find('#classified-tracking').removeClass('is-invalid');
                            if (shipping_form.find('#classified-tracking').val() === '') {
                                shipping_form.find('#classified-tracking').addClass('is-invalid');
                                return false;
                            }else {
                                $data.shipping_details = shipping_form.find('#classified-tracking').val();
                            }
                        }
                        if (shipping_form.find('#classified-rating-request').length > 0) {
                            $data.rating_request = shipping_form.find('#classified-rating-request').is(':checked');
                        }
                    }
                    $_this.addClass('cubewp-processing-ajax');
                    $.ajax({
                        url: classified_params.classified_ajax_url, type: 'POST', data: $data, dataType: "json", success: function (response) {
                            $_this.removeClass('cubewp-processing-ajax');
                            if (response.success === true) {
                                cwp_notification_ui('success', response.data);
                            } else {
                                cwp_notification_ui('error', response.data);
                            }
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                            modal.modal('hide');
                        }
                    });
                });
            } else {
                return false;
            }
        });
    }

    $(document).on('click', '.asdasdasdas', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'http://localhost/classified/wp-admin/admin-ajax.php', data: {
                order_id: 2263, action: 'woocommerce_get_order_details', security: 'd6c57ff659'
            }, type: 'GET', success: function (response) {
                if (response.success) {
                    $('body').append(response.data.item_html);
                }
            }
        });
    });

    if (classified_boost_item.length > 0) {
        classified_boost_item.on('click', function (e) {
            e.preventDefault();
            var $this = $(this), item_id = $this.attr('data-item-id');
            if (!$this.hasClass('cubewp-processing-ajax')) {
                $this.addClass('cubewp-processing-ajax');
                $.ajax({
                    url: classified_params.classified_ajax_url, type: 'POST', data: {
                        'action': 'classified_boost_item_modal',
                        'item_id': item_id,
                        'nonce': classified_params.classified_boost_item_modal_nonce
                    }, dataType: "json", success: function (response) {
                        if (response.success === true) {
                            var boost_modal = $('.classified-boost-modal'), booster_form;
                            if (boost_modal.length > 0) {
                                boost_modal.remove();
                            }
                            boost_modal = $('body').append(response.data).find('.classified-boost-modal');
                            boost_modal.modal('show');
                            $this.removeClass('cubewp-processing-ajax');
                            cwp_conditional_fields('[name="cwp_user_form[cwp_meta]');
                            booster_form = boost_modal.find('#cwp-from-cwp_booster');
                            booster_form.prepend('<input type="hidden" value="' + item_id + '" name="cwp_user_form[cwp_meta][cwp_associated_post]">');
                            booster_form.find('div[data-id="cwp_associated_post"]').remove();
                            booster_form.find('#cwp_booster_days').before("<p class='description'>" + $this.attr('data-ppd-desc') + "</p>");
                            booster_form.find('#cwp_booster_budget').before("<p class='description'>" + $this.attr('data-ppc-desc') + "</p>");
                        } else {
                            cwp_notification_ui('error', response.data);
                            $this.removeClass('cubewp-processing-ajax');
                        }
                    }
                });
            }
        });
    }

    if (classified_mark_item_sold.length > 0) {
        classified_mark_item_sold.on('click', function (e) {
            e.preventDefault();
            var $this = $(this), item_id = $this.attr('data-item-id');
            if (!$this.hasClass('cubewp-processing-ajax')) {
                new Classified_confirm(classified_mark_item_sold_modal_texts, function (modal_element, $this) {
                    $this.addClass('cubewp-processing-ajax');
                    $.ajax({
                        url: classified_params.classified_ajax_url, type: 'POST', data: {
                            'action': 'classified_mark_item_sold',
                            'item_id': item_id,
                            'nonce': classified_params.classified_mark_item_sold_nonce
                        }, dataType: "json", success: function (response) {
                            $this.removeClass('cubewp-processing-ajax');
                            if (response.success === true) {
                                cwp_notification_ui('success', response.data);
                            } else {
                                cwp_notification_ui('error', response.data);
                            }
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                            modal_element.modal('hide');
                        }
                    });
                });
            } else {
                return false;
            }
        });
    }

    if (classified_item_remaining_time.length > 0) {
        classified_item_remaining_time.each(function () {
            var $this = $(this), $current_date = $this.data('current-date'), $end_date = $this.data('end-date'),
                target = $this.find('time'), target_date = new Date($end_date), current_date = new Date($current_date),
                count = new Countdown(target_date, current_date);
            count.countdown(function (obj) {
                target.text(obj.hours + ":" + obj.minutes + ":" + obj.seconds);
            });
        });
    }

    if (classified_complete_profile.length > 0) {
        classified_complete_profile.on('click', function (event) {
            event.preventDefault();
            jQuery('.classified_user_profile').trigger('click');
            $('.classified-view-profile-container').toggleClass('classified-active-section');
            $('.classified-edit-profile-container').toggleClass('classified-active-section');
        });
    }

    if (classified_see_categories.length > 0) {
        classified_see_categories.on('click', function (event) {
            event.preventDefault();
            $('.classified-table-item-categories').find('h6').css('display', 'inline-block');
            $(this).hide();
        });
    }

    if (classified_dashboard_sidebar_tabs.length > 0) {
        classified_dashboard_sidebar_tabs.on('click', function (event) {
            var $this = $(this), target = $this.attr('data-target'), permalink = $this.attr('data-permalink');
            if (target === 'expander') return false;
            if (target === 'href') {
                window.location.href = permalink;
            } else {
                event.preventDefault();
                window.history.replaceState({path: permalink}, '', permalink);
                target = $(target);
                if (target.length > 0) {
                    classified_dashboard_sidebar_tabs.removeClass('classified-active-tab');
                    classified_dashboard_tab_content.removeClass('cwp-active-tab-content');
                    $this.addClass('classified-active-tab');
                    target.addClass('cwp-active-tab-content');
                    classified_animate_progress();
                } else {
                    location.replace(permalink);
                }
            }
        });

        $('.classified-active-tab').trigger('click');
    }

    if (classified_dashboard_expand_tabs.length > 0) {
        classified_dashboard_expand_tabs.on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            $('.cwp-user-dashboard').toggleClass('classified-tabs-expanded');
        });

        if ( ! isMobileOrTablet() ) {
            $('.cwp-user-dashboard').addClass('classified-tabs-expanded');
        }

    }

    if (classified_dashboard_home_tab.length > 0) {
        classified_dashboard_home_tab.on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            window.location.href = $(this).attr('href');
        });
    }

    function classified_animate_progress() {
        var classified_progress = $('.classified-progress');
        if (classified_progress.length > 0) {
            classified_progress.each(function () {
                var $this = $(this), width = $this.attr('data-value'),
                    transition = getComputedStyle(document.documentElement).getPropertyValue('--transition');
                $this.find('span').css('width', '0');
                setTimeout(function () {
                    $this.find('span').css('width', width);
                }, transition)
            });
        }
    }

    $(document).ready(function () {
        classified_animate_progress();
    });

})(jQuery);

function isMobileOrTablet() {
    const userAgent = navigator.userAgent;
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent);
}