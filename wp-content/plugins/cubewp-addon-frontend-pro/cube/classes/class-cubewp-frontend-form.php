<?php
/**
 * contains all frontend form sections and fields
 *
 * @version 1.0
 * @package cubewp-frontend/cube/classes
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Frontend_Form
 */


class CubeWp_Frontend_Form {    
    public $custom_fields;
    public $wp_default_fields;
    /**
	 * Method frontend_form_section
	 *
	 * @param array $section_data
	 * @param string $type
	 *
	 * @return string
     * @since  1.0.0
	 */
    public function frontend_form_section( $section_data = array(), $type = '' ){
        extract(shortcode_atts(array(
                'section_id'          =>  '',
                'section_title'       =>  '',
                'section_description' =>  '',
                'section_class'       =>  '',
                'section_number'      =>  '',
                'total_sections'      =>  '',
                'fields'              =>  '',
                'post_content'        =>  array(),
            ), $section_data)
        );
        $this->wp_default_fields  =  cubewp_post_type_default_fields($type);
        $this->custom_fields =  CWP()->get_custom_fields( 'post_types' );
        $section_id =  $section_id != '' ? ' id="'. esc_attr($section_id).'"' : '';
        $output  = '<div'. $section_id .' class="cwp-frontend-section-container '. esc_attr($section_class).'" data-section="'.esc_attr($section_number).'">';
        $output .= '<div class="cwp-frontend-section-heading-container">';
        if( isset($section_title) && $section_title != '' ){
            $output .= '<h2>'. esc_attr($section_title) .'</h2>';
        }
        if( isset($section_description) && $section_description != '' ){
            $output .= apply_filters('the_content', $section_description);
        }
        $output .= '</div>';
        if(isset($section_data['fields']) && !empty($section_data['fields'])){
            $output .= '<div class="cwp-frontend-section-content-container">';
                $output .= apply_filters("cubewp/frontend/form/{$type}/section/fields", $type, $section_data['fields'], $section_data['post_content'] );
            $output .= '</div>';
        }
        $output .= '</div>';
        $output = apply_filters("cubewp/frontend/form/{$type}/section/", $output, $section_data );
        return $output;
    }

    /**
	 * Method fields
	 *
	 * @param string $empty
	 * @param string $fields
	 *
	 * @return string
     * @since  1.0.0
	 */
    public function fields( $type='', $fields = array(),$post_content = array() ) {
        $output = '';
        foreach($fields as $field_name => $field_options){
            if(isset($this->custom_fields[$field_name]) && !empty($this->custom_fields[$field_name])){
                $field_options['custom_name'] =   'cwp_user_form[cwp_meta][' . $field_name . ']';
                $field_options['post_type'] =   $type;
                $field_options['id']          =   isset($this->custom_fields[$field_name]['id']) ? $this->custom_fields[$field_name]['id'] : $field_name;
                if($field_options['type'] == 'google_address' ){
                    $field_options['custom_name_lat'] =   'cwp_user_form[cwp_meta][' . $field_options['name'].'_lat' . ']';
                    $field_options['custom_name_lng'] =   'cwp_user_form[cwp_meta][' . $field_options['name'].'_lng' . ']';
                }
                if($field_options['type'] == 'taxonomy' ){
                    $field_options['filter_taxonomy'] = isset($this->custom_fields[$field_name]['filter_taxonomy']) ? $this->custom_fields[$field_name]['filter_taxonomy'] : '';
                    $field_options['appearance']      = $field_options['display_ui'];
                }


                if(isset($post_content) && !empty($post_content)){
                    $field_options['value'] = get_post_meta( $post_content->ID, $field_options['name'], true );
                    if($field_options['type'] == 'google_address' ){
                        $field_options['lat'] = get_post_meta( $post_content->ID, $field_options['name'].'_lat', true );
                        $field_options['lng'] = get_post_meta( $post_content->ID, $field_options['name'].'_lng', true );
                    }
                }else{
                    $field_options['value'] = isset($this->custom_fields[$field_name]['default_value']) ? $this->custom_fields[$field_name]['default_value'] : '';
                }
                $field_options = wp_parse_args($field_options, $this->custom_fields[$field_name]);
                if(isset($field_options['sub_fields']) && !empty($field_options['sub_fields'])){
                    $sub_fields = explode(',', $field_options['sub_fields']);
                    $field_options['sub_fields'] = array();
                    foreach($sub_fields as $sub_field){
                        $field_options['sub_fields'][] = $this->custom_fields[$sub_field];
                    }
                }
                if(isset($field_options['group_id']) && !empty($field_options['group_id'])){
                    $terms  = get_post_meta($field_options['group_id'], '_cwp_group_terms', true);
                    if(isset($terms) && !empty($terms)){
                        //$termSLug = cwp_term_by('id','comma', $terms, false);
                        $field_options['container_attrs'] = ' data-terms="'. $terms .'"';
                        $field_options['container_class'] = ' cwp-conditional-by-term';
                    }
                }
            }else if(isset($this->wp_default_fields[$field_name]) && !empty($this->wp_default_fields[$field_name])){
                $field_options['custom_name'] = 'cwp_user_form[' . $field_name . ']';
                $field_options['id']          =   isset($this->wp_default_fields[$field_name]['id']) ? $this->wp_default_fields[$field_name]['id'] : $field_name;
                
                if( $field_name == 'featured_image' ){
                    $field_options['custom_name'] = 'cwp_user_form[cwp_meta][' . $field_name . ']';
                    $field_options['container_class'] = isset($this->wp_default_fields[$field_name]['container_class']) ? $this->wp_default_fields[$field_name]['container_class'] : '';
                }
                if(isset($post_content) && !empty($post_content)){
                    if( $field_name == 'the_title' ){
                        $field_options['value'] = isset($post_content->post_title) ? $post_content->post_title : '';
                    }
                    if( $field_name == 'the_content' ){
                        $field_options['value'] = isset($post_content->post_content) ? $post_content->post_content : '';
                    }
                    if( $field_name == 'the_excerpt' ){
                        $field_options['value'] = isset($post_content->post_excerpt) ? $post_content->post_excerpt : '';
                    }
                    if( $field_name == 'featured_image' ){
                        $field_options['value'] = get_post_thumbnail_id($post_content->ID);
                    }
                }
                
                $field_options = wp_parse_args($field_options, $this->wp_default_fields[$field_name]);
            }else{
                if( $field_options['type'] == 'taxonomy' ){
                    $field_options['id']              = $field_name;
                    $field_options['filter_taxonomy'] = $field_name;
                    $field_options['appearance']      = $field_options['display_ui'];
                    $field_options['custom_name']     = 'cwp_user_form[term]['. $field_name .']';
                    if(isset($post_content->ID)){
                        $post_terms = wp_get_post_terms($post_content->ID, $field_name);
                        if(isset($post_terms) && !empty($post_terms)){
                            $field_options['value'] = wp_list_pluck($post_terms,'term_id');
                        }
                    }
                }
                $field_options = apply_filters( 'cubewp/custom/cube/field/options', $field_options );
            }
            $output .=  apply_filters("cubewp/frontend/{$field_options['type']}/field", '', $field_options);
        }
        return $output;
    }

    /**
	 * Method redirect_to_plans
	 *
	 * @param string $type
	 * @param int $plan_id
	 *
	 * @return string
     * @since  1.0.0
	 */
    public static function redirect_to_plans($type='',$plan_id=''){
        global $cwpOptions;
        if(class_exists('CubeWp_Payments_Load')){
            if( isset($cwpOptions['paid_submission']) && $cwpOptions['paid_submission'] == 'yes' && $plan_id <= 0 ){
                $query_args           =   array( 
                    'post_type'       => 'price_plan', 
                    'post_status'     => 'publish', 
                    'posts_per_page'  => -1, 
                    'fields'          => 'ids',
                    'meta_query'      => array(
                        array(
                            'key'	    =>  'plan_post_type',
                            'value'	    =>  $type,
                            'compare'   =>  '=',
                        )
                    )
                );
                $price_plans = get_posts( $query_args );
                if( isset($price_plans) && !empty($price_plans) ){
                    $price_plan = isset($cwpOptions['price_plan']) ? $cwpOptions['price_plan'] : '';
                    if( $price_plan != '' ){
                        wp_redirect(add_query_arg('cwp_ptype', $type, get_permalink($price_plan)));
                        exit();
                    }
                }
                wp_reset_postdata();
            }
        }
    }

    /**
	 * Method save_post_custom_fields
	 *
	 * @param array $metas
	 * @param array $fieldOptions
	 * @param array $FILES
	 * @param int $postID
	 *
	 * @return string
     * @since  1.0.0
	 */
    public static function save_post_custom_fields($metas = array(), $FILES = array(), $postID = ''){
        $fieldOptions = CWP()->get_custom_fields( 'post_types' );
        if(isset($metas) && !empty($metas)){
            foreach($metas as $key => $val){
                $singleFieldOptions = isset($fieldOptions[$key]) ? $fieldOptions[$key] : array();
                if ( empty( $singleFieldOptions ) ) {
                    $custom_cubes_args = array(
                        'name' => $key
                    );
                    $singleFieldOptions = apply_filters( 'cubewp/custom/cube/field/options', $custom_cubes_args );
                }
                if( $key == 'featured_image' ){
                    $attachment_id = cwp_upload_form_file( $key, $val, $FILES, $postID );
                    if(isset($attachment_id) && !empty($attachment_id)){
                        set_post_thumbnail($postID, $attachment_id);
                    }else{
                        delete_post_meta( $postID, '_thumbnail_id' );
                    }
                }
                $value = $val;
                if ( isset($singleFieldOptions['type']) && ( isset($singleFieldOptions['relationship']) && $singleFieldOptions['type'] == 'post' && $singleFieldOptions['relationship'] ) && is_array( $singleFieldOptions ) && count( $singleFieldOptions ) > 0 ) {
                    if ( ! is_array( $value ) ) {
                        $value = array( $value );
                    }
                    if ( ! empty($value) && count($value) > 0) {
                        (new CubeWp_Relationships)->save_relationship( $postID, $value, $key, 'PTP' );
                    }
                }else if ( isset($singleFieldOptions['type']) && ( isset($singleFieldOptions['relationship']) && $singleFieldOptions['type'] == 'user' && $singleFieldOptions['relationship'] ) && is_array( $singleFieldOptions ) && count( $singleFieldOptions ) > 0 ) {
                    if ( ! is_array( $value ) ) {
                        $value = array( $value );
                    }
                    if ( ! empty($value) && count($value) > 0) {
                        (new CubeWp_Relationships)->save_relationship( $postID, $value, $key, 'PTU' );
                    }
                }
                $save_format = isset( $singleFieldOptions['files_save'] ) && ! empty( $singleFieldOptions['files_save'] ) ? $singleFieldOptions['files_save'] : 'ids';
                $format_separator = isset( $singleFieldOptions['files_save_separator'] ) && ! empty( $singleFieldOptions['files_save_separator'] ) ? $singleFieldOptions['files_save_separator'] : 'array';
	            if ( ( isset( $singleFieldOptions['type'] ) && $singleFieldOptions['type'] == 'dropdown' && ( isset( $singleFieldOptions['multiple'] ) && $singleFieldOptions['multiple'] ) ) || ( isset( $singleFieldOptions['type'] ) && $singleFieldOptions['type'] == 'checkbox' ) || ( isset( $singleFieldOptions['appearance'] ) && $singleFieldOptions['appearance'] == 'multi_select' ) ) {
					if ( $format_separator != 'array' ) {
						$val = implode( $format_separator, $val );
		            }
	            }
                if(isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'gallery' ){
                    $attachment_ids = cwp_upload_form_gallery_images( $key, $val, $FILES, $postID );
                    if(isset($attachment_ids) && !empty($attachment_ids)){
                        if ( $save_format == 'urls' ) {
                            $_attachment_ids = array();
                            foreach ( $attachment_ids as $attachment_id ) {
                                $_attachment_ids[] = wp_get_attachment_url( cwp_get_attachment_id( $attachment_id ) );
                            }
                            $attachment_ids = $_attachment_ids;
                        }
                        if ( $format_separator == 'array' ) {
                            update_post_meta( $postID, $key, $attachment_ids );
                        }else {
                            $attachment_ids = implode( $format_separator, $attachment_ids );
                            update_post_meta( $postID, $key, $attachment_ids );
                        }
                    }else{
                        delete_post_meta( $postID, $key );
                    }

                }else if((isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'file') ||
                (isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'image') ){
                    $attachment_id = cwp_upload_form_file( $key, $val, $FILES, $postID );
                    if(isset($attachment_id) && !empty($attachment_id)){
                        if ( $save_format == 'urls' ) {
                            $attachment_url = wp_get_attachment_url( cwp_get_attachment_id( $attachment_id ) );
                            update_post_meta( $postID, $key, $attachment_url );
                        }else {
                            update_post_meta( $postID, $key, $attachment_id );
                        }
                    }else{
                        delete_post_meta( $postID, $key );
                    }
                } else if(isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'repeating_field' ){
                    $arr = array();
					$val = array_filter( $val );
					if ( ! empty( $val ) ) {
						$singleFieldOptions_bk = $singleFieldOptions;
						foreach ( $val as $_key => $_val ) {
						   $singleFieldOptions = $singleFieldOptions_bk;
							$parent_field = $singleFieldOptions;
							$singleFieldOptions = isset($fieldOptions[$_key]) ? $fieldOptions[$_key] : array();
							if ( empty( $singleFieldOptions ) && isset( $parent_field['sub_fields'] ) ) {
								$sub_fields = $parent_field['sub_fields'];
								$singleFieldOptions = isset($sub_fields[$_key]) ? $sub_fields[$_key] : array();
							}
							if(isset($singleFieldOptions['type']) || str_contains( $_key, '_lat' ) || str_contains( $_key, '_lng' ) ){
								foreach($_val as $field_key => $field_val){
									if((isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'gallery')){
										$field_val = cwp_upload_form_repeating_gallery_images( $key, $_key,$field_key, $field_val, $FILES, $postID );
										$save_format = isset( $singleFieldOptions['files_save'] ) && ! empty( $singleFieldOptions['files_save'] ) ? $singleFieldOptions['files_save'] : 'ids';
										$format_separator = isset( $singleFieldOptions['files_save_separator'] ) && ! empty( $singleFieldOptions['files_save_separator'] ) ? $singleFieldOptions['files_save_separator'] : 'array';
										if ( ! empty( $field_val ) ) {
											if ( $save_format == 'urls' ) {
												$_attachment_ids = array();
												foreach ( $field_val as $_field_val ) {
													$_attachment_ids[] = wp_get_attachment_url( cwp_get_attachment_id( $_field_val ) );
												}
												$field_val = $_attachment_ids;
											}
											if ( $format_separator != 'array' ) {
												$field_val = implode( $format_separator, $field_val );
											}
										}
									}
									if((isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'file') || 
									(isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'image')
									){
										if(isset($FILES['cwp_user_form']['name']['cwp_meta'][$key][$_key][$field_key]) && $FILES['cwp_user_form']['name']['cwp_meta'][$key][$_key][$field_key] != ''){
											$file = array(
												'name'     => $FILES['cwp_user_form']['name']['cwp_meta'][$key][$_key][$field_key],
												'type'     => $FILES['cwp_user_form']['type']['cwp_meta'][$key][$_key][$field_key],
												'tmp_name' => $FILES['cwp_user_form']['tmp_name']['cwp_meta'][$key][$_key][$field_key],
												'error'    => $FILES['cwp_user_form']['error']['cwp_meta'][$key][$_key][$field_key],
												'size'     => $FILES['cwp_user_form']['size']['cwp_meta'][$key][$_key][$field_key]
											);
											$field_val = cwp_handle_attachment( $file, $postID);
										}
										if ( ! empty( $field_val ) ) {
											$save_format = isset( $singleFieldOptions['files_save'] ) && ! empty( $singleFieldOptions['files_save'] ) ? $singleFieldOptions['files_save'] : 'ids';
											if ( $save_format == 'urls' ) {
												$attachment_url = wp_get_attachment_url( cwp_get_attachment_id( $field_val ) );
												$field_val = $attachment_url;
											}
										}
										if( $field_val != 0 ){
											$arr[$field_key][$_key] = $field_val;
										}
									}else if ( ( isset( $singleFieldOptions['type'] ) && $singleFieldOptions['type'] == 'dropdown' && ( isset( $singleFieldOptions['multiple'] ) && $singleFieldOptions['multiple'] ) ) || ( isset( $singleFieldOptions['type'] ) && $singleFieldOptions['type'] == 'checkbox' ) || ( isset( $singleFieldOptions['appearance'] ) && $singleFieldOptions['appearance'] == 'multi_select' ) ) {
										$format_separator = isset( $singleFieldOptions['files_save_separator'] ) && ! empty( $singleFieldOptions['files_save_separator'] ) ? $singleFieldOptions['files_save_separator'] : 'array';
										if ( $format_separator != 'array' ) {
											$field_val = implode( $format_separator, $field_val );
										}
										$arr[$field_key][$_key] = $field_val;
									}else{
										$arr[$field_key][$_key] = $field_val;
									}

								}
							}
						}
					}
					if(isset($arr) && !empty($arr)){
					   $_arr = array_filter($arr);
					   update_post_meta( $postID, $key, $_arr );
					}else{
					   delete_post_meta( $postID, $key );
					}
                }else{
                    if(isset($singleFieldOptions['type']) && ($singleFieldOptions['type'] == 'date_picker' || $singleFieldOptions['type'] == 'date_time_picker' || $singleFieldOptions['type'] == 'time_picker') ){
                        $val = strtotime($val);
                    }
                    $lat[] = $key;
                    update_post_meta( $postID, $key, $val );
                }
            }
        }
    }

        /**
	 * Method assign_plan_to_post
	 *
	 * @param int $pid
	 * @param int $plan_id
	 * @param int $postID
	 *
	 * @return string
     * @since  1.0.0
	 */
    public static function assign_plan_to_post($pid = 0, $plan_id = 0,$postID = 0){
        if($postID == 0 || $plan_id == 0) return;
        global $cwpOptions;
        if( $pid == 0){
            $paid_submission      =  isset($cwpOptions['paid_submission']) ? $cwpOptions['paid_submission'] : '';
            $plan_price           =  get_post_meta($plan_id, 'plan_price', true);
            update_post_meta( $postID, 'plan_id', $plan_id);
            if( $paid_submission != 'no' && $plan_id > 0 && $plan_price > 0 ){
                update_post_meta( $postID, 'payment_status', 'pending');
            }else{
                if( $plan_id > 0  && class_exists("CubeWp_Payments_Load") ){
                    $plan_duration  =  cwp_plan_duration($plan_id);
                    if( $plan_duration > 0 ){
                        $post_expired   = strtotime("+". $plan_duration." days", strtotime(current_time('Y-m-d H:i:s')));
                        update_post_meta($postID, 'post_expired', $post_expired);
                    }
                }
                update_post_meta( $postID, 'payment_status', 'free');
            }
        }
    }
}