<?php
defined( 'ABSPATH' ) || exit;

$author_style = get_query_var( 'author-style','author-style-2' );
if ( $author_style == 'author-style-1' ) {
	wp_enqueue_style( 'classified-author-1-styles' );
	wp_enqueue_script( 'classified-author-1-scripts' );
	get_template_part( 'templates/author/author-style-1' );
}else {
	wp_enqueue_style( 'classified-author-2-styles' );
	wp_enqueue_script( 'classified-author-2-scripts' );
	get_template_part( 'templates/author/author-style-2' );
}