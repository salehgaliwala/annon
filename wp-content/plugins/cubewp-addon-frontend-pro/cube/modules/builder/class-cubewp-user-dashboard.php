<?php

/**
 * Dashboard Page for frontend
 *
 * @package cubewp-addon-frontend/cube/modules/builder
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_User_Dashboard
 */
class CubeWp_User_Dashboard {

	/**
	 * CubeWp_User_Dashboard Constructor.
	 */
	public function __construct() {
		add_action( 'cubewp_user_dashboard', array( $this, 'create_user_dashboard' ) );
		add_action( 'wp_ajax_cwp_new_dashboard_tab_ajax', array( $this, 'cwp_new_dashboard_tab_ajax' ) );
		add_action( 'wp_ajax_cwp_dashboard_content_type_fields', array( $this, 'cwp_dashboard_content_type_fields' ) );
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
	
	/**
	 * Method create_user_dashboard
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function create_user_dashboard() {
		$this->save_group();
		$this->manage_dashboard();
	}
	
	/**
	 * Method save_group
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function save_group() {
		if ( isset( $_POST['cwp_save_dashboard'] ) ) {
			if ( isset( $_POST['cwp_userdash'] ) ) {
				$dashboard = CubeWp_Sanitize_Muli_Array($_POST['cwp_userdash']);
				update_option( 'cwp_userdash', $dashboard );
			} else {
				delete_option( 'cwp_userdash' );
			}
		}
	}
	
	/**
	 * Method manage_dashboard
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function manage_dashboard() {
		$dashboard_ui = '<form method="post" action="" class="user-dashboard-builder" id="user-dashboard-form">';

		$dashboard_ui .= '<div id="cubewp-title-bar">';
		$dashboard_ui .= '<h1>' . esc_html__("User Dashboard Builder", "cubewp-frontend") . '</h1>';
		$dashboard_ui .= '<div class="shoftcode-area">';
		if ( ! empty( self::check_saved_value() ) ) {
			$dashboard_ui .= '<div class="cwpform-shortcode"><div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path></svg>[cwp_dashboard][/cwp_dashboard]</div></div>';
		}
		$dashboard_ui .= $this->save_button();
		$dashboard_ui .= '</div>';
        $dashboard_ui .= '</div>';

		$dashboard_ui .= '<div class="cubewp-dashboard-builder">';
		$dashboard_ui .= '<div class="cubewp-dashboard-builder-area">';
		$dashboard_ui .= '<div class="cubewp-dashboard-builder-area-topbar">';
		$dashboard_ui .= $this->add_new_tab_btn();
		$dashboard_ui .= '</div>';
		$dashboard_ui .= '<div class="cubewp-dashboard-builder-area-content cwp-validation">';
		$dashboard_ui .= '<div class="cubewp-dashboard-builder-area-content-heading">';
		$dashboard_ui .= '<p class="cubewp-user-dashboard-tab-sorter"></p>';
		$dashboard_ui .= '<p class="cubewp-user-dashboard-tab-icon">' . esc_html__( "Icon", "cubewp-frontend" ) . '</p>';
		$dashboard_ui .= '<p class="cubewp-user-dashboard-tab-title">' . esc_html__( "Title", "cubewp-frontend" ) . '</p>';
		$dashboard_ui .= '<p class="cubewp-user-dashboard-tab-user-role">' . esc_html__( "User Role", "cubewp-frontend" ) . '</p>';
		$dashboard_ui .= '<p class="cubewp-user-dashboard-tab-content-type">' . esc_html__( "Content Type", "cubewp-frontend" ) . '</p>';
		$dashboard_ui .= '<p class="cubewp-user-dashboard-tab-id">' . esc_html__( "Tab ID", "cubewp-frontend" ) . '</p>';
		$dashboard_ui .= '<p class="cubewp-user-dashboard-tab-actions"></p>';
		$dashboard_ui .= '</div>';
		$dashboard_ui .= $this->cwp_saved_content();
		$dashboard_ui .= '</div>';
		$dashboard_ui .= '</div>';
		$dashboard_ui .= '</div>';
		$dashboard_ui .= '</form>';

		echo cubewp_core_data($dashboard_ui);
	}
	
	/**
	 * Method add_new_tab_btn
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function add_new_tab_btn() {
		return '<button type="button" class="button" id="cwp-add-new-tab-btn"><span class="dashicons dashicons-plus-alt"></span> ' . __( 'Add New Tab', 'cubewp' ) . '</button>';
	}
	
	/**
	 * Method cwp_saved_content
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function cwp_saved_content() {
		$cwp_userdash  = self::check_saved_value();
        $counter=1;
		$saved_content = '';
		if ( isset( $cwp_userdash ) && ! empty( $cwp_userdash ) ) {
			foreach ( $cwp_userdash as $content_id => $content_fields ) {
				$content_fields['content_id'] = $content_id;
                $content_fields['counter'] = $counter;
                $counter++;
				$saved_content .= $this->cwp_dashboard_tab( $content_fields );
			}
		}

		return $saved_content;
	}
	
	/**
	 * Method check_saved_value
	 *
	 * @return array
	 * @since  1.0.0
	 */
	private static function check_saved_value() {
		if ( isset( $_POST['cwp_save_dashboard'] ) && ! empty( $_POST['cwp_save_dashboard'] ) && isset( $_POST['cwp_userdash'] ) ) {
			return CubeWp_Sanitize_Muli_Array($_POST['cwp_userdash']);
		} else {
			return CWP()->cubewp_options( 'cwp_userdash' );
		}
	}
	
	/**
	 * Method cwp_dashboard_tab
	 *
	 * @param array $FieldData
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function cwp_dashboard_tab( $FieldData = array() ) {
		$defaults     = array(
			'content_type' => '',
			'content_id'   => 'cwp_tab_' . rand( 10000000, 1000000000000 ),
		);
		$tab_ui       = '';
		$FieldData    = wp_parse_args( $FieldData, $defaults );
		$hide_class   = ( isset( $FieldData['title'] ) && ( $FieldData['title'] != '' ) ) ? 'hidden' : '';
		$hide_c_class = '';
		$title        = $FieldData['title'] ?? esc_html__( "Title", "cubewp-frontend" );
		$icon         = $FieldData['icon'] ?? esc_html__( "Icon", "cubewp-frontend" );
		$user_role    = $FieldData['user_role'] ?? esc_html__( "User Role", "cubewp-frontend" );
		$content_type = $FieldData['content_type'];
        $counter = isset($FieldData["counter"]) ? $FieldData["counter"] : 1;
		if ( empty( $content_type ) ) {
			$content_type = esc_html__( "Content Type", "cubewp-frontend" );
		}
		if ( empty( $user_role ) ) {
			$user_role = "---";
		}
		if ( empty( $icon ) ) {
			$icon = "---";
		}
        if ($hide_class != 'hidden') {
	        $hide_c_class = 'active-tab';
        }
		$tab_ui .= '<div class="cubewp-user-dashboard-tab ' . $hide_c_class . '">';
		$tab_ui .= '<div class="cubewp-user-dashboard-tab-info">';
		$tab_ui .= '<p class="cubewp-user-dashboard-tab-sorter"><svg width="20" height="25" viewBox="0 0 320 512" fill="#bfbfbf"><path d="M40 352c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zm192 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 320l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40zM232 192c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 160l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40L40 32C17.9 32 0 49.9 0 72l0 48c0 22.1 17.9 40 40 40zM232 32c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0z"></path></svg><span class="field-counter">' . $counter . '</span></p>';
		$tab_ui .= '<p class="cubewp-user-dashboard-tab-icon"><span class="' . $icon . '"></span></p>';
		$tab_ui .= '<p class="cubewp-user-dashboard-tab-title">' . $title . '</p>';
		$tab_ui .= '<p class="cubewp-user-dashboard-tab-user-role">' . $user_role . '</p>';
		$tab_ui .= '<p class="cubewp-user-dashboard-tab-content-type">' . $content_type . '</p>';
		$tab_ui .= '<p class="cubewp-user-dashboard-tab-id">' . $FieldData['content_id'] . '</p>';
		$tab_ui .= '<p class="cubewp-user-dashboard-tab-actions">';
		$tab_ui .= '<span class="dashicons dashicons-trash cubewp-user-dashboard-tab-action-delete"></span>';
		$tab_ui .= '<span class="dashicons dashicons-arrow-down-alt2 cubewp-user-dashboard-tab-action-toggler"></span>';
		$tab_ui .= '</p>';
		$tab_ui .= '</div>';
		$tab_ui .= '<div class="cubewp-user-dashboard-tab-form ' . $hide_class . '">';
		$tab_ui .= $this->cubewp_user_dashboard_tab_options( $FieldData );
		$tab_ui .= '</div>';
		$tab_ui .= '</div>';

		return $tab_ui;
	}
	
	/**
	 * Method cubewp_user_dashboard_tab_options
	 *
	 * @param array $options_fields
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	private function cubewp_user_dashboard_tab_options( $options_fields ) {
		$fields_ui  = '';
		$content_id = $options_fields['content_id'] ?? '';
		$title      = $options_fields['title'] ?? "";
		$icon       = $options_fields['icon'] ?? "";
		$user_role  = $options_fields['user_role'] ?? "";

		$fields_ui .= '<div class="cubewp-user-dashboard-tab-form-field">';
		$fields_ui .= apply_filters( 'cubewp/admin/dashboard/text/field', '', array(
			'id'          => '',
			'name'        => 'cwp_userdash[' . $content_id . '][title]',
			'value'       => $title,
			'class'       => 'tab-title-field',
			'placeholder' => esc_html__( 'Set your tab title here..', 'cubewp' ),
			'label'       => esc_html__( 'Tab Title', 'cubewp' ),
			'required'    => true,
			'extra_attrs' => 'maxlength=50'
		) );
		$fields_ui .= '</div>';
		$fields_ui .= '<div class="cubewp-user-dashboard-tab-form-field">';
		$fields_ui .= apply_filters( 'cubewp/admin/dashboard/text/field', '', array(
           'id'          => '',
           'name'        => 'cwp_userdash[' . $content_id . '][icon]',
           'value'       => $icon,
           'class'       => 'tab-icon-field',
           'placeholder' => esc_html__( 'Your icon here', 'cubewp' ),
           'label'       => esc_html__( 'Tab Icon', 'cubewp' ),
           'required'    => false,
           'description' => esc_html__( 'You can use this field to pass icon class to frontend user dashboard tab.', 'cubewp-frontend' )
        ) );
		$fields_ui .= self::dashicons_list();
		$fields_ui .= '</div>';
		$fields_ui .= '<div class="cubewp-user-dashboard-tab-form-field">';
		$fields_ui .= apply_filters( 'cubewp/admin/dashboard/dropdown/field', '', array(
			'id'          => '',
			'name'        => 'cwp_userdash[' . $content_id . '][user_role]',
			'placeholder' => esc_html__( 'Select User Role', 'cubewp' ),
			'value'       => $user_role,
			'class'       => 'tab-role-field',
			'label'       => esc_html__( 'Select User Role', 'cubewp' ),
			'options'     => cwp_get_user_roles_name(),
			'description' => esc_html__( 'Do not select any role if you want to show this tab to all registered users', 'cubewp-frontend' )
		) );
		$fields_ui .= '</div>';
		$fields_ui .= '<div class="cubewp-user-dashboard-tab-form-field">';
		$fields_ui .= apply_filters( 'cubewp/admin/dashboard/dropdown/field', '', array(
			'id'          => $options_fields['content_id'],
			'class'       => 'tab-type-field',
			'name'        => 'cwp_userdash[' . $content_id . '][content_type]',
			'value'       => $options_fields['content_type'],
			'placeholder' => esc_html__( 'Select content type', 'cubewp' ),
			'label'       => esc_html__( 'Content Type', 'cubewp' ),
			'options'     => $this->user_dash_content_types(),
			'required'    => true,
		) );
		$fields_ui .= '</div>';
		$fields_ui .= '<div class="cubewp-user-dashboard-tab-form-conditional-fields">';
		$fields_ui .= $this->cwp_saved_content_fields( $options_fields );
		$fields_ui .= '</div>';

		return $fields_ui;
	}
	
	/**
	 * Method user_dash_content_types
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function user_dash_content_types() {
		$types = array(
			'post_types'   => esc_html__( 'Post Types', 'cubewp' ),
			'custom_tab'   => esc_html__( 'Custom Tab', 'cubewp' ),
			'page_content' => esc_html__( 'Page content', 'cubewp' ),
			'saved'        => esc_html__( 'Saved', 'cubewp' ),
			'logout'       => esc_html__( 'Logout', 'cubewp' ),
		);

		return apply_filters( 'user/dashboard/content/types', $types );
	}
	
	/**
	 * Method cwp_saved_content_fields
	 *
	 * @param array $fields
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function cwp_saved_content_fields( $fields = array() ) {
		$defaults = array(
			'content_type' => '',
			'content_id'   => 'cwp_tab_' . rand( 10000000, 1000000000000 ),
		);
		$fields   = wp_parse_args( $fields, $defaults );

		$content_id   = isset( $fields['content_id'] ) ? $fields['content_id'] : '';
		$content_type = isset( $fields['content_type'] ) ? $fields['content_type'] : '';
		$content      = isset( $fields['content'] ) ? $fields['content'] : '';

		$fields_ui = '';
		if ( $content_type == 'post_types' ) {
			$fields_ui .= '<div class="cubewp-user-dashboard-tab-form-field">';
			$fields_ui .= apply_filters( 'cubewp/admin/dashboard/dropdown/field', '', array(
				'id'          => '',
				'name'        => 'cwp_userdash[' . $content_id . '][content]',
				'value'       => $content,
				'label'       => esc_html__( 'Tab Content (Post Types)', 'cubewp' ),
				'options'     => CWP_all_post_types( 'dashboard' ),
				'required'    => true,
				'description' => esc_html__( 'This tab will show user posts with action buttons.', 'cubewp-frontend' )
			) );
			$fields_ui .= '</div>';
		}

		if ( $content_type == 'custom_tab' ) {
			$fields_ui .= '<div class="cubewp-user-dashboard-tab-form-field">';
			$fields_ui .= apply_filters( 'cubewp/admin/dashboard/textarea/field', '', array(
				'id'          => '',
				'name'        => 'cwp_userdash[' . $content_id . '][content]',
				'value'       => $content,
				'class'       => 'cwp-textarea-field',
				'placeholder' => esc_html__( ' ex: dashicons-admin-post', 'cubewp' ),
				'label'       => esc_html__( 'Tab Content (Custom content)', 'cubewp' ),
				'required'    => true,
				'extra_attrs' => 'rows=8',
				'description' => esc_html__( 'This tab will show the custom content that you will enter here.', 'cubewp-frontend' )
			) );
			$fields_ui .= '</div>';
		}

		if ( $content_type == 'page_content' ) {
			$fields_ui .= '<div class="cubewp-user-dashboard-tab-form-field">';
			$fields_ui .= apply_filters( 'cubewp/admin/dashboard/dropdown/field', '', array(
				'id'          => '',
				'name'        => 'cwp_userdash[' . $content_id . '][content]',
				'value'       => $content,
				'label'       => esc_html__( 'Tab Content (Pages)', 'cubewp' ),
				'options'     => cwp_pages_list(),
				'required'    => true,
				'description' => esc_html__( 'This tab will show your selected page content', 'cubewp-frontend' )
			) );
			$fields_ui .= '</div>';
		}
		$fields_ui = apply_filters( 'cwp_dashboard_content_type_fields', $fields_ui, $fields );

		return $fields_ui;
	}
	
	/**
	 * Method save_button
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function save_button() {
		if ( ! empty( self::check_saved_value() ) ) {
			return '<button type="submit" class="button button-primary cwp-save-button" name="cwp_save_dashboard"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Save Changes', 'cubewp-frontend' ) . '</button>';
		}else {
			return '<button type="submit" class="button button-primary cwp-save-button" name="cwp_save_dashboard">' . esc_html__( 'Get Shortcode', 'cubewp-frontend' ) . '</button>';
		}
	}
	
	/**
	 * Method cwp_new_dashboard_tab_ajax
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function cwp_new_dashboard_tab_ajax() {
		check_ajax_referer( 'cwp-user_tabs_nonce', 'nonce' );
		if ( true ) {
			wp_send_json_success( $this->cwp_dashboard_tab() );
		} else {
			wp_send_json_error( array( 'error' => $custom_error ) );
		}
	}
	
	/**
	 * Method cwp_dashboard_content_type_fields
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function cwp_dashboard_content_type_fields() {
		check_ajax_referer( 'cwp-user_tabs_nonce', 'nonce' );
		if ( true ) {
			$content_id     = isset( $_POST['content_id'] ) ? sanitize_text_field($_POST['content_id']) : '';
			$content_type   = isset( $_POST['content_type'] ) ? sanitize_text_field($_POST['content_type']) : '';
			$content_fields = $this->cwp_saved_content_fields( array(
				'content_id'   => $content_id,
				'content_type' => $content_type
			) );
			wp_send_json_success( $content_fields );
		}
	}

	
	/**
	 * Method dashicons_list
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	private static function dashicons_list() {
		return '<div class="cubewp-dashboard-icons">
		<span class="dashicons dashicons-megaphone"></span>
		<span class="dashicons dashicons-heart"></span>
		<span class="dashicons dashicons-admin-users"></span>
		<span class="dashicons dashicons-admin-generic"></span>
		<span class="dashicons dashicons-format-image"></span>
		<span class="dashicons dashicons-admin-site-alt"></span>
		<span class="dashicons dashicons-html"></span>
		<span class="dashicons dashicons-car"></span>
		<span class="dashicons dashicons-location"></span>
				<span class="dashicons dashicons-admin-post"></span>
		<span class="dashicons dashicons-exit"></span>
				<a href="https://developer.wordpress.org/resource/dashicons" target="_blank" class="button" type="button">'.esc_html__('More Icons','cubewp-frontend').'</a>
			</div>';
	}

}