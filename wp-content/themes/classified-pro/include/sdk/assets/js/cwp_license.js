jQuery( document ).on( "click", "#cwp_license_submit", function(e) {
	e.preventDefault();
	var cwp_license_key = jQuery('#cwp_license_key').val();
	var cwp_user_email = jQuery('#cwp_user_email').val();
	var cwp_license_form_meta_nonce = jQuery('input[name="cwp_license_form_meta_nonce"]').val();
	if( cwp_license_key == '' || cwp_license_key == null  ){
		alert('Please add required fields');
		return false;
	}
	jQuery('.loader-import-data').css({
        "opacity": "1",
        "visibility": "visible",
    });
	jQuery.ajax({
		url: cwp_admin_license_params.ajax_url,
		type: 'POST',
		data: {
			'action': 'cwp_license_verification',
			'cwp_license_key': cwp_license_key,
			'cwp_user_email': cwp_user_email,
			'security_nonce': cwp_admin_license_params.security_nonce
		},
		dataType: "json",
		success: function (response) {
			if(response.status == 'success' ){
				jQuery.each(response.data, function(order_id, item) {
					jQuery.each(item, function(slug, license) {
						var plugin_name = slug.replace(/-addon-/g, " ");
						plugin_name = plugin_name.replace("-", " ");
						plugin_name = plugin_name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
							return letter.toUpperCase();
						});
						if(  typeof license.activated === 'undefined' ){
							if(  typeof license.invalid === 'undefined' ){
								jQuery('.cubewp-theme-importer-main .importer-tab.plugins .cube-setup-grid-list ul.cube-setup-list-theme').append('<li class="progress-list active" id="parent-'+slug+'"><div class="check-icons"><input type="checkbox" data-order="'+order_id+'" data-cwp_source="yes" data-license="'+license.key+'" data-download_id="'+license.download_id+'" id="'+slug+'" name="cwp-plugins-installation" value="'+slug+'" checked></div>'+plugin_name+'</li>');
							}else{
								jQuery('.cubewp-theme-importer-main .importer-tab.plugins .cube-setup-grid-list ul.cube-setup-list-theme').append('<li class="progress-list cwp_hide"><div class="check-icons"><img class="hide-color" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAADI0lEQVRIie2Wz2vcVhCAv5G0Vnrx7ikU5xACJcQttARMyS1yWaQ8hxJqs2DIP5A0x9KSo48hIccU/wEtBLa7IYXuajeGuMf8ILSnJi2UtjRN6cmbS7GzetODZEertWxv8TFzkRjNm2/mzcx7gjdyyCKTGKsx75Ikp4GjmeofrH0ia2s/HRpQg+AIvn8F1UvAOyVmv6C6iufdkm53838DtV4/g8htRI4DA0Q6qK4Df2UmM4gEqC4AVeA3YFn6/QcTAzWKllD9CnCB61h7Q9bWBiWBVXHdL1D9HEhwnIsSx+0DA7VeP4Pj3Af+xXEWJY7XywIrBDmPagt4C5Gz0us93BeoQXCESuUpIjM4TnhQWAHaA/7EdWeLNXXGVvj+laxm14swbTTcMUBBJ73efeAmcIIk+bRoPw5Mu3GAtTdG1GG4wsbGXTXG39EZ47OxcVfDcGXEh7XXgJeIXNoTqFH0Hmnrf5dvEG00XFTnEDlPkrTVGF+N8UmSNiLnUZ3LZ5qt7aB6Uuv12fIMrX0/e/s+r5ZmM8HzloAOsECStEmSNrAAdPC8JWk2kxFfIqkPx/mgHChyLHs+L26FdLubVKufIPJtBloAYra2lnYddtXnIz5LgJoZlx8I+W8i+9up2nKgta9PkOJ6Y3wGgzvAx6Rb20E1YmqqlW+k156dbR8vyoGe92MWeTACazRchsMW2zVz3UVcd5Htmg6HrbGRUT2bvf2QV48Pfhj+DBzF2uMjnRqGK6jO4Xk7NVNjfIbDFiKPpd/fGQ0NghpTU78DL6TfP1WeYRrZKpCejfnI+v0VarUL+QaRbneTWu1CHgZApXIVmEZkteh+PMN0vp4CxxCJspPjwKJh+BHQA/6gWp2VZnNrzwyzDJaBBNWWRtH8hLBvgFc4znIRBntdT+fOLWLt16TX002svVZ6PQVBjUrlKiKfAa9QvSj37t3ZzXbvCziKPkT1NnACeEk6CuuIpOOjOoPIPGCAaeBXHGdZ4vhRmc/9fzGM8bH2MnAZ1ZMlZs8QWWV6+svdtnEi4Ag8DE+hehqRt1OF/o3rPpE4fjaJnzdyqPIfOBNOT3Frp0sAAAAASUVORK5CYII="><img class="colored" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg=="></div>'+plugin_name+' Failed!<br>'+license.invalid+'</li>');
							}
						}else{
							jQuery('.cubewp-theme-importer-main .importer-tab.plugins .cube-setup-grid-list ul.cube-setup-list-theme').append('<li class="progress-list active" id="parent-'+slug+'"><div class="check-icons"><img class="colored" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg=="></div>'+plugin_name+'</li>');
						}
						
					});
				});
				jQuery('.loader-import-data').css({
					"opacity": "0",
					"visibility": "hidden",
				});
				jQuery('.cubewp-theme-importer-main .importer-tab.licensing').removeClass('active');
				jQuery('.cube-setup-grid-list .cube-setup-list-theme li.progress-list.plugins').addClass('active');
				jQuery('.cubewp-theme-importer-main .importer-tab.plugins').addClass('active');
			}else{
				jQuery('.importer-tab.failed .plugin-required-des').text(response.msg);
				jQuery('.loader-import-data').css({
					"opacity": "0",
					"visibility": "hidden",
				});
				jQuery('.cubewp-theme-importer-main .importer-tab.licensing').removeClass('active');
				jQuery('.cube-setup-grid-list .cube-setup-list-theme li.progress-list.failed').removeClass('hide');
				jQuery('.cube-setup-grid-list .cube-setup-list-theme li.progress-list.failed').addClass('active');
				jQuery('.cubewp-theme-importer-main .importer-tab.failed').addClass('active');
				jQuery('.importer-tab.failed .importer-failed-lists ul').append( '<li><a class="cwp_continues_import">Conitnue Import with CubeWP Framework</a></li>' );
			}
		}
	})
});
jQuery(document).ready(function() {
	jQuery(document).on('click', '.next-step-import', function() {
		jQuery('.loader-import-data').css({
			"opacity": "1",
			"visibility": "visible",
		});
		setTimeout(function() {
			jQuery('.loader-import-data').css({
				"opacity": "0",
				"visibility": "hidden",
			});
		}, 600);
		var class_active_tabs = jQuery('.cubewp-theme-importer-main .importer-tab');
		var this_active_tabs = jQuery('.cubewp-theme-importer-main .importer-tab.active');
		if (jQuery(class_active_tabs).hasClass = "active") {
			jQuery(class_active_tabs).removeClass('active');
			event.preventDefault();
			jQuery(this_active_tabs).next('.cubewp-theme-importer-main .importer-tab').addClass('active');
		}
		/******************** Steps headigs ********************/
		var step_next_headings = jQuery('.cube-setup-list-theme .progress-list');
		var step_next_headings_active = jQuery('.cube-setup-list-theme .progress-list.active');
		if (jQuery(step_next_headings).hasClass = "active") {
			event.preventDefault();
			jQuery(step_next_headings_active).next('.progress-list').addClass('active');
		}
	});
});
jQuery(document).ready(function() {
	jQuery(document).on('click', '.cwp_continues_import' , function(){
		jQuery('.cubewp-theme-importer-main .importer-tab.plugins').addClass('active');
		jQuery('.cubewp-theme-importer-main .importer-tab.failed').removeClass('active');
		jQuery('.cube-setup-grid-list .cube-setup-list-theme li.progress-list.plugins').addClass('active');
		jQuery('.cube-setup-grid-list .cube-setup-list-theme li.progress-list.failed').addClass('hide');
		return false;
		
	});
});
jQuery(document).ready(function() {
	jQuery(document).on('click', '.cubewp-theme-importer-main .importer-tab .cwp_import_demo' , function(){
		jQuery(this).append('<div class="loader"></div>');
		jQuery(this).addClass('processing');
	});
	jQuery(document).ajaxStop(function() {
		if( jQuery('.cubewp-theme-importer-main .importer-tab '+jQuery('.cube-theme-importer').data('selector')).hasClass('done')){
			jQuery('.cubewp-theme-importer-main .importer-tab .cwp_import_demo').find('.loader').remove();
			setTimeout(function() {		
				jQuery('.cubewp-theme-importer-main .importer-tab.dummy_content').removeClass('active');
				jQuery('.cubewp-theme-importer-main .importer-tab.success').addClass('active');
				jQuery('.cube-setup-grid-list .cube-setup-list-theme li.progress-list.completed').addClass('active');
				var seconds = 6;
				var interval = setInterval(function() {
					seconds = seconds - 1;
					jQuery('.importer-time.countdown').text(seconds);
					if(seconds == 0){
						clearInterval(interval);
					}
				}, 1000);
			}, 2500);
			setTimeout(function() {		
				window.location.replace(jQuery('.cube-theme-importer').data('redirect_url'));
			}, 8500);
		}
	});
});	
		
jQuery(document).ready(function() {
	jQuery(document).on('click', '#install-plugins' , function(){
		event.preventDefault();
		jQuery('.cwp_hide').css('display','none');
		var install = jQuery("input[name='cwp-plugins-installation']:checked:first");
		if ( install.length > 0) {
			jQuery(this).text( "Processing..." );
			jQuery(this).addClass( "disabled" );
		}
		jQuery('.cwp_skip_plugin').closest('li.progress-list').remove();
		var total = jQuery("input[name='cwp-plugins-installation']:checked").length;
		if( jQuery(this).data('cubeframework') == 'enabled' && jQuery("input[name='cwp-plugins-installation']:checked").length == 0 ){
			jQuery('.loader-import-data').css({
				"opacity": "1",
				"visibility": "visible",
			});
			if (jQuery('.cubewp-theme-importer-main .importer-tab').hasClass = "active") {
				jQuery('.cubewp-theme-importer-main .importer-tab').removeClass('active');
				jQuery('.cubewp-theme-importer-main .importer-tab.dummy_content').addClass('active');
				jQuery('.cube-setup-grid-list .cube-setup-list-theme li.progress-list.content').addClass('active');
			}
			setTimeout(function() {
				jQuery('.loader-import-data').css({
					"opacity": "0",
					"visibility": "hidden",
				});
			}, 600);
			
			
		}else if( jQuery("input[name='cwp-plugins-installation']:checked").length == 0 ){
			window.location.replace( window.location.href+'&cwp=success' );
			return false;
		}
		cwp_license_activate();
	});
});
function cwp_license_activate(){
	jQuery("input[name='cwp-plugins-installation']:checked").prop( "disabled", 1 );
	var $this = jQuery("input[name='cwp-plugins-installation']:checked:first");
	if ($this.length > 0) {
		$this.replaceWith( "<div class='loader'></div>" );
		slug = $this.val();
		cwp_source = $this.data('cwp_source');
		source = $this.data('source');
		base = $this.data('base');
		if( source || cwp_source == 'yes' ) {
			order_id = $this.data('order');
			license_key = $this.data('license');
			download_id = $this.data('download_id');
			jQuery.ajax({
				url: cwp_admin_license_params.ajax_url,
				type: 'POST',
				data: {
					'action': 'cwp_activate_license',
					'order_id': order_id,
					'slug': slug,
					'source': source,
					'base': base,
					'license_key': license_key,
					'download_id': download_id,
					'security_nonce': cwp_admin_license_params.security_nonce
				},
				dataType: "json",
				success: function (response) {
					jQuery('#parent-'+slug+' .check-icons').html('<img class="colored" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">');
					cwp_license_activate();
				}
			});
		}else{
			jQuery.ajax({
				url: cwp_admin_license_params.ajax_url,
				type: 'POST',
				data: {
					'action': 'cwp_activate_required_addons',
					'source': source,
					'slug': slug,
					'base': base,
					'security_nonce': cwp_admin_license_params.security_nonce
				},
				dataType: "json",
				success: function (response) {
					jQuery('#parent-'+slug+' .check-icons').html('<img class="colored" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAAABmJLR0QA/wD/AP+gvaeTAAAD30lEQVRIiaWWX0xbdRTHP+fXBlznFobRqUNniM5kLyM8IC9GjWUBtLs12iqlXYj/luiDD5rszdQ39dknEg3ZWmR0CqXgQFCWzZkMAvjq3IvC2JZg5tzcgJZ7fOgt3DIItPs+/s75/T73e+65v3ugDIV6qQj1UlHOXtkqIR7HXK6jUT3GElU/UAM84oSvA1dARhW7/5lpJuJx7LKB0TTNiHwOHHIt3wL+BHwCVQrVrtgMqscTFqMlAWMj7NRF6UJ4AwDlvIgmNEsm8TpX3bkdfTyVNTSJkXdQnssfKqd2iP12Z4A7WwKPZthn2zKIUIdyCfSjhMXw5nVYU/sALYJ8BdQC0znVQI/F/KZAx9kvCHXAmaVFDafC3N4OrKDIIHuMLUmgBZj2iT7vdmrcyU4Z6xAG5nbpkVJhAN2vcsN3VS0VxoD6O5ivN3ToNMgZlEtLS9qQCnOzVJhbHX1U5TwyCTytov5kgJ/AcRiPY5xuxKh+WC4s+h2PxQbMZx1DPNr1Gv+g+jGAqHyJ5s0ZgMt1NAKHUM6fCDJWDuytNI/jlbOKfprN4QdIWAwg/ArUx9I0rAIREwRQtKscWFuavV6RUYQDKKd3XuNUISZoN4DtMZYLqC8D6jGMlOPMg5wDDiKSmtutbZ3HyBbi2RUG8gj1rwHhSWDhRIAr9+PMN2+3n32JnDunJ8gscNthYJxL+CGBv0uFeUR+Bg6inPZd04jbWZGUeeDhUC8V3rW14ieLZWhVlR5V/SBpkXDHnDKOAwcQSc3tsiNnreL9RZJ8h+65gZpUmGVggfxfYFW2zU2gUkS62tNE3TAvMl4o49yDdmR9GTfQPmCh8xjZwjucBapi36/+dkhaXLDRMLBSgN4D261tW8EiQ+wHfMBfsNo0Mgpge7Dcyd1HSLuhXuRiKTAAkyMAoJJnGAAjdhpARNrXb3BDEWpKgeW95M80K3mGAaid4iLKb8AL0TTNG0EFPayqn5QCi2YIAo0CUyctJsB1ebdnOCwqI8Dv6tWGZCv/bsvBJgoNU125LJNA7T2XN0AywI8gvcCzkpOeUC+ecmEvjuOtXJYeoFaQ7gKsCAiwtGh3AJNAS+UDMtjRR1WpsNAw1TW35AegCZjZIfZ77ngRMBXmbk41CMwAzTmPTMQytG4XFs0QdMrYJDBFVl9ZP9dsOES9n8F3V803ir7pLF0Q0YTtYTDZypw7NzLEfpMj4HRjo3Pstz6x393WEOXW0X78tpEvgHrX8n8Cs6oYhBryH3XhsClb9Lj7nZUEhPw08Ec9DQZjKeoHngD2OuHrwKwgY9h2/0mLCQTd6sySdT+j/v+yRJpXixIM8gAAAABJRU5ErkJggg==">');
					cwp_license_activate();
				}
			});
		}
	}else {
		jQuery('#install-plugins').removeClass( "disabled" );
		jQuery('#install-plugins').text('Proceed with Dummy Content');
	}
}

jQuery(document).ready(function() {
	jQuery(document).on("change", "input[name='cwp-plugins-installation']" , function(){
		if( jQuery(this).is(':checked') ){
			if( jQuery(this).hasClass('cwp_skip_plugin') ){
				jQuery(this).removeClass('cwp_skip_plugin');
			}
		}else{
			jQuery(this).addClass('cwp_skip_plugin');
		}
		if( jQuery("input[name='cwp-plugins-installation']:checked").length > 0 ){
			jQuery('#install-plugins').removeClass('uncheck');
		}else{
			jQuery('#install-plugins').addClass('uncheck');
		}
	});
});