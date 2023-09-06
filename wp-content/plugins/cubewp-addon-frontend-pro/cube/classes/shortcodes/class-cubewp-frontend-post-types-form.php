<?php

/**
 * Post Type's frontend forms shortcode.
 *
 * @package cubewp-addon-frontend/cube/classes/shortcodes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Post_Types_Form
 */
class CubeWp_Frontend_Post_Types_Form extends CubeWp_Frontend_Form {

    private $form_edit_class;

    public function __construct() {
        add_shortcode('cwpForm', array($this, 'frontend_form'));
        add_action('wp_ajax_cubewp_submit_post_form', array($this, 'cubewp_submit_post_form'));
        add_action('wp_ajax_nopriv_cubewp_submit_post_form', array($this, 'cubewp_submit_post_form'));
    }
    
    /**
	 * Method get_post_breadcrumb_tags
	 *
	 * @param array $params
	 * @param null $content
	 *
	 * @return string
     * @since  1.0.0
	 */
    public function frontend_form($params = array(), $content = null) {
        global $post_content,$cwpOptions;
        
        if (!is_user_logged_in()) {
			if( !isset($cwpOptions['allow_instant_signup']) || !$cwpOptions['allow_instant_signup'] ){
				return cwp_alert_ui('Sorry! You can\'t access this page without login.', 'info');
			}
        }
        
        $pid = 0;
        $url = get_permalink();
        if(isset($_GET['pid']) && $_GET['pid'] > 0){
            $pid = sanitize_text_field($_GET['pid']);
            $post_content = get_post($pid);
            $url = add_query_arg('pid', $pid, get_permalink());
            if( $post_content->post_author != get_current_user_id()){
                return cwp_alert_ui('Sorry! You can\'t update this post.', 'warning');
            }
        }

        CubeWp_Enqueue::enqueue_style( 'select2' );
        CubeWp_Enqueue::enqueue_script( 'select2' );
        CubeWp_Enqueue::enqueue_style( 'cwp-timepicker' );
        CubeWp_Enqueue::enqueue_script( 'cwp-timepicker' );
        CubeWp_Enqueue::enqueue_script( 'jquery-ui-datepicker' );
        CubeWp_Enqueue::enqueue_script( 'cwp-submit-post' );
        CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );
        CubeWp_Enqueue::enqueue_style( 'frontend-fields' );
        CubeWp_Enqueue::enqueue_script( 'cwp-frontend-fields' );
        $cwpform_post_types       =  CWP()->get_form( 'post_type' );
        
        // default parameters
        extract(shortcode_atts(array(
                'type' => 'post',
                'content' => '',
            ), $params)
        );

        $plan_id  =  isset($_POST['plan_id']) ? sanitize_text_field($_POST['plan_id']) : 0;
        $type     =  isset($_POST['type']) ? sanitize_text_field($_POST['type']) : $type;
        
        add_filter("cubewp/frontend/form/{$type}/section/fields",array($this, 'fields'),10,3);
        
        $form_fields  =  isset($cwpform_post_types[$type]) ? $cwpform_post_types[$type] : array();
        if(!empty($content)){
            $form_fields  =  isset($form_fields[$content]) ? $form_fields[$content] : array();
        }
        if(isset($post_content) && !empty($post_content)){
            $this->form_edit_class = 'cubewp-post-form-edit';
            $plan_id = get_post_meta($post_content->ID, 'plan_id', true);
        }else{
            self::redirect_to_plans($type,$plan_id);
        }
        if( !empty($plan_id) && $plan_id > 0 ){
            $form_fields  =  isset($form_fields[$plan_id]) ? $form_fields[$plan_id] : array();
        }
        if(empty($form_fields) || ! isset( $form_fields['groups'] ) || empty( $form_fields['groups'] ) ){
            return cwp_alert_ui('Sorry! You can\'t submit post due to empty form fields.', 'warning');
        }
        
        $form_container_class  =  isset($form_fields['form']['form_container_class']) ? $form_fields['form']['form_container_class']   : '';
        $form_class            =  isset($form_fields['form']['form_class'])           ? $form_fields['form']['form_class']             : '';
        $form_id               =  isset($form_fields['form']['form_id'])              ? $form_fields['form']['form_id']                : '';
        $submit_button_title   =  isset($form_fields['form']['submit_button_title'])  ? $form_fields['form']['submit_button_title']    : '';
        $submit_button_class   =  isset($form_fields['form']['submit_button_class'])  ? $form_fields['form']['submit_button_class']    : '';
     
        
        $form_id               =   $form_id != ''             ? $form_id              : 'cwp-from-'.$type;
        $form_class            =   $form_class != ''          ? 'cwp-user-form-submit '. $form_class           : 'cwp-user-form-submit cwp-from-'.$type;
        $submit_button_title   =   $submit_button_title != '' ? $submit_button_title  : esc_html__("Submit", "cubewp-frontend");
        $submit_button_class   =   $submit_button_class != '' ? $submit_button_class  : 'cwp-from-submit';
        
        $output = '<div class="cwp-frontend-form-container '. esc_attr($form_container_class) .'">
            <form method="post" id="'. esc_attr($form_id) .'" class="'. esc_attr($form_class) .' '. $this->form_edit_class .'" action="'. esc_url($url) .'" enctype="multipart/form-data">
            <input type="hidden" name="cwp_user_form[post_type]" value="'. esc_attr($type) .'">
            <input type="hidden" name="cwp_user_form[pid]" value="'. absint($pid) .'">
            <input type="hidden" name="cwp_user_form[plan_id]" value="'. absint($plan_id) .'">';

            $output .= apply_filters("cubewp/{$type}/form/fields",'');
            $output .='<div class="tab-content">';
                if(isset($form_fields['groups']) && !empty($form_fields['groups'])){
                    $section_content = '';
                    $section_number = 1;
                    foreach($form_fields['groups'] as $section_data ){
                        $section_data['total_sections'] = count($form_fields['groups']);
                        $section_data['section_number'] = $section_number;
                        $section_data['post_content'] = $post_content;
                        $section_content .= $this->frontend_form_section( $section_data, $type );
                        $section_number++;
                    }
                    $output .= $section_content;
                }
            $output .= '</div>';
            $recaptcha = isset($form_fields['form']['form_recaptcha']) ? $form_fields['form']['form_recaptcha'] : 'disabled';
            $output .= CubeWp_Frontend_Recaptcha::cubewp_captcha_form_attributes($recaptcha);
            $submitBTN = '<input class="'. esc_attr($submit_button_class) .'" type="submit" value="'. esc_attr($submit_button_title) .'">';
        $output .= apply_filters("cubewp/frontend/form/{$type}/button",$submitBTN,$submit_button_title,$submit_button_class);
        $output .='</form>
        </div>';
        
        $args = array(
            'form_fields'  =>   $form_fields,
            'post_type'    =>   $type,
            'content'      =>   $content,
            'pid'          =>   $pid,
            'plan_id'      =>   $plan_id,
        );
        $output = apply_filters("cubewp/frontend/form/{$type}", $output, $args);
        return $output;
        
    }
        
    /**
     * Method cubewp_submit_post_form
     *
     * @return array json
     * @since  1.0.0
     */
    public function cubewp_submit_post_form(){

        if ( ! wp_verify_nonce($_POST['security_nonce'], "cubewp_submit_post_form")) {
            wp_send_json(
               array(
                  'type' => 'error',
                  'msg'  => esc_html__('Sorry! Security Verification Failed.', 'cubewp-frontend'),
               )
            );
        }
        if (isset($_POST['g-recaptcha-response'])) {
            CubeWp_Frontend_Recaptcha::cubewp_captcha_verification("cubewp_captcha_post_submission", cubewp_core_data($_POST['g-recaptcha-response']));
        }
        if (isset($_POST['cwp_user_form'])) {
            global $cwpOptions;

            $pid        = isset($_POST['cwp_user_form']['pid'])          ? sanitize_text_field($_POST['cwp_user_form']['pid'])             : '';
            $plan_id    = isset($_POST['cwp_user_form']['plan_id'])      ? sanitize_text_field($_POST['cwp_user_form']['plan_id'])         : 0;
            $post_type  = isset($_POST['cwp_user_form']['post_type'])    ? sanitize_text_field($_POST['cwp_user_form']['post_type'])       : 'none';
            $title      = isset($_POST['cwp_user_form']['the_title'])    ? sanitize_text_field($_POST['cwp_user_form']['the_title'])       : '';
            $content    = isset($_POST['cwp_user_form']['the_content'])  ? wp_kses_post($_POST['cwp_user_form']['the_content']) : '';
            $metas      = isset($_POST['cwp_user_form']['cwp_meta'])  ? CubeWp_Sanitize_Fields_Array($_POST['cwp_user_form']['cwp_meta'],'post_types') : '';
            $excerpt    = isset($_POST['cwp_user_form']['the_excerpt'])  ? wp_kses_post($_POST['cwp_user_form']['the_excerpt']) : '';
            do_action("cubewp/{$post_type}/before/submit/actions",$_POST);

            if(isset($pid) && $pid > 0){
                $post_author = get_post_field( 'post_author', $pid );
                if( $post_author != get_current_user_id()){
                    wp_send_json(
                        array(
                            'type'        =>  'error',
                            'msg'         =>  esc_html__('Sorry! You can\'t update this post.', 'cubewp-frontend'),
                        )
                    );
                }
                $post_data  =  array();
                $post_data['ID']             =  esc_sql($pid);
                $post_data['post_title']     =  wp_strip_all_tags($title);
                $post_data['post_content']   =  !empty($content) ? wp_kses_post($content) : '';
                $post_data['post_excerpt']   =  !empty($excerpt) ? wp_kses_post($excerpt) : '';
                $postID  = wp_update_post( $post_data );
            }else{
                $post_arr   = array(
                    'post_title'   => wp_strip_all_tags($title),
                    'post_content' => wp_kses_post($content),
                    'post_excerpt' => wp_kses_post($excerpt),
                    'post_status'  => isset($cwpOptions['post_admin_approved'][ $post_type ]) ? $cwpOptions['post_admin_approved'][ $post_type ] : 'pending',
                    'post_type'    => $post_type,
                    'post_author'  => get_current_user_id(),
                );
                $paid_submission = isset($cwpOptions['paid_submission']) ? $cwpOptions['paid_submission'] : 'no';
				if ( $paid_submission == 'yes' ) {
					$post_arr['post_status'] = 'pending';
				}
                $postID = wp_insert_post($post_arr);
            }
            if(!is_wp_error($postID)){
                do_action("cubewp/{$post_type}/after/post/create",$postID,$_POST);
                if(isset($_POST['cwp_user_form']['term'])){
                    if(is_array($_POST['cwp_user_form']['term'])){
                        foreach($_POST['cwp_user_form']['term'] as $taxonomy => $terms){
                            $terms = is_array($terms) ? $terms : array($terms);
                            $terms = array_map( 'intval', $terms );
                            $terms = array_unique( $terms );
                            wp_set_object_terms($postID, $terms, $taxonomy);
                        }
                    }
                }
                $this->assign_plan_to_post($pid, $plan_id,$postID);
                $this->save_post_custom_fields($metas, $_FILES, $postID);
                $return = apply_filters("cubewp/{$post_type}/after/submit/actions",array(
                        'type'        =>  'success',
                        'msg'         =>  esc_html__('Success! The submission was successful.', 'cubewp-frontend'),
                        'redirectURL' =>  get_permalink($postID)
                    ),array('post_id'=>$postID,'post_type'=>$post_type));
                
                wp_send_json($return);
            }
        }
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
    
}