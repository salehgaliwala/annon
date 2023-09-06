<?php

/**
 * Builder admin creates form builder.
 *
 * @package cubewp-addon-frontend/cube/modules/builder
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Builder_Admin
 */
class CubeWp_Builder_Admin extends CubeWp_Form_Builder{

    use CubeWp_Builder_Ui;
    protected static $FORM_TYPE = '';
    protected static $Wraper_class = '';
    protected static $Form_title = 'Post Types Form Builder';

    public function __construct($FormType) {
        add_action( "cubewp_{$FormType}", array( $this, "create_{$FormType}" ) );
    }
        
    /**
     * Method create_user_profile_form
     *
     * @return string
     * @since  1.0.0
     */
    public function create_user_profile_form() {
        self::$FORM_TYPE = 'user_profile';
        self::$Wraper_class = '';
        self::$Form_title = 'User Profile Form Builder';
        self::Builder_Ui();
    }    
    /**
     * Method create_user_registration_form
     *
     * @return mixed
     * @since  1.0.0
     */
    public function create_user_registration_form() {
        self::$FORM_TYPE = 'user_register';
        self::$Wraper_class = '';
        self::$Form_title = 'User Signup Form Builder';
        self::Builder_Ui();
    }
        
    /**
     * Method create_post_types_form
     *
     * @return mixed
     * @since  1.0.0
     */
    public function create_post_types_form() {
        
        CubeWp_Enqueue::enqueue_style( 'cubewp-builder' );
        CubeWp_Enqueue::enqueue_script( 'cubewp-builder' );
        
        $post_Type = new CubeWp_Post_Types_Form();
        add_filter('cubewp/builder/post_type/default/fields',array($post_Type,'default_wp_fields'),10,2);
        add_filter('cubewp/builder/post_type/taxonomies/fields',array($post_Type,'taxonomy_fields'),10,2);
        add_filter('cubewp/builder/post_type/group/fields',array($post_Type,'post_type_group_fields'),10,2);
        $post_Type->create_post_types_form();
    }
        
    /**
     * Method create_single_layout
     *
     * @return mixed
     * @since  1.0.0
     */
    public function create_single_layout() {
        
        CubeWp_Enqueue::enqueue_style( 'cubewp-builder' );
        CubeWp_Enqueue::enqueue_script( 'cubewp-builder' );
        
        $single_page = new CubeWp_Single_Layout();
        add_filter('cubewp/builder/single_layout/default/fields',array($single_page,'default_wp_fields'),10,2);
        add_filter('cubewp/builder/single_layout/taxonomies/fields',array($single_page,'taxonomy_fields'),10,2);
        add_filter('cubewp/builder/single_layout/group/fields',array($single_page,'post_type_group_fields'),10,2);
        $single_page->create_single_layout();
    }
        
    /**
     * Method default_wp_fields
     *
     * @param null $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function default_wp_fields($empty,$key) {
        $wp_default_fields  =  cubewp_user_default_fields();
        $output = '';
        $args = array(
            'section_title'        =>  esc_html__("WordPress Default Fields", "cubewp-frontend"),
            'section_description'  =>  '',
            'section_class'        =>  '',
            'open_close_class'     =>  'open',
            'form_relation'        =>  $key,
            'form_type'            =>  'user',
            'fields'               =>  $wp_default_fields,
        );
        $output .= $this->cwpform_form_section( $args );
        $output .= self::cubewp_custom_cubes_user_forms($key);
        return $output;
    }
        
    /**
     * Method taxonomy_fields
     *
     * @param null $empty
     * @param string $key
     *
     * @return void
     * @since  1.0.0
     */
    public function taxonomy_fields($empty,$key) {
        
            return '';

    }
        
    /**
     * Method post_type_group_fields
     *
     * @param null $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function post_type_group_fields($empty,$key) {
        $groups  = cwp_get_groups_by_user_role($key);
        $output = '';
        if(isset($groups) && !empty($groups)){
            foreach($groups as $group){
                $fields = get_post_meta($group, '_cwp_group_fields', true);
                $fields = isset($fields) && !empty($fields) ? json_decode($fields, true) : array();
                if ( !empty( $fields ) && count($fields)>0) {
                    $args = array(
                        'section_title'        =>  esc_html(get_the_title($group)),
                        'section_description'  =>  '',
                        'section_class'        =>  '',
                        'open_close_class'     =>  'close',
                        'form_relation'        =>  $key,
                        'form_type'            =>  'user',
                        'fields'               =>  $fields,
                    );
                    $output .= $this->cwpform_form_section( $args );
                }
            }
            return $output;
        }
    }
        
    /**
     * Method load_section_right
     *
     * @param null $empty
     * @param string $key
     * @param string $data
     *
     * @return string html
     * @since  1.0.0
     */
    public function load_section_right($empty,$key,$data) {
        $cwp_user_forms  =  CWP()->get_form(self::$FORM_TYPE);
        $output = '';
        if(isset($cwp_user_forms) && !empty($cwp_user_forms[$key]['groups'])){
            $counter = 0;
            foreach($cwp_user_forms[$key]['groups'] as $section_data){
                $counter++;
                $section_data['open_close_class'] = ($counter == 1) ? 'open' : 'close';
                $section_data['form_relation']    = $key;
                $section_data['form_type']        = 'user';
                $output .= $this->cwpform_form_section( $section_data );
            }
            return $output;
        } 
    }
    
    /**
     * Method setting_tab
     *
     * @param null $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function setting_tab($empty,$key) {
        $cwp_user_forms  =  CWP()->get_form(self::$FORM_TYPE);
        $form_fields = isset($cwp_user_forms[$key]['form']) ? $cwp_user_forms[$key]['form'] : array();
        return $this->cwpform_form_setting_fields( $form_fields, self::$FORM_TYPE,$key ); 
    }
        
    /**
     * Method Builder_Ui
     *
     * @return string html
     * @since  1.0.0
     */
    public function Builder_Ui(){
        CubeWp_Enqueue::enqueue_style( 'cubewp-builder' );
        CubeWp_Enqueue::enqueue_script( 'cubewp-builder' );
        
        add_filter('cubewp/builder/'.self::$FORM_TYPE.'/default/fields',array($this,'default_wp_fields'),10,2);
        add_filter('cubewp/builder/'.self::$FORM_TYPE.'/taxonomies/fields',array($this,'taxonomy_fields'),10,2);
        add_filter('cubewp/builder/'.self::$FORM_TYPE.'/group/fields',array($this,'post_type_group_fields'),10,2);
        add_filter('cubewp/builder/default/right/section',array($this,'load_section_right'),10,4);
        add_filter('cubewp/builder/right/settings',array($this,'setting_tab'),10,2);
  
        $user_roles          =  cwp_get_user_roles_name();
        $args = array(
            'form_type'         => self::$FORM_TYPE,
            'wrapper_class'     => self::$Wraper_class,
            'page_title'        => self::$Form_title,
            'switcher_types'    => $user_roles,
            'switcher_title'    => esc_html__('Select User Role','cubewp'),
        );
        echo self::CubeWp_Form_Builder($args);
        $this->cwpform_section_popup_ui(self::$FORM_TYPE);
    }
    
    /**
     * Method cubewp_custom_cubes_post_type_forms
     *
     * @param string $key
     *
     * @return string html
     * @since  1.0.11
     */
    public function cubewp_custom_cubes_user_forms($key){
        $output = '';
        $fields = self::cubewp_custom_user_default_fields($key);
        if( !empty($fields) && is_array($fields) ){
            $args = array(
                'section_title'        =>  esc_html__( 'Custom Fields Section' , 'cube-frontend'  ),
                'section_description'  =>  '',
                'section_class'        =>  '',
                'open_close_class'     =>  'close',
                'form_relation'        =>  $key,
                'form_type'            =>  'user',
                'fields'               =>  $fields,
                'section_type'         =>  'group_fields',
            );
            $output = $this->cwpform_form_section( $args );
        }
        return $output;
    }
    
    /**
     * Method cubewp_custom_post_default_fields
     *
     * @param string $key
     *
     * @return string html
     * @since  1.0.11
     */
    public function cubewp_custom_user_default_fields($key){
        $fields = array();
        return apply_filters("cubewp/builder/user/custom/cubes",$fields,$key);
    }
    
    
}
