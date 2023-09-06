<?php



/**

 * user's frontend forms shortcode.

 *

 * @package cubewp-addon-frontend/cube/classes/shortcodes

 * @version 1.0

 * 

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



/**

 * CubeWp_Frontend_User_Registration

 */

class CubeWp_Frontend_User_Registration{

    

    public function __construct() {

        

        add_shortcode('cwpRegisterForm', array($this, 'register_frontend_form'));

    }

    

    /**

	 * Method register_frontend_form

	 *

	 * @param null $content 

	 * @param array $params 

	 *

	 * @return string

	 * @since  1.0.0

	 */

    public function register_frontend_form($params = array(), $content = null) {

        

        if(is_admin()){

            return '';

        }

        

        if(is_user_logged_in()){

            $logout_btn = '<a href="' . esc_url(wp_logout_url(get_permalink())) . '">' . esc_html__("Logout", "cubewp-frontend") . '</a>';

			return cwp_alert_ui(sprintf(esc_html__('You have already logged in. %s if you want to proceed.', "cubewp-frontend"), $logout_btn));

        }

        

        $users_can_register = get_option('users_can_register');

        if( $users_can_register != 1 ){

            return cwp_alert_ui('User registration option is disabled by the admin.');

        }

        

        extract(shortcode_atts(array(

                'role'    => 'subscriber',

            ), $params)

        );

        if(class_exists('CubeWp_Enqueue')){

            CubeWp_Enqueue::enqueue_style( 'select2' );

            CubeWp_Enqueue::enqueue_script( 'select2' );

            CubeWp_Enqueue::enqueue_style( 'frontend-fields' );

            CubeWp_Enqueue::enqueue_script( 'cwp-frontend-fields' );

            CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );

            CubeWp_Enqueue::enqueue_script( 'cwp-user-register' );

            CubeWp_Enqueue::enqueue_style( 'cwp-login-register' );

        }

        

        

        $cwp_user_forms             =  CWP()->get_form( 'user_register' );

        $this->user_custom_fields   =  CWP()->get_custom_fields( 'user' );

        $this->user_default_fields  =  cubewp_user_default_fields();



        $form_fields                =  isset($cwp_user_forms[$role]) ? $cwp_user_forms[$role] : array();

        

        if(empty($form_fields)){

            return cwp_alert_ui('Sorry! You can\'t update your profile due to empty form fields.', 'warning');

        }

        

        $form_container_class     =  isset($form_fields['form']['form_container_class']) ? $form_fields['form']['form_container_class']   : '';

        $form_class               =  isset($form_fields['form']['form_class'])           ? $form_fields['form']['form_class']             : '';

        $form_id                  =  isset($form_fields['form']['form_id'])              ? $form_fields['form']['form_id']                : '';

        $submit_button_title      =  isset($form_fields['form']['submit_button_title'])  ? $form_fields['form']['submit_button_title']    : '';

        $submit_button_class      =  isset($form_fields['form']['submit_button_class'])  ? $form_fields['form']['submit_button_class']    : '';

     

        

        $form_id                  =   $form_id != ''             ? $form_id              : 'cwp-from-'. $role;

        $form_class               =   $form_class != ''          ? 'cwp-user-register '. $form_class           : 'cwp-user-register cwp-from-'. $role;

        $submit_button_title      =   $submit_button_title != '' ? $submit_button_title  : esc_html__("Register", "cubewp-frontend");

        $submit_button_class      =   $submit_button_class != '' ? 'submit-btn '. $submit_button_class  : 'submit-btn ';

        if(isset($form_fields['groups']) && !empty($form_fields['groups'])){

            $output = '<div class="cwp-frontend-form-container '. esc_attr($form_container_class) .'">

                <form method="post" id="'. esc_attr($form_id) .'" class="'. esc_attr($form_class) .'" action="'. esc_url(get_permalink()) .'" enctype="cwp-from-submit">

                    <input type="hidden" name="cwp_user_register[default_fields][role]" value="'. esc_attr($role) .'">

                    <div class="tab-content">';

                        

                        $section_content = '';

                        foreach($form_fields['groups'] as $section_data ){

                            $section_content .= $this->frontend_form_section( $section_data, $role );

                        }

                        $output .= $section_content;

                    

                        $output .= '</div>';

                        $recaptcha = isset($form_fields['form']['form_recaptcha']) ? $form_fields['form']['form_recaptcha'] : 'disabled';

                        $output .= CubeWp_Frontend_Recaptcha::cubewp_captcha_form_attributes($recaptcha);

                        $output .= '<input class="'. esc_attr($submit_button_class) .'" type="submit" value="'. esc_attr($submit_button_title) .'">

                </form>

            </div>';

        }else{

            $output = cwp_alert_ui('Sorry! There is no form fields available in this user role form.');

        }

        $output = apply_filters("cubewp/user/registration/form", $output, $form_fields);

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



        $output = apply_filters("cubewp/user/registration/form/section", $output, $section_data );                        

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

    public function fields( $fields = array() ) {

        global $current_user;



        $output = '';

        foreach($fields as $field_name => $field_options){



            if(isset($this->user_custom_fields[$field_name]) && !empty($this->user_custom_fields[$field_name])){

                

                $field_options['custom_name'] =   'cwp_user_register[custom_fields][' . $field_name . ']';

                $field_options['id']          =   isset($this->user_custom_fields[$field_name]['id']) ? $this->user_custom_fields[$field_name]['id'] : $field_name;

                

                $field_options = wp_parse_args($field_options, $this->user_custom_fields[$field_name]);

                

                if($field_options['type'] == 'google_address' ){

                    $field_options['custom_name_lat'] =   'cwp_user_profile[custom_fields][' . $field_options['name'].'_lat' . ']';

                    $field_options['custom_name_lng'] =   'cwp_user_profile[custom_fields][' . $field_options['name'].'_lng' . ']';

                }

                if($field_options['type'] == 'taxonomy' ){

                    $field_options['filter_taxonomy'] = isset($this->user_custom_fields[$field_name]['filter_taxonomy']) ? $this->user_custom_fields[$field_name]['filter_taxonomy'] : '';

                }

                $field_options = wp_parse_args($field_options, $this->user_custom_fields[$field_name]);

                  

                if(isset($field_options['sub_fields']) && !empty($field_options['sub_fields'])){

                    $sub_fields = explode(',', $field_options['sub_fields']);

                    $field_options['sub_fields'] = array();

                    foreach($sub_fields as $sub_field){

                        $field_options['sub_fields'][] = $this->user_custom_fields[$sub_field];

                    }

                }

                

                $field_options['value'] = isset($this->user_custom_fields[$field_name]['default_value']) ? $this->user_custom_fields[$field_name]['default_value'] : '';



            }else if(isset($this->user_default_fields[$field_name]) && !empty($this->user_default_fields[$field_name])){

                $user_pass = $this->user_default_fields[$field_name]['name'];

                if($user_pass == 'user_pass'){

                    $field_options['required'] = '1';

                }

                $field_options['custom_name'] = 'cwp_user_register[default_fields][' . $field_name . ']';

                $field_options['id']          =   isset($this->user_default_fields[$field_name]['id']) ? $this->user_default_fields[$field_name]['id'] : $field_name;

                $field_options = wp_parse_args($field_options, $this->user_default_fields[$field_name]);

                

            }

            $output .=  apply_filters("cubewp/user/registration/{$field_options['type']}/field", '', $field_options);

        }

        

        return $output;

        

    }

    public static function init() {

        $CubeClass = __CLASS__;

        new $CubeClass;

    }

    

}