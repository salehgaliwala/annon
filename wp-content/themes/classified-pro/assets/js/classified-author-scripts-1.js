(function ($) {
    'use strict';

    $(document).ready(function () {
        var classified_dashboard_edit_profile = $('.classified-author-profile-action');

        if (classified_dashboard_edit_profile.length > 0) {
            classified_dashboard_edit_profile.on('click', function (event) {
                event.preventDefault();
                var $this = $(this), target = $this.attr('data-target');
                target = $(target);
                if (target.length > 0) {
                    $('.classified-author-profile-action-target').removeClass('classified-active-section');
                    target.addClass('classified-active-section');
                }
            });
        }
    });
})(jQuery);