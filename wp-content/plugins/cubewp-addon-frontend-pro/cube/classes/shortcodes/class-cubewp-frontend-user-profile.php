<?php

/**
 * User profile frontend forms shortcode.
 *
 * @package cubewp-addon-frontend/cube/classes/shortcodes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_User_Profile
 */
class CubeWp_Frontend_User_Profile {

    private $user_custom_fields;
    private $user_default_fields;
    
    public function __construct() {
        add_shortcode('cwpProfileForm', array($this, 'profile_frontend_form'));
        add_action('wp_ajax_cwp_delete_user', array($this, 'cubewp_delete_user'));
        add_action('wp_ajax_cwp_download_user', array($this, 'cubewp_download_user'));
    }
    
     /**
	 * Method profile_frontend_form
	 *
	 * @param null $content 
	 * @param array $params 
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public function profile_frontend_form($params = array(), $content = null) {
        global $current_user, $cwpOptions;
        if(is_admin()){
            return '';
        }
        
        if(!is_user_logged_in()){
            return cwp_alert_ui('Sorry! You can\'t access this page without login.', 'info');
        }
        
        CubeWp_Enqueue::enqueue_style( 'frontend-fields' );
        CubeWp_Enqueue::enqueue_script( 'cwp-frontend-fields' );
        CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );
        CubeWp_Enqueue::enqueue_script( 'cwp-user-profile' );
        
        $cwp_user_forms             =  CWP()->get_form( 'user_profile' );
        $this->user_custom_fields   =  CWP()->get_custom_fields( 'user' );
        $this->user_default_fields  =  cubewp_user_default_fields();
        
        $role         =  cwp_get_current_user_roles();
        $form_fields  =  isset($cwp_user_forms[$role]) ? $cwp_user_forms[$role] : array();
        
        if(empty($form_fields)){
            return cwp_alert_ui('Sorry! You can\'t update your profile due to empty form fields.', 'warning');
        }
        
        $form_container_class     =  isset($form_fields['form']['form_container_class']) ? $form_fields['form']['form_container_class']   : '';
        $form_class               =  isset($form_fields['form']['form_class'])           ? $form_fields['form']['form_class']             : '';
        $form_id                  =  isset($form_fields['form']['form_id'])              ? $form_fields['form']['form_id']                : '';
        $submit_button_title      =  isset($form_fields['form']['submit_button_title'])  ? $form_fields['form']['submit_button_title']    : '';
        $submit_button_class      =  isset($form_fields['form']['submit_button_class'])  ? $form_fields['form']['submit_button_class']    : '';
     
        
        $form_id                  =   $form_id != ''             ? $form_id              : 'cwp-from-'. $role;
        $form_class               =   $form_class != ''          ? 'cwp-user-form-submit '. $form_class           : 'cwp-user-profile cwp-from-'. $role;
        $submit_button_title      =   $submit_button_title != '' ? $submit_button_title  : esc_html__("Update", "cubewp-frontend");
        $submit_button_class      =   $submit_button_class != '' ? 'submit-btn '. $submit_button_class  : 'submit-btn ';
        
        $output = '<div class="cwp-frontend-form-container '. esc_attr($form_container_class) .'">
            <form method="post" id="'. esc_attr($form_id) .'" class="'. esc_attr($form_class) .'" action="'. esc_url(get_permalink()) .'" enctype="cwp-from-submit">
               <input type="hidden" name="user_id" value="'. esc_attr($current_user->ID) .'">
                <div class="tab-content">';
                    if(isset($form_fields['groups']) && !empty($form_fields['groups'])){
                        $section_content = '';
                        foreach($form_fields['groups'] as $section_data ){
                            $section_content .= $this->frontend_form_section( $section_data, $role );
                        }
                        $output .= $section_content;
                    }
                    $output .= '</div>';
                    $gdpr_compliance = isset($cwpOptions['user_gdpr_compliance']) ? $cwpOptions['user_gdpr_compliance'] : '';
                    if ($gdpr_compliance) {
                        $output .= '<div class="cwp-user-profile-actions">';
                        if (! current_user_can('delete_user', $current_user->ID)) {
                            $output .= '<a id="cwp-delete-user" data-user-id="' . esc_attr($current_user->ID) . '">' . esc_html__('Delete My Account & Content', 'cubewp-frontend') . '</a>';
                        }
                        $output .= '<a id="cwp-download-user" data-user-id="' . esc_attr($current_user->ID) . '">' . esc_html__('Download My Details', 'cubewp-frontend') . '</a>';
                        $output .= '</div>';
                    }
                    $recaptcha = isset($form_fields['form']['form_recaptcha']) ? $form_fields['form']['form_recaptcha'] : 'disabled';
                    $output .= CubeWp_Frontend_Recaptcha::cubewp_captcha_form_attributes($recaptcha);
                    $output .= '<input class="'. esc_attr($submit_button_class) .'" type="submit" value="'. esc_attr($submit_button_title) .'">
            </form>
        </div>';
        
        $args = array(
            'form_fields'  =>   $form_fields,
            'role'         =>   $role,
        );
        $output = apply_filters("cubewp/user/profile", $output, $args);
        
        return $output;
        
    }
    
    /**
	 * Method frontend_form_section
	 *
	 * @param string $role 
	 * @param array $section_data 
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public function frontend_form_section( $section_data = array(), $role = '' ){
        extract(shortcode_atts(array(
                'section_id'          =>  '',
                'section_title'       =>  '',
                'section_description' =>  '',
                'section_class'       =>  '',
                'fields'              =>  '',
            ), $section_data)
        );
        
        $section_id =  $section_id != '' ? ' id="'. esc_attr($section_id).'"' : '';
        $output  = '<div'. $section_id .' class="cwp-frontend-section-container '. esc_attr($section_class).'">';
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
                    $output .= $this->fields($section_data['fields']);
                $output .= '</div>';
            }
        $output .= '</div>';

        $output = apply_filters("cubewp/user/profile/form/section/", $output, $section_data );                        
        return $output;
    }
     
    /**
     * Method fields
     *
     * @param array $fields
     *
     * @return void
     * @since  1.0.0
     */
    public function fields( $fields = array() ) {
        global $current_user;
       
        $output = '';
        foreach($fields as $field_name => $field_options){
            
            if(isset($this->user_custom_fields[$field_name]) && !empty($this->user_custom_fields[$field_name])){
                
                $field_options['custom_name'] =   'cwp_user_profile[custom_fields][' . $field_name . ']';
                $field_options['id']          =   isset($this->user_custom_fields[$field_name]['id']) ? $this->user_custom_fields[$field_name]['id'] : $field_name;
                
                $field_options = wp_parse_args($field_options, $this->user_custom_fields[$field_name]);
                
                if($field_options['type'] == 'google_address' ){
                    $field_options['custom_name_lat'] =   'cwp_user_profile[custom_fields][' . $field_options['name'].'_lat' . ']';
                    $field_options['custom_name_lng'] =   'cwp_user_profile[custom_fields][' . $field_options['name'].'_lng' . ']';
                }
                if($field_options['type'] == 'taxonomy' ){
                    $field_options['filter_taxonomy'] = isset($this->user_custom_fields[$field_name]['filter_taxonomy']) ? $this->user_custom_fields[$field_name]['filter_taxonomy'] : '';
                }
                if(isset($field_options['sub_fields']) && !empty($field_options['sub_fields'])){
                    $sub_fields = explode(',', $field_options['sub_fields']);
                    $field_options['sub_fields'] = array();
                    foreach($sub_fields as $sub_field){
                        $field_options['sub_fields'][] = $this->user_custom_fields[$sub_field];
                    }
                }

                $field_options['value'] = get_user_meta($current_user->ID, $field_options['name'], true);
                if($field_options['type'] == 'google_address' ){
                    $field_options['lat'] = get_user_meta( $current_user->ID, $field_options['name'].'_lat', true );
                    $field_options['lng'] = get_user_meta( $current_user->ID, $field_options['name'].'_lng', true );
                }
            }else if(isset($this->user_default_fields[$field_name]) && !empty($this->user_default_fields[$field_name])){
                
                $field_options['custom_name'] = 'cwp_user_profile[default_fields][' . $field_name . ']';
                $field_options['id']          =   isset($this->user_default_fields[$field_name]['id']) ? $this->user_default_fields[$field_name]['id'] : $field_name;
                
                
                $field_options = wp_parse_args($field_options, $this->user_default_fields[$field_name]);
                
                if( $field_name != 'user_pass' && $field_name != 'confirm_pass' ){
                    $field_options['value'] = isset($current_user->$field_name) ? $current_user->$field_name : '';
                    if($field_options['value'] == '' ){
                        $field_options['value'] = get_user_meta($current_user->ID, $field_name, true);
                    }
                }
                
            }

            $output .=  apply_filters("cubewp/user/profile/{$field_options['type']}/field", '', $field_options);
        }
        
        return $output;
        
    }

    /**
     * Method cubewp_delete_user
     *
     * @return json
     * @since  1.0.12
     */
    public function cubewp_delete_user()
    {
        $user_id = sanitize_text_field($_POST['user_id']);
        if (!wp_verify_nonce($_POST['nonce'], 'cubewp_delete_user_profile')) {
        wp_send_json( array(
        'type' => 'error',
        'msg'  => esc_html__( "Security verification failed.", "cubewp-frontend" ),
        ) );
        }
        if (! current_user_can('delete_user', $user_id)) {
            if (wp_delete_user($user_id)) {
            wp_send_json( array(
            'type'        => 'success',
            'msg'         => esc_html__( "User deleted successfully.", "cubewp-frontend" ),
            'redirectURL' => home_url(),
            ) );
            } else {
            wp_send_json( array(
            'type' => 'error',
            'msg'  => esc_html__( "An error occurred while deleting the user.", "cubewp-frontend" ),
            ) );
            }
        } else {
        wp_send_json( array(
        'type' => 'error',
        'msg'  => esc_html__( "This user cannot be deleted.", "cubewp-frontend" ),
        ) );
        }
    }
    /**
     * Method cubewp_download_user
     *
     * @return json
     * @since  1.1.5
     */
    public function cubewp_download_user()
    {
        $user_id = sanitize_text_field($_POST['user_id']);
        if (!wp_verify_nonce($_POST['nonce'], 'cubewp_download_user_profile')) {
            wp_send_json(array('msg' => esc_html('Security Verification Failed.', 'cubewp-frontend'), 'type' => 'error'));
        }
        $user = get_user_by('ID', $user_id);
        $user_data = get_userdata($user_id);
        $data_download = array(
            array('User ID', 'Username', 'Email', 'First Name', 'Last Name', 'Display Name', 'Registered Date'),
            array($user->ID, $user->user_login, $user->user_email, $user->first_name, $user->last_name, $user->display_name, $user->user_registered),
        );
        if (!empty($data_download)) {
            $total_count = count($data_download) - 1;
            if ($total_count > 0) {
                $csv = self::array_to_csv($data_download);
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename="cubewp-user-details.csv";');
                echo $csv;
                exit();
            }
        }
    }
    /**
     * Method array_to_csv
     *
     * @param array $array
     * 
     * @return string
     * @since  1.1.5
     */
    public function array_to_csv($array)
    {
        ob_start();
        $fp = fopen('php://output', 'w');
        foreach ($array as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        return ob_get_clean();
    }

    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
    
}