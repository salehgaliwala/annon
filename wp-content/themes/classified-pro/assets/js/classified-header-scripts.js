(function ($) {
    'use strict';
    var $window = $(window),
        sticky_header = $('.classified-sticky-header'),
        current_location = $('.classified-header .cwp-get-current-location'),
        hidden_ele;

    $(document).ready(function () {
        hidden_ele = $('.classified-visible-on-load')
        if (hidden_ele.length > 0) {
            hidden_ele.each(function () {
                $(this).removeClass('classified-visible-on-load');
            });
        }
        if (current_location.length > 0) {
            setTimeout(function (){
                current_location.trigger('click');
            }, 1000);
        }
    });

    if (sticky_header.length > 0) {
        $window.on('scroll', function () {
            var scroll = $window.scrollTop(), header_height = sticky_header.outerHeight();

            if (scroll >= 300) {
                $('html').css('padding-top', header_height);
                sticky_header.addClass('classified-header-is-sticky');
            } else {
                $('html').removeAttr('style');
                sticky_header.removeClass('classified-header-is-sticky');
            }
        });
    }

})(jQuery);

jQuery(document).ready(function($) {
    $('#add-to-cart-button').on('click', function(e) {
        e.preventDefault();

        var productId = $(this).data('product-id');
        
        var plan_id = '2221';
        var customData = {
            plan_id: plan_id
        };
        
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params_custom.ajax_url,
            data: {
                action: 'add_product_to_cart',
                product_id: productId,
                custom_data: customData
            },
            success: function(response) {
                console.log(response);
                // Refresh the cart fragment to display updated cart contents
              window.location.href = wc_add_to_cart_params_custom.checkout_url;
            }
        });
    });
});
