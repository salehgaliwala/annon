<?php

/**
 * User Login Template.
 *
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
echo do_shortcode('[cwpLoginForm]');
wp_footer();