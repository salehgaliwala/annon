(function ($) {
    'use strict';
    var classified_register_trigger = $('.classified-register-trigger'),
        classified_login_trigger = $('.classified-login-trigger');

    if (classified_register_trigger.length > 0) {
        classified_register_trigger.on('click', function (x) {
            var $this = $(this),
                $parent = $this.closest('#classified-login-register');

            $parent.find('.classified-login-container').addClass('d-none');
            $parent.find('.classified-register-container').removeClass('d-none');
        });
    }

    if (classified_login_trigger.length > 0) {
        classified_login_trigger.on('click', function (x) {
            var $this = $(this),
                $parent = $this.closest('#classified-login-register');

            $parent.find('.classified-login-container').removeClass('d-none');
            $parent.find('.classified-register-container').addClass('d-none');
        });
    }

})(jQuery);