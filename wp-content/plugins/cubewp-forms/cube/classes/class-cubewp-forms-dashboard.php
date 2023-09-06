<?php
/**
 * User Dashboard Frontend Custom Forms Shortcode.
 *
 * @package cubewp-addon-forms/cube/classes
 * @version 1.0
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CubeWp Forms Dashboard Class.
 *
 * @class CubeWp_Forms_Dashboard
 */
class CubeWp_Forms_Dashboard{
    
    public function __construct() {
		CubeWp_Enqueue::enqueue_style('cubewp-dashboard-leads');
        CubeWp_Enqueue::enqueue_script('cwp-tabs');
        CubeWp_Enqueue::enqueue_script('cubewp-forms-dashboard');
        add_action( 'wp_ajax_cwp_forms_data', array( $this ,'cwp_get_forms_data' ) );
	}
    /**
	 * Method cwp_leads
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function cwp_leads() {
        $data = [];
        $form_data = cwp_forms_all_leads_by_post_author(get_current_user_id());
        if(isset($form_data) && !empty($form_data)){
            $form_data = array_reverse($form_data);
            foreach($form_data as $leads){
                $leadid = $leads['lead_id'];
                $form_fields =  CWP()->get_custom_fields( 'custom_forms' );
                $data[$leads['form_id']]['form_name'] = $leads['form_name'];
                $data[$leads['form_id']][$leadid]['post_id'] = $leads['single_post'];
                $data[$leads['form_id']][$leadid]['user_id'] = $leads['user_id'];
                $data[$leads['form_id']][$leadid]['post_author'] = $leads['post_author'];
                if(isset($leads['dete_time']) && !empty(isset($leads['dete_time']))){
                    $data[$leads['form_id']][$leadid]['dete_time'] = $leads['dete_time'];
                }
                $fields = unserialize($leads['fields']);
                foreach($fields as $key=> $lead){
                    if(isset($form_fields[$key])){
                        $field_data = $form_fields[$key];
                        $data[$leads['form_id']][$leadid][$key]['label'] = $field_data['label'];
                        $data[$leads['form_id']][$leadid][$key]['type'] = $field_data['type'];
                        $data[$leads['form_id']][$leadid][$key]['value'] = $lead;
                    }
                }
            }
        }
        $form_details=$data;
        $output='';
        $output=self::lead_details($form_details);
        $output.=self::cwp_form_data_sidebar();
        return $output;
    }
    
        /**
	 * Method lead_details
	 *
	 * @param array $form_details
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function lead_details($form_details) {
        $output='';
        if(!empty(self::cwp_lead_tabs_content($form_details))){
            $output.='<div class="cwp-table-responsive">';
            if(count($form_details) > 1 ){
                $output.=self::cwp_lead_tabs($form_details);
            }
            $output.=self::cwp_lead_tabs_content($form_details);
            $output .='</div>';
        }else{
            $output ='';
            $output .=ob_start();
            ?>
                <div class="cwp-empty-posts"><img class="cwp-empty-img" src="<?php echo  esc_url(CWP_PLUGIN_URI.'cube/assets/frontend/images/no-result.png') ?>" alt=""><h2><?php echo esc_html__("No Leads Found", "cubewp-forms");?></h2><p><?php echo esc_html__("There are no leads found.", "cubewp-forms");?></p></div>
            <?php
            $output .=ob_get_clean();
        }
        return $output;
    }
    
    /**
	 * Method cwp_lead_tabs
	 *
	 * @param array $form_details
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function cwp_lead_tabs($form_details) {
        $output='';
        $output .='<div class="cwp-leads-forms-tabs">
                    <ul class="cwp-tabs" role="tablist">';
        $keyind = 0;
        foreach($form_details as $form_id => $form_detail){
            $form_name = $form_detail['form_name'];
            $active_class = $keyind == 0 ? 'cwp-active-tab' : '';
            $output .='<li class="cwp-author-'.$form_id.'-tab '.$active_class.'">
                        <a class="list-group-item" data-toggle="tab" href="#cwp-author-'.$form_id.'">'.$form_name.'</a>
                        </li>';
            $keyind++;
        }
        $output .='</ul></div>';
        return $output;
    }
    
    /**
	 * Method cwp_lead_tabs_content
	 *
	 * @param array $form_details
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function cwp_lead_tabs_content($form_details) {
        $keyind = 0;
        $output='';
        if(count($form_details) > 1 ){
            $output .='<div class="cwp-leads-form-content">';
            $keyind = 0;
        }
        foreach($form_details as $form_id => $form_detail){
            unset($form_detail['form_name']);
            $active_class = $keyind == 0 ? 'cwp-active-tab-content' : '';
            if(count($form_details) > 1 ){
            $output .='<div class="cwp-tab-content '.$active_class.'" id="cwp-author-'.$form_id.'">';
            }
            $output .='<table class="cwp-user-dashboard-tables cwp-form-leads">';
            $count=0;
            $dete_time = '';
            foreach($form_detail as $lead_id => $form_desc){
                $post_id = $form_desc['post_id'];
                $author_id = $form_desc['user_id'];
                if(isset($form_desc['dete_time']) && !empty(isset($form_desc['dete_time']))){
                    $dete_time = $form_desc['dete_time'];
                }
                if($count < 1){
                    $output .=self::cwp_form_table_head($post_id);
                }
                $output .=self::cwp_form_table_data($form_id,$lead_id,$form_desc,$post_id,$author_id,$dete_time);
                $count++;
            }
            $output .='</table>';
            if(count($form_details) > 1 ){
                $output .='</div>';
            }
            $keyind++;
        }
        if(count($form_details) > 1 ){
            $output .='</div>';
        }
        return $output;
    }
    
    /**
	 * Method cwp_form_table_head
	 *
	 * @param int $post_id
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function cwp_form_table_head($post_id) {
        $output='';
        $output ='<tr class="cwp-dashboard-list-head">';
        if(!empty($post_id)){
        $output .='<th class="cwp-dashboard-listing-title">'.__('Post','cubewp-forms').'</th>';
        }
        $output .='<th>'.__('Submitted By','cubewp-forms').'</th>';
        $output .='<th>'.__('Date','cubewp-forms').'</th>';
        $output .='<th>'.__('Action','cubewp-forms').'</th>';
        $output .='</tr>';
        return $output;
    }
    
    /**
	 * Method lead_details
	 *
	 * @param int $form_id
     * @param int $lead_id
     * @param array $form_desc
     * @param int $post_id
     * @param int $author_id
     * @param int $dete_time
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function cwp_form_table_data($form_id,$lead_id,$form_desc,$post_id,$author_id,$dete_time) {
        $dete_time = wp_date('M j, Y', $dete_time);
        $author_name=get_the_author_meta( 'display_name', $author_id );
        $output='';
        $output ='<tr>';
        if(!empty($post_id)){
        $output .='<td class="cwp-dashboard-list-title-content">'. CubeWp_Frontend_User_Dashboard::get_post_details($post_id) .'</td>';
        }
        $output .='<td>'.$author_name.'</td>';
        $output .='<td>'.$dete_time.'</td>';
        $output .='<td><div class="cwp-dasboard-list-action">
                        <span class="cwp-dashboard-tooltip">'.__('View Submission', 'cubewp-forms').'</span><a class="cwp-user-dashboard-tab-content-post-action cwp-form-action-view" type="button" target="_blank" data-form_id="'.$form_id.'" data-lead_id="'.$lead_id.'"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
				        </svg>
				        </a></div></td>';
        $output .='</tr>';
        return $output;
    }
    
    /**
	 * Method cwp_get_forms_data
	 *
	 * @return array json
	 * @since  1.0.0
	 */
    public function cwp_get_forms_data() {

        if ( ! wp_verify_nonce(sanitize_text_field($_POST['security_nonce']), "cubewp_forms_dashboard")) {
            wp_send_json(
               array(
                  'type' => 'error',
                  'msg'  => esc_html__('Sorry! Security Verification Failed.', 'cubewp-forms'),
               )
            );
        }
        global $wpdb;
        $leadid = sanitize_text_field( $_POST['lead_id'] ) ;
        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        $form_output = '';
        if(isset($form_data['fields']) && !empty($form_data['fields'])){
            $form_fields =  CWP()->get_custom_fields( 'custom_forms' );
            $fields = unserialize($form_data['fields']);
            foreach($fields as $key=> $lead){
                $field_data = isset($form_fields[$key]) ? $form_fields[$key] : '';
                $field_label = isset($field_data['label']) ? $field_data['label'] : '';
                $field_type = isset($field_data['type']) ? $field_data['type'] : '';
                if( $field_type == 'repeating_field'){
                    if(is_array($lead) && !empty($lead)){
                        $form_output .='<div class="cwp-forms-field">';
                        $form_output .='<h6>'.$field_data['label'].'</h6>';
                        $form_output .='<div class="cwp-forms-repeating-fields">';
                        foreach($lead as $k => $val){
                            foreach($val as $_k => $_val){
                                $type = $form_fields[$_k]['type'];
                                if(isset($_val) && !empty($_val)){
                                    $label = isset($form_fields[$_k]['label']) ? $form_fields[$_k]['label'] : '';
                                    $form_output .='<div class="cwp-forms-repeating-field">';
                                    $form_output .='<h6>'.$label.'</h6>';
                                    $form_output .='<p>'.CubeWp_Forms_Leads::cwp_forms_render_value($type, $_val).'</p>';
                                    $form_output .='</div>';
                                }
                            }
                        }
                        $form_output .='</div></div>';
                    }
                }else{
                    $lead = CubeWp_Forms_Leads::cwp_forms_render_value($field_type, $lead);
                    if(isset($lead) && !empty($lead)){
                        $label = isset($field_data['label']) ? $field_data['label'] : '';
                        $form_output .='<div class="cwp-forms-field">';
                        $form_output .='<h6>'.$label.'</h6>';
                        $form_output .='<p>'.$lead.'</p>';
                        $form_output .='</div>';
                    }
                }
            }
            wp_send_json( array('output' => $form_output) );
        }
        die();
    }
    
    /**
	 * Method cwp_form_data_sidebar
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function cwp_form_data_sidebar() {
        $output ='<div class="cwp-form-sidebar">
        <div class="cwp-form-head">
            <h4>'. __('Lead Details', 'cubewp-forms').'</h4>
            <span class="cwp-close-sidebar"><span class="dashicons dashicons-no"></span><p>'. __('Close', 'cubewp-forms').'</p></span>
        </div>
        <div class="cwp-form-data-content"></div>
        </div>';
        return $output;
    }
    
    /**
	 * Method lead_details
	 *
	 * @param sting $field_type
     * @param array $field_value
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function cwp_forms_render_value($field_type, $field_value){
        if ($field_type == 'date_picker') {
			$value = wp_date(get_option('date_format'), $value);
		}
		if ($field_type == 'time_picker') {
			$value = wp_date(get_option('time_format'), $value);
		}
		if ($field_type == 'date_time_picker') {
			$value = wp_date(get_option('date_format') . ' H:i:s', $value);
		}
        if( $field_type == 'terms'){
            $value = render_taxonomy_value($value);
        }
        if( $field_type == 'post'){
            $value = render_post_value($value);
        }
        if( $field_type == 'user'){
            $value = render_user_value($value);
        }
        if( $field_type == 'image' || $field_type == 'gallery'){
            $value = render_media_value($value);
        }
        if( $field_type == 'file'){
            $value = render_file_value($value);
        }
		
		return $value;
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}