<?php

/**
 * Post type's single page layout creator for frontend.
 *
 * @package cubewp-addon-frontend/cube/modules/builder
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Single_Layout
 */
class CubeWp_Single_Layout extends CubeWp_Form_Builder {
    
    use CubeWp_Builder_Ui;
    protected static $FORM_TYPE = 'single_layout';
    protected static $Wraper_class = '';
    protected static $Form_title = 'Single Post Template Editor';
    
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
        $wp_default_fields   =  self::cubewp_single_page_default_fields($key);
        if(!empty($wp_default_fields) && count($wp_default_fields)>0){
            $args = array(
                'section_title'        =>  esc_html__("Custom Cubes", "cubewp-frontend"),
                'section_description'  =>  '',
                'section_class'        =>  '',
                'open_close_class'     =>  'open',
                'form_relation'        =>  $key,
                'form_type'            =>  self::$FORM_TYPE,
                'fields'               =>  $wp_default_fields,
            );
            return $this->cwpform_form_section( $args );
        }
    }
        
    /**
     * Method taxonomy_fields
     *
     * @param null $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function taxonomy_fields($empty,$key) {
        $taxonomies   = get_object_taxonomies($key, 'objects');
        if(isset($taxonomies) && !empty($taxonomies)){
            $taxonomy_fields = array();
            foreach($taxonomies as $taxonomy){
                $taxonomy_fields[$taxonomy->name] = array(
                    'label'         =>  $taxonomy->label,
                    'name'          =>  $taxonomy->name,
                    'type'          =>  'taxonomy',
                );
            }

            $args = array(
                'section_title'        =>  esc_html__("Taxonomies", "cubewp-frontend"),
                'section_description'  =>  '',
                'section_class'        =>  '',
                'open_close_class'     =>  'close',
                'form_relation'        =>  $key,
                'form_type'            =>  self::$FORM_TYPE,
                'fields'               =>  $taxonomy_fields,
            );
            return $this->cwpform_form_section( $args );
        }
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
        $groups = cwp_get_groups_by_post_type($key);
        $output = '';
        if(isset($groups) && !empty($groups)){
            foreach($groups as $group){ 
                $fields = get_post_meta($group, '_cwp_group_fields', true);
                $fields = isset($fields) && !empty($fields) ? explode(',', $fields) : array();
                $args = array(
                    'section_title'        =>  esc_html(get_the_title($group)),
                    'section_description'  =>  '',
                    'section_class'        =>  '',
                    'open_close_class'     =>  'close',
                    'form_relation'        =>  $key,
                    'form_type'            =>  self::$FORM_TYPE,
                    'fields'               =>  $fields,
                );
                $output .= $this->cwpform_form_section( $args );
            }
            return $output;
        }
    }
        
    /**
     * Method load_section_sidebar
     *
     * @param null $empty
     * @param string $key
     * @param string $data
     *
     * @return string html
     * @since  1.0.0
     */
    public function load_section_sidebar($empty,$key,$data) {
        $cwp_single_layout  =  CWP()->get_form( 'single_layout' );
        $output = '';
        if(isset($cwp_single_layout) && !empty($cwp_single_layout[$key])){
            $counter = 0;
            if(isset($cwp_single_layout[$key]['sidebar']) && !empty($cwp_single_layout[$key]['sidebar'])){
                foreach($cwp_single_layout[$key]['sidebar'] as $section_data){
                    $counter++;
                    $section_data['open_close_class'] = ($counter == 1) ? 'open' : 'close';
                    $section_data['form_relation']    = $key;
                    $section_data['form_type']        = self::$FORM_TYPE;
                    $output .= $this->cwpform_form_section( $section_data );
                }
            }
            return $output;
        }

    }
    
    public function load_section_content($empty,$key,$data) {
        $cwp_single_layout  =  CWP()->get_form( 'single_layout' );
        $output = '';
        if(isset($cwp_single_layout) && !empty($cwp_single_layout[$key])){
            $counter = 0;
            if(isset($cwp_single_layout[$key]['content']) && !empty($cwp_single_layout[$key]['content'])){
                foreach($cwp_single_layout[$key]['content'] as $section_data){
                    $counter++;
                    $section_data['open_close_class'] = ($counter == 1) ? 'open' : 'close';
                    $section_data['form_relation']    = $key;
                    $section_data['form_type']        = self::$FORM_TYPE;
                    $output .= $this->cwpform_form_section( $section_data );
                }
            }
            return $output;
        }

    }

    /**
     * Method setting_tab
     *
     * @param string $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function setting_tab($empty,$key) {
        $form_options  =  CWP()->get_form(self::$FORM_TYPE);
        $form_fields = isset($form_options[$key]['form']) ? $form_options[$key]['form'] : array();
        return $this->cwpform_form_setting_fields( $form_fields, self::$FORM_TYPE,$key );
    }

    /**
     * Method create_single_layout
     *
     * @return string html
     * @since  1.0.0
     */
    public function create_single_layout(){

        add_filter('cubewp/builder/single/right/sidebar/section',array($this,'load_section_sidebar'),10,4);
        add_filter('cubewp/builder/single/right/content/section',array($this,'load_section_content'),10,4);
        add_filter('cubewp/builder/right/settings',array($this,'setting_tab'),10,2);
        add_filter( 'cubewp/builder/cubes/fields', array($this, 'cubewp_single_page_shortcode_setting'),10,2);
        $cwp_custom_types  = CWP_all_post_types(self::$FORM_TYPE);
        unset($cwp_custom_types['post']);
        $args = array(
            'form_type'         => self::$FORM_TYPE,
            'wrapper_class'     => self::$Wraper_class,
            'page_title'        => self::$Form_title,
            'switcher_types'    => $cwp_custom_types,
            'switcher_title'    => esc_html__('Select Post Type','cubewp-frontend'),
        );
        echo self::CubeWp_Form_Builder($args);
        $this->cwpform_section_popup_ui(self::$FORM_TYPE);
    }
        
    /**
     * Method cubewp_single_page_default_fields
     *
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function cubewp_single_page_default_fields($key){
        $author = array(
            'author'     =>  array(
                'label'      =>  __("Author Details", "cubewp-framework"),
                'name'       =>  'author',
                'type'       =>  'author',
            ),
            'shortcode'     =>  array(
                'label'      =>  __("Shortcode", "cubewp-frontend"),
                'name'       =>  'shortcode',
                'shortcode'  =>  '',
                'type'       =>  'shortcode',
            )
        );
        if (post_type_supports($key, 'comments')) {
            $author['comments'] = array(
                'label' => __("Comments", "cubewp-framework"),
                'name' => 'comments',
                'type' => 'comments',
            );
        }
        return apply_filters("cubewp/builder/single/custom/cubes",$author,$key);
    }
    
    /**
     * Method cubewp_single_page_default_fields
     *
     * @param array $field single field data
     *
     * @return array 
     * @since  1.0.0
     */
    public function cubewp_single_page_shortcode_setting($field_args, $field){
        if($field['name']=='shortcode'){
            $field_args['shortcode'] = array(
                'class'       => 'group-field field-shortcode',
                'label'       => esc_html__( "Shortcode", "cubewp-frontend" ),
                'name'       => 'shortcode',
                'type'       => 'text',
                'value'       => $field['shortcode'],
                'extra_attrs' => 'data-name="shortcode"',
            );
        }
        return $field_args;
    }
    
}