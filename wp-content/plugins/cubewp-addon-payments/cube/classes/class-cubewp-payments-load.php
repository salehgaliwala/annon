<?php

/**
 * CubeWP payments initializer.
 *
 * @package cubewp-addon-payments/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Payments_Load
 */
class CubeWp_Payments_Load {
    
    /**
     * Payment addon version.
     *
     * @var string
     */
    public static $CubeWp_Frontrnd_version = '1.0.7';
    /**
	 * The single instance of the class.
	 *
	 * @var CubeWp_payments_Load
	 */
    protected static $Load = null;
    public $admin_notices;
    
    public static function instance() {
        if(class_exists('WooCommerce')){
            if ( is_null( self::$Load ) ) {
                self::$Load = new self();
            }
            return self::$Load;
        }else {
            $msg = sprintf( esc_html__( 'CubeWP Payments requires WooCommerce to be installed and active. You can download %s here.', 'cubewp-framework' ), '<a href="'. site_url("/wp-admin/plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=772&height=552").'" target="_blank">WooCommerce</a>' );
            new CubeWp_Admin_Notice("cubewp-woocommerce-required", '<strong>' . $msg . '</strong>', 'error', false);
            return false;
        }
    }
    /**
     * CubeWp_Load Constructor.
     * @since  1.0.0
     */
    public function __construct() {
        /* CUBEWP_VERSION is defined for current cubewp version */
        if (!defined('CUBEWP_PAYMENTS_VERSION')) {
            define('CUBEWP_PAYMENTS_VERSION', self::$CubeWp_Frontrnd_version);
        }

        self::includes();
		self::init_hooks();
    }
        
    /**
     * Method init_hooks
     *
     * @return void
     * @since  1.0.0
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), -1);
        add_action('init', array($this, 'init'), 0);
    }
    
    /**
     * Include required core files used in admin and on the frontend.
     * @since  1.0.0
     */
    public function includes() {
        require_once CUBEWP_PAYMENTS_PLUGIN_DIR . 'cube/helpers/functions.php';

        if (CWP()->is_request('frontend')) {
            self::frontend_includes();
        }
        if(CWP()->cubewp_options('cubewp-addon-payments') == 'expired'){
            $message = sprintf(esc_html__('CubeWP Payments license has expired, please renew your license at "%1$s"', 'cubewp-payments'), '<a href="https://cubewp.com" target="_blank">CubeWP Website</a>');
            new CubeWp_Admin_Notice("cwp-payments-expired", $message, 'error');
        }
    }
    /**
     * Init CubeWp when WordPress Initialises.
     * @since  1.0.0
     */
    public function init() {
	    add_action('init', array('CubeWp_Payment_Hooks', 'init'), 9);
        add_action('init', array('CubeWp_Woo_Hooks', 'init'), 9);
        add_action('init', array('CubeWp_Price_Plans', 'init'), 9);
        add_action('init', array('CubeWp_Post_Types_Columns', 'init'), 9);
        add_action('init', array('CubeWp_Expire_Posts_Transient', 'init'), 9);
        add_action('init', array('CubeWp_Payments_Elementor', 'init'), 9);
	    add_action('init', array('CubeWp_Payments_Enqueue', 'init'), 10);
        require_once CUBEWP_PAYMENTS_PLUGIN_DIR . 'cube/helpers/wc-dashboard.php';
        // Set up localisation.
        self::load_plugin_textdomain();

    }
    
    /**
     * cubewp_plugins_loaded action hook to load something on plugin_loaded action.	 
     * @since  1.0.0
     */
    public function on_plugins_loaded() {
        do_action('CUBEWP_PAYMENTS_loaded');
    }
    
    /**
     * Include required frontend files.
     * @since  1.0.0
     */
    public function frontend_includes() {
        add_action('init', array('CubeWp_Payments_Price_Plans', 'init'), 9);

    }
    
    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     * - WP_LANG_DIR/cubewp/cubewp-LOCALE.mo
     * - WP_LANG_DIR/plugins/cubewp-LOCALE.mo
     * @since  1.0.0
     */
    public function load_plugin_textdomain() {
        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            // @todo Remove when start supporting WP 5.0 or later.
            $locale = is_admin() ? get_user_locale() : get_locale();
        }

        $locale = apply_filters('plugin_locale', $locale, 'cubewp-payments');

        unload_textdomain('cubewp-payments');
        load_textdomain('cubewp-payments', WP_LANG_DIR . '/cubewp-addon-payments/cubewp-payments-' . $locale . '.mo');
        load_plugin_textdomain('cubewp-payments', false, plugin_basename(dirname(CUBEWP_PAYMENTS_PLUGIN_FILE)) . '/languages');
    }

}