(function($) {

	$( document ).ready(function() {

		$('.somfrp-wp-picker-container .somfrp-colour-picker').wpColorPicker({
			width: 250,
			hide: true
		});

    // Move .updated and .error alert boxes to the error wrap if on the settings page
    if ( $( '#somfrp-admin-notices' ).length ) {
      $( 'div.notice' ).not('#somfrp-admin-notices div.notice').each(function() {
        $( this ).remove();
      });
    }

	});

})( jQuery );