<?php

/**
 * Script and styles enqueue for CubeWP Custom Forms.
 *
 * @package cubewp-addon-forms/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp Forms Enqueue
 *
 * @class CubeWp_Forms_Enqueue
 */
class CubeWp_Forms_Enqueue {

	public function __construct() {
		add_filter( 'frontend/style/register', array( $this, 'register_frontend_styles' ) );
		add_filter( 'admin/script/enqueue', array( $this, 'load_admin_scripts' ) );
		add_filter( 'frontend/script/register', array( $this, 'register_frontend_scripts' ) );
		add_filter('get_frontend_script_data', array($this, 'get_frontend_script_data'),10,2);
		add_filter( 'admin/style/register', array( $this, 'register_admin_styles' ) );
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
			'cubewp-dashboard-leads' => array(
				'src'     => CWP_FORMS_PLUGIN_URL . 'cube/assets/css/cubewp-dashboard-leads.css',
				'deps'    => array(),
				'version' => CUBEWP_FORMS_VERSION,
				'has_rtl' => false,
			),
            'cubewp-frontend-forms' => array(
				'src'     => CWP_FORMS_PLUGIN_URL . 'cube/assets/css/cubewp-frontend-forms.css',
				'deps'    => array(),
				'version' => CUBEWP_FORMS_VERSION,
				'has_rtl' => false,
			),
		);

		return array_merge( $register_styles, $styles );
	}
	/**
	 * Method register_admin_styles
	 *
	 * @param array $styles
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_admin_styles( $styles ) {
		$register_styles = array(
			'cubewp-admin-leads' => array(
				'src'     => CWP_FORMS_PLUGIN_URL . 'cube/assets/css/cubewp-admin-leads.css',
				'deps'    => array(),
				'version' => CUBEWP_FORMS_VERSION,
				'has_rtl' => false,
			),
		);

		return array_merge( $register_styles, $styles );
	}

	/**
	 * Method register_frontend_scripts
	 *
	 * @param array $script
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_frontend_scripts( $script ) {
		$register_scripts = array(
			'cubewp-forms-dashboard' => array(
				'src'     => CWP_FORMS_PLUGIN_URL . 'cube/assets/js/cubewp-forms-dashboard.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_FORMS_VERSION,
			),
			'cubewp-custom-form-submit' => array(
				'src'     => CWP_FORMS_PLUGIN_URL . 'cube/assets/js/custom-form-submit.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_FORMS_VERSION,
			)
		);

		return array_merge( $script, $register_scripts );
	}

    /**
	 * Method get_frontend_script_data
	 *
	 * @param array $data
	 *
	 * @return void
	 * @since  1.0.0
	 */
    public static function get_frontend_script_data($data,$handle) {
		global $wp;
        if($handle == 'cubewp-forms-dashboard'){
            $params = array(
					'ajax_url'  => admin_url( 'admin-ajax.php' ),
                    'security_nonce' => wp_create_nonce("cubewp_forms_dashboard")
				);
            return $params;
        }
		if($handle == 'cubewp-custom-form-submit'){
            $params = array(
					'ajax_url'  => admin_url( 'admin-ajax.php' ),
                    'security_nonce' => wp_create_nonce("cubewp_forms_submit")
				);
            return $params;
        }
		return $data;
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
		if(CWP()->is_admin_screen('cubewp_form_fields')){
			CubeWp_Enqueue::enqueue_script('cubewp-custom-fields');
			CubeWp_Enqueue::enqueue_style('cubewp-custom-fields');
			CubeWp_Enqueue::enqueue_script('cubewp-metaboxes-validation');
		}
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
}