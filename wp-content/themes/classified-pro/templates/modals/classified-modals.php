<?php
defined( 'ABSPATH' ) || exit;

global $classified_modals;
$classified_include_modals = $classified_modals;
if ( ! is_user_logged_in() ) {
	get_template_part( 'templates/modals/classified-modal-login-register' );
}else {
	get_template_part( 'templates/modals/classified-modal-ad-type' );
}
if ( ! empty( $classified_include_modals ) && is_array( $classified_include_modals ) ) {
	foreach ( $classified_include_modals as $modal ) {
		if ( is_array( $modal ) ) {
			foreach ( $modal as $modal_name => $data ) {
				$type = 'post_id';
				$id = $data;
				if ( is_array( $data ) && ! empty( $data ) ) {
					$type = $data['type'];
					$id = $data['id'];
				}
				set_query_var( 'type', $type );
				set_query_var( 'id', $id );
				get_template_part( 'templates/modals/classified-modal-' . $modal_name );
			}
		} else {
			get_template_part( 'templates/modals/classified-modal-' . $modal );
		}
	}
}