<?php
defined( 'ABSPATH' ) || exit;
global $post;
$post_id = $post->ID;

$banner_background = $subtitle_display = '';
if ( has_post_thumbnail( $post_id ) ) {
	$thumbnail_url = get_the_post_thumbnail_url( $post_id );
	$banner_background = 'style="background-image: url(\'' . esc_url( $thumbnail_url ) . '\');"';
}
$subtitle = get_post_meta( $post_id, 'classified_page_subtitle', true );
if ( ! empty( $subtitle ) && is_string( $subtitle ) ) {
    $subtitle_display = '<h3>' . esc_html( $subtitle ) . '</h3>';
}
?>
<div id="classified-banner" <?php echo wp_kses_post( $banner_background ); ?>>
    <div class="container">
        <div class="classified-banner-content">
            <h1><?php echo esc_html( $post->post_title ); ?></h1>
            <?php echo wp_kses_post( $subtitle_display ); ?>
        </div>
    </div>
</div>