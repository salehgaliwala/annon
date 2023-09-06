<?php

/**
 * Post Type's frontend forms shortcode.
 *
 * @package cubewp-addon-forms/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Post_Types_Form
 */
class CubeWp_Forms_Frontend_Custom_Form {

    public $custom_fields;
    private $wp_default_fields;
    private $form_edit_class;

    public function __construct() {
        add_shortcode('cwpCustomForm', array($this, 'frontend_form'));
        add_action('wp_ajax_cubewp_submit_custom_form', array($this, 'cubewp_submit_custom_form'));
        add_action('wp_ajax_nopriv_cubewp_submit_custom_form', array($this, 'cubewp_submit_custom_form'));
        $this->custom_fields =  CWP()->get_custom_fields( 'custom_forms' );
    }
    
     /**
	 * Method repeating_field_form_name
	 *
	 * @param array $args
	 *
	 * @return string
     * @since  1.0.0
	 */
    public function repeating_field_form_name($args = array()) {
        $args['custom_name'] = str_replace("cwp_user_form[cwp_meta]","cwp_custom_form[fields]",$args['custom_name']);
        return $args;
    }
    /**
	 * Method frontend_form
	 *
	 * @param array $params
	 * @param null $content
	 *
	 * @return string
     * @since  1.0.0
	 */
    public function frontend_form($params = array(), $content = null) {
        if(is_admin()){
            return '';
        }
        // default parameters
        extract(shortcode_atts(array(
                'form_id' => 0,
            ), $params)
        );
        $form_login_only = get_post_meta($form_id, '_cwp_group_login', true);
        if($form_login_only == 1){
            if (!is_user_logged_in()) {
                return cwp_alert_ui("You Must Login For Submission.",'info');
            }
        }
        
        CubeWp_Enqueue::enqueue_style( 'select2' );
        CubeWp_Enqueue::enqueue_style( 'frontend-fields' );
        CubeWp_Enqueue::enqueue_style( 'cwp-timepicker' );
        CubeWp_Enqueue::enqueue_style( 'cubewp-frontend-forms' );

        CubeWp_Enqueue::enqueue_script( 'select2' );
        CubeWp_Enqueue::enqueue_script( 'cwp-timepicker' );
        CubeWp_Enqueue::enqueue_script( 'jquery-ui-datepicker' );
        CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );
        CubeWp_Enqueue::enqueue_script( 'cubewp-custom-form-submit' );
        CubeWp_Enqueue::enqueue_script( 'cwp-frontend-fields' );
        
        $cwpform_custom =  get_post_meta($form_id, '_cwp_group_fields', true);
        $form_fields  =  !empty($cwpform_custom) ? explode(",",$cwpform_custom) : array();
        if(empty($form_fields) || count($form_fields) == 0){
            return cwp_alert_ui('Sorry! No fields available for this form.','info');
        }
        $section_data = array();
        $recaptcha_meta           =   get_post_meta($form_id, '_cwp_group_recaptcha', true);
        
        $submit_form_id           =   get_post_meta($form_id, '_cwp_group_form_id', true);
        $submit_form_id           =   !empty($submit_form_id) ? $submit_form_id : 'cwp-from-'.$form_id;

        $form_class               =   'cwp-user-form-submit';
        $submit_button_title      =   get_post_meta($form_id, '_cwp_group_button_text', true);
        $submit_button_title      =   !empty($submit_button_title ) ? $submit_button_title  : esc_html__("Submit", "cubewp-forms");

        $submit_button_width      =   get_post_meta($form_id, '_cwp_group_button_width', true);
        $submit_button_width      =   !empty($submit_button_width) ? 'style="width:'.$submit_button_width.'"' : '';

        $submit_button_position   =   get_post_meta($form_id, '_cwp_group_button_position', true);
        $submit_button_position   =   !empty($submit_button_position) ? ' position-'.$submit_button_position : '';

        $submit_button_class      =   get_post_meta($form_id, '_cwp_group_button_class', true);
        $submit_button_class      =   !empty($submit_button_class) ? $submit_button_class.' cwp-from-submit'.$submit_button_position : 'cwp-from-submit '.$submit_button_position;

        add_filter( 'cubewp/frontend/post/repeating_field/args', array($this, 'repeating_field_form_name') );
        
        $single_input = is_single() || is_page() ? '<input type="hidden" value="'.get_the_ID().'" name="cwp_custom_form[single_post]" >':'';

        $output = '<div class="cwp-frontend-form-container">
        <form method="post" id="'. esc_attr($submit_form_id) .'" class="cwp-custom-form '. esc_attr($form_class) .'" action="" enctype="multipart/form-data">
        <input type="hidden" name="cwp_custom_form[form_id]" value="'. esc_attr($form_id) .'">
        <input type="hidden" name="cwp_custom_form[form_data_id]" value="'. uniqid (rand()) .'">
        '.$single_input.'
        <div class="tab-content">';
        $section_data['fields'] = $form_fields;
        $output .= $this->frontend_form_section( $section_data, $form_id );
        $output .= '</div>';
        $recaptcha = !empty($recaptcha_meta) ? $recaptcha_meta : 'disabled';
        $output .= CubeWp_Frontend_Recaptcha::cubewp_captcha_form_attributes($recaptcha);
        $submitBTN = '<input '.$submit_button_width.' class="'. esc_attr($submit_button_class) .'" type="submit" value="'. esc_attr($submit_button_title) .'">';
        $output .= apply_filters("cubewp/frontend/form/{$form_id}/button",$submitBTN,$submit_button_title,$submit_button_class);
        $output .='</form>
        </div>';
        
        $args = array(
            'form_fields'  =>   $form_fields,
            'form_id'    =>   $form_id,
        );
        $output = apply_filters("cubewp/frontend/form/{$form_id}", $output, $args);
        return $output;
        
    }
    
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
                'fields'              =>  '',
            ), $section_data)
        );
        $form_header = get_post_meta($type, '_cwp_group_display', true);
        $section_title       = get_post_field( 'post_title', $type );
        $section_description     = get_post_field( 'post_content', $type );
        $output  = '<div class="cwp-frontend-section-container">';
            if( $form_header == 1 ){
                $output .= '<div class="cwp-frontend-section-heading-container">';
                if( isset($section_title) && $section_title != '' ){
                    $output .= '<h2>'. esc_attr($section_title) .'</h2>';
                }
                if( isset($section_description) && $section_description != '' ){
                    $output .= apply_filters('the_content', $section_description);
                }
                $output .= '</div>';
            }
            if(isset($section_data['fields']) && !empty($section_data['fields'])){
                $output .= '<div class="cwp-frontend-section-content-container">';
                    $output .= $this->fields($fields);
                $output .= '</div>';
            }
        $output .= '</div>';

        return $output;
    }
    
    /**
	 * Method fields
	 *
	 * @param array $fields
	 *
	 * @return string
     * @since  1.0.0
	 */
    public function fields($fields = array() ) {
        $output = '';
        foreach($fields as $field_name){
            
            if(isset($this->custom_fields[$field_name]) && !empty($this->custom_fields[$field_name])){
                $field_options = $this->custom_fields[$field_name];
                $field_options['custom_name'] =   'cwp_custom_form[fields][' . $field_name . ']';
                $field_options['id']          =   isset($this->custom_fields[$field_name]['id']) ? $this->custom_fields[$field_name]['id'] : $field_name;
                if($field_options['type'] == 'google_address' ){
                    $field_options['custom_name_lat'] =   'cwp_custom_form[fields][' . $field_options['name'].'_lat' . ']';
                    $field_options['custom_name_lng'] =   'cwp_custom_form[fields][' . $field_options['name'].'_lng' . ']';
                }
                $field_options['value'] = isset($this->custom_fields[$field_name]['default_value']) ? $this->custom_fields[$field_name]['default_value'] : '';
                $field_options = wp_parse_args($field_options, $this->custom_fields[$field_name]);
                if(isset($field_options['sub_fields']) && !empty($field_options['sub_fields'])){
                    $sub_fields = explode(',', $field_options['sub_fields']);
                    $field_options['sub_fields'] = array();
                    foreach($sub_fields as $sub_field){
                        $field_options['sub_fields'][] = $this->custom_fields[$sub_field];
                    }
                }
                
                $output .=  apply_filters("cubewp/frontend/{$field_options['type']}/field", '', $field_options);
            }
        }
        return $output;
    }
        
    /**
     * Method cubewp_submit_custom_form
     *
     * @return array json
     * @since  1.0.0
     */
    public function cubewp_submit_custom_form(){

        if ( ! wp_verify_nonce(sanitize_text_field($_POST['security_nonce']), "cubewp_forms_submit")) {
            wp_send_json(
               array(
                  'type' => 'error',
                  'msg'  => esc_html__('Sorry! Security Verification Failed.', 'cubewp-forms'),
               )
            );
        }
        if (isset($_POST['g-recaptcha-response'])) {
            CubeWp_Frontend_Recaptcha::cubewp_captcha_verification("cubewp_captcha_custom_form_submission", cubewp_core_data($_POST['g-recaptcha-response']));
        }
        if (isset($_POST['cwp_custom_form'])) {
            global $cwpOptions;
            $data_id        = isset($_POST['cwp_custom_form']['form_data_id'])    ? sanitize_text_field($_POST['cwp_custom_form']['form_data_id']) : 0;
            $form_id        = isset($_POST['cwp_custom_form']['form_id'])    ? sanitize_text_field($_POST['cwp_custom_form']['form_id']) : 0;
            $single_form    = isset($_POST['cwp_custom_form']['single_post']) ? sanitize_text_field($_POST['cwp_custom_form']['single_post']) : '';
            $metas          = isset($_POST['cwp_custom_form']['fields'])  ? CubeWp_Sanitize_Fields_Array($_POST['cwp_custom_form']['fields'],'custom_forms') : '';

            $form_data = [];
            $form_data['lead_id'] = $data_id;
            $form_data_id = json_decode(get_post_meta($form_id, '_cwp_custom_form_data_id', true));
            $form_data_id = is_object($form_data_id) ? $form_data_id : (object) $form_data_id;
            $form_data_id->$data_id = $data_id;
            update_post_meta($form_id, '_cwp_custom_form_data_id', json_encode($form_data_id));
            $form_data['user_id'] = get_current_user_id();
            $form_data['dete_time'] = strtotime("now");
            
            if(!empty($single_form)){
                if(get_post_type( $single_form ) != 'page'){
                    $single_author  = get_post_field( 'post_author', $single_form );
                    $form_data['post_author'] = $single_author;
                    $user_form_data_id = json_decode(get_user_meta($single_author, '_cwp_custom_form_data_id', true));
                    $user_form_data_id = is_object($user_form_data_id) ? $user_form_data_id : (object) $user_form_data_id;
                    $user_form_data_id->$data_id = $single_form;
                    update_user_meta( $single_author, '_cwp_custom_form_data_id', json_encode($user_form_data_id));
                }
                $form_data['single_post'] = $single_form;
            }
            $form_data['form_id'] = $form_id;
            $form_data['form_name'] = get_post_field( 'post_name', $form_id );
            if(!empty($data_id)){
                $message = '<div>';
                $message .= '<h2>' . esc_html__('Form Entry Details', 'cubewp-forms') . '</h2>';
                if(isset($metas) && !empty($metas)){
                    $fieldOptions = $this->custom_fields;
                    foreach($metas as $key => $val){
                        $singleFieldOptions = isset($fieldOptions[$key]) && isset($fieldOptions[$key]['type']) ? $fieldOptions[$key] : array();
    
                        if(isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'gallery' ){
                            $attachment_ids = cwp_upload_custom_form_gallery_images( $key, $val, $_FILES, $form_id );
                            if(isset($attachment_ids) && !empty($attachment_ids)){
                                $form_data['fields'][$key] = $attachment_ids;
                            }
                        }else if((isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'file') ||
                         (isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'image') ){
                            $attachment_id = cwp_upload_custom_form_file( $key, $val, $_FILES, $form_id );
                            if(isset($attachment_id) && !empty($attachment_id)){
                                $form_data['fields'][$key] = $attachment_id;
                            }
                        }else if(isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'repeating_field' ){
                            $arr = $this->cubewp_repeating_field_save($key, $val, $_FILES, $form_id);
                            if(isset($arr) && !empty($arr)){
                                $_arr = array_filter($arr);
                                $form_data['fields'][$key] = $_arr;
                            }
                        }else{
                            if(isset($singleFieldOptions['type']) && ($singleFieldOptions['type'] == 'date_picker' || $singleFieldOptions['type'] == 'date_time_picker' || $singleFieldOptions['type'] == 'time_picker') ){
                                $val = strtotime($val);
                            }
                            $form_data['fields'][$key] = $val;
                            if(isset($singleFieldOptions['label'])){
                                $message .= '<h3>' . esc_html( $singleFieldOptions['label'] ) . '</h3>';
                            }
                            $message .= '<p>' . esc_html( $val ) . '</p>';
                        }
                    }
                    cwp_insert_leads($form_data);
                }
                $_emails = get_post_meta($form_id, '_cwp_group_emails', true);
                $_emails = explode( ',', $_emails );
                $emails = array();
                foreach ( $_emails as $email ) {
                $emails[] = sanitize_email( trim( $email ) );
                }

                $message = apply_filters("cubewp/custom/form/submit/email", $message, $emails, $form_id);
                $headers = array();
                $reply_to_field = get_post_meta($form_id, '_cwp_group_user_email', true);
                if ( ! empty( $reply_to_field ) ) {
                    $reply_to = isset( $form_data['fields'][ $reply_to_field ] ) ? $form_data['fields'][ $reply_to_field ] : '';
                    $reply_to = sanitize_email( $reply_to );
                    if ( ! empty( $reply_to ) && is_email( $reply_to ) ) {
                        $headers = array( "Reply-To: $reply_to" );
                    }
                }
                cubewp_send_mail(
                    $emails,
                    sprintf( esc_html__( 'You have received a new entry from %s', 'cubewp-forms' ), get_post_field( 'post_name', $form_id ) ),
                    $message,
                    $headers
                );
                $return = apply_filters("cubewp/custom/form/{$form_id}/after/submit/actions",array(
                    'type'  =>  'success',
                    'msg'   =>  esc_html__('Success! The submission was successful.', 'cubewp-forms'),
                    'redirectURL'   =>  get_the_permalink($single_form),
                ),
                array(
                    'form_id' => $form_id
                ));
                
                wp_send_json($return);
            }
        }
    }

    /**
	 * Method cubewp_repeating_field_save
	 *
	 * @param int $key
     * @param array $val
     * @param array $FILES
     * @param int $postID
	 *
	 * @return array
	 * @since  1.0.0
	 */
    private function cubewp_repeating_field_save( $key = '',$val = array(),$FILES = array(), $postID = '' ){
        $fieldOptions = $this->custom_fields;
        $arr = array();
        if(empty($val)) return $arr;
        foreach($val as $_key => $_val){
            $singleFieldOptions = isset($fieldOptions[$_key]) ? $fieldOptions[$_key] : array();
            foreach($_val as $field_key => $field_val){
                if((isset($singleFieldOptions) && $singleFieldOptions['type'] == 'gallery')){
                    $field_val = cwp_upload_custom_form_repeating_gallery_images( $key, $_key,$field_key, $field_val, $FILES, $postID );
                }
                if((isset($singleFieldOptions) && $singleFieldOptions['type'] == 'file') || 
                (isset($singleFieldOptions) && $singleFieldOptions['type'] == 'image')
                ){
                    if(isset($FILES['cwp_custom_form']['name']['fields'][$key][$_key][$field_key]) && $FILES['cwp_custom_form']['name']['fields'][$key][$_key][$field_key] != ''){
                        $file = array( 
                            'name'     => $FILES['cwp_custom_form']['name']['fields'][$key][$_key][$field_key],
                            'type'     => $FILES['cwp_custom_form']['type']['fields'][$key][$_key][$field_key],
                            'tmp_name' => $FILES['cwp_custom_form']['tmp_name']['fields'][$key][$_key][$field_key],
                            'error'    => $FILES['cwp_custom_form']['error']['fields'][$key][$_key][$field_key],
                            'size'     => $FILES['cwp_custom_form']['size']['fields'][$key][$_key][$field_key] 
                        );
                        $field_val = cwp_handle_attachment( $file, $postID);
                    }
                    if( $field_val != 0 ){
                        $arr[$field_key][$_key] = $field_val;
                    }
                }else{
                    
                    $arr[$field_key][$_key] = $field_val;
                }

            }
            
        }
        return $arr;
    }

    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
    
}