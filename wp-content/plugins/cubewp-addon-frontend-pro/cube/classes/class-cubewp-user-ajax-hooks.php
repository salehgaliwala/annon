<?php

/**
 * This file handles all frontend user based ajax calls.
 *
 * @package cubewp-addon-frontend/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CubeWp_User_Ajax_Hooks
 */
class CubeWp_User_Ajax_Hooks{
    
    public function __construct() {
        add_action('wp_ajax_nopriv_cubewp_submit_user_register', array($this, 'cubewp_submit_user_register'));
        add_action('wp_ajax_cubewp_update_user_profile', array($this, 'cubewp_update_user_profile'));
        add_action('wp_ajax_nopriv_cubewp_ajax_login', array($this, 'cubewp_ajax_login'));
        add_filter('wp_ajax_cubewp_publish_post', array($this, 'cubewp_publish_post'), 10);
        add_action( 'wp_ajax_nopriv_cubewp_ajax_forget_password', array( $this, 'cubewp_ajax_forget_password' ) );
    }
        
    /**
     * Method cubewp_submit_user_register
     *
     * @return array json
     * @since  1.0.0
     */
    public function cubewp_submit_user_register(){
        
        if ( ! wp_verify_nonce($_POST['security_nonce'], "cubewp_submit_user_register")) {
            wp_send_json(
                array(
                'type' => 'error',
                'msg'  => esc_html__('Sorry! Security Verification Failed.', 'cubewp-frontend'),
                )
            );
        }


        if (isset($_POST['g-recaptcha-response'])) {
            CubeWp_Frontend_Recaptcha::cubewp_captcha_verification("cubewp_captcha_user_registration", cubewp_core_data($_POST['g-recaptcha-response']));
        }

        $default_fields  =   isset($_POST['cwp_user_register']['default_fields']) ? CubeWp_Sanitize_Text_Array($_POST['cwp_user_register']['default_fields'])  : array();
        $custom_fields   =   isset($_POST['cwp_user_register']['custom_fields']) ? CubeWp_Sanitize_Fields_Array($_POST['cwp_user_register']['custom_fields'],'user')  : array();
        $user_login      =   isset($default_fields['user_login'])    ? sanitize_text_field($default_fields['user_login'])     :  '';
        $user_login_not  =   !isset($default_fields['user_login'])   ? true : false;
        $user_email      =   isset($default_fields['user_email'])    ? sanitize_email($default_fields['user_email'])          :  '';
        $user_email_not  =   !isset($default_fields['user_email'])   ? true : false;
        $user_pass       =   isset($default_fields['user_pass'])     ? sanitize_text_field($default_fields['user_pass'])      :  '';
        $confirm_pass    =   isset($default_fields['confirm_pass'])  ? sanitize_text_field($default_fields['confirm_pass'])   :  '';

        
        if($user_login_not == true){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("The default username field doesn't exist.", "cubewp-framework"),
                )
            );
        }else if($user_login == ''){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("The username field is empty.", "cubewp-framework"),
                )
            );
        }else if($user_email_not == true){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("The default email field doesn't exist.", "cubewp-framework"),
                )
            );
        }else if($user_email == ''){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("The email field is empty.", "cubewp-framework"),
                )
            );
        }else if(!is_email($user_email)){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("The email address is invalid.", "cubewp-framework"),
                )
            );
        }else if(username_exists($user_login)){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("This username already exists.", "cubewp-framework"),
                )
            );
        }else if(email_exists($user_email)){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("This Email already exists.", "cubewp-framework"),
                )
            );
        }
        
        if( (isset($default_fields['user_pass']) && isset($default_fields['confirm_pass'])) && $default_fields['confirm_pass'] != $default_fields['user_pass']){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("Password Mismatch.", "cubewp-framework"),
                )
            );
        }
        
        if($user_pass == '' ){
            $default_fields['password'] = wp_generate_password( 12, false );
        }
        
        $wp_insert_data = array();
        foreach($default_fields as $key => $val){
            $wp_insert_data[$key] = $val;
        }
        $wp_insert_data = apply_filters('cubewp/before/user/registration', $wp_insert_data );
        $user_id = wp_insert_user($wp_insert_data);
        if(is_wp_error($user_id)){
            $error = $user_id->get_error_message();
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => $user_id->get_error_message(),
                )
            );
        }else{
            
            if( $user_pass != '' ){
                $info = array();
                $info['user_login']    = $user_login;
                $info['user_password'] = $user_pass;
                $info['remember']      = true;
                if (is_ssl()) {
                    wp_signon( $info, true );
                }else{
                    wp_signon( $info, false );
                }
            }
            
            $user_data = get_user_by('id', $user_id);
            
            
            // Update User Custom Fields
            $fieldOptions = CWP()->get_custom_fields( 'user' );
            if(isset($custom_fields) && !empty($custom_fields)){
                foreach($custom_fields as $key => $val){
                    $singleFieldOptions = isset($fieldOptions[$key]) ? $fieldOptions[$key] : array();
                    if(isset($singleFieldOptions['type'])){
                        $value = $val;
                        if ( ( isset($singleFieldOptions['relationship']) && $singleFieldOptions['type'] == 'post' && $singleFieldOptions['relationship'] ) && is_array( $singleFieldOptions ) && count( $singleFieldOptions ) > 0 ) {
                            if ( ! is_array( $value ) ) {
                                $value = array( $value );
                            }
                            if ( ! empty($value) && count($value) > 0) {
                                (new CubeWp_Relationships)->save_relationship( $user_id, $value, $key, 'UTP' );
                            }
                        }else if ( (isset($singleFieldOptions['relationship']) && $singleFieldOptions['type'] == 'user' && $singleFieldOptions['relationship'] ) && is_array( $singleFieldOptions ) && count( $singleFieldOptions ) > 0 ) {
                            if ( ! is_array( $value ) ) {
                                $value = array( $value );
                            }
                            if ( ! empty($value) && count($value) > 0) {
                                (new CubeWp_Relationships)->save_relationship( $user_id, $value, $key, 'UTU' );
                            }
                        }

                        if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'gallery' ){

                            $attachment_ids = cwp_upload_user_gallery_images( $key, $val, $_FILES, $user_id, 'cwp_user_register' );
                            if(isset($attachment_ids) && !empty($attachment_ids)){
                                update_user_meta( $user_id, $key, $attachment_ids );
                            }else{
                                delete_user_meta( $user_id, $key );
                            }

                        }else if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'file' ){

                            $attachment_id = cwp_upload_user_file( $key, $val, $_FILES, $user_id, 'cwp_user_register' );
                            if(isset($attachment_id) && !empty($attachment_id)){
                                update_user_meta( $user_id, $key, $attachment_id );
                            }else{
                                delete_user_meta( $user_id, $key );
                            }

                        }else if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'image' ){

                            $attachment_id = cwp_upload_user_file( $key, $val, $_FILES, $user_id, 'cwp_user_register' );
                            if(isset($attachment_id) && !empty($attachment_id)){
                                update_user_meta( $user_id, $key, $attachment_id );
                            }else{
                                delete_user_meta( $user_id, $key );
                            }

                        } else if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'repeating_field' ){

                            $arr = array();
                            foreach($val as $_key => $_val){
                                $singleFieldOptions = isset($fieldOptions[$_key]) ? $fieldOptions[$_key] : array();
                                foreach($_val as $field_key => $field_val){
                                    if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'file' || isset($singleFieldOptions) && $singleFieldOptions['type'] == 'image' ){
                                        if(isset($_FILES['cwp_user_register']['name']['custom_fields'][$key][$_key][$field_key]) && $_FILES['cwp_user_register']['name']['custom_fields'][$key][$_key][$field_key] != ''){
                                            $file = array( 
                                                'name'     => $_FILES['cwp_user_register']['name']['custom_fields'][$key][$_key][$field_key],
                                                'type'     => $_FILES['cwp_user_register']['type']['custom_fields'][$key][$_key][$field_key],
                                                'tmp_name' => $_FILES['cwp_user_register']['tmp_name']['custom_fields'][$key][$_key][$field_key],
                                                'error'    => $_FILES['cwp_user_register']['error']['custom_fields'][$key][$_key][$field_key],
                                                'size'     => $_FILES['cwp_user_register']['size']['custom_fields'][$key][$_key][$field_key] 
                                            );
                                            $field_val = cwp_handle_attachment( $file, $post_id);
                                        }
                                        if( $field_val != 0 ){
                                            $arr[$field_key][$_key] = $field_val;
                                        }
                                    }else{
                                        $arr[$field_key][$_key] = $field_val;
                                    }

                                }
                            }
                            if(isset($arr) && !empty($arr)){
                                $_arr = array_filter($arr);
                                update_user_meta( $user_id, $key, $_arr );
                            }else{
                                delete_user_meta( $user_id, $key );
                            }

                        }else{

                            if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'date_picker' || $singleFieldOptions['type'] == 'date_time_picker' || $singleFieldOptions['type'] == 'time_picker' ){
                                $val = strtotime($val);
                            }
                            update_user_meta( $user_id, $key, $val );

                        }
                    }
                }
            }
            do_action( 'cubewp/after/user/registration', $user_id );
            if($user_pass == '' ){
                wp_send_json(
                    array(
                        'type' => 'success',
                        'msg'  => esc_html__("Go to your inbox or spam/junk and get your password", "cubewp-framework")
                    )
                );
            }else{
                wp_send_json(
                    array(
                        'type'         => 'success',
                        'msg'          => esc_html__("Registration and login were successful; the page will redirect shortly.", "cubewp-framework"),
                        'redirectURL'  => home_url()
                    )
                );
            }
        }
        
        wp_die();
    }
        
    /**
     * Method cubewp_update_user_profile
     *
     * @return array json
     * @since  1.0.0
     */
    public function cubewp_update_user_profile(){

        if ( ! wp_verify_nonce($_POST['security_nonce'], "cubewp_update_user_profile")) {
            wp_send_json(
                array(
                'type' => 'error',
                'msg'  => esc_html__('Sorry! Security Verification Failed.', 'cubewp-frontend'),
                )
            );
        }

        global $wpdb;
        
        if (isset($_POST['g-recaptcha-response'])) {
            CubeWp_Frontend_Recaptcha::cubewp_captcha_verification("cubewp_captcha_update_profile", cubewp_core_data($_POST['g-recaptcha-response']));
        }
        $current_user    =   wp_get_current_user();
        $default_fields  =   isset($_POST['cwp_user_profile']['default_fields']) ? CubeWp_Sanitize_Text_Array($_POST['cwp_user_profile']['default_fields'])  : array();
        $custom_fields   =   isset($_POST['cwp_user_profile']['custom_fields'])  ? CubeWp_Sanitize_Fields_Array($_POST['cwp_user_profile']['custom_fields'],'user')   : array();
        $user_id         =   isset($_POST['user_id'])                ? sanitize_text_field($_POST['user_id'])              :  '';
        $user_login      =   isset($default_fields['user_login'])    ? sanitize_text_field($default_fields['user_login'])  :  '';
        $user_email      =   isset($default_fields['user_email'])    ? sanitize_email($default_fields['user_email'])       :  '';
        $user_pass       =   isset($default_fields['user_pass'])     ? sanitize_text_field($default_fields['user_pass'])   :  '';
        
        $username_exists = username_exists($user_login);
        $email_exists    = email_exists($user_email);
        if($user_login != '' && $username_exists != $user_id && $username_exists != 0 ){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("This Username already exists.", "cubewp-framework"),
                )
            );
        }else if($user_email != '' && $email_exists != $user_id && $email_exists != 0){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("This Email already exists.", "cubewp-framework"),
                )
            );
        }
        
        if( (isset($default_fields['user_pass']) && (isset($default_fields['confirm_pass'])) && $default_fields['user_pass'] != $default_fields['confirm_pass'])){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("Password Mismatch.", "cubewp-framework"),
                )
            );
        }
        
        $user_fields = array();
        if(isset($default_fields['user_login'])){
            $user_fields['user_login']  =  sanitize_user_field( 'user_login', $default_fields['user_login'], $user_id, 'edit' );
            unset($default_fields['user_login']);
        }
        if(isset($default_fields['user_email'])){
            $user_fields['user_email']  =  sanitize_user_field( 'user_email', $default_fields['user_email'], $user_id, 'edit' );
            unset($default_fields['user_email']);
        }
        if(isset($default_fields['display_name'])){
            $user_fields['display_name']  = sanitize_user_field( 'display_name', $default_fields['display_name'], $user_id, 'edit' );
            unset($default_fields['display_name']);
        }
        if(isset($default_fields['user_url'])){
            $user_fields['user_url']  =  sanitize_user_field( 'user_url', $default_fields['user_url'], $user_id, 'edit' );
            unset($default_fields['user_url']);
        }
        
        if(isset($default_fields['user_pass']) && !empty($default_fields['user_pass'])){
            $user_fields['user_pass']  = sanitize_user_field( 'user_pass', $default_fields['user_pass'], $user_id, 'edit' );
            unset($default_fields['user_pass']);
        }
        if(isset($default_fields['confirm_pass'])){
            unset($default_fields['confirm_pass']);
        }
        
        if(isset($user_fields) && !empty($user_fields)){
            $wpdb->update($wpdb->users, $user_fields, array('ID' => $user_id));
            $user_fields['ID']  =  $user_id;
            $user_fields = apply_filters( 'cubewp/before/user/update', $user_fields );
            wp_update_user( $user_fields );
            
            if( $user_login != '' || (isset($user_fields['user_pass']) && $user_fields['user_pass'] != '')  ){
                $user_data = get_user_by('id', $user_id);
                $info = array();
                $info['user_login']    = $user_data->user_login;
                $info['user_password'] = $user_data->user_pass;
                $info['remember']      = true;
                if (is_ssl()) {
                    wp_signon( $info, true );
                }else{
                    wp_signon( $info, false );
                }
            }
            
        }
        
        // Update User Default Fields
        if(isset($default_fields) && !empty($default_fields)){
            foreach($default_fields as $key => $val){
                if( $val != ''){
                    update_user_meta($user_id, $key, $val);
                }
            }
        }
        
        // Update User Custom Fields
        $fieldOptions = CWP()->get_custom_fields( 'user' );
        if(isset($custom_fields) && !empty($custom_fields)){
            
            foreach($custom_fields as $key => $val){
                $singleFieldOptions = isset($fieldOptions[$key]) ? $fieldOptions[$key] : array();
                $value = $val;
                if ( ( $singleFieldOptions['type'] == 'post' && $singleFieldOptions['relationship'] ) && is_array( $singleFieldOptions ) && count( $singleFieldOptions ) > 0 ) {
                    if ( ! is_array( $value ) ) {
                        $value = array( $value );
                    }
                    if ( ! empty($value) && count($value) > 0) {
                        (new CubeWp_Relationships)->save_relationship( $user_id, $value, $key, 'UTP' );
                    }
                }else if ( ( $singleFieldOptions['type'] == 'user' && $singleFieldOptions['relationship'] ) && is_array( $singleFieldOptions ) && count( $singleFieldOptions ) > 0 ) {
                    if ( ! is_array( $value ) ) {
                        $value = array( $value );
                    }
                    if ( ! empty($value) && count($value) > 0) {
                        (new CubeWp_Relationships)->save_relationship( $user_id, $value, $key, 'UTU' );
                    }
                }

                if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'gallery' ){
                    $attachment_ids = cwp_upload_user_gallery_images( $key, $val, $_FILES, $user_id, 'cwp_user_profile');
                    if(isset($attachment_ids) && !empty($attachment_ids)){
                        update_user_meta( $user_id, $key, $attachment_ids );
                    }else{
                        delete_user_meta( $user_id, $key );
                    }
                }else if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'file' ){
                    $attachment_id = cwp_upload_user_file( $key, $val, $_FILES, $user_id , 'cwp_user_profile');
                    if(isset($attachment_id) && !empty($attachment_id)){
                        update_user_meta( $user_id, $key, $attachment_id );
                    }else{
                        delete_user_meta( $user_id, $key );
                    }
                }else if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'image' ){
                    $attachment_id = cwp_upload_user_file( $key, $val, $_FILES, $user_id , 'cwp_user_profile');
                    if(isset($attachment_id) && !empty($attachment_id)){
                        update_user_meta( $user_id, $key, $attachment_id );
                    }else{
                        delete_user_meta( $user_id, $key );
                    }
                } else if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'repeating_field' ){
                    $arr = array();
                    
                    foreach($val as $_key => $_val){
                        $singleFieldOptions = isset($fieldOptions[$_key]) ? $fieldOptions[$_key] : array();
                        foreach($_val as $field_key => $field_val){
                            if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'file' || isset($singleFieldOptions) && $singleFieldOptions['type'] == 'image' ){
                                if(isset($_FILES['cwp_user_profile']['name']['custom_fields'][$key][$_key][$field_key]) && $_FILES['cwp_user_profile']['name']['custom_fields'][$key][$_key][$field_key] != ''){
                                    $file = array( 
                                        'name'     => $_FILES['cwp_user_profile']['name']['custom_fields'][$key][$_key][$field_key],
                                        'type'     => $_FILES['cwp_user_profile']['type']['custom_fields'][$key][$_key][$field_key],
                                        'tmp_name' => $_FILES['cwp_user_profile']['tmp_name']['custom_fields'][$key][$_key][$field_key],
                                        'error'    => $_FILES['cwp_user_profile']['error']['custom_fields'][$key][$_key][$field_key],
                                        'size'     => $_FILES['cwp_user_profile']['size']['custom_fields'][$key][$_key][$field_key] 
                                    );
                                    $field_val = cwp_handle_attachment( $file, $post_id);
                                }
                                if( $field_val != 0 ){
                                    $arr[$field_key][$_key] = $field_val;
                                }
                            }else{
                                $arr[$field_key][$_key] = $field_val;
                            }

                        }
                    }
                    if(isset($arr) && !empty($arr)){
                        $_arr = array_filter($arr);
                        update_user_meta( $user_id, $key, $_arr );
                    }else{
                        delete_user_meta( $user_id, $key );
                    }
                }else{
                    if(isset($singleFieldOptions) && $singleFieldOptions['type'] == 'date_picker' || $singleFieldOptions['type'] == 'date_time_picker' || $singleFieldOptions['type'] == 'time_picker' ){
                        $val = strtotime($val);
                    }
                    update_user_meta( $user_id, $key, $val );
                }
            }
        }
        do_action( 'cubewp/after/user/profile/update', $user_id );
        wp_send_json(
            array(
                'type' => 'success',
                'msg'  => esc_html__("Your profile has been updated successfully.", "cubewp-framework"),
                'redirectURL'  => false
            )
        );
        
    }
        
    /**
     * Method cubewp_ajax_login
     *
     * @return array Json
     * @since  1.0.0
     */
    public function cubewp_ajax_login(){
        
        check_ajax_referer( 'cubewp-login-nonce', 'security' );
        
        $user_login  = isset($_POST['user_login']) ? sanitize_text_field($_POST['user_login']) :  '';
        $user_pass   = isset($_POST['user_pass'])  ? sanitize_text_field($_POST['user_pass'])  :  '';
        
        if($user_login == ''){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("The username field is empty.", "cubewp-framework"),
                )
            );
        }else if($user_pass == ''){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("The password field is empty.", "cubewp-framework"),
                )
            );
        }
        
        $info = array();
        $info['user_login']    = $user_login;
        $info['user_password'] = $user_pass;
        $info['remember']      = true;
        
        if (is_ssl()) {
            $user_signon = wp_signon( $info, true );
        }else{
            $user_signon = wp_signon( $info, false );
        }

        if ( is_wp_error($user_signon) ){
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__("Wrong username or password.", "cubewp-framework")
                )
            );
        } else {
            wp_send_json(
                array(
                    'type'         => 'success',
                    'msg'          => esc_html__("Login successful, redirecting...", "cubewp-framework"),
                    'redirectURL'  => apply_filters( 'cubewp/after/login/redirect-url', home_url() ),
                )
            );
        }
        
    }

    /**
     * Method cubewp_ajax_login
     *
     * @since  1.0.0
     */
    public function cubewp_ajax_forget_password() {
        check_ajax_referer( 'cubewp-forget-password-nonce', 'security' );
        $user_login = isset( $_POST['user_login'] ) ? sanitize_text_field( $_POST['user_login'] ) : '';
    
        if ( $user_login == '' ) {
        wp_send_json( array(
            'type' => 'error',
            'msg'  => esc_html__( "The username field is empty.", "cubewp-frontend" ),
        ));
        }
    
        if (username_exists($user_login) || email_exists($user_login)) {
        if (retrieve_password($user_login)) {
            wp_send_json( array(
                'type'        => 'success',
                'msg'         => esc_html__( "Password Reset Mail Sent Successfully. Please Check Your Email And Also Look Into Spam.", "cubewp-frontend" ),
            ));
        } else {
            wp_send_json( array(
                'type' => 'error',
                'msg'  => esc_html__( "Something Went Wrong, Try Again Later.", "cubewp-frontend" ),
            ));
        }
        }else {
        wp_send_json( array(
            'type' => 'error',
            'msg'  => esc_html__( "This Username Or Email Doesn't Exist.", "cubewp-frontend" ),
        ));
        }
    }
        
    /**
     * Method cubewp_publish_post
     *
     * @return array json
     * @since  1.0.0
     */
    public function cubewp_publish_post(){
            
        $post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : 0;
        if( isset($post_id) && $post_id > 0 ){
                $current_user = wp_get_current_user();
               
                if (user_can( $current_user, 'administrator' )) {
                        // no restriction for admin
                       
                    }
                else {
                    
                    $current_post_available = get_user_meta(get_current_user_id(), 'post_available', true);
                    if($current_post_available > 0)
                    {
                        update_user_meta(get_current_user_id(), 'post_available', $current_post_available - 1);	 
                        $post_args = array(
                                'ID'           => $post_id,
                                'post_status'  => 'publish',
                            );
                            wp_update_post($post_args);
                            wp_send_json(
                                    array(
                                        'type'        =>  'success',
                                        'msg'         =>  sprintf(__('Success! Your %s has been published.', 'cubewp-frontend'), get_post_type($post_id)),
                                        'redirectURL' =>  get_permalink($post_id)
                                    )
                                );
                    }	 
                    else
                    {
                       wp_send_json(
                                array(
                                    'type'        =>  'error',
                                    'msg'         =>  sprintf(__('Insufficient post credit.', 'cubewp-frontend'), get_post_type($post_id)),
                                    'redirectURL' =>  get_permalink(2)
                                )
                            );	
                    }
                }    

               
            
            
            
        }

    }
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}