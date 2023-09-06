<?php

/**
 * CubeWP frontend initializer.
 *
 * @package cubewp-addon-frontend/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Load
 */
class CubeWp_Frontend_Load {
    
    /**
     * Current this plugin version.
     *
     * @var string
     */
    public static $CubeWp_Frontrnd_version = '1.0.22';
    /**
	 * The single instance of the class.
	 *
	 * @var CubeWp_Load
	 */
    protected static $Load = null;
    
    public static function instance() {
		if ( is_null( self::$Load ) ) {
			self::$Load = new self();
		}
		return self::$Load;
	}
    /**
     * CubeWp_Load Constructor.
     */
    public function __construct() {
        /* CUBEWP_VERSION is defined for current cubewp version */
        if (!defined('CUBEWP_FRONTEND_VERSION')) {
            define('CUBEWP_FRONTEND_VERSION', self::$CubeWp_Frontrnd_version);
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
        require_once CUBEWP_FRONTEND_PLUGIN_DIR . 'cube/helpers/functions.php';
        
        add_action('init', array('CubeWp_Frontend_Admin', 'init'), 9);
        add_action( 'init', array( 'CubeWp_Emails', 'init' ), -1 );
        if(CWP()->is_request('admin')){
            add_action('init', array('CubeWp_User_Dashboard', 'init'), 9);
            add_action('init', array('CubeWp_Loop_Builder', 'init'), 9);
        }
        if (CWP()->is_request('frontend')) {
            self::frontend_includes();
        }
    }
    /**
     * Init CubeWp when WordPress Initialises.
     * @since  1.0.0
     */
    public function init() {        
        // Set up localisation.
        self::load_plugin_textdomain();
    }
    
    /**
     * cubewp_plugins_loaded action hook to load something on plugin_loaded action.
     * @since  1.0.0
     */
    public function on_plugins_loaded() {
        do_action('cubewp_frontend_loaded');
    }
    
    /**
     * Include required frontend files.
     * @since  1.0.0
     */
    public function frontend_includes() {
        add_action('init', array('CubeWp_Frontend_User_Login', 'init'), 9);
        add_action('init', array('CubeWp_User_Ajax_Hooks', 'init'), 9);
        add_action('init', array('CubeWp_Frontend_User_Profile', 'init'), 9);
        add_action('init', array('CubeWp_Frontend_Post_Types_Form', 'init'), 9);
        add_action('init', array('CubeWp_Frontend_User_Registration', 'init'), 9);
        add_action('init', array('CubeWp_Frontend_User_Dashboard', 'init'), 9);
        add_action('init', array('CubeWp_Quick_SignUP', 'init'), 10);
    }
    
    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     * - WP_LANG_DIR/cubewp-addon-frontend/cubewp-frontend-LOCALE.mo
     * - WP_LANG_DIR/plugins/cubewp-frontend-LOCALE.mo
     * @since  1.0.0
     */
    public function load_plugin_textdomain() {
        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            // @todo Remove when start supporting WP 5.0 or later.
            $locale = is_admin() ? get_user_locale() : get_locale();
        }

        $locale = apply_filters('plugin_locale', $locale, 'cubewp-frontend');

        unload_textdomain('cubewp-frontend');
        load_textdomain('cubewp-frontend', WP_LANG_DIR . '/cubewp-addon-frontend/cubewp-frontend-' . $locale . '.mo');
        load_plugin_textdomain('cubewp-frontend', false, plugin_basename(dirname(CUBEWP_FRONTEND_PLUGIN_FILE)) . '/languages');
    }

}