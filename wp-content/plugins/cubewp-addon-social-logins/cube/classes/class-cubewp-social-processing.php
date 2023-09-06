<?php
/**
 * CubeWp Social Logins
 *
 * @package cubewp-addon-social-logins/cube/classes
 * @version 1.0
 *
 */

 if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class CubeWp_Social_Processing
 */
class CubeWp_Social_Processing {

	/**
	 * CubeWp_Social_Processing Constructor.
	 */
	public function __construct() {
		if ( isset( $_GET['cubewp-social-login'] ) && ! empty( $_GET['cubewp-social-login'] ) ) {
			$method = sanitize_text_field( $_GET['cubewp-social-login'] );
			if ( method_exists( $this, 'cwp_sl_process_' . $method ) ) {
				$method = 'cwp_sl_process_' . $method;
				self::$method();
			}
		}
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'disconnect_social' ) {
			self::cwp_sl_disconnect_socials();
		}
	}

	/**
	 * Method cwp_sl_process_google
	 *
	 * @return void
	 * @since  1.0.0
	 */
	private static function cwp_sl_process_google() {
		$client_id     = cwp_sl_get_settings( 'cwp_sl_google_client_id' );
		$client_secret = cwp_sl_get_settings( 'cwp_sl_google_client_secret' );
		$endpoint_url  = 'https://oauth2.googleapis.com/token';
		$data          = array(
			'code'          => $_GET['code'],
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'redirect_uri'  => add_query_arg( array( 'cubewp-social-login' => 'google' ), CUBEWP_SOCIAL_REDIRECT),
			'grant_type'    => 'authorization_code',
		);
		$response      = wp_remote_post( $endpoint_url, array(
			'body' => $data,
		) );
		if ( ! is_wp_error( $response ) ) {
			$body   = wp_remote_retrieve_body( $response );
			$result = json_decode( $body );
			if ( isset( $result->access_token ) ) {
				$userinfo_url      = 'https://www.googleapis.com/oauth2/v1/userinfo';
				$headers           = array(
					'Authorization' => 'Bearer ' . $result->access_token,
				);
				$userinfo_response = wp_remote_get( $userinfo_url, array(
					'headers' => $headers,
				) );
				if ( ! is_wp_error( $userinfo_response ) ) {
					$userinfo_body = wp_remote_retrieve_body( $userinfo_response );
					$userinfo      = json_decode( $userinfo_body );
					$args          = array(
						'id'     => $userinfo->id,
						'email'  => $userinfo->email,
						'name'   => $userinfo->name,
						'method' => 'google'
					);
					self::cwp_sl_process_social_return( $args );
				}
			}
		}
	}

	/**
	 * Method cwp_sl_process_facebook
	 *
	 * @return void
	 * @since  1.0.0
	 */
	private static function cwp_sl_process_facebook() {
		$app_id             = cwp_sl_get_settings( 'cwp_sl_facebook_app_id' );
		$app_secret         = cwp_sl_get_settings( 'cwp_sl_facebook_app_secret' );
		$endpoint           = 'https://graph.facebook.com/v11.0/oauth/access_token';
		$endpoint_args      = array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(
				'client_id'     => $app_id,
				'client_secret' => $app_secret,
				'code'          => $_GET['code'],
				'redirect_uri'  => add_query_arg( array( 'cubewp-social-login' => 'facebook' ), CUBEWP_SOCIAL_REDIRECT.('/') ),
			),
			'cookies'     => array()
		);
		$response           = wp_remote_post( $endpoint, $endpoint_args );
		$response_body      = wp_remote_retrieve_body( $response );
		$response_obj       = json_decode( $response_body );
		$access_token       = $response_obj->access_token;
		$fields             = 'email,name,id';
		$user_info_url      = 'https://graph.facebook.com/v11.0/me?fields=' . $fields . '&access_token=' . $access_token;
		$user_info_args     = array(
			'method'      => 'GET',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(),
			'cookies'     => array()
		);
		$user_info_response = wp_remote_post( $user_info_url, $user_info_args );
		$user_info_json     = wp_remote_retrieve_body( $user_info_response );
		$user_info          = json_decode( $user_info_json );
		$args               = array(
			'id'     => $user_info->id,
			'email'  => $user_info->email ?? '',
			'name'   => $user_info->name,
			'method' => 'facebook'
		);
		self::cwp_sl_process_social_return( $args );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	/**
	 * Method cwp_sl_process_social_return
	 *
	 * @param array $args
	 *
	 * @return null
	 * @since  1.0.0
	 */
	private static function cwp_sl_process_social_return( $args ) {
		$id           = sanitize_text_field( $args['id'] );
		$email        = sanitize_email( $args['email'] );
		$name         = sanitize_text_field( $args['name'] );
		$method       = sanitize_text_field( $args['method'] );
		$user_id      = false;
		$update_metas = false;
		if ( ! is_user_logged_in() ) {
			$user = get_users( array(
					'meta_key'     => 'cubewp_' . $method . '_login_id',
					'meta_value'   => $id,
					'meta_compare' => '=',
					'number'       => 1,
				) );
			if ( ! empty( $user ) && is_array( $user ) ) {
				$user    = $user[0];
				$user_id = $user->ID;
			} else {
				if ( empty( $email ) ) {
					$email = sanitize_email( $id . '@' . $method . '.com' );
				}
				$user = get_user_by( 'email', $email );
				if ( $user ) {
					$user_id = $user->ID;
				} else {
					$user_name = sanitize_user( $name, true );
					if ( username_exists( $user_name ) ) {
						$user_name = $user_name . '-' . $id;
					}
					$new_user_data = array(
						'user_login'    => $user_name,
						'user_email'    => $email,
						'user_pass'     => wp_generate_password(),
						'display_name'  => $name,
						'user_nicename' => $name,
						'nickname'      => $name,
						'role'          => 'subscriber',
					);
					$user_id       = wp_insert_user( $new_user_data );
				}
				$update_metas = true;
			}
			if ( $user_id && ! is_wp_error( $user_id ) ) {
				wp_set_auth_cookie( $user_id );
			}
		}
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		if ( $update_metas ) {
			update_user_meta( $user_id, 'cubewp_' . $method . '_login_id', $id );
		}
		wp_redirect( esc_url( CUBEWP_SOCIAL_REDIRECT ) );
		exit;
	}

	/**
	 * Method cwp_sl_disconnect_socials
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	private static function cwp_sl_disconnect_socials() {
		if ( wp_verify_nonce( sanitize_text_field( $_GET['nonce'] ), 'cwp_sl_disconnect_nonce' ) ) {
			$method  = sanitize_text_field( $_GET['method'] );
			$user_id = sanitize_text_field( $_GET['user_id'] ?? get_current_user_id() );
			if ( ! empty( $method ) ) {
				delete_user_meta( $user_id, 'cubewp_facebook_login_id' );

				echo cwp_alert_ui( esc_html__( 'Social Login Disconnected.' ), 'success' );
				header( "refresh:3;url=" . CUBEWP_SOCIAL_REDIRECT );
				return true;
			}
		}

		echo cwp_alert_ui( esc_html__( 'Something went wrong! Try again later.' ) );
		header( "refresh:3;url=" . CUBEWP_SOCIAL_REDIRECT );
		return false;
	}
}