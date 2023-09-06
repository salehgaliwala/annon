<?php
defined( 'ABSPATH' ) || exit;

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$post_id = get_the_ID();
		do_action( 'cubewp_single_page_notification', $post_id );
		$post_type = get_post_type( $post_id );
		if ( is_cubewp_single_page_builder_active( $post_type ) ) {
			echo cubewp_single_page_builder_output( $post_type );
		} else {
			if ( $post_type == 'post' ) {
				get_template_part( 'templates/single/blog-style-1' );
			} else if ( if_theme_can_load() ) {
				get_template_part( 'templates/single/single-style-1' );
			}
		}
		do_action( 'cubewp_post_confirmation', $post_id );
	}
}