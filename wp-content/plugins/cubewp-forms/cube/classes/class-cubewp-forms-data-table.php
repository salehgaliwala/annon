<?php
/**
 * CubeWp Forms Data Table
 *
 * @package cubewp-addon-forms/cube/classes
 * @version 1.0
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CubeWp Forms Data Table Class.
 *
 * @class CubeWp_Forms_Data_Table
 */
class CubeWp_Forms_Data_Table extends WP_List_Table{
    
    
    
    public static $customTax = array();
    function __construct(){
    	global $status, $page;
        parent::__construct(self::$customTax );
    }
    
    
        /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ){
        return isset($item[$column_name]) ? $item[$column_name] : '-';
    }
    
   /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns(){

        $columns = array(
            'cb'            =>   '<input type="checkbox" />',
            'form_name'    =>   esc_html__('Form Name', 'cubewp-forms'),
            'lead_id'   =>   esc_html__('Lead ID', 'cubewp-forms'),
            'user_id'   =>   esc_html__('User', 'cubewp-forms'),
            'single_post'   =>   esc_html__('Single Post (If available)', 'cubewp-forms'),
            'date_time'   =>   esc_html__('Date & Time', 'cubewp-forms'),
        );
        return $columns;
    }
    
    /**
    * Method for group_name column
    * @param array $item an array of DB data
    * @return string
    */
    function column_form_name( $item ) {
        $title = sprintf( '<a href="%s"><strong>' . $item['form_name'] . '</strong></a>', CubeWp_Submenu::_page_action('cubewp-custom-form-data','edit', '&leadid='.esc_attr( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_edit_group' )));
        $actions = [
            'edit' => sprintf( '<a href="%s">'. esc_html__('View Details', 'cubewp-forms') .'</a>', CubeWp_Submenu::_page_action('cubewp-custom-form-data','edit', '&leadid='.esc_attr( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_edit_group' ))),
            'delete' => sprintf( '<a href="%s">'. esc_html__('Delete', 'cubewp-forms') .'</a>', CubeWp_Submenu::_page_action('cubewp-custom-form-data','delete', '&leadid='.esc_attr( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_delete_group' ))),
        ];
        return $title . $this->row_actions( $actions );
    }
    /**
	 * Get value for checkbox column.
	 *
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'cwp_group_bulk_action',  // Let's simply repurpose the table's singular label ("movie").
			$item['ID']                // The value of the checkbox should be the record's ID.
		);
	}
    protected function get_bulk_actions() {
		$actions = array(
			'delete' => _x( 'Delete', 'List table bulk action', 'cubewp-forms' ),
		);

		return $actions;
	}
    protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) { 
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'bulk-' . $this->_args['plural'] ) ) {
                if(isset($_REQUEST['cwp_group_bulk_action'])){
                    $bulk_request = CubeWp_Sanitize_text_Array($_REQUEST['cwp_group_bulk_action']);
                   	foreach($bulk_request as $leadid){
						cwp_remove_lead($leadid);
						
                   	} 
                }                                
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_delete_group')) {
                if(isset($_REQUEST['leadid'])){
					$leadid = sanitize_text_field($_REQUEST['leadid']);
					cwp_remove_lead($leadid);
                }
                wp_redirect( CubeWp_Submenu::_page_action('cubewp-custom-form-data') );
            }
        }
        
		
	}
    
   public function prepare_items() {
		global $wpdb; //This is used only if making any database queries
		/*
		 * First, lets decide how many records per page to show
		 */
		$per_page = 50;
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();


		$form_data = cwp_forms_all_leads();
		$form_data = !empty($form_data) && is_array($form_data) ? array_reverse($form_data): array();
		if ( isset( $_REQUEST['filter_form'] ) && !empty( $_REQUEST['filter_form'] ) && ( $_REQUEST['filter_form'] != '0' ) ) {
			$formId = sanitize_text_field($_REQUEST['filter_form']);
		}
		$_data = array();
		if(isset($form_data) && !empty($form_data) && !isset($formId)){
            foreach($form_data as $lead){
				$data = array();
				$data['ID']         = $lead['lead_id'];
				$data['form_name']  = isset($lead['form_id']) ? get_the_title($lead['form_id']) : '';
				$data['lead_id'] = $lead['lead_id'];
				$data['user_id'] = isset($lead['user_id']) && !empty($lead['user_id']) ? get_userdata($lead['user_id'])->user_login : esc_html__( 'No User Data', 'cubewp-forms' );
				$data['single_post'] = isset($lead['single_post']) ? '<a href="'.get_the_permalink( $lead['single_post'] ).'" target="_blank">'.get_the_title($lead['single_post']).'</a>' : '';
				$data['date_time'] = isset($lead['dete_time']) ? date("m/d/Y h:i:s A T",$lead['dete_time']) : '';
				$_data[] = $data;				
			}
            $data = $_data;
        }elseif(isset($formId) && isset($form_data) && !empty($form_data)){
			foreach($form_data as $leadID => $lead){
				if($formId == $lead['form_id']){
					$data = array();
					$data['ID']         = $lead['lead_id'];
					$data['form_name']  = isset($lead['form_id']) ? get_the_title($lead['form_id']) : '';
					$data['lead_id'] = $lead['lead_id'];
					$_data[] = $data;
				}
			}
            $data = $_data;
		}else{
            $data = array();
        }

		$current_page = $this->get_pagenum();

		$total_items = count( $data );

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	function extra_tablenav( $which ) {
		if ( $which == "top" ){
			$args = array(
			'numberposts' => -1,
			'fields'      => 'ids',
			'post_type'   => 'cwp_forms'
			);
			$forms = get_posts( $args );
			if(isset($forms) && !empty($forms)){
				?>
				<div class="alignleft actions">
					<label for="filter_form" class="screen-reader-text">Filter by date</label>
					<select name="filter_form" id="filter-by-form">
						<option value="0"><?php esc_html_e( 'All Forms', 'cubewp-forms' ); ?></option>
						<?php
						foreach($forms as $form){
							$selected = isset( $_REQUEST['filter_form'] ) && $_REQUEST['filter_form'] == $form ? 'selected=selected':'';
						?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr($form); ?>"><?php echo get_post_field( 'post_title', $form ); ?></option>
							
						<?php
						}
						?>
					</select>
					<input type="submit" id="doaction" class="button action" value="Filter">		
				</div>
				<?php
			}
		}
				
	}
    
}