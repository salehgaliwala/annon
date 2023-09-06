<?php
/**
 * Display fields of custom fields.
 *
 * @version 1.0
 * @package cubewp/cube/modules/post-types
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * CubeWp_Posttype_Custom_Fields_Display
 */
class CubeWp_Forms_Leads {

    public function __construct() {
        add_action( 'cubewp_custom_form_data', array( $this, 'cwp_custom_fields_run' ) );
    }
    /**
     * Method cwp_custom_fields_run
     *
     * @return void
     * @since  1.0.0
     */
    public static function cwp_custom_fields_run(  ) {
        self::group_display();
    }
    
    /**
     * Method group_display
     *
     * @return string html
     * @since  1.0.0
     */
    public static function group_display() {
        if(isset($_GET['action']) && 'edit' == sanitize_text_field( $_GET['action'] ) && isset($_GET['leadid'])){
            return self::edit_group(sanitize_text_field($_GET['leadid']));
        }
        $customFieldsGroupTable = new CubeWp_Forms_Data_Table();
        ?>
        <div class="wrap cwp-post-type-wrape">
            <h1 class="wp-heading-inline"><?php esc_html_e("CubeWP Leads", 'cubewp-forms'); ?></h1>
            <hr class="wp-header-end">
            <?php $customFieldsGroupTable->prepare_items(); ?>
            <form id="posts-filter" method="post">
                <input type="hidden" name="page" value="cubewp-leads">
                <?php $customFieldsGroupTable->display(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Method edit_group
     *
     * @param int $leadID
     *
     * @return string
     * @since  1.0.0
     */
    public static function edit_group($leadID = 0) {
       
        ?>
        <div class="wrap">            
            <div class="cwpform-title-outer  margin-bottom-0 margin-left-minus-20  margin-right-0">
            <h1><?php esc_html_e('CubeWP Leads', 'cubewp-forms') ?> </h1>		
            </div>
            <div id="poststuff"  class="padding-0">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-2" class="postbox-container postbox-container-top">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><span><?php esc_html_e('Lead Details', 'cubewp-forms'); ?></span></h2>
                        </div>
                        <div class="inside lead-detail-box">
                            <div class="main">
                                <?php echo self::lead_details($leadID); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e('Information', 'cubewp-forms'); ?></h2>
                            </div>
                            <div class="inside lead-detail-box">
                                <?php echo self::cwp_lead_information($leadID); ?>
                            </div>
                        </div>
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e('How to manage leads from frontend?', 'cubewp-forms'); ?></h2>
                            </div>
                            <div class="cwp-manage-leads">
                                <img src="<?php echo CWP_FORMS_PLUGIN_URL . 'cube/assets/images/cwp-dashboard-leads.png'; ?>" alt="" />
                                <h5><?php esc_html_e('Try CubeWP Frontend Pro to unlock powerful front-end features.'); ?></h5>
                                <div class="cwp-leads-frontend-pro">
                                    <span class="dashicons dashicons-yes"></span>
                                    <p><?php esc_html_e('Frontend User Dashboard Builder', 'cubewp-forms'); ?></p>
                                    <span class="dashicons dashicons-yes"></span>
                                    <p><?php esc_html_e('Frontend User Profile Form Builder', 'cubewp-forms'); ?></p>
                                    <span class="dashicons dashicons-yes"></span>
                                    <p><?php esc_html_e('Frontend Post Types Form Builder', 'cubewp-forms'); ?></p>
                                    <span class="dashicons dashicons-yes"></span>
                                    <p><?php esc_html_e('Frontend User Signup Form Builder', 'cubewp-forms'); ?></p>
                                    <span class="dashicons dashicons-yes"></span>
                                    <p><?php esc_html_e('Frontend Single-post Template Editor', 'cubewp-forms'); ?></p>
                                    <span class="dashicons dashicons-yes"></span>
                                    <p><?php esc_html_e('And several advanced features.', 'cubewp-forms'); ?></p>
                                </div>
                            </div>
                            <div class="cwp-leads-learn-more">
                                <a target="_blank" href="https://cubewp.com/cubewp-frontend-pro/">
                                    <?php esc_html_e('Learn More', 'cubewp-forms'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <?php
    }

    /**
     * Method lead_details
     *
     * @param int $leadID
     *
     * @return string
     * @since  1.0.0
     */
    private static function lead_details($leadID = 0) {
        CubeWp_Enqueue::enqueue_style('cubewp-admin-leads');
        $output = '';
        $value = '';
        $repeater = [];
        $form_data = cwp_forms_all_leads_by_lead_id($leadID);
        $form_fields =  CWP()->get_custom_fields( 'custom_forms' );
        if(isset($form_data['fields']) && !empty($form_data['fields'])){
            $output .=  ' <table>';
            $fields = unserialize($form_data['fields']);
            foreach($fields as $key=> $lead){
                $data = isset($form_fields[$key]) ? $form_fields[$key] : array();
                $field_type = isset($data['type']) ? $data['type'] : "";
                if( $field_type == 'repeating_field'){
                    if(is_array($lead) && !empty($lead)){
                        foreach($lead as $k => $val){
                            foreach($val as $_k => $_val){
                                if(isset($form_fields[$_k])){
                                    $type = $form_fields[$_k]['type'];
                                    $repeater['label'][] = $form_fields[$_k]['label'];
                                    $repeater['value'][] = self::cwp_forms_render_value($type, $_val);
                                }
                            }
                        }
                    }
                }else{
                    $value = self::cwp_forms_render_value($field_type, $lead);
                }
                if($field_type == 'repeating_field'){
                    $output .= '<tr>
                    <th>'.$data['label'].'</th>
                    <td class="cwp-forms-repeating-fields">';
                    foreach($repeater['label'] as $k => $label){
                        if(isset($repeater['value'][$k]) && !empty($repeater['value'][$k])){
                            $output .= '<div class="cwp-forms-repeating-field">';
                            $output .= '<h6>'.$label.' </h6><p>'.$repeater['value'][$k].'</p>';
                            $output .= '</div>';
                        }
                    }
                    $output .= '</td> </tr>';
                }else{
                    if(!empty($value)){
                        $label = isset($data['label']) ? $data['label'] : '';
                        $output .= ' <tr>
                        <th>'.$label.'</th>
                        <td>'.$value.'</td>
                        </tr>';
                    }
                }
                
			}
        }
        $output .=  ' </table>';

       return $output;
    }
    
    /**
     * Method cwp_forms_render_value
     *
     * @param string $field_type
     * @param array $value
     *
     * @return value
     * @since  1.0.0
     */
    public static function cwp_forms_render_value($field_type, $value){
        if ($field_type == 'date_picker') {
            $value2 = strtotime($value);
            if($value2 != false){
                $value = $value2;
            }
            $value = wp_date(get_option('date_format'), $value);
        }
        if ($field_type == 'time_picker') {
            $value2 = strtotime($value);
            if($value2 != false){
                $value = $value2;
            }
            $value = wp_date(get_option('time_format'), $value);
        }
        if ($field_type == 'date_time_picker') {
            $value2 = strtotime($value);
            if($value2 != false){
                $value = $value2;
            }
            $value = wp_date(get_option('date_format') . ' H:i:s', $value);
        }
        if( $field_type == 'taxonomy'){
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
        if( is_array($value)){
            $value = implode(",",$value);
        }
		
		return $value;
    }
    
     /**
     * Method cwp_lead_information
     *
     * @param int $leadID
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_lead_information($leadID = 0) {
        $form_id = cwp_form_id_by_lead_id($leadID);
        $post_id = cwp_post_id_by_lead_id($leadID);
        $user_id = cwp_author_id_by_lead_id($leadID);
        $date_id = cwp_lead_date_by_lead_id($leadID);
        $form_name  = isset($form_id) ? get_the_title($form_id) : '';
        $user = !empty($user_id) ? get_userdata($user_id)->user_login : '';
        $lead_date = isset($date_id) ? date("m/d/Y",$date_id) : '';
        $lead_time = isset($date_id) ? date("h:i:s A T",$date_id) : '';
        ob_start();
        ?>
        <div class="cwp-lead-form-details">
            <?php if(!empty($form_name)){ ?>
                <p><?php esc_html_e('Form Name:', 'cubewp-forms'); ?></p>
                <h6><?php echo esc_attr($form_name); ?></h6>
            <?php }if(!empty($leadID)){ ?>
                <p><?php esc_html_e('Lead ID:', 'cubewp-forms'); ?>
                <h6><?php echo esc_attr($leadID); ?></h6>
            <?php }if(!empty($user)){ ?>
                <p><?php esc_html_e('User:', 'cubewp-forms'); ?></p>
                <h6><?php echo esc_attr($user); ?></h6>
            <?php }if(!empty($post_id)){ ?>
                <p><?php esc_html_e('Source:', 'cubewp-forms'); ?></p>
                <h6><?php echo '<a href="'.esc_url(get_the_permalink( $post_id )).'" target="_blank">'.esc_attr(get_the_title($post_id)).'</a>'; ?></h6>
            <?php }if(!empty($lead_date)){ ?>
                <p><?php esc_html_e('Date:', 'cubewp-forms'); ?></p>
                <h6><?php echo esc_attr($lead_date); ?></h6>
            <?php }if(!empty($lead_time)){ ?>
                <p><?php esc_html_e('Time:', 'cubewp-forms'); ?></p>
                <h6><?php echo esc_attr($lead_time); ?></h6>
            <?php } ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}