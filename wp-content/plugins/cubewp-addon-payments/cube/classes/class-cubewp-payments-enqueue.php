<?php

/**
 * Script and styles enqueue for payment system.
 *
 * @package cubewp-addon-payments/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Payments_Enqueue
 */
class CubeWp_Payments_Enqueue {

	public function __construct() {
		add_filter( 'frontend/style/register', array( $this, 'register_frontend_styles' ) );
		add_filter( 'admin/script/register', array( $this, 'register_admin_scripts' ) );
		add_filter( 'admin/script/enqueue', array( $this, 'load_admin_scripts' ) );
		add_filter( 'frontend/script/register', array( $this, 'register_frontend_scripts' ) );
		add_filter( 'get_frontend_script_data', array( $this, 'get_frontend_script_data' ), 10, 2 );
	}
	
	/**
	 * Method register_frontend_styles
	 *
	 * @param array $styles
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_frontend_styles( $styles ) {
		$register_styles = array(
			'cubewp-plans' => array(
				'src'     => CUBEWP_PAYMENTS_PLUGIN_URL . 'cube/assets/frontend/css/cubewp-plans.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
		);

		return array_merge( $register_styles, $styles );
	}
	
	/**
	 * Method register_admin_scripts
	 *
	 * @param array $script
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_admin_scripts( $script ) {
		$register_scripts = array(
			'cubewp-payments-admin' => array(
				'src'     => CUBEWP_PAYMENTS_PLUGIN_URL . 'cube/assets/admin/js/cubewp-payments-admin-scripts.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			)
		);

		return array_merge( $script, $register_scripts );
	}
	
	/**
	 * Method load_admin_scripts
	 *
	 * @param array $data
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public static function load_admin_scripts( $data ) {
		CubeWp_Enqueue::enqueue_script( 'cubewp-payments-admin' );
	}
	
	/**
	 * Method get_frontend_script_data
	 *
	 * @param string $data
	 * @param string $handle contains script handles
	 *
	 * @return array
	 * @since  1.0.6
	 */
	public static function get_frontend_script_data( $data, $handle ) {
		if ( $handle == 'cubewp-payments-dashboard-scripts' ) {
			return array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'dispute_nonce' => wp_create_nonce( 'cubewp-payments-make-dispute' ),
				'empty_error_msg' => esc_html__( 'Please enter details about dispute.', 'cubewp-payments' ),
				'error_msg' => esc_html__( 'Something went wrong try again later.', 'cubewp-payments' )
			);
		}

		return $data;
	}

	/**
	 * Method register_frontend_scripts
	 *
	 * @param array $styles
	 *
	 * @return array
	 * @since  1.0.6
	 */
	public static function register_frontend_scripts( $scripts ) {
		$register_scripts = array(
			'cubewp-payments-dashboard-scripts' => array(
				'src'     => CUBEWP_PAYMENTS_PLUGIN_URL . 'cube/assets/frontend/js/cubewp-payments-dashboard-scripts.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
		);

		return array_merge( $register_scripts, $scripts );
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
}