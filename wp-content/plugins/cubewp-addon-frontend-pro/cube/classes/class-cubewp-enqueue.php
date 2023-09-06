<?php

/**
 * Enqueue class to register and enque script/styles.
 *
 * @package cubewp-addon-frontend/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * CubeWp_Frontend_Enqueue
 */
class CubeWp_Frontend_Enqueue {

	public function __construct() {
		add_filter( 'frontend/style/register', array( $this, 'register_frontend_styles' ) );
		add_filter( 'frontend/script/register', array( $this, 'register_frontend_scripts' ) );

		add_filter( 'admin/script/register', array( $this, 'register_admin_scripts' ) );
		add_filter( 'admin/style/register', array( $this, 'register_admin_style' ) );

		add_filter( 'frontend/script/enqueue', array( $this, 'load_frontend_scripts' ) );
		add_filter( 'admin/script/enqueue', array( $this, 'load_admin_scripts' ) );

		add_filter( 'get_frontend_script_data', array( $this, 'get_frontend_script_data' ), 10, 2 );
		add_filter( 'cubewp_get_admin_script', array( $this, 'get_admin_script_data' ), 10, 2 );
	}
	
	/**
	 * Method register_frontend_styles
	 *
	 * @param array $styles contains already registered styles for frontend
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_frontend_styles( $styles ) {
		$register_styles = array(
			'cwp-login-register'     => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/frontend/css/cubewp-login-register.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'cwp-frontend-dashboard' => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/frontend/css/cubewp-dashboard.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
		);

		return array_merge( $register_styles, $styles );
	}
	
	/**
	 * Method register_frontend_scripts
	 *
	 * @param array $script contains already registered scripts for frontend
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_frontend_scripts( $script ) {
		$register_scripts = array(
			'cwp-submit-post'          => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/frontend/js/submit-form.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-user-login'           => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/frontend/js/user-login.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-user-profile'         => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/frontend/js/user-profile.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-user-register'        => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/frontend/js/user-register.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-single'               => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/frontend/js/cubewp-frontend.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-frontend-dashboard'   => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/frontend/js/cubewp-dashboard.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
		);

		return array_merge( $register_scripts, $script );
	}
	
	/**
	 * Method register_admin_scripts
	 *
	 * @param array $script contains already registered scripts for admin
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_admin_scripts( $script ) {
		$register_scripts = array(
			'cwp-user-dashboard' => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/admin/js/user-dashboard.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cubewp-builder'     => array(
				'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/admin/js/cubewp-builder.js',
				'deps'    => array( 'jquery' ),
				'version' => '',
			),
		);

		return array_merge( $script, $register_scripts );
	}
	
	/**
	 * Method register_admin_style
	 *
	 * @param array $styles contains already registered styles for admin
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function register_admin_style( $styles ) {
		$register_style = array(
            'cwp-user-dashboard' => array(
               'src'     => CUBEWP_FRONTEND_PLUGIN_URL . 'cube/assets/admin/css/user-dashboard.css',
               'deps'    => array(),
               'version' => CUBEWP_VERSION,
               'has_rtl' => false,
            ),
		);

		return array_merge( $register_style, $styles );
	}

	
	/**
	 * Method load_admin_scripts
	 *
	 * @param string $data 
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public static function load_admin_scripts( $data ) {
       if ( CWP()->is_admin_screen( 'cubewp_user_dashboard' ) ) {
          CubeWp_Enqueue::enqueue_style( 'cwp-user-dashboard' );
          CubeWp_Enqueue::enqueue_script( 'cwp-user-dashboard' );
       }
	   if ( CWP()->is_admin_screen('cubewp_loop_builder') ) {
			CubeWp_Enqueue::enqueue_script( 'ace-editor' );
		}
	   if(CWP()->is_admin_screen('cubewp_post_types_form') || 
          CWP()->is_admin_screen('cubewp_user_profile_form') ||
		  CWP()->is_admin_screen('cubewp_user_registration_form')||
		  CWP()->is_admin_screen('cubewp_loop_builder')||
		  CWP()->is_admin_screen('cubewp_single_layout'))
       {
			CubeWp_Enqueue::enqueue_style( 'cwp-form-builder' );
			CubeWp_Enqueue::enqueue_script( 'cubewp-builder' );
            
        }
    }
	
	/**
	 * Method load_frontend_scripts
	 *
	 * @param string $data
	 *
	 * @return void
	 */
	public static function load_frontend_scripts( $data ) {
		
		if ( is_page() ) {
			if ( has_shortcode( get_the_content(), 'cwpForm' ) ) {
				CubeWp_Enqueue::enqueue_style( 'select2' );
				CubeWp_Enqueue::enqueue_script( 'select2' );
				CubeWp_Enqueue::enqueue_style( 'cwp-timepicker' );
				CubeWp_Enqueue::enqueue_script( 'cwp-timepicker' );
				CubeWp_Enqueue::enqueue_script( 'jquery-ui-datepicker' );
				CubeWp_Enqueue::enqueue_script( 'cwp-submit-post' );
				CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );
				CubeWp_Enqueue::enqueue_style( 'frontend-fields' );
			}
			if ( has_shortcode( get_the_content(), 'cwpRegisterForm' ) ) {
				CubeWp_Enqueue::enqueue_style( 'select2' );
				CubeWp_Enqueue::enqueue_script( 'select2' );
				CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );
				CubeWp_Enqueue::enqueue_script( 'cwp-user-register' );
				CubeWp_Enqueue::enqueue_style( 'cwp-login-register' );
				CubeWp_Enqueue::enqueue_style( 'frontend-fields' );
			}
			if ( has_shortcode( get_the_content(), 'cwpLoginForm' ) ) {
				CubeWp_Enqueue::enqueue_style( 'cwp-login-register' );
				CubeWp_Enqueue::enqueue_script( 'cwp-user-login' );
				CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );
			}
			if (has_shortcode(get_the_content(), 'cwp_dashboard')) {
				CubeWp_Enqueue::enqueue_script('cwp-frontend-dashboard');
				CubeWp_Enqueue::enqueue_style('cwp-frontend-dashboard');
				CubeWp_Enqueue::enqueue_script('cwp-single');
			}
		}
		if ( CubeWp_Frontend::is_cubewp_single() ) {
			CubeWp_Enqueue::enqueue_script( 'cwp-single' );
		}
	}
	
	/**
	 * Method get_frontend_script_data
	 *
	 * @param string $data
	 * @param string $handle contains script handles
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function get_frontend_script_data( $data, $handle ) {
		global $wp;
		if ( $handle == 'cwp-user-login' ) {
			$params = array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'admin_url' => admin_url(),
				'error_msg' => esc_html__('Something Went Wrong. Try Again Later.', 'cubewp-frontend')
			);

			return $params;
		} else if ( $handle == 'cwp-submit-post' ) {
			$params = array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'admin_url' => admin_url(),
				'security_nonce' => wp_create_nonce("cubewp_submit_post_form")
			);

			return $params;
		} else if ( $handle == 'cwp-single' ) {
			$params = array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'admin_url' => admin_url(),
			);

			return $params;
		} else if ( $handle == 'cwp-user-register' ) {
			$params = array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'admin_url' => admin_url(),
				'security_nonce' => wp_create_nonce("cubewp_submit_user_register")
			);

			return $params;
		}else if ( $handle == 'cwp-user-profile' ) {
			$params = array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'admin_url' => admin_url(),
				'security_nonce' => wp_create_nonce("cubewp_update_user_profile"),
				'delete_nonce' => wp_create_nonce("cubewp_delete_user_profile"),
				'download_nonce' => wp_create_nonce("cubewp_download_user_profile"),
				'warning' => esc_html__('Are you sure, You want to proceed?', 'cubewp-frontend')
			);

			return $params;
		}else if ( $handle == 'cwp-frontend-fields' ) {
			$params = array(
			   'ajax_url'  => admin_url( 'admin-ajax.php' ),
			   'security_nonce' => wp_create_nonce("cubewp_dynamic_options"),
			   'max_upload_size' => esc_html__( 'Maximum Upload Size Exceeded.', 'cubewp-frontend' ),
			   'max_upload_files' => esc_html__( 'Maximum Upload Image Limit Exceeded.', 'cubewp-frontend' )
			);
		 
			return $params;
		}else if ( $handle == 'cwp-frontend-dashboard' ) {
			$params = array(
			   'ajax_url'  => admin_url( 'admin-ajax.php' ),
			   'security_nonce' => wp_create_nonce("cubewp_delete_post"),
				'warning' => esc_html__('Are you sure, You want to proceed?', 'cubewp-frontend')
			);

			return $params;
		 }

		return $data;
	}
	
	/**
	 * Method get_admin_script_data
	 *
	 * @param string $data
	 * @param string $handle contains script handles
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function get_admin_script_data( $data, $handle ) {
		global $wp;
		if ( $handle == 'cwp-user-dashboard' ) {
			$params = array(
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( "cwp-user_tabs_nonce" ),
			);

			return $params;
		}

		return $data;
	}

}

new CubeWp_Frontend_Enqueue();