(function ($) {
    'use strict';

    var classified_items_load_more = $('.classified-items-load-more'),
        category_card_collapse = $('.classified-see-more-category'),
        classified_content_section = $('.classified-hide-no-content-section');

    if (category_card_collapse.length > 0) {
        category_card_collapse.on('click', function () {
            var $this = $(this), more_text = $this.attr('data-more'), less_text = $this.attr('data-less'),
                collapse = $this.closest('.classified-category-card').find('.classified-category-card-have-collapse');
            if (collapse.length < 1) {
                collapse = $this.closest('.classified-form-field').find('.classified-category-card-have-collapse');
            }
            if ($this.hasClass('collapsed')) {
                collapse.addClass('expand-collapsed')
                $this.removeClass('collapsed');
                $this.text(less_text);
            } else {
                $this.addClass('collapsed');
                collapse.removeClass('expand-collapsed');
                $this.text(more_text);
            }
        });
    }

    if (classified_content_section.length > 0) {
        classified_content_section.each(function () {
            if ($(this).find('.classified-section-have-content').length > 0) {
                $(this).show().find('.classified-section-have-content').remove();
            }
        });
    }

    if (classified_items_load_more.length > 0) {
        classified_items_load_more.on('click', function (event) {
            event.preventDefault();
            var $this = $(this);
            $this.addClass('cubewp-processing-ajax');
            $.ajax({
                url: classified_script_obj.classified_ajax_url, type: 'POST', dataType: "json", data: {
                    'action': 'classified_load_more_items',
                    'page-num': $this.attr('data-page-num'),
                    'recommended': $this.attr('data-recommended'),
                    'author': $this.attr('data-author'),
                    'boosted': $this.attr('data-boosted'),
                    'layout-style': $this.attr('data-layout-style'),
                    'col-class': $this.attr('data-col-class'),
                    'posts-per-page': $this.attr('data-posts-per-page'),
                    'categories-terms': $this.attr('data-categories-terms'),
                    'post-types': $this.attr('data-post-types')
                }, success: function (response) {
                    $this.removeClass('cubewp-processing-ajax');
                    if (response.type === "success") {
                        $this.parent().parent().find('.row').append(response.html);
                        if (response.current_page > response.max_pages) {
                            $this.parent().remove();
                        } else {
                            $this.attr('data-page-num', response.current_page);
                        }
                        $(document.body).trigger('classified_more_items_loaded');
                    } else {
                        cwp_notification_ui('error', response.msg);
                    }
                }
            });
        });
    }

    function init_tooltips() {
        var tooltip_trigger_list = [].slice.call(document.querySelectorAll('[data-classified-tooltip="true"]'));
        tooltip_trigger_list.map(function (tooltip_trigger_element) {
            return new bootstrap.Tooltip(tooltip_trigger_element, {
                template: '<div class="tooltip classified-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
            })
        });
    }

    $(document.body).on('classified_more_items_loaded', function () {
        init_tooltips(); // Initializing the tooltips
    })

    $(document.body).on('cubewp_search_results_loaded', function () {
        init_tooltips(); // Initializing the tooltips
    })

    init_tooltips(); // Initializing the tooltips

})(jQuery);