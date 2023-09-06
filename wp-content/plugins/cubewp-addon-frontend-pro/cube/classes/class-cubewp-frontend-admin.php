<?php

/**
 * Gateway to enter cubewp frontend admin side.
 *
 * @package cubewp-addon-frontend/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Admin
 */
class CubeWp_Frontend_Admin {
    
    const CubeWp = 'CubeWp_';
    public function __construct() {
                
        if (CWP()->is_request('admin')) {
            global $cwpOptions;

            spl_autoload_register(array($this, '_modules'));
            self::CubeWp_form_builder('user_registration_form');
            self::CubeWp_form_builder('user_profile_form');
            self::CubeWp_form_builder('post_types_form');
            self::CubeWp_form_builder('single_layout');
            add_filter("cubewp/builder/search_filters/group/fields",array($this,'_group_filter'),9,2);
            add_filter("cubewp/builder/search_fields/group/fields",array($this,'_group_search'),9,2);

            if( isset($cwpOptions['allow_instant_signup']) && $cwpOptions['allow_instant_signup'] ){
				add_filter( 'cubewp/builder/post_type/custom/cubes/sections', array( $this, 'cubewp_quick_signup_post_type_form_cube' ), 11, 2 );
			}

            if(CWP()->cubewp_options('cubewp-addon-frontend-pro-status') == 'expired'){
                $message = sprintf(esc_html__('CubeWP Frontend Pro license has expired, please renew your license at "%1$s"', 'cubewp-frontend'), '<a href="https://cubewp.com" target="_blank">CubeWP Website</a>');
                new CubeWp_Admin_Notice("cwp-frontend-expired", $message, 'error');
            }
        }

    }
        
    /**
     * Method CubeWp_form_builder
     *
     * @param string $type builder name
     *
     * @return void
     * @since  1.0.0
     */
    protected static function CubeWp_form_builder($type) {
        return new CubeWp_Builder_Admin($type); 
    }
        
    /**
     * Method custom_fields_group
     *
     * @param string $form_type name of form
     * @param string $key form key name
     *
     * @return void
     * @since  1.0.0
     */
    private function custom_fields_group( $form_type, $key ) {
	    $output = '';
        $GroupFields = '';
        $form_builder = new CubeWp_Form_Builder();
		$groups = cwp_get_groups_by_post_type( $key );
		if ( isset( $groups ) && !empty( $groups ) && count($groups)>0) {
			foreach ( $groups as $group ) {
				$fields = get_post_meta( $group, '_cwp_group_fields', true );
				$fields = isset( $fields ) && ! empty( $fields ) ? explode( ',', $fields ) : array();
                if ( !empty( $fields ) && count($fields)>0) {
                    $args   = array(
                        'section_title'       => esc_html( get_the_title( $group ) ),
                        'section_description' => '',
                        'section_class'       => '',
                        'open_close_class'    => 'close',
                        'form_relation'       => $key,
                        'form_type'           => $form_type,
                        'fields'              => $fields,
                    );
                    $output .= $form_builder->cwpform_form_section( $args );
                }
			}
		}
	    $output .= self::cubewp_custom_cubes( $form_type, $key );

		return $output;
	}

	/**
	 * Method cubewp_custom_cubes
	 *
	 * @param string $key
	 *
	 * @return string html
	 * @since  1.0.11
	 */
	public function cubewp_custom_cubes( $form_type, $key ){
		$output = '';
		$custom_cubes_sections = apply_filters( 'cubewp/builder/' . $form_type . '/custom/cubes/sections', array(), $key );
		if ( ! empty( $custom_cubes_sections ) && is_array( $custom_cubes_sections ) ) {
			$form_builder = new CubeWp_Form_Builder();
			foreach ( $custom_cubes_sections as $args ) {
				$default = array(
					'section_title'       => '',
					'section_description' => '',
					'section_class'       => '',
					'open_close_class'    => 'close',
					'form_relation'       => $key,
					'form_type'           => $form_type,
					'fields'              => array(),
					'section_type'        => 'group_fields',
				);
				$args = wp_parse_args( $args, $default );

				$output .= $form_builder->cwpform_form_section( $args );
			}
		}

		return $output;
	}
        
    /**
     * Method _group_filter
     *
     * @param string $empty
     * @param string $key key name of group fields
     *
     * @return void
     * @since  1.0.0
     */
    public function _group_filter( $empty, $key ) {
		return $this->custom_fields_group( 'search_filters', $key );
	}
    public function _group_search( $empty, $key ) {
		return $this->custom_fields_group( 'search_fields', $key );
	}
    
    /**
     * All CubeWP classes files to be loaded automatically.
     *
     * @param string $className Class name.
     * @since  1.0.0
     */
    private function _modules($className) {

        // If class does not start with our prefix (CubeWp), nothing will return.
        if (false === strpos($className, 'CubeWp')) {
            return null;
        }
        $modules = array(
            'builder' => 'modules/',
        );
        
        foreach($modules as $module=>$path){
            $file_name = $path.$module.'/class-' .str_replace('_', '-', strtolower($className)).'.php';
            $file = CUBEWP_FRONTEND_FILES.$file_name;
            // Checking if exists then include.
            if (file_exists($file)) {
                require_once $file;
            }
        }

        
        return;
    }
    
    /**
     * Quick Sign On custom cube added in Post Type Form
     *
     *
     * @since  1.0.11
     */
	public function cubewp_quick_signup_post_type_form_cube( $sections, $post_type ) {
        $sections[] = array(
           'section_title'        =>  esc_html__( 'Quick Signup' , 'cubewp-frontend'  ),
           'section_description'  =>  '',
           'section_class'        =>  '',
           'open_close_class'     =>  'close',
           'form_relation'        =>  $post_type,
           'form_type'            =>  'post_type',
           'fields'               =>  array(
              'instant_signup' => array(
                 'label' =>  __("Quick Signup", "cubewp-frontend"),
                 'name' =>  'instant_signup',
                 'type' =>  'instant_signup',
              )
           ),
           'section_type'         =>  'group_fields',
        );
    
        return $sections;
    }

    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}