(function ($) {
    'use strict';
    var archive_container = $('.cwp-archive-container'),
        show_hide_map = $('#classified-map-toggle'),
        show_child_terms = $('.classified-expand-more-terms'),
        toggle_filters = $('.classified-archive-show-filters'),
        layout_switcher = $('.cwp-archive-toggle-Listing-style .listing-switcher');

    if (layout_switcher.length > 0) {
        layout_switcher.on("click", function () {
            var $this = jQuery(this);
            if ($this.hasClass('list-view')) {
                archive_container.removeClass('classified-grid-view');
                archive_container.addClass('classified-list-view');
            } else if ($this.hasClass('grid-view')) {
                archive_container.removeClass('classified-list-view');
                archive_container.addClass('classified-grid-view');
            }
        });
    }

    if (toggle_filters.length > 0) {
        toggle_filters.on('click', function () {
            var $this = $(this), shown_text = $this.attr('data-shown-text'),
                hidden_text = $this.attr('data-hidden-text'),
                filters = $this.closest('div').find('.classified-search-filters-container');

            if (filters.length > 0) {
                if ($this.hasClass('filters-shown')) {
                    filters.slideUp();
                    $this.removeClass('filters-shown');
                    $this.html('<i class="fa-solid fa-filter" aria-hidden="true"></i>' + shown_text);
                } else {
                    filters.slideDown();
                    $this.addClass('filters-shown');
                    $this.html('<i class="fa-solid fa-filter" aria-hidden="true"></i>' + hidden_text);
                }
            }
        });
    }

    if (show_child_terms.length > 0) {
        show_child_terms.on('click', function () {
            var $this = $(this), terms_container = $this.next('ul');
            if ($this.hasClass('expanded')) return false;
            $('.classified-term-container > .classified-expand-more-terms').removeClass('expanded');
            $('.classified-term-container > ul').slideUp();
            terms_container.slideDown();
            $this.addClass('expanded');
        });
    }

    if (show_hide_map.length > 0) {
        show_hide_map.on('input', function () {
            var $this = $(this), content = $('.cwp-search-result-output'), map = $('.cwp-archive-content-map');
            if ($this.is(':checked')) {
                content.hide();
                map.show();
                if (typeof cwp_search_filters_ajax_content === 'function') {
                    cwp_search_filters_ajax_content();
                }
            } else {
                content.show();
                map.hide();
            }
        });
    }

})(jQuery);