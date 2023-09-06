<?php

/**
 * fields output.
 *
 * @package cubewp-addon-frontend/cube/helpers
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Method cubewp_get_post_meta_value
 *
 * @param string $output
 * @param string $meta_key
 * @param int $post_id
 *
 * @return mixed
 */
if(!function_exists('cubewp_get_post_meta_value')){
    add_filter('cubewp_get_post_meta_value', 'cubewp_get_post_meta_value', 10, 3);    
    function cubewp_get_post_meta_value( $output = '', $meta_key = '', $post_id = 0 ) {
        
        $value = get_post_meta( $post_id, $meta_key, true );
        
        $default_fields  =  cubewp_post_type_default_fields();
        $custom_fields   =  get_option('cwp-custom-fields');
        
        $field_type = '';
        $field_options = array();
        if(isset($default_fields[$meta_key]) && !empty($default_fields[$meta_key])){
            $field_options = isset($default_fields[$meta_key]) ? $default_fields[$meta_key] : array();
            $field_type    = isset($default_fields[$meta_key]['type']) ? $default_fields[$meta_key]['type'] : 'text';
        }
        if(isset($custom_fields[$meta_key]) && !empty($custom_fields[$meta_key])){
            $field_options = isset($custom_fields[$meta_key]) ? $custom_fields[$meta_key] : array();
            $field_type    = isset($custom_fields[$meta_key]['type']) ? $custom_fields[$meta_key]['type'] : 'text';
        }
        
        switch ($field_type){
            case 'dropdown':
                if(isset($field_options['options']) && !empty($field_options['options'])){
                    $options = cwp_convert_choices_to_array($field_options['options']);
                    $output = isset($options[$value]) ? $options[$value] : $value;
                }
            break;
            case 'default':
                $output = $value;
            break;
        }
        
        return $output;
    }
}

/**
 * Method cubewp_get_post_meta_value_with_label
 *
 * @param string $output
 * @param string $meta_key
 * @param int $post_id
 *
 * @return mixed
 */
if(!function_exists('cubewp_get_post_meta_value_with_label')){
    add_filter('cubewp_get_post_meta_value_with_label', 'cubewp_get_post_meta_value_with_label', 10, 3);
    function cubewp_get_post_meta_value_with_label( $output = '', $meta_key = '', $post_id = 0 ) {
        
        $value = apply_filters('cubewp_get_post_meta_value', '', $meta_key, $post_id);
        
        $default_fields  =  cubewp_post_type_default_fields();
        $custom_fields   =  get_option('cwp-custom-fields');
        
        $label = '';
        if(isset($default_fields[$meta_key]) && !empty($default_fields[$meta_key])){
            $label = isset($default_fields[$meta_key]['label']) ? $default_fields[$meta_key]['label'] : '';
        }
        if(isset($custom_fields[$meta_key]) && !empty($custom_fields[$meta_key])){
            $label = isset($custom_fields[$meta_key]['label']) ? $custom_fields[$meta_key]['label'] : '';
        }
        
        return array( 'value' => $value, 'label' => $label );
    }
}