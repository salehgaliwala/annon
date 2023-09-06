<?php
defined('ABSPATH') || exit;

if ( if_theme_can_load() ) {
	if ( classified_is_archive() ) {
		get_template_part('templates/archive/archive-style-1');
	}else {
		get_template_part('templates/archive/blog-archive-style-1');
	}
}else {
	get_template_part('templates/archive/blog-archive-style-1');
}