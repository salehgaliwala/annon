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
 * CubeWp Social Logins Settings Class.
 *
 * @class CubeWp_Social_Logins_Settings
 */
class CubeWp_Social_Logins_Settings
{
	/**
	 * CubeWp_Social_Logins_Settings Constructor.
	 */
	public function __construct()
	{
		add_filter('cubewp/options/sections', array($this, 'cwp_sl_settings'));
		add_filter('cubewp/builder/user/custom/cubes', array($this, 'cubewp_social_login_cube') , 11);
	}

	/**
	 * Method cwp_sl_settings
	 *
	 * @param array $sections
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cwp_sl_settings($sections)
	{
		$sections['cubewp-social-logins'] = array(
			'title'  => __('Social Logins', 'cubewp-social-logins'),
			'id'     => 'cubewp-social-logins',
			'icon'   => 'dashicons dashicons-networking',
			'fields' => array(
				array(
					'id'      => 'cubewp_social_logins',
					'title'   => __('Social Logins', 'cubewp-social-logins'),
					'desc'    => __('Enable CubeWP Social Logins.', 'cubewp-social-logins'),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'cwp_sl_google',
					'title'   => __('Google', 'cubewp-social-logins'),
					'desc'    => __('Enable if you want to allow users to login or signup with google.', 'cubewp-social-logins'),
					'type'    => 'switch',
					'default' => '0',
					'required' => array(
						array('cubewp_social_logins', 'equals', '1')
					)
				),
				array(
					'id'      => 'cwp_sl_google_client_id',
					'title'   => __('Client ID', 'cubewp-social-logins'),
					'desc'    => __('Enter your google client id here', 'cubewp-social-logins'),
					'type'    => 'text',
					'default' => '',
					'required' => array(
						array('cwp_sl_google', 'equals', '1')
					)
				),
				array(
					'id'      => 'cwp_sl_google_client_secret',
					'title'   => __('Client Secret', 'cubewp-social-logins'),
					'desc'    => __('Enter your google client secret key here', 'cubewp-social-logins'),
					'type'    => 'text',
					'default' => '',
					'required' => array(
						array('cwp_sl_google', 'equals', '1')
					)
				),
				array(
					'id'      => 'cwp_sl_facebook',
					'title'   => __('Facebook', 'cubewp-social-logins'),
					'desc'    => __('Enable if you want to allow users to login or signup with facebook.', 'cubewp-social-logins'),
					'type'    => 'switch',
					'default' => '0',
					'required' => array(
						array('cubewp_social_logins', 'equals', '1')
					)
				),
				array(
					'id'      => 'cwp_sl_facebook_app_id',
					'title'   => __('APP ID', 'cubewp-social-logins'),
					'desc'    => __('Enter your facebook app id here', 'cubewp-social-logins'),
					'type'    => 'text',
					'default' => '',
					'required' => array(
						array('cwp_sl_facebook', 'equals', '1')
					)
				),
				array(
					'id'      => 'cwp_sl_facebook_app_secret',
					'title'   => __('APP Secret', 'cubewp-social-logins'),
					'desc'    => __('Enter your facebook app secret key here', 'cubewp-social-logins'),
					'type'    => 'text',
					'default' => '',
					'required' => array(
						array('cwp_sl_facebook', 'equals', '1')
					)
				),
				array(
					'id'      => 'cwp_sl_append_form_login',
					'title'   => __('Social Buttons Into Login Form', 'cubewp-social-logins'),
					'desc'    => __('Enable if you want to add social buttons for login form.', 'cubewp-social-logins'),
					'type'    => 'switch',
					'default' => '0',
					'required' => array(
						array('cubewp_social_logins', 'equals', '1')
					)
				),
				array(
					'id'      => 'cwp_sl_form_btn_placement',
					'title'   => __('Buttons Placement', 'cubewp-social-logins'),
					'desc'    => __('Select the placement of social login buttons.', 'cubewp-social-logins'),
					'type'    => 'select',
					'options' => array(
						'bottom' => esc_html__('After Form', 'cubewp-social-logins'),
						'top'    => esc_html__('Before Form', 'cubewp-social-logins'),
					),
					'default' => 'bottom',
					'required' => array(
						array('cubewp_social_logins', 'equals', '1')
					)
				),
				array(
					'id'      => 'cwp_sl_form_btn_layout',
					'title'   => __('Buttons Full Width Layout', 'cubewp-social-logins'),
					'desc'    => __('On = full width layout, Off = inline layout.', 'cubewp-social-logins'),
					'type'    => 'switch',
					'default' => '1',
					'required' => array(
						array('cubewp_social_logins', 'equals', '1')
					)
				),
			),
		);

		return $sections;
	}

	/**
     * Method cubewp_social_login_cube
     *
     * @return array
     * @since  1.0.11
     */
	public function cubewp_social_login_cube($fields) {
		$args = array(
			'social_login' => array(
				'label' =>  __("Social Logins", "cubewp-social-logins"),
				'name' =>  'social_login',
				'type' =>  'social_login',
			)
		);

		return array_merge($fields,$args);
	}

	public static function init()
	{
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}
