<?php
/**
 * Plugin Name: CubeWP Payments
 * Plugin URI: https://cubewp.com/
 * Description: Enable monetization via different revenue channels using woocommerece payment methods.
 * Version: 1.0.7
 * Author: CubeWP
 * Author URI: https://CubeWp.com
 * Text Domain: cubewp-payments
 * Domain Path: /languages/
 *
 * @package cubewp-frontend
 */
defined('ABSPATH') || exit;
if ( ! defined('CUBEWP_PAYMENTS')) {
	define('CUBEWP_PAYMENTS', 'CubeWp');
}

/* CUBEWP_PAYMENTS_PLUGIN_DIR Defines for load Php files */
if ( ! defined('CUBEWP_PAYMENTS_PLUGIN_DIR')) {
	define('CUBEWP_PAYMENTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
}


/* CUBEWP_PAYMENTS_PLUGIN_URL Defines for load JS and CSS files */
if ( ! defined('CUBEWP_PAYMENTS_PLUGIN_URL')) {
	define('CUBEWP_PAYMENTS_PLUGIN_URL', plugin_dir_url(__FILE__));
}

/* CUBEWP_PAYMENTS_PLUGIN_FILE Defines for file access */
if ( ! defined('CUBEWP_PAYMENTS_PLUGIN_FILE')) {
	define('CUBEWP_PAYMENTS_PLUGIN_FILE', __FILE__);
}
spl_autoload_register('CWP_Payments_autoload_classes');

/**
 * All CubeWP classes files to be loaded automatically.
 *
 * @param string $className Class name.
 */
function CWP_Payments_autoload_classes($className) {

	// If class does not start with our prefix (CubeWp), nothing will return.
	if (false === strpos($className, 'CubeWp')) {
		return null;
	}
	// Replace _ with - to match the file name.
	$file_name = str_replace('_', '-', strtolower($className));

	// Calling class file.
	$files = array(
		CUBEWP_PAYMENTS_PLUGIN_DIR . 'cube/classes/class-' . $file_name . '.php',
	);

	// Checking if exists then include.
	foreach ($files as $file) {
		if (file_exists($file)) {
			require $file;
		}
	}

	return;
}

/**
 * CubeWP addon register activation Hook.
 *
 * @since  1.0
 */
register_activation_hook( CUBEWP_PAYMENTS_PLUGIN_FILE, 'payments_active_plugin');

/**
 * CubeWP addon register activation hook callback function.
 *
 * @since  1.0
 */
function payments_active_plugin(){
	if(!function_exists('CWP')){
		die('Sorry! CubeWP Framework is not installed');
	}

	do_action( 'cubewp/addon/activation', 'cubewp-addon-payments' );
}