<?php

/**
 * Payments elementor.
 *
 * @package cubewp-addon-payments/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Payments_Elementor
 */
class CubeWp_Payments_Elementor {
	public function __construct() {
		add_filter("cubewp/elementor/widgets/classes", array($this, 'register_widgets'), 11);
		add_filter("cubewp/elementor/widgets/files", array($this, 'require_widgets_files'), 11, 2);
	}
	
	/**
	 * Method register_widgets
	 *
	 * @param string $classes
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function register_widgets($classes) {
		return array_merge(array(
			'Pricing_Plans_Widget'
		), $classes);
	}
	
	/**
	 * Method require_widgets_files
	 *
	 * @param array $files
	 * @param string $file_name
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function require_widgets_files($files, $file_name) {
		return array_merge(array(
			CUBEWP_PAYMENTS_PLUGIN_DIR . 'cube/classes/page-builders/elementor-widgets/class-' . $file_name . '.php',
		), $files);
	}
	
	/**
	 * Method init
	 *
	 * @return void
	 */
	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
}