<?php
defined('ABSPATH') || exit;

$queried_object = get_queried_object();
$post_content   = '';
if (isset($queried_object) && is_object($queried_object)) {
	$post_content = $queried_object->post_content ?? '';
}

if ( ! has_shortcode($post_content, 'cwp_dashboard')) {
    if ( ! if_theme_can_load()) {
        get_template_part('templates/footers/footer-style-1');
    }else {
        get_template_part('templates/footers/footer-style-2');
    }
}

if (if_theme_can_load()) {
    get_template_part('templates/headers/off-canvas-menu');
    get_template_part('templates/modals/classified-modals');
}