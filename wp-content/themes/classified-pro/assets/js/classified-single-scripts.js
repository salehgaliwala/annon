(function ($) {
    'use strict';
    var pretty_photo = $("[rel^='classified[item_gallery]']"),
        sticky_sidebar = $('.classified-sticky-sidebar'),
        gallery_slider = $('.classified-gallery-slider'),
        gallery_slider_nav = $('.classified-gallery-slider-nav'),
        items_slider = $('.classified-items-slider'),
        sections_nav = $('.classified-tabs-container'),
        sticky_header = $('.classified-sticky-header'),
        single_copier = $('.classified-single-copy-content'),
        show_value = $('.classified-show-value'),
        offset_top = 30;

    if (show_value.length > 0) {
        show_value.on('click', function () {
            var $this = $(this),
                value = $this.attr('data-show-value'),
                text = $this.attr('data-show-text'),
                link = $this.find('a');
            $this.text(text);
            $this.parent().find('p').text(value);
            $this.append('<a href="' + link.attr('href') + '" class="stretched-link" target="_blank"></a>');
            $this.parent().find('div').replaceWith(function(){
                return $('<a class="d-flex align-items-center" target="_blank" href="' + link.attr('href') + '" />').append($(this).contents());
            });
            $this.removeClass('classified-show-value');
        });
    }

    if (single_copier.length > 0) {
        single_copier.on('click', function (e) {
            e.preventDefault();
            var $this = $(this), copy_text = $this.text(), copied_text = $this.attr('data-text'),
                temp_text = document.createElement("input");
            if ($this.hasClass('active-event')) return false;
            temp_text.value = $this.closest('p').clone().children().remove().end().text();
            document.body.appendChild(temp_text);
            temp_text.select();
            document.execCommand("copy");
            document.body.removeChild(temp_text);
            $this.text(copied_text);
            $this.addClass('active-event');
            setTimeout(function () {
                $this.text(copy_text);
                $this.removeClass('active-event');
            }, 5000)
        });
    }

    $(document).ready(function(){
        if (pretty_photo.length > 0) {
            pretty_photo.prettyPhoto({
                social_tools: false
            });
        }
    });

    if (sticky_sidebar.length > 0) {
        if (sticky_header.length > 0) {
            offset_top = offset_top + sticky_header.outerHeight();
        }
        sticky_sidebar.stick_in_parent({
            'offset_top': offset_top
        });
    }

    if (gallery_slider.length > 0) {
        if (gallery_slider.find('.classified-gallery-slider-slide').length > 1) {
            gallery_slider.slick({
                arrows: true,
                infinite: true,
                slidesToShow: 1,
                fade: true, // @todo Make These Options Dynamic START
                autoplay: true,
                autoplaySpeed: 6000, // @todo Make These Options Dynamic END
                appendArrows: $('.classified-gallery-actions'),
                prevArrow: '<i class="fa-solid fa-arrow-left classified-gallery-prev-action" aria-hidden="true"></i>',
                nextArrow: '<i class="fa-solid fa-arrow-right classified-gallery-next-action" aria-hidden="true"></i>',
                asNavFor: '.classified-gallery-slider-nav'
            });
        }
    }
    else {
        $('.classified-gallery-actions').remove();
    }

    if (gallery_slider_nav.length > 0) {
        if (gallery_slider_nav.find('.classified-gallery-nav-slide').length > 1) {
            gallery_slider_nav.slick({
                vertical: true,
                verticalSwiping: true,
                swipeToSlide: true,
                arrows: false,
                infinite: true,
                slidesToShow: 5,
                asNavFor: '.classified-gallery-slider',
                focusOnSelect: true,
                responsive: [{
                    breakpoint: 991, settings: {
                        vertical: false, verticalSwiping: false, infinite: true, slidesToShow: 5,
                    }
                }, {
                    breakpoint: 500, settings: {
                        vertical: false, verticalSwiping: false, infinite: true, slidesToShow: 3,
                    }
                }]
            });
        }
    }

    if (items_slider.length > 0) {
        items_slider.each(function () {
            var slides_to_show = $(this).attr('data-slides-to-show'),
                items_slider_slides = $(this).find('.classified-items-slider-slide');
            if ( items_slider_slides.length > 1 && items_slider_slides.length > slides_to_show ) {
                $(this).slick({
                    autoplay: false,
                    slidesToShow: slides_to_show,
                    slidesToScroll: 3,
                    infinite: true,
                    arrows: true,
                    prevArrow: '<i class="classified-items-slider-arrow fa-solid fa-chevron-left" aria-hidden="true"></i>',
                    nextArrow: '<i class="classified-items-slider-arrow fa-solid fa-chevron-right" aria-hidden="true"></i>',
                    responsive: [
                        {
                            breakpoint: 1200,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 2,
                            }
                        },
                        {
                            breakpoint: 991,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1,
                            }
                        },
                        {
                            breakpoint: 500,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                            }
                        }
                    ]
                });
            }
        })
    }

    if (sections_nav.length > 0) {
        sections_nav.find('.classified-tab a').on('click', function (e) {
            e.preventDefault();
            var target_id = $($(this).attr('href')), target_offset;
            if (target_id.length > 0) {
                $('html, body').animate({
                    scrollTop: (target_id.offset().top - 50)
                }, 500);
            }
            window.location.hash = '';
        });
    }

})(jQuery);