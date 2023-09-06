<?php
defined('ABSPATH') || exit;

/**
 * Method cwp_check_if_sl_connected
 *
 * @param mixed $method
 * @param bool $user_id
 *
 * @return mixed
 * @since  1.0.0
 */
if (!function_exists('cwp_check_if_sl_connected')) {
	function cwp_check_if_sl_connected($method, $user_id = false)
	{
		if (!$user_id) {
			$user_id = get_current_user_id();
		}
		if (!empty($method)) {
			$id = get_user_meta($user_id, 'cubewp_' . $method . '_login_id', true);

			return !empty($id);
		}

		return false;
	}
}

/**
 * Method cwp_sl_get_settings
 *
 * @param string $setting
 * @param bool $default
 *
 * @return mixed
 * @since  1.0.0
 */
if (!function_exists('cwp_sl_get_settings')) {
	function cwp_sl_get_settings($setting = '', $default = false)
	{
		global $cwpOption;
		if (empty($cwpOption)) {
			$cwpOption = get_option('cwpOptions');
		}
		if ($setting) {
			return $cwpOption[$setting] ?? $default;
		}

		return $cwpOption;
	}
}

/**
 * Method cube_cubewp_social_login
 *
 * @param array $args
 *
 * @return string
 * @since  1.0.0
 */
if (!function_exists('cubewp_social_login_custom_field_output')) {
    function cubewp_social_login_custom_field_output($args)
    {
		$output = '';
		$output .= '<div class="cwp-field-container cwp-field-social-login ' . $args['container_class'] . '">';
		$output .= do_shortcode('[cubewp-social-logins type="login"]');
		$output .= '</div>';
		return $output;

	}
	add_filter('cubewp/frontend/cubewp_social_login/field', 'cubewp_social_login_custom_field_output', 12 , 2);
}