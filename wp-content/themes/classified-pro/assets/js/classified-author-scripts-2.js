(function ($) {
    'use strict';
    var classified_author_edit_profile = $('p.classified-author-edit-profile');

    if (classified_author_edit_profile.length > 0) {
        classified_author_edit_profile.on('click', function (event) {
            event.preventDefault();
            var $this = $(this),
                parent = $this.closest('.classified-author-profile'),
                default_text = $this.attr('data-default-text'),
                active_text = $this.attr('data-active-text');

            if ($this.hasClass('classified-active-tab')) {
                $this.removeClass('classified-active-tab');
                $this.html(default_text);
                parent.find('div.classified-author-edit-profile').fadeOut();
                parent.find('div.classified-author-tabs-container').fadeIn();
            } else {
                $this.addClass('classified-active-tab');
                $this.html(active_text);
                parent.find('div.classified-author-edit-profile').fadeIn();
                parent.find('div.classified-author-tabs-container').fadeOut();
            }
        });
    }
})(jQuery);