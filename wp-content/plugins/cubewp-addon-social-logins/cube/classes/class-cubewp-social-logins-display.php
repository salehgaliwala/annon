<?php

/**
 * CubeWp Social Logins
 *
 * @package cubewp-addon-social-logins/cube/classes
 * @version 1.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp Social Logins Display Class.
 *
 * @class CubeWp_Social_Logins_Display
 */
class CubeWp_Social_Logins_Display {

	/**
	 * CubeWp_Social_Logins_Display Constructor.
	 */
	public function __construct() {
		add_filter( 'frontend/style/register', array( $this, 'cwp_sl_register_frontend_styles' ) );
		add_shortcode( 'cubewp-social-logins', array( $this, 'cubewp_social_logins_callback' ) );
		$add_social_into_form = cwp_sl_get_settings( 'cwp_sl_append_form_login' );
		if ( $add_social_into_form ) {
			add_filter( 'cubewp/user/login-forget/form', array( $this, 'cwp_sl_login_display' ) );
		}
		add_filter( 'cubewp/user/registration/social_login/field', array(
			$this,
			'cubewp_social_login_field_output'
		), 12, 2 );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	/**
	 * Method cubewp_social_logins_callback
	 *
	 * @param array $attrs
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cubewp_social_logins_callback( $attrs ) {
		$type     = $attrs['type'] ?? false;
		$google   = cwp_sl_get_settings( 'cwp_sl_google' );
		$facebook = cwp_sl_get_settings( 'cwp_sl_facebook' );
		if ( ! $google && ! $facebook ) {
			return cwp_alert_ui( esc_html__( 'Please Configure Social Login From CubeWP Settings', 'cubewp-social-logins' ) );
		}
		$social_placement_in_form = cwp_sl_get_settings( 'cwp_sl_form_btn_placement', 'bottom' );
		$layout                   = cwp_sl_get_settings( 'cwp_sl_form_btn_layout' );
		$layout                   = $layout ? 'cubewp-social-logins-full' : '';
		$disconnect               = false;
		if ( $type == 'login' ) {
			$google_text = $facebook_text = esc_html__( 'Login With', 'cubewp-social-logins' );
		} else if ( empty( $type ) ) {
			$user_id       = get_current_user_id();
			$google_meta   = cwp_check_if_sl_connected( 'google', $user_id );
			$facebook_meta = cwp_check_if_sl_connected( 'facebook', $user_id );
			if ( $google_meta ) {
				$google_text = esc_html__( 'Disconnect', 'cubewp-social-logins' );
				$disconnect  = true;
			} else {
				$google_text = esc_html__( 'Connect', 'cubewp-social-logins' );
			}
			if ( $facebook_meta ) {
				$facebook_text = esc_html__( 'Disconnect', 'cubewp-social-logins' );
				$disconnect    = true;
			} else {
				$facebook_text = esc_html__( 'Connect', 'cubewp-social-logins' );
			}
		} else {
			$google_text = $facebook_text = sprintf( esc_html__( '%s With', 'cubewp-social-logins' ), $type );
		}
		ob_start();
		wp_enqueue_style( 'cubewp-social-logins' );
		?>
		<div
			class="cubewp-social-logins cubewp-social-logins-placement-<?php echo esc_attr( $social_placement_in_form ); ?> <?php echo esc_attr( $layout ); ?>">
			<?php
			if ( $social_placement_in_form == 'bottom' ) {
				?>
				<div class="cubewp-social-login-separator">
					<span>
						<?php esc_html_e( 'Or', 'cubewp-social-logins' ); ?>
					</span>
				</div>
				<?php
			}
			if ( $google ) {
				?>
				<div class="cubewp-social-login">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16px" height="16px"><path fill="#fbbb00" d="M113.47 309.408 95.648 375.94l-65.139 1.378C11.042 341.211 0 299.9 0 256c0-42.451 10.324-82.483 28.624-117.732h.014L86.63 148.9l25.404 57.644c-5.317 15.501-8.215 32.141-8.215 49.456.002 18.792 3.406 36.797 9.651 53.408z"></path><path fill="#518ef8" d="M507.527 208.176C510.467 223.662 512 239.655 512 256c0 18.328-1.927 36.206-5.598 53.451-12.462 58.683-45.025 109.925-90.134 146.187l-.014-.014-73.044-3.727-10.338-64.535c29.932-17.554 53.324-45.025 65.646-77.911h-136.89V208.176h245.899z"></path><path fill="#28b446" d="m416.253 455.624.014.014C372.396 490.901 316.666 512 256 512c-97.491 0-182.252-54.491-225.491-134.681l82.961-67.91c21.619 57.698 77.278 98.771 142.53 98.771 28.047 0 54.323-7.582 76.87-20.818l83.383 68.262z"></path><path fill="#f14336" d="m419.404 58.936-82.933 67.896C313.136 112.246 285.552 103.82 256 103.82c-66.729 0-123.429 42.957-143.965 102.724l-83.397-68.276h-.014C71.23 56.123 157.06 0 256 0c62.115 0 119.068 22.126 163.404 58.936z"></path></svg>
					<p><?php echo sprintf( esc_html__( '%s Google', 'cubewp-social-logins' ), $google_text ); ?></p>
					<a href="<?php echo esc_url( self::cwp_sl_get_endpoint_url( 'google', $disconnect ) ); ?>"></a>
				</div>
				<?php
			}
			if ( $facebook ) {
				?>
				<div class="cubewp-social-login">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="16px" height="16px"><path fill="#1877f2" d="M1024,512C1024,229.23016,794.76978,0,512,0S0,229.23016,0,512c0,255.554,187.231,467.37012,432,505.77777V660H302V512H432V399.2C432,270.87982,508.43854,200,625.38922,200,681.40765,200,740,210,740,210V336H675.43713C611.83508,336,592,375.46667,592,415.95728V512H734L711.3,660H592v357.77777C836.769,979.37012,1024,767.554,1024,512Z"></path><path fill="#fff" d="M711.3,660,734,512H592V415.95728C592,375.46667,611.83508,336,675.43713,336H740V210s-58.59235-10-114.61078-10C508.43854,200,432,270.87982,432,399.2V512H302V660H432v357.77777a517.39619,517.39619,0,0,0,160,0V660Z"></path></svg>
					<p><?php echo sprintf( esc_html__( '%s Facebook', 'cubewp-social-logins' ), $facebook_text ); ?></p>
					<a href="<?php echo esc_url( self::cwp_sl_get_endpoint_url( 'facebook', $disconnect ) ); ?>"></a>
				</div>
				<?php
			}
			if ( $social_placement_in_form == 'top' ) {
				?>
				<div class="cubewp-social-login-separator">
					<span>
						<?php esc_html_e( 'Or', 'cubewp-social-logins' ); ?>
					</span>
				</div>
				<?php
			}
			?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Method cwp_sl_get_endpoint_url
	 *
	 * @param mixed $social_type
	 * @param bool $disconnect
	 *
	 * @return string|bool
	 * @since  1.0.0
	 */
	public static function cwp_sl_get_endpoint_url( $social_type, $disconnect = false ) {
		if ( $disconnect ) {
			return add_query_arg( array(
				'action'  => 'disconnect_social',
				'method'  => $social_type,
				'user_id' => get_current_user_id(),
				'nonce'   => wp_create_nonce( 'cwp_sl_disconnect_nonce' ),
			), esc_url( CUBEWP_SOCIAL_REDIRECT ) );
		} else {
			if ( $social_type == 'google' ) {
				$client_id    = cwp_sl_get_settings( 'cwp_sl_google_client_id' );
				$endpoint_url = 'https://accounts.google.com/o/oauth2/auth';

				return add_query_arg( array(
					'client_id'     => $client_id,
					'redirect_uri'  => add_query_arg( array( 'cubewp-social-login' => 'google' ), CUBEWP_SOCIAL_REDIRECT ),
					'scope'         => 'email+profile',
					'response_type' => 'code',
					'state'         => array(),
				), esc_url( $endpoint_url ) );
			} else if ( $social_type == 'facebook' ) {
				$app_id       = cwp_sl_get_settings( 'cwp_sl_facebook_app_id' );
				$endpoint_url = 'https://www.facebook.com/v11.0/dialog/oauth';

				return add_query_arg( array(
					'client_id'    => $app_id,
					'redirect_uri' => add_query_arg( array( 'cubewp-social-login' => 'facebook' ), CUBEWP_SOCIAL_REDIRECT ),
					'scope'        => 'email,public_profile'
				), esc_url( $endpoint_url ) );
			}
		}

		return false;
	}

	/**
	 * Method cwp_sl_register_frontend_styles
	 *
	 * @param array $styles
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function cwp_sl_register_frontend_styles( $styles ) {
		return array_merge( $styles, array(
			'cubewp-social-logins' => array(
				'src'     => CWP_SL_PLUGIN_URL . 'cube/assets/css/cubewp-social-logins.css',
				'deps'    => array(),
				'version' => CWP_SL_PLUGIN_VERSION,
				'has_rtl' => false,
			)
		) );
	}

	/**
	 * Method cwp_sl_login_display
	 *
	 * @param string $output
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cwp_sl_login_display( $output ) {
		$social_placement_in_form = cwp_sl_get_settings( 'cwp_sl_form_btn_placement', 'bottom' );
		ob_start();
		echo do_shortcode( '[cubewp-social-logins type="login"]' );
		if ( $social_placement_in_form == 'bottom' ) {
			$output .= ob_get_clean();
		} else {
			$output = ob_get_clean() . $output;
		}

		return $output;
	}

	/**
	 * Method cubewp_social_login_field_output
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cubewp_social_login_field_output( $args ) {
		$output = '<div class="cwp-field-container cwp-field-social-login">';
		$output .= do_shortcode( '[cubewp-social-logins type="continue"]' );
		$output .= '</div>';

		return $output;
	}
}
