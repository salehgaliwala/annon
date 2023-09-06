<?php
function cwp_license_verification_callback() {

	if ( isset( $_POST['security_nonce'] ) && wp_verify_nonce( $_POST['security_nonce'], 'cubewp-admin-nonce' ) ) {
		$paramters      = array();
		$license_key    = sanitize_text_field( $_POST['cwp_license_key'] );
		$cwp_user_email = sanitize_text_field( $_POST['cwp_user_email'] );
		$username       = $first_name = $last_name = $display_name = '';

		//checking if user did not added any email other than admin then it will get admin data.
		if ( empty( $cwp_user_email ) ) {
			$user_data      = wp_get_current_user();
			$cwp_user_email = $user_data->user_email;
			$username       = $user_data->user_login;
			$display_name   = $user_data->display_name;
			$first_name     = get_the_author_meta( 'first_name', $user_data->ID );
			$last_name      = get_the_author_meta( 'last_name', $user_data->ID );
		}
		$paramters   = array(
			'user_login'     => $username,
			'license_key'    => $license_key,
			'vendor_profile' => 'cridiostudio',
			'user_email'     => $cwp_user_email,
			'display_name'   => $display_name,
			'site_url'       => site_url(),
			'first_name'     => $first_name,
			'last_name'      => $last_name,
		);
		$api_url     = 'https://cubewp.com'; //route of api call which is cubewp website.
		$request_url = $api_url . '/wp-json/cubewp_license_verification/verifier/v1';
		$args        = array(
			'method'  => "POST",
			'timeout' => 100,
			'body'    => json_encode( $paramters ),
			'headers' => array(
				'accept'       => 'application/json',
				'content-type' => 'application/json'
			),
		);
		$response    = wp_remote_post( $request_url, $args );

		$res = '';
		if ( is_wp_error( $response ) ) {
			$response = array(
				'status' => 'error',
				'msg'    => esc_html__( 'There is error with url or parameters', 'classified-pro' )
			);
			wp_send_json( $response );
		}
		if ( isset( $response['body'] ) ) {
			$body = json_decode( $response['body'] );
		}
		if ( isset( $body->error ) ) {
			$response = array( 'status' => 'error', 'msg' => $body->error );
			wp_send_json( $response );
		} else {
			//if purchase key valid and data is returned
			$addons = array( 'cubewp-framework' );
			$valid  = false;
			if ( isset( $body->licenses_data ) && ! empty( $body->licenses_data ) ) {
				foreach ( $body->licenses_data as $license_key => $response ) {
					foreach ( $response as $slug => $data ) {
						if ( $data->license == 'valid' ) {
							//saving all data to cubewp options to sync for future updates
							$valid = true;
							update_option( $slug, $data );
							update_option( $slug . '_key', $license_key );
							update_option( $slug . '-status', $data->license );
							$addons[] = $slug;
						}
					}
				}
				if ( $valid ) {
					update_option( 'purchase-code', $license_key );
					update_option( 'associated-addons', $addons );
				}
			}
			if ( isset( $body->addon_licenses ) && ! empty( $body->addon_licenses ) ) {
				foreach ( $body->addon_licenses as $order_id => $licenses ) {
					foreach ( $licenses as $slug => $license ) {
						$base      = str_replace( '-addon', '', str_replace( '-pro', '', $slug ) );
						$file_name = str_replace( '-', ' ', $base );
						$file_name = str_replace( 'cubewp', 'CubeWp', $file_name );
						$base      = str_replace( ' ', '_', ucwords( $file_name ) );
						if ( class_exists( $base . '_Load' ) ) { // if a plugin is already activated
							$body->addon_licenses->$order_id->$slug->activated = 'yes';
						} else if ( isset( $license->invalid ) && ! empty( $license->invalid ) ) {
							$res .= ucwords( str_replace( '-', ' ', $slug ) ) . ' ' . str_replace( '_', ' ', $license->invalid ) . ',';
						}
					}
				}
				$response = array( 'status' => 'success', 'data' => $body->addon_licenses );
				wp_send_json( $response );
			}

		}
	} else {
		$response = array( 'status' => 'error', 'msg' => 'Invalid nonce specified' );
		wp_send_json( $response );
	}
}

// callback function of each plugin activation if license is valid
function cwp_activate_license_cb() {
	if ( isset( $_POST['security_nonce'] ) && wp_verify_nonce( $_POST['security_nonce'], 'cubewp-admin-nonce' ) ) {
		$slug = sanitize_text_field( $_POST['slug'] );
		if ( isset( $order_id ) && ! empty( $order_id ) ) {
			$order_id = sanitize_text_field( $_POST['order_id'] );
		} else {
			$order_id = '';
		}
		if ( isset( $_POST['order_id'] ) && ! empty( $_POST['order_id'] ) ) {
			$download_id = sanitize_text_field( $_POST['download_id'] );
		} else {
			$download_id = '';
		}
		if ( isset( $_POST['license_key'] ) && ! empty( $_POST['license_key'] ) ) {
			$license_key = sanitize_text_field( $_POST['license_key'] );
		} else {
			$license_key = '';
		}
		$source    = sanitize_text_field( isset( $_POST['source'] ) && ! empty( $_POST['source'] ) ? $_POST['source'] : null );
		$base      = sanitize_text_field( isset( $_POST['base'] ) && ! empty( $_POST['base'] ) ? $_POST['base'] : null );
		$license   = array( 'key' => $license_key, 'download_id' => $download_id );
		$license   = (object) $license;
		$pluginDir = WP_PLUGIN_DIR . '/' . $slug;
		if ( ! file_exists( $pluginDir ) ) {
			// get download link from route using helper function 
			if ( ! empty( $source ) ) {
				$download_link = $source;
			} else {
				$download_link = cwp_get_item_download_link( $license );
			}
			if ( $download_link ) {
				cwp_plugin_activate( $download_link, $slug, $order_id, $base );
			} else {
				$res = esc_html__( 'Failed to download file/No file founded!', 'classified-pro' );
				wp_send_json( $res );
			}
		} else {
			if ( isset( $_POST['source'] ) && ! empty( $_POST['source'] ) ) {
				activate_plugin( $pluginDir . '/' . $base . '.php' );
			} else {
				$base      = str_replace( '-addon', '', str_replace( '-pro', '', $slug ) );
				$file_name = str_replace( '-', ' ', $base );
				$file_name = str_replace( 'cubewp', 'CubeWp', $file_name );
				$class     = str_replace( ' ', '_', ucwords( $file_name ) );
				if ( ! class_exists( $class . '_Load' ) ) {
					activate_plugin( $pluginDir . '/' . $base . '.php' );
				}
			}
		}

		$res = ucwords( str_replace( '-', ' ', $slug ) ) . esc_html__( ' Activated!', 'classified-pro' );
		$res = str_replace( 'Addon', '', $res );
		wp_send_json( $res );
	}
}

//callback function for cubewp framework activation
function cwp_activate_required_addons_cb() {
	if ( isset( $_POST['security_nonce'] ) && wp_verify_nonce( $_POST['security_nonce'], 'cubewp-admin-nonce' ) ) {
		$cubewp_plugin = WP_PLUGIN_DIR . '/' . $_POST['slug'];
		$base          = $_POST['base'];
		if ( empty( $base ) && $_POST['slug'] == 'cubewp-framework' ) {
			$base = 'cube';
		}
		if ( ! file_exists( $cubewp_plugin ) ) {
			// activate cubewp framework using helper function 
			cwp_activate_directory_plugin( $_POST['slug'], $base );
		} else {
			activate_plugin( $cubewp_plugin . '/' . $base . '.php' );
		}
	}
}