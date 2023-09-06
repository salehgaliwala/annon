<?php
/**
 * Plugin Name: CubeWP Forms
 * Plugin URI: https://cubewp.com/
 * Description: LeadGen & Contact Form by CubeWP – Drag & Drop Form Builder for WordPress
 * Version: 1.0.4
 * Author: CubeWP
 * Author URI: https://CubeWp.com
 * Text Domain: cubewp-forms
 * Domain Path: /languages/
 * @package cubewp-forms
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/* CUBEWP_FRONTEND_PLUGIN_DIR Defines for load Php files */
if (!defined('CWP_FORMS_PLUGIN_DIR')) {
    define('CWP_FORMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

/* CUBEWP_FRONTEND_PLUGIN_URL Defines for load JS and CSS files */
if (!defined('CWP_FORMS_PLUGIN_URL')) {
    define('CWP_FORMS_PLUGIN_URL', plugins_url( '/', __FILE__ ));
}

/* CUBEWP_FRONTEND_PLUGIN_FILE Defines for file access */
if (!defined('CUBEWP_FORMS_PLUGIN_FILE')) {
    define('CUBEWP_FORMS_PLUGIN_FILE', __FILE__);
}
spl_autoload_register('CWP_FORMS_autoload_classes');

/**
 * All CubeWP classes files to be loaded automatically.
 *
 * @param string $className Class name.
 */
function CWP_FORMS_autoload_classes($className) {
    
    // If class does not start with our prefix (CubeWp), nothing will return.
    if (false === strpos($className, 'CubeWp_Forms')) {
        return null;
    }
    // Replace _ with - to match the file name.
    $file_name = str_replace('_', '-', strtolower($className));
    // Calling class file.
    $files = CWP_FORMS_PLUGIN_DIR . 'cube/classes/class-' . $file_name . '.php';
    if (file_exists($files)) {
        require $files;
    }

    
    return;
}

/**
 * Method cubewp_custom_forms_init
 *
 * @since  1.0
 * @return void
 */
function cubewp_custom_forms_init(){
	
	return new CubeWp_Forms_Custom();
	
}
add_action( 'cubewp_loaded', 'cubewp_custom_forms_init');

/**
 * Method cubewp_framework_required_notice_for_forms
 *
 * @return void
 * @since  1.0.0
 */
if ( ! function_exists('cubewp_framework_required_notice_for_forms')) {
    function cubewp_framework_required_notice_for_forms() {
        if ( ! function_exists( 'CWP' ) ) {
        ?>
            <div class="notice notice-error">
                <p><strong><?php esc_html_e( 'CubeWP Forms', 'cubewp-forms' ); ?></strong></p>
                <p><?php echo sprintf( esc_html__( '%sCubeWP Framework%s is required to run CubeWP Forms.', 'cubewp-forms' ), '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=cubewp-framework&TB_iframe=true' ) . '" class="thickbox open-plugin-details-modal">', '</a>' ); ?></p>
            </div>
            <?php
        }
    }
    add_action( 'admin_notices', 'cubewp_framework_required_notice_for_forms' );
}