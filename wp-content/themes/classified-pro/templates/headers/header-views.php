<?php
defined( 'ABSPATH' ) || exit;

if ( ! has_shortcode( get_the_content(), 'cwp_dashboard' ) ) {
	if ( ! if_theme_can_load() ) {
		get_template_part( 'templates/headers/header-style-1' );
	} else {
		get_template_part( 'templates/headers/header-style-2' );
	}
}

get_template_part( 'templates/banners/banner-views' );