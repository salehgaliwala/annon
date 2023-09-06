<?php

/**
 * post type form builder.
 *
 * @package cubewp-addon-frontend/cube/modules/builder
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Post_Types_Form
 */
class CubeWp_Post_Types_Form extends CubeWp_Form_Builder {
    
    use CubeWp_Builder_Ui;
    protected static $FORM_TYPE = 'post_type';
    protected static $Wraper_class = '';
    protected static $Form_title = 'Post Types Form Builder';
    
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
        $wp_default_fields   =  cubewp_post_type_default_fields($key);
        $args = array(
            'section_title'        =>  esc_html__("WordPress Default Fields", "cubewp-frontend"),
            'section_description'  =>  '',
            'section_class'        =>  '',
            'open_close_class'     =>  'open',
            'form_relation'        =>  $key,
            'form_type'            =>  self::$FORM_TYPE,
            'fields'               =>  $wp_default_fields,
        );
        
        return $this->cwpform_form_section( $args );
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
                    'display_ui'    =>  'select',
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
        $output = '';
        $groups = cwp_get_groups_by_post_type($key);
        if(isset($groups) && !empty($groups)){
            foreach($groups as $group){
                $fields = get_post_meta($group, '_cwp_group_fields', true);
                $terms  = get_post_meta($group, '_cwp_group_terms', true);
                $fields = isset($fields) && !empty($fields) ? explode(',', $fields) : array();
                $terms  = isset($terms) && !empty($terms) ? explode(',', $terms) : array();
                if ( !empty( $fields ) && count($fields)>0) {
                    $args = array(
                        'section_title'        =>  esc_html(get_the_title($group)),
                        'section_description'  =>  '',
                        'section_class'        =>  '',
                        'open_close_class'     =>  'close',
                        'form_relation'        =>  $key,
                        'form_type'            =>  'post_type',
                        'fields'               =>  $fields,
                        'section_type'         =>  'group_fields',
                        'terms'                =>  $terms,
                    );
                    $output .= $this->cwpform_form_section( $args );
                }
            }
        }
        $output .= self::cubewp_custom_cubes_post_type_forms($key);
        return $output;
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

        $cwpform_post_types  =  CWP()->get_form( self::$FORM_TYPE );
        $content_switcher = $data['content_switcher'];
        $output = '';
        if(isset($cwpform_post_types) && !empty($cwpform_post_types[$key][$content_switcher]['groups'])){
            $counter = 0;
            foreach($cwpform_post_types[$key][$content_switcher]['groups'] as $section_data){
                $counter++;
                $section_data['open_close_class'] = ($counter == 1) ? 'open' : 'close';
                $section_data['form_relation']    = $key;
                $section_data['form_type']        = 'post_type';
                $section_data['content_switcher'] = $content_switcher;
                $output .= $this->cwpform_form_section( $section_data );
            }
            return $output;
        }else if(isset($cwpform_post_types) && !empty($cwpform_post_types[$key]['groups'])){
            $counter = 0;
            foreach($cwpform_post_types[$key]['groups'] as $section_data){            
                $counter++;
                $section_data['open_close_class'] = ($counter == 1) ? 'open' : 'close';
                $section_data['form_relation']    = $key;
                $section_data['form_type']        = 'post_type';
                $output .= $this->cwpform_form_section( $section_data );
            }
            return $output;
        }
    }

    public function setting_tab($empty,$key,$data) {
        
        $form_options  =  CWP()->get_form( self::$FORM_TYPE );
        $content_switcher = isset($data['content_switcher']) ? $data['content_switcher'] : '';
        $form_fields = isset($form_options[$key]['form']) ? $form_options[$key]['form'] : array();
        $form_fields = isset($form_options[$key][$content_switcher]['form']) ? $form_options[$key][$content_switcher]['form'] : $form_fields;
        return $this->cwpform_form_setting_fields( $form_fields, self::$FORM_TYPE,$key );
    }
    
    /**
     * Method create_post_types_form
     *
     * @return string html
     * @since  1.0.0
     */
    public function create_post_types_form(){
        
        add_filter('cubewp/builder/default/right/section',array($this,'load_section_right'),10,4);
        add_filter('cubewp/builder/right/settings',array($this,'setting_tab'),10,3);

        $types = CWP_all_post_types('post_types');
        $args = array(
            'form_type'         => self::$FORM_TYPE,
            'wrapper_class'     => self::$Wraper_class,
            'page_title'        => self::$Form_title,
            'switcher_types'    => $types,
            'switcher_title'    => esc_html__('Select Post Type','cubewp'),
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
    public function cubewp_custom_cubes_post_type_forms( $key ){
        $output = '';
        $custom_cubes_sections = apply_filters( 'cubewp/builder/post_type/custom/cubes/sections', array(), $key );
        if ( ! empty( $custom_cubes_sections ) && is_array( $custom_cubes_sections ) ) {
            foreach ( $custom_cubes_sections as $args ) {
               $default = array(
                  'section_title'       => '',
                  'section_description' => '',
                  'section_class'       => '',
                  'open_close_class'    => 'close',
                  'form_relation'       => $key,
                  'form_type'           => 'post_type',
                  'fields'              => array(),
                  'section_type'        => 'group_fields',
               );
               $args = wp_parse_args( $args, $default );
        
               $output .= $this->cwpform_form_section( $args );
            }
        }
        
        return $output;
    }

}