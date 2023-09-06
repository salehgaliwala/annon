<?php

/**
 * User Login frontend form shortcode.
 *
 * @version 1.0
 *
 * @package cubewp-addon-frontend/cube/classes/shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_User_Login
 */
class CubeWp_Frontend_User_Login {

	public function __construct() {
		add_shortcode( 'cwpLoginForm', array( $this, 'login_frontend_form' ) );
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	/**
	 * Method cwp_dashboard_callback
	 *
	 * @param null  $content
	 * @param array $params
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function login_frontend_form( $params = array(), $content = '' ) {
		if ( is_user_logged_in() ) {
			$logout_btn = '<a href="' . esc_url(wp_logout_url(get_permalink())) . '">' . esc_html__("Logout", "cubewp-frontend") . '</a>';
			return cwp_alert_ui(sprintf(esc_html__('You have already logged in. %s if you want to proceed.', "cubewp-frontend"), $logout_btn));
		}
		extract(shortcode_atts(
			array(
				'class' => 'cwp-user-login',
			), $params
		));
		CubeWp_Enqueue::enqueue_style( 'frontend-fields' );
		CubeWp_Enqueue::enqueue_style( 'cwp-login-register' );
		CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );
		CubeWp_Enqueue::enqueue_script( 'cwp-user-login' );

		$output = '<div class="cwp-frontend-form-container ' . esc_attr( $class ) . '">';
		$output .= '<div class="cwp-frontend-section-container">';
		$output .= '<form id="login-form" method="post" action="' . esc_url( get_permalink() ) . '">';
		$output .= '<div class="cwp-frontend-section-heading-container">';
		$output .= '<h2>' . esc_html__("Login", "cubewp-frontend") . '</h2>';
		$output .= '</div>';
		$output .= '<div class="cwp-frontend-section-content-container">';
		$login_fields = cubewp_user_login_fields();
		foreach ( $login_fields as $login_field ) {
			$output .= apply_filters( "cubewp/user/profile/{$login_field['type']}/field", '', $login_field );
		}
		$output .= '<p class="cwp-field-container">' . esc_html__("Forget Password?", "cubewp-frontend") . ' <a href="javascript:void();" class="cubewp-forget-password-form-trigger">' . esc_html__("Reset", "cubewp-frontend") . '</a></p>';
		$output .= wp_nonce_field( 'cubewp-login-nonce', 'security' );
		$output .= '<input type="submit" value="' . esc_html__("Login", "cubewp-frontend") . '">';
		$output .= '</div>';
		$output .= '</form>';
		$output .= '<form id="forget-password-form" method="post" action="' . esc_url( get_permalink() ) . '" style="display: none;">';
		$output .= '<div class="cwp-frontend-section-heading-container">';
		$output .= '<h2>' . esc_html__("Reset Password", "cubewp-frontend") . '</h2>';
		$output .= '</div>';
		$output .= '<div class="cwp-frontend-section-content-container">';
		$forget_fields = cubewp_forget_password_fields();
		foreach ( $forget_fields as $forget_field ) {
			$output .= apply_filters( "cubewp/user/profile/{$forget_field['type']}/field", '', $forget_field );
		}
		$output .= '<p class="cwp-field-container">' . esc_html__("Go Back To", "cubewp-frontend") . ' <a href="javascript:void();" class="cubewp-login-form-trigger">' . esc_html__("Login", "cubewp-frontend") . '</a></p>';
		$output .= wp_nonce_field( 'cubewp-forget-password-nonce', 'security' );
		$output .= '<input type="submit" value="' . esc_html__("Reset Password", "cubewp-frontend") . '">';
		$output .= '</div>';
		$output .= '</form>';
		$output .= '</div>';
		$output .= '</div>';


		return apply_filters( 'cubewp/user/login-forget/form', $output, $params );
	}
}