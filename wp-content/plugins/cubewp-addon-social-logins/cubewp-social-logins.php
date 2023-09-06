<?php
/**
 * Plugin Name: CubeWP Social Logins
 * Plugin URI:
 * Description: CubeWP Social Login, a CubeWP Framework extension, enables visitors to log in or sign up with their social media accounts.
 * Version: 1.0.1
 * Author: CubeWP
 * Author URI: https://cubewp.com
 * Text Domain: cubewp-social-logins
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit;

/**
 * CWP_SL_PLUGIN_VERSION is defined for current Plugin version
 */
if ( ! defined( 'CWP_SL_PLUGIN_VERSION' ) ) {
	define( 'CWP_SL_PLUGIN_VERSION', '1.0.1' );
}

/**
 * CWP_SL_PLUGIN_PATH Defines for load Php files
 */
if ( ! defined( 'CWP_SL_PLUGIN_PATH' ) ) {
	define( 'CWP_SL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * CWP_SL_PLUGIN_URL Defines for load JS and CSS files
 */
if ( ! defined( 'CWP_SL_PLUGIN_URL' ) ) {
	define( 'CWP_SL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/* CUBEWP_SL_PLUGIN_FILE Defines for file access */
if ( ! defined( 'CUBEWP_SL_PLUGIN_FILE' ) ) {
	define( 'CUBEWP_SL_PLUGIN_FILE', __FILE__ );
}

/**
 * CUBEWP_SOCIAL_REDIRECT Defines for load JS and CSS files
 */
if ( ! defined( 'CUBEWP_SOCIAL_REDIRECT' ) ) {
	define( 'CUBEWP_SOCIAL_REDIRECT', home_url() );
}

/**
 * All Classified Plugin classes files to be loaded automatically.
 *
 * @param string $className Class name.
 */
if ( ! function_exists( 'cwp_sl_plugin_autoload_classes' ) ) {
	function cwp_sl_plugin_autoload_classes( $className ) {
		// If class does not start with our prefix (CubeWp), nothing will return.
		if ( false === strpos( $className, 'CubeWp' ) ) {
			return null;
		}
		// Replace _ with - to match the file name.
		$file_name = str_replace( '_', '-', strtolower( $className ) );
		// Calling class file.
		$files = array(
			CWP_SL_PLUGIN_PATH . 'cube/classes/class-' . $file_name . '.php'
		);
		// Checking if exists then include.
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				require $file;
			}
		}

		return $className;
	}

	spl_autoload_register( 'cwp_sl_plugin_autoload_classes' );
}

/**
 * Method cubewp_social_logins_init
 *
 * @since  1.0
 * @return void
 */
function cubewp_social_logins_init(){
    
    return new CubeWp_Social_Logins_Load();
    
}
add_action( 'cubewp_loaded', 'cubewp_social_logins_init');

/**
 * CubeWP addon register activation hook callback function.
 *
 * @since  1.0
 */
function social_logins_active_plugin() {
	if ( ! function_exists( 'CWP' ) ) {
		die( 'Sorry! CubeWP Framework is not installed' );
	}

	do_action( 'cubewp/addon/activation', 'cubewp-addon-social-logins' );
}