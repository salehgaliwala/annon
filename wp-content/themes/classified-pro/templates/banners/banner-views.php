<?php
defined( 'ABSPATH' ) || exit;
global $post;
$banner = get_post_meta( $post->ID ?? 0, 'classified_page_banner', true );
if ( empty( $banner ) ) {
	$banner = 'No';
}
if ( $banner == 'Yes' ) {
	$show_banner = true;
}else {
	$show_banner = false;
}

if ( $show_banner ) {
	get_template_part( 'templates/banners/banner-style-1' );
}