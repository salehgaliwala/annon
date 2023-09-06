<?php
/**
 * Plugin Name: CubeWP Classified
 * Plugin URI:
 * Description: CubeWP Classified Engine is an extension for the CubeWP Framework that includes all the essential core features to develop WordPress themes specifically for creating classified websites.
 * Version: 1.0.10
 * Author: CubeWP
 * Author URI: https://CubeWp.com
 * Text Domain: cubewp-classified
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit;

/**
 * CLASSIFIED_PLUGIN_VERSION is defined for current Classified Plugin version
 */
if ( ! defined( 'CLASSIFIED_PLUGIN_VERSION' ) ) {
	define( 'CLASSIFIED_PLUGIN_VERSION', '1.0.10' );
}

/**
 * CLASSIFIED_PLUGIN_VERSION is defined for files access
 */
if ( ! defined( 'CLASSIFIED_PLUGIN_FILE' ) ) {
	define( 'CLASSIFIED_PLUGIN_FILE', __FILE__ );
}

/**
 * CLASSIFIED_PLUGIN_PATH Defines for load Php files
 */
if ( ! defined( 'CLASSIFIED_PLUGIN_PATH' ) ) {
	define( 'CLASSIFIED_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * CLASSIFIED_PLUGIN_URL Defines for load JS and CSS files
 */
if ( ! defined( 'CLASSIFIED_PLUGIN_URL' ) ) {
	define( 'CLASSIFIED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * All Classified Plugin classes files to be loaded automatically.
 *
 * @param string $className Class name.
 */
if ( ! function_exists( 'classified_plugin_autoload_classes' ) ) {
	function classified_plugin_autoload_classes( $className ) {
		// If class does not start with our prefix (Classified), nothing will return.
		if ( false === strpos( $className, 'Classified' ) ) {
			return null;
		}
		// Replace _ with - to match the file name.
		$file_name = str_replace( '_', '-', strtolower( $className ) );
		// Calling class file.
		$files = array(
			CLASSIFIED_PLUGIN_PATH . 'classes/class-' . $file_name . '.php',
			CLASSIFIED_PLUGIN_PATH . 'classes/shortcodes/class-' . $file_name . '.php',
			CLASSIFIED_PLUGIN_PATH . 'classes/widgets/class-' . $file_name . '.php',
		);
		// Checking if exists then include.
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				require $file;
			}
		}

		$modules = array(
			'modules/offer-buy-item',
			'modules/user-verification',
			'modules/personalization',
			'modules/report'
		);
		foreach ( $modules as $module ) {
			$file = $module . '/class-' . $file_name . '.php';
			$file = CLASSIFIED_PLUGIN_PATH . $file;
			// Checking if exists then include.
			if ( ! file_exists( $file ) ) {
				continue;
			}
			require $file;
		}

		return $className;
	}

	spl_autoload_register( 'classified_plugin_autoload_classes' );
}

/**
 * CubeWP addon register activation Hook.
 *
 * @since  1.0
 */
register_activation_hook( CLASSIFIED_PLUGIN_FILE, 'classified_addon_active_plugin' );

/**
 * CubeWP addon register activation hook callback function.
 *
 * @since  1.0
 */
function classified_addon_active_plugin() {
	if( ! function_exists( 'CWP' ) ) {
		die( 'Sorry! CubeWP Framework is not installed' );
	}

	do_action( 'cubewp/addon/activation', 'cubewp-addon-classified' );
}