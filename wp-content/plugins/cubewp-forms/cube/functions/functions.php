<?php

if ( ! function_exists( 'cwp_leads_create_database' ) ) {
	function cwp_leads_create_database() {
		global $wpdb;
		$charset_collate       = $wpdb->get_charset_collate();
		$wpdb->query("CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "cwp_forms_leads` (
            `id` bigint(20) unsigned NOT NULL auto_increment,
            `lead_id` longtext NOT NULL,
            `user_id` bigint(20) DEFAULT NULL,
            `form_id` bigint(20) NOT NULL DEFAULT '0',
            `form_name` longtext NOT NULL,
            `post_author` bigint(20) UNSIGNED NOT NULL,
            `single_post` bigint(20) DEFAULT NULL,
            `fields` longtext NOT NULL,
            `dete_time` longtext NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate");
	}
	add_action( 'admin_init', 'cwp_leads_create_database', 20 );
}

if ( ! function_exists( 'cwp_insert_leads' ) ) {
	function cwp_insert_leads($data = array()) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . "cwp_forms_leads", array(
            'lead_id'       => isset($data['lead_id']) ? $data['lead_id'] : '',
            'user_id'       => isset($data['user_id']) ? $data['user_id'] : '',
            'form_id'       => isset($data['form_id']) ? $data['form_id'] : '',
            'form_name'     => isset($data['form_name']) ? $data['form_name'] : '',
            'post_author'   => isset($data['post_author']) ? $data['post_author'] : '',
            'single_post'   => isset($data['single_post']) ? $data['single_post'] : '',
            'fields'        => isset($data['fields']) ? serialize($data['fields']) : array(),
            'dete_time'	    => isset($data['dete_time']) ? $data['dete_time'] : ''
        ), array( '%s', '%d', '%d', '%s', '%d', '%s', '%s', '%d' ) );
    }
}

if ( ! function_exists( 'cwp_forms_all_leads' ) ) {
	function cwp_forms_all_leads( ) {
		global $wpdb;
		$leads     = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cwp_forms_leads", ARRAY_A );
		if(!empty($leads) && count($leads) > 0){
			return $leads;
		}
		return array();
	}
}

if ( ! function_exists( 'cwp_forms_all_leads_by_lead_id' ) ) {
	function cwp_forms_all_leads_by_lead_id( $leadid = '') {
		global $wpdb;
		$leads     = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cwp_forms_leads WHERE lead_id='{$leadid}'", ARRAY_A );
		if(!empty($leads) && count($leads) > 0){
			return $leads;
		}
		return array();
	}
}

if ( ! function_exists( 'cwp_forms_all_leads_by_id' ) ) {
	function cwp_forms_all_leads_by_id( $id = '') {
		global $wpdb;
		$leads     = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cwp_forms_leads WHERE id={$id}", ARRAY_A );
		if(!empty($leads) && count($leads) > 0){
			return $leads;
		}
		return array();
	}
}
if ( ! function_exists( 'cwp_forms_all_leads_by_post_author' ) ) {
	function cwp_forms_all_leads_by_post_author( $id = '') {
		global $wpdb;
		$leads     = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cwp_forms_leads WHERE post_author={$id}", ARRAY_A );
		if(!empty($leads) && count($leads) > 0){
			return $leads;
		}
		return array();
	}
}
/**
 * Method cwp_dashboard_leads_tab
 *
 * @return string html
 * @since  1.0.0
 */
if ( ! function_exists('cwp_dashboard_leads_tab')) {
    function cwp_dashboard_leads_tab(){
        return CubeWp_Forms_Dashboard::cwp_leads();
    }
}

/**
 * Method cwp_remove_lead_from_post
 *
 * @param int $leadid
 *
 * @return void
 * @since  1.0.0
 */
if ( ! function_exists('cwp_remove_lead_from_post')) {
    function cwp_remove_lead_from_post($leadid = 0){
        if($leadid == 0)
        return;

        $form_id = cwp_form_id_by_lead_id($leadid);
        $form_data_id = get_post_meta($form_id, '_cwp_custom_form_data_id', true);
        $form_data_id = json_decode($form_data_id);
        unset($form_data_id->$leadid);
        update_post_meta($form_id, '_cwp_custom_form_data_id', json_encode($form_data_id));
    }
}

/**
 * Method cwp_remove_lead_from_author
 *
 * @param int $leadid
 *
 * @return void
 * @since  1.0.0
 */
if ( ! function_exists('cwp_remove_lead_from_author')) {
    function cwp_remove_lead_from_author($leadid = 0){
        if($leadid == 0)
        return;

        $author_id = cwp_author_id_by_lead_id($leadid);
        $user_form_data_id = json_decode(get_user_meta($author_id, '_cwp_custom_form_data_id', true));
        unset($user_form_data_id->$leadid);
        update_user_meta( $author_id, '_cwp_custom_form_data_id', json_encode($user_form_data_id));
    }
}

/**
 * Method cwp_remove_lead
 *
 * @param int $leadid
 *
 * @return void
 * @since  1.0.0
 */
if ( ! function_exists('cwp_remove_lead')) {
    function cwp_remove_lead($leadid = 0){
        if($leadid == 0)
        return;
        global $wpdb;
        cwp_remove_lead_from_author($leadid);
        cwp_remove_lead_from_post($leadid);
        $wpdb->delete( $wpdb->prefix.'cwp_forms_leads', array( 'lead_id' => $leadid ), array( '%s' ) );
    }
}

/**
 * Method cwp_form_id_by_lead_id
 *
 * @param int $leadid
 *
 * @return int
 * @since  1.0.0
 */
if ( ! function_exists('cwp_form_id_by_lead_id')) {
    function cwp_form_id_by_lead_id($leadid = 0){
        if($leadid == 0)
        return;

        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        if(isset($form_data['form_id'])){
            return $form_data['form_id'];
        }
    }
}

/**
 * Method cwp_post_id_by_lead_id
 *
 * @param int $leadid
 *
 * @return int
 * @since  1.0.0
 */
if ( ! function_exists('cwp_post_id_by_lead_id')) {
    function cwp_post_id_by_lead_id($leadid = 0){
        if($leadid == 0)
        return;

        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        if(isset($form_data['single_post'])){
            return $form_data['single_post'];
        }
    }
}

/**
 * Method cwp_author_id_by_lead_id
 *
 * @param int $leadid
 *
 * @return int
 * @since  1.0.0
 */
if ( ! function_exists('cwp_author_id_by_lead_id')) {
    function cwp_author_id_by_lead_id($leadid = 0){
        if($leadid == 0)
        return;

        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        if(isset($form_data['post_author'])){
            return $form_data['post_author'];
        }
    }
}

/**
 * Method cwp_lead_date_by_lead_id
 *
 * @param int $leadid
 *
 * @return int
 * @since  1.0.0
 */
if ( ! function_exists('cwp_lead_date_by_lead_id')) {
    function cwp_lead_date_by_lead_id($leadid = 0){
        if($leadid == 0)
        return;

        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        if(isset($form_data['dete_time'])){
            return $form_data['dete_time'];
        }
    }
}
/**
 * Method cwp_upload_custom_form_gallery_images
 *
 * @param int $key
 * @param array $val
 * @param array $val
 * @param int $post_id
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists('cwp_upload_custom_form_gallery_images')) {
    function cwp_upload_custom_form_gallery_images( $key = '', $val = array(), $files = array(), $post_id = 0 ){
        
        $attachment_ids = array();
        if(isset($val) && !empty($val) && is_array($val)){
            foreach($val as $file_id){
                if(isset($files['cwp_custom_form']['name']['fields'][$key][$file_id])){
                    $file_names = $files['cwp_custom_form']['name']['fields'][$key][$file_id];
                    foreach($file_names as $file_key => $file_name){
                        if( $file_name != '' ){
                            $file = array( 
                                'name'     => $files['cwp_custom_form']['name']['fields'][$key][$file_id][$file_key],
                                'type'     => $files['cwp_custom_form']['type']['fields'][$key][$file_id][$file_key],
                                'tmp_name' => $files['cwp_custom_form']['tmp_name']['fields'][$key][$file_id][$file_key],
                                'error'    => $files['cwp_custom_form']['error']['fields'][$key][$file_id][$file_key],
                                'size'     => $files['cwp_custom_form']['size']['fields'][$key][$file_id][$file_key] 
                            );
                            $attachment_ids[] = cwp_handle_attachment( $file, $post_id);
                        }
                    }
                }else{
                    $attachment_ids[] = $file_id;
                }
            }
        }
        return $attachment_ids;
    }
}

/**
 * Method cwp_upload_custom_form_repeating_gallery_images
 *
 * @param int $key
 * @param int $_key
 * @param int $field_key
 * @param array $val
 * @param array $files
 * @param int $post_id
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists('cwp_upload_custom_form_repeating_gallery_images')) {
    function cwp_upload_custom_form_repeating_gallery_images( $key = '',$_key = '',$field_key= '', $val = array(), $files = array(), $post_id = 0 ){
        
        $attachment_ids = array();
        if(isset($val) && !empty($val) && is_array($val)){
            foreach($val as $file_id){
                if(isset($files['cwp_custom_form']['name']['fields'][$key][$_key][$field_key][$file_id])){
                    $file_names = $files['cwp_custom_form']['name']['fields'][$key][$_key][$field_key][$file_id];
                    foreach($file_names as $file_key => $file_name){
                        if( $file_name != '' ){
                            $file = array( 
                                'name'     => $files['cwp_custom_form']['name']['fields'][$key][$_key][$field_key][$file_id][$file_key],
                                'type'     => $files['cwp_custom_form']['type']['fields'][$key][$_key][$field_key][$file_id][$file_key],
                                'tmp_name' => $files['cwp_custom_form']['tmp_name']['fields'][$key][$_key][$field_key][$file_id][$file_key],
                                'error'    => $files['cwp_custom_form']['error']['fields'][$key][$_key][$field_key][$file_id][$file_key],
                                'size'     => $files['cwp_custom_form']['size']['fields'][$key][$_key][$field_key][$file_id][$file_key] 
                            );
                            $attachment_ids[] = cwp_handle_attachment( $file, $post_id);
                        }
                    }
                }else{
                    $attachment_ids[] = $file_id;
                }
            }
        }
        return $attachment_ids;
    }
}

/**
 * Method cwp_upload_custom_form_file
 *
 * @param int $key
 * @param array $val
 * @param array $files
 * @param int $post_id
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists('cwp_upload_custom_form_file')) {
    function cwp_upload_custom_form_file( $key = '', $val = array(), $files = array(), $post_id = 0 ){
    
        $attachment_id = '';
        if(isset($files['cwp_custom_form']['name']['fields'][$key]) && $files['cwp_custom_form']['name']['fields'][$key] != ''){
            $file = array( 
                'name'     => $files['cwp_custom_form']['name']['fields'][$key],
                'type'     => $files['cwp_custom_form']['type']['fields'][$key],
                'tmp_name' => $files['cwp_custom_form']['tmp_name']['fields'][$key],
                'error'    => $files['cwp_custom_form']['error']['fields'][$key],
                'size'     => $files['cwp_custom_form']['size']['fields'][$key] 
            );
            $attachment_id = cwp_handle_attachment( $file, $post_id);
        }else if(isset($val) && $val != 0){
            $attachment_id = $val;
        }
        return $attachment_id;
    }
}
add_filter( 'cubewp/custom_fields/custom_forms/fields', 'custom_form_fields_update', 9,2 );

/**
 * Method custom_form_fields_update
 *
 * @param array $fields_settings 
 * @param array $fieldData 
 *
 * @return array
 * @since  1.0.0
 */
function custom_form_fields_update($fields_settings = array(), $fieldData = array()) {
    unset($fields_settings['field_rest_api']);
    unset($fields_settings['field_admin_size']);
    unset($fields_settings['field_relationship']);
    unset($fields_settings['field_map_use']);
    return $fields_settings;
}

if ( ! function_exists( 'cubewp_add_recaptcha_settings_sections' ) ) {
    function cubewp_add_recaptcha_settings_sections( $sections ) {
       $sections['recaptcha-settings'] = array(
          'title'  => __( 'reCAPTCHA Config', 'cubewp' ),
          'id'     => 'recaptcha-settings',
          'icon'   => 'dashicons-shield',
          'fields' => array(
             array(
                'id'      => 'recaptcha',
                'type'    => 'switch',
                'title'   => __( 'Enable reCAPTCHA', 'cubewp-framework' ),
                'default' => '0',
                'desc'    => __( 'Enable if you reCAPTCHA on your CubeWP forms.', 'cubewp-framework' ),
             ),
             array(
                'id'       => 'recaptcha_type',
                'type'     => 'select',
                'title'    => __( 'Select reCAPTCHA Type', 'cubewp-framework' ),
                'subtitle' => '',
                'desc'     => __( 'Select the type of reCAPTCHA you want to use on to your CubeWP forms.', 'cubewp-framework' ),
                'options'  => array(
                   'google_v2' => __( 'Google reCAPTCHA v2 Checkbox', 'cubewp-framework' ),
                ),
                'default'  => 'google_v2',
                'required' => array(
                   array( 'recaptcha', 'equals', '1' )
                )
             ),
             array(
                'id'       => 'google_recaptcha_sitekey',
                'type'     => 'text',
                'title'    => __( 'Site Key', 'cubewp-framework' ),
                'default'  => '',
                'desc'     => __( 'Please enter google reCAPTCHA v2 Or v3 site key here.', 'cubewp-framework' ),
                'required' => array(
                   array( 'recaptcha', 'equals', '1' )
                )
             ),
             array(
                'id'       => 'google_recaptcha_secretkey',
                'type'     => 'text',
                'title'    => __( 'Secret Key', 'cubewp-framework' ),
                'default'  => '',
                'desc'     => __( 'Please enter google reCAPTCHA v2 Or v3 secret key here.', 'cubewp-framework' ),
                'required' => array(
                   array( 'recaptcha', 'equals', '1' )
                )
             ),
          )
       );
 
       return $sections;
    }
 
    add_filter( 'cubewp/options/sections', 'cubewp_add_recaptcha_settings_sections', 9, 1 );
 }