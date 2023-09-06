<?php

/**
 * CubeWp Forms Initialization.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CubeWp Forms Class.
 *
 * @class CubeWp_Forms
 */

class CubeWp_Forms_Custom {

	/**
	 * Wordpress required version.
	 *
	 * @var string
	 */
	public static $CubeWp_Forms_version = '1.0.4';
	/**
	 * The single instance of the class.
	 *
	 * @var CubeWp_Load
	 */
	protected static $Load = null;

	/**
	 * CubeWp_Load Constructor.
	 */
	public function __construct() {
		/* CUBEWP_VERSION is defined for current cubewp version */
		if ( ! defined( 'CUBEWP_FORMS_VERSION' ) ) {
			define( 'CUBEWP_FORMS_VERSION', self::$CubeWp_Forms_version );
		}
		self::init_hooks();
		self::includes();
	}

	/**
     * Method init_hooks
     *
     * @since  1.0.0
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), -1);
        add_action('init', array($this, 'init'), 0);
    }

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		require_once CWP_FORMS_PLUGIN_DIR . 'cube/functions/functions.php';
		if ( CWP()->is_request( 'frontend' ) ) {
			self::frontend_includes();
		}
		if ( CWP()->is_request( 'admin' ) ) {
			add_filter('cubewp-submenu', array($this, 'Cubewp_Menu'), 10);
			add_action('admin_init', array('CubeWp_Forms_Leads', 'init'), 9);
			add_filter('user/dashboard/content/types', array($this, 'cwp_dashboard_adds'), 9,1);
			add_action( 'cubewp_form_fields', array( 'CubeWp_Forms_UI', 'cwp_custom_fields_run' ),9 );
			new CubeWp_Ajax( '',
				'CubeWp_Forms_UI',
				'cwp_duplicate_custom_forms_custom_field'
			);
		}
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		add_action('init', array('CubeWp_Forms_Frontend_Custom_Form', 'init'), 9);
		add_action('init', array('CubeWp_Forms_Dashboard', 'init'), 9);
	}
	/**
	 * Init CubeWp when WordPress Initialises.
	 */
	public function init() {
		add_filter('cubewp/posttypes/new', array($this, 'CWP_cpt'), 10);
		add_action('init', array('CubeWp_Forms_Enqueue', 'init'), 10);
		// Set up localisation.
		self::load_plugin_textdomain();
		
	}

	/**
     * cubewp_plugins_loaded action hook to load something on plugin_loaded action.
     * Method on_plugins_loaded
     *
     * @since  1.0.0
     */
    public function on_plugins_loaded() {
        do_action('cubewp_forms_loaded');
    }

	public static function cwp_dashboard_adds($content){
        $data = array(
            'leads' => 'Leads',
        );
        return array_merge($content,$data);
    }
	
	/**
     * Method Cubewp_Menu
     *
     * @param array $defaut contains array of menu pages
     *
     * @return array
     * @since  1.0.0
     */
    public function Cubewp_Menu($defaut) {
        $settings = array(
            array(
                'id'           =>  'cubewp-form-fields',
                'title'        =>  esc_html__('CubeWP Forms', 'cubewp-forms'),
                'callback'     =>  'cubewp-form-fields',
				'icon'     => CWP_PLUGIN_URI .'cube/assets/admin/images/cubewp-admin.svg',
                'position'     => 60
            ),
			array(
                'id'           =>  'cubewp-form-fields-sub',
                'parent'       =>  'cubewp-form-fields',
                'title'        =>  esc_html__('Custom Forms', 'cubewp-forms'),
                'callback'     =>  'cubewp-form-fields',
            ),
			array(
                'id'           =>  'cubewp-custom-form-data',
                'parent'       =>  'cubewp-form-fields',
                'title'        =>  esc_html__('All Leads', 'cubewp-forms'),
                'callback'     =>  'cubewp-custom-form-data',
            ),
        );

        return array_merge($defaut,$settings);
    }


	/**
     * CWP CPT
     * parse arguments to register post types. 
     * make sure all argumanted passses properly
     * @since 1.0
     * @version 1.0
     */
    public static function CWP_cpt($default) {
        $reviewsCPT = array(
            'cwp_forms'            => array(
                'label'                  => 'CubeWP Forms',
                'singular'               => 'cwp_form',
                'icon'                   => '',
                'slug'                   => 'cwp_forms',
                'description'            => '',
                'supports'               => array('title', 'author', 'custom-fields'),
                'hierarchical'           => false,
                'public'                 => false,
                'show_ui'                => false,
                'menu_position'          => false,
                'show_in_menu'           => false,
                'show_in_nav_menus'      => false,
                'show_in_admin_bar'      => false,
                'can_export'             => true,
                'has_archive'            => false,
                'exclude_from_search'    => true,
                'publicly_queryable'     => true,
                'query_var'              => false,
                'rewrite'                => false,
                'rewrite_slug'           => '',
                'rewrite_withfront'      => false,
                'show_in_rest'           => true,
            )
        );
        return array_merge($default,$reviewsCPT);
    }

		
	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 * - WP_LANG_DIR/cubewp/cubewp-LOCALE.mo
	 * - WP_LANG_DIR/plugins/cubewp-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			// @todo Remove when start supporting WP 5.0 or later.
			$locale = is_admin() ? get_user_locale() : get_locale();
		}

		$locale = apply_filters( 'plugin_locale', $locale, 'cubewp-forms' );

		unload_textdomain( 'cubewp-forms' );
		load_textdomain( 'cubewp-forms', WP_LANG_DIR . '/cubewp-addon-forms/cubewp-forms-' . $locale . '.mo' );
		load_plugin_textdomain( 'cubewp-forms', false, plugin_basename( dirname( CUBEWP_FORMS_PLUGIN_FILE ) ) . '/languages' );
	}

}