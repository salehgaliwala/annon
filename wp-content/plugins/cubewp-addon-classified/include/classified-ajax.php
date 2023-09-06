<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'classified_boost_item_modal' ) ) {
	function classified_boost_item_modal() {
		if ( ! classified_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'classified_boost_item_modal_nonce' ) ) {
			wp_send_json_error( esc_html__( "Nonce Security Error", "cubewp-classified" ) );
		}
		$post_id = sanitize_text_field( $_POST['item_id'] );

		ob_start();
		set_query_var( 'id', $post_id );
		get_template_part( 'templates/modals/classified-modal-boost' );
		$modal = ob_get_clean();

		wp_send_json_success( $modal );
	}

	add_action( 'wp_ajax_classified_boost_item_modal', 'classified_boost_item_modal' );
}

if ( ! function_exists( 'classified_mark_item_sold' ) ) {
	function classified_mark_item_sold() {
		if ( classified_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'classified_mark_item_sold_nonce' ) ) {
			$post_id = sanitize_text_field( $_POST['item_id'] );
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				wp_send_json_error( esc_html__( "User Permission Error", "cubewp-classified" ) );
			}
			$post = array(
				'ID'          => $post_id,
				'post_status' => 'sold',
			);
			wp_update_post( $post );

			wp_send_json_success( esc_html__( "Item Marked As Sold", "cubewp-classified" ) );
		} else {
			wp_send_json_error( esc_html__( "Nonce Security Error", "cubewp-classified" ) );
		}
	}

	add_action( 'wp_ajax_classified_mark_item_sold', 'classified_mark_item_sold' );
}

if ( ! function_exists( 'classified_load_more_items' ) ) {
	function classified_load_more_items() {
		$page_num         = sanitize_text_field( $_POST['page-num'] ?? '' );
		$recommended      = sanitize_text_field( $_POST['recommended'] ?? '' );
		$boosted          = sanitize_text_field( $_POST['boosted'] ?? '' );
		$layout_style     = sanitize_text_field( $_POST['layout-style'] ?? '' );
		$col_class        = sanitize_text_field( $_POST['col-class'] ?? '' );
		$posts_per_page   = sanitize_text_field( $_POST['posts-per-page'] ?? '' );
		$categories_terms = sanitize_text_field( $_POST['categories-terms'] ?? '' );
		$post_types       = sanitize_text_field( $_POST['post-types'] ?? '' );
		$author           = sanitize_text_field( $_POST['author'] ?? '' );

		$args = array();
		if ( ! empty( $categories_terms ) ) {
			$args['categories_terms'] = explode( ',', $categories_terms );
		}
		if ( $recommended == 'yes' ) {
			$args['recommended'] = true;
		}
		$args['post_type']      = explode( ',', $post_types );
		$args['paged']          = $page_num;
		$args['posts_per_page'] = $posts_per_page;
		if ( ! empty( $author ) ) {
			$args['author'] = $author;
		}
		$query = ( new Classified_Query() )->Query( $args );
		ob_start();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				if ( ! empty( $layout_style ) ) {
					set_query_var( 'layout_style', $layout_style );
				}
				if ( ! empty( $col_class ) ) {
					set_query_var( 'col_class', $col_class );
				}
				if ( $recommended == 'yes' ) {
					set_query_var( 'recommended', true );
				} else {
					set_query_var( 'recommended', false );
				}
				if ( $boosted == 'yes' ) {
					set_query_var( 'boosted', true );
				} else {
					set_query_var( 'boosted', false );
				}
				get_template_part( 'templates/loop/loop-views' );
			}
		}
		wp_reset_postdata();
		wp_reset_query();
		$ui = ob_get_clean();
		wp_send_json( array(
			'type'         => 'success',
			'html'         => $ui,
			'current_page' => ( (int) $page_num + 1 ),
			'max_pages'    => $query->max_num_pages
		) );
	}

	add_action( 'wp_ajax_classified_load_more_items', 'classified_load_more_items' );
	add_action( 'wp_ajax_nopriv_classified_load_more_items', 'classified_load_more_items' );
}