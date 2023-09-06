(function ($) {
    'use strict';

    var featured_items = $('.classified-featured-items'),
        show_child_terms = $('.classified-expand-more-terms'),
        featured_items_masonry = $('.classified-featured-items-masonry'),
        forms_in_multi_search = $('.classified-multi-search-container .cwp-search-form'),
        toggle_in_multi_search = $('.classified-multi-search-container.classified-multi-search-style-1 .cwp-field-radio-toggle'),
        verify_email_form = $('#classified-verify-email'),
        categories_carousal = $('.classified-category-cards.classified-have-carousal'),
        multi_search_tabs = $('.classified-multi-search-tab-btn');

    if(multi_search_tabs.length > 0) {
        multi_search_tabs.on('click', function(){
            var $this = $(this),
                widget_image = $this.find('img'),
                container = $this.closest('.elementor-section');
            if (widget_image.length > 0 && container.length > 0) {
                var image_src = widget_image.attr('src');
                if (image_src !== '') {
                    container.css('background-image', 'url('+image_src+')');
                }
            }
        });
    }

    if (categories_carousal.length > 0) {
        categories_carousal.slick({
            slide: '.classified-category-card',
            dots: false,
            arrows: true,
            prevArrow: '<i class="fa-solid fa-square-caret-left carousal-left-control" aria-hidden="true"></i>',
            nextArrow: '<i class="fa-solid fa-square-caret-right carousal-right-control" aria-hidden="true"></i>',
            infinite: true,
            centerMode: false,
            variableWidth: false,
            autoplay: true,
			swipeToSlide: true,
            slidesToScroll: 1,
            slidesToShow: ( categories_carousal.outerWidth() / 140 ),
        });
    }

    if (verify_email_form.length > 0) {
        verify_email_form.on('submit', function (event) {
            event.preventDefault();
            var $this = $(this),
                nonce = $this.find('#nonce').val(),
                email = $this.find('#classified-confirm-email').val(),
                button = $this.find('[type=submit]');
            button.addClass('cubewp-processing-ajax');
            $.ajax({
                url: classified_elements_scripts_params.classified_ajax_url, type: 'POST', dataType: "json", data: {
                    'action': 'classified_resend_verification_email',
                    'email': email,
                    'nonce': nonce
                }, success: function (response) {
                    button.removeClass('cubewp-processing-ajax');
                    if (response.success === true) {
                        cwp_notification_ui('success', response.data);
                    } else {
                        cwp_notification_ui('error', response.data);
                    }
                }
            });
        });
    }

    if (toggle_in_multi_search.length > 0) {
        toggle_in_multi_search.each(function () {
            $(this).removeClass('cwp-field-radio-toggle');
        });
    }

    $(document).ready(function () {
        if (forms_in_multi_search.length > 0) {
            forms_in_multi_search.show();
        }
        if (featured_items_masonry.length > 0) {
            featured_items_masonry.find('.classified-featured-item').each(function () {
                if ($(this).find('.classified-featured-items-masonry-grid').length === 0) {
                    $(this).remove();
                }
            });
            featured_items_masonry.show(function () {
                featured_items_masonry.slick({
                    slide: '.classified-featured-item',
                    dots: false,
                    arrows: false,
                    rows: 0,
                    infinite: true,
                    slidesToShow: 6,
                    slidesToScroll: 3,
                    autoplay: true,
                    responsive: [{
                        breakpoint: 1100, settings: {
                            slidesToShow: 4, slidesToScroll: 2,
                        }
                    }, {
                        breakpoint: 991, settings: {
                            slidesToShow: 2, slidesToScroll: 1,
                        }
                    }, {
                        breakpoint: 500, settings: {
                            slidesToShow: 1, slidesToScroll: 1,
                        }
                    }]
                });
            });
        }
    });

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

    if (featured_items.find('.classified-featured-item').length > 1) {
        featured_items.slick({
            slide: '.classified-featured-item',
            dots: false,
            arrows: false,
            rows: 0,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: false,
        });
        var time = 5, $bar, isPause, tick, percentTime;
        $bar = $('.classified-featured-items-slider-progress .classified-featured-items-progress-bar');

        function startProgressbar() {
            resetProgressbar();
            percentTime = 0;
            isPause = false;
            tick = setInterval(interval, 10);
        }

        function interval() {
            if (isPause === false) {
                percentTime += 1 / (time + 0.05);
                $bar.css({
                    width: percentTime + "%"
                });
                if (percentTime >= 100) {
                    featured_items.slick('slickNext');
                    startProgressbar();
                }
            }
        }

        function resetProgressbar() {
            $bar.css({
                width: 0 + '%'
            });
            clearTimeout(tick);
        }

        startProgressbar();
        featured_items.on({
            mouseover: function () {
                isPause = true;
            }, mouseleave: function () {
                isPause = false;
            }
        });
        featured_items.on("beforeChange", function () {
            resetProgressbar();
            startProgressbar();
        });
    }

})(jQuery);