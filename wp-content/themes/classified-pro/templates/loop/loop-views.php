<?php
defined( 'ABSPATH' ) || exit;
if ( if_theme_can_load() ) {
	$layout_style = 'style-1';
	$layout_style = get_query_var( 'layout_style', $layout_style );
	if ( $layout_style == 'style-1' ) {
		set_query_var( 'focused', false );
		wp_enqueue_style( 'classified-loop-style1-styles' );
		get_template_part( 'templates/loop/loop-style-1' );
	} else if ( $layout_style == 'style-1-focused' ) {
		set_query_var( 'focused', true );
		wp_enqueue_style( 'classified-loop-style1-styles' );
		get_template_part( 'templates/loop/loop-style-1' );
	} else if ( $layout_style == 'style-2' ) {
		wp_enqueue_style( 'classified-loop-style2-styles' );
		get_template_part( 'templates/loop/loop-style-2' );
	}  else if ( $layout_style == 'cubewp-loop-builder' ) {
		if ( function_exists( 'cubewp_get_loop_builder_by_post_type' ) ) {
			$post_id        = get_query_var( 'post_id', get_the_ID() );
			$col_class      = get_query_var( 'col_class', 'col-12 col-md-6 col-lg-4' );
			$dynamic_layout = cubewp_get_loop_builder_by_post_type( get_post_type( $post_id ) );
			if ( ! empty( $dynamic_layout ) ) {
				?>
				<div <?php post_class( $col_class ); ?>>
					<?php
					echo cubewp_core_data( $dynamic_layout );
					?>
				</div>
				<?php

				return true;
			}
		}
	} else {
		cwp_pre( esc_html__( 'Invalid layout style.', 'classified-pro' ) );
	}
}