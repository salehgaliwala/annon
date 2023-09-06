<?php
/**
 * Plugin Name: CubeWP Frontend Pro
 * Plugin URI: https://cubewp.com/
 * Description: Enables CubeWP Framework users to create even more advanced search forms and filters for different post types. In addition, it can help in building advanced frontend post submission forms with custom fields, user dashboards, and post types single layout with dynamic content..
 * Version: 1.0.22
 * Author: CubeWP
 * Author URI: https://cubewp.com
 * Text Domain: cubewp-frontend
 * Domain Path: /languages/
 *
 * @package cubewp-frontend
 */
defined('ABSPATH') || exit;
if ( ! defined('CUBEWP_FRONTEND')) {
	define('CUBEWP_FRONTEND', 'CubeWp_Frontend');
}

/* CUBEWP_FRONTEND_PLUGIN_DIR Defines for load Php files */
if ( ! defined('CUBEWP_FRONTEND_PLUGIN_DIR')) {
	define('CUBEWP_FRONTEND_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

/* CUBEWP_FRONTEND_FILES Defines for load Php files */
if ( ! defined('CUBEWP_FRONTEND_FILES')) {
	define('CUBEWP_FRONTEND_FILES', plugin_dir_path(__FILE__).'cube/');
}


/* CUBEWP_FRONTEND_PLUGIN_URL Defines for load JS and CSS files */
if ( ! defined('CUBEWP_FRONTEND_PLUGIN_URL')) {
	define('CUBEWP_FRONTEND_PLUGIN_URL', plugin_dir_url(__FILE__));
}

/* CUBEWP_FRONTEND_PLUGIN_FILE Defines for file access */
if ( ! defined('CUBEWP_FRONTEND_PLUGIN_FILE')) {
	define('CUBEWP_FRONTEND_PLUGIN_FILE', __FILE__);
}

/**
 * All CubeWP classes files to be loaded automatically.
 *
 * @param string $className Class name.
 */
spl_autoload_register('CWP_frontend_autoload_classes');
function CWP_frontend_autoload_classes($className) {

	// If class does not start with our prefix (CubeWp), nothing will return.
	if (false === strpos($className, 'CubeWp')) {
		return null;
	}
	// Replace _ with - to match the file name.
	$file_name = str_replace('_', '-', strtolower($className));
	
	// Calling class file.
	$files = array(
		CUBEWP_FRONTEND_PLUGIN_DIR . 'cube/classes/class-' . $file_name . '.php',
		CUBEWP_FRONTEND_PLUGIN_DIR . 'cube/classes/shortcodes/class-' . $file_name . '.php',
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
register_activation_hook( CUBEWP_FRONTEND_PLUGIN_FILE, 'cubewp_frontend_active');

/**
 * CubeWP addon register activation hook callback function.
 *
 * @since  1.0
 */
function cubewp_frontend_active(){
	
	if(!function_exists('CWP')){
		die('Sorry! CubeWP Framework is not installed');
	}

	do_action( 'cubewp/addon/activation', 'cubewp-addon-frontend-pro' );
	
}
