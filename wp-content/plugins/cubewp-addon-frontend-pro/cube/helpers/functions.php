<?php

/**
 * functions.php.
 *
 * @package cubewp-addon-frontend/cube/helpers
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists('cwp_upload_form_gallery_images')) {
    function cwp_upload_form_gallery_images( $key = '', $val = array(), $files = array(), $post_id = 0 ){
        
        $attachment_ids = array();
        if(isset($val) && !empty($val) && is_array($val)){
            foreach($val as $file_id){
                if(isset($files['cwp_user_form']['name']['cwp_meta'][$key][$file_id])){
                    $file_names = $files['cwp_user_form']['name']['cwp_meta'][$key][$file_id];
                    foreach($file_names as $file_key => $file_name){
                        if( $file_name != '' ){
                            $file = array( 
                                'name'     => $files['cwp_user_form']['name']['cwp_meta'][$key][$file_id][$file_key],
                                'type'     => $files['cwp_user_form']['type']['cwp_meta'][$key][$file_id][$file_key],
                                'tmp_name' => $files['cwp_user_form']['tmp_name']['cwp_meta'][$key][$file_id][$file_key],
                                'error'    => $files['cwp_user_form']['error']['cwp_meta'][$key][$file_id][$file_key],
                                'size'     => $files['cwp_user_form']['size']['cwp_meta'][$key][$file_id][$file_key] 
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

if ( ! function_exists('cwp_upload_form_repeating_gallery_images')) {
    function cwp_upload_form_repeating_gallery_images( $key = '',$_key = '',$field_key= '', $val = array(), $files = array(), $post_id = 0 ){
        
        $attachment_ids = array();
        if(isset($val) && !empty($val) && is_array($val)){
            foreach($val as $file_id){
                if(isset($files['cwp_user_form']['name']['cwp_meta'][$key][$_key][$field_key][$file_id])){
                    $file_names = $files['cwp_user_form']['name']['cwp_meta'][$key][$_key][$field_key][$file_id];
                    foreach($file_names as $file_key => $file_name){
                        if( $file_name != '' ){
                            $file = array( 
                                'name'     => $files['cwp_user_form']['name']['cwp_meta'][$key][$_key][$field_key][$file_id][$file_key],
                                'type'     => $files['cwp_user_form']['type']['cwp_meta'][$key][$_key][$field_key][$file_id][$file_key],
                                'tmp_name' => $files['cwp_user_form']['tmp_name']['cwp_meta'][$key][$_key][$field_key][$file_id][$file_key],
                                'error'    => $files['cwp_user_form']['error']['cwp_meta'][$key][$_key][$field_key][$file_id][$file_key],
                                'size'     => $files['cwp_user_form']['size']['cwp_meta'][$key][$_key][$field_key][$file_id][$file_key] 
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

if ( ! function_exists('cwp_upload_form_file')) {
    function cwp_upload_form_file( $key = '', $val = array(), $files = array(), $post_id = 0 ){
    
        $attachment_id = '';
        if(isset($files['cwp_user_form']['name']['cwp_meta'][$key]) && $files['cwp_user_form']['name']['cwp_meta'][$key] != ''){
            $file = array( 
                'name'     => $files['cwp_user_form']['name']['cwp_meta'][$key],
                'type'     => $files['cwp_user_form']['type']['cwp_meta'][$key],
                'tmp_name' => $files['cwp_user_form']['tmp_name']['cwp_meta'][$key],
                'error'    => $files['cwp_user_form']['error']['cwp_meta'][$key],
                'size'     => $files['cwp_user_form']['size']['cwp_meta'][$key] 
            );
            $attachment_id = cwp_handle_attachment( $file, $post_id);
        }else if(isset($val) && $val != 0){
            $attachment_id = $val;
        }
        return $attachment_id;
    }
}

if ( ! function_exists('cwp_upload_user_gallery_images')) {
    function cwp_upload_user_gallery_images( $key = '', $val = array(), $files = array(), $user_id = 0, $form ='' ){
        
        $attachment_ids = array();
        if(isset($val) && !empty($val) && is_array($val)){
            foreach($val as $file_id){
                if(isset($files[$form]['name']['custom_fields'][$key][$file_id])){
                    $file_names = $files[$form]['name']['custom_fields'][$key][$file_id];
                    foreach($file_names as $file_key => $file_name){
                        if( $file_name != '' ){
                            $file = array( 
                                'name'     => $files[$form]['name']['custom_fields'][$key][$file_id][$file_key],
                                'type'     => $files[$form]['type']['custom_fields'][$key][$file_id][$file_key],
                                'tmp_name' => $files[$form]['tmp_name']['custom_fields'][$key][$file_id][$file_key],
                                'error'    => $files[$form]['error']['custom_fields'][$key][$file_id][$file_key],
                                'size'     => $files[$form]['size']['custom_fields'][$key][$file_id][$file_key] 
                            );
                            $attachment_ids[] = cwp_handle_attachment( $file);
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

if ( ! function_exists('cwp_upload_user_file')) {
    function cwp_upload_user_file( $key = '', $val = array(), $files = array(), $user_id = 0 ,$form =''){
    
        $attachment_id = '';
        if(isset($files[$form]['name']['custom_fields'][$key]) && $files[$form]['name']['custom_fields'][$key] != ''){
            $file = array( 
                'name'     => $files[$form]['name']['custom_fields'][$key],
                'type'     => $files[$form]['type']['custom_fields'][$key],
                'tmp_name' => $files[$form]['tmp_name']['custom_fields'][$key],
                'error'    => $files[$form]['error']['custom_fields'][$key],
                'size'     => $files[$form]['size']['custom_fields'][$key] 
            );
            $attachment_id = cwp_handle_attachment( $file);
        }else if(isset($val) && $val != 0){
            $attachment_id = $val;
        }
        return $attachment_id;
    }
}

if ( ! function_exists('cwp_get_proximity_sql')) {
    function cwp_get_proximity_sql( $lat_meta_key = '', $lng_meta_key = '', $lat = '', $lng = '', $units = 'mi', $proximity = 50 ) {
        global $wpdb;
        
        $earth_radius = $units == 'mi' ? 3959 : 6371;
        
        $sql = "
        SELECT $wpdb->posts.ID,
        ( %s * IFNULL( acos(
            cos( radians(%s) ) *
            cos( radians( latitude.meta_value ) ) *
            cos( radians( longitude.meta_value ) - radians(%s) ) +
            sin( radians(%s) ) *
            sin( radians( latitude.meta_value ) )
        ), 0 ) )
        AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
        FROM $wpdb->posts
        INNER JOIN $wpdb->postmeta
            AS latitude
            ON $wpdb->posts.ID = latitude.post_id
        INNER JOIN $wpdb->postmeta
            AS longitude
            ON $wpdb->posts.ID = longitude.post_id
        WHERE 1=1
            AND ($wpdb->posts.post_status = 'publish' )
            AND latitude.meta_key='". $lat_meta_key ."'
            AND longitude.meta_key='". $lng_meta_key ."'
        HAVING distance < %s
        ORDER BY distance ASC";
        
        $sql = $wpdb->prepare( $sql, $earth_radius, $lat, $lng, $lat, $proximity );
        
        $post_ids = (array) $wpdb->get_results( $sql, OBJECT_K );
        
        if ( empty( $post_ids ) ) { $post_ids = ['none']; }
        
        return $post_ids;
    }
}

if ( ! function_exists('cubewp_price')) {
    function cubewp_price( $price = 0 ){
        if(class_exists('woocommerce')){
            return wc_price($price);
        }else{
            return $price;
        }
        
    }
}

if ( ! function_exists('cubewp_add_quick_signup_into_settings')) {
    function cubewp_add_quick_signup_into_settings( $section_fields ) {
        $fields   = array();
        $fields[] = array(
           'id'      => 'allow_instant_signup',
           'type'    => 'switch',
           'title'   => __( 'Instant Sign In/Sign Up', 'cubewp-framework' ),
           'default' => '0',
           'desc'    => __( 'If you want to enable quick sign in or signup on post type submission form then enable this option.', 'cubewp-framework' ),
        );
        $fields[] = array(
           'id'      => 'allow_password',
           'type'    => 'switch',
           'title'   => __( 'Enable Password', 'cubewp-framework' ),
           'default' => '0',
           'desc'    => __( 'If you want to enable password field with signup on post type submission form then enable this option.', 'cubewp-framework' ),
           'required' => array(
              array( 'allow_instant_signup', 'equals', '1' )
           )
        );
        $fields[] = array(
           'id'      => 'email_otp_verification',
           'type'    => 'switch',
           'title'   => __( 'Enable Email Verification', 'cubewp-framework' ),
           'default' => '0',
           'desc'    => __( 'Enable If you want to setup email verification with signup on post type submission form', 'cubewp-framework' ),
           'required' => array(
              array( 'allow_instant_signup', 'equals', '1' )
           )
        );
        $fields[] = array(
           'id'      => 'user_gdpr_compliance',
           'type'    => 'switch',
           'title'   => __('Enable GDPR Compliance', 'cubewp-frontend'),
           'default' => '1',
           'desc'    => __('Enable GDPR Compliance on User Profile Form', 'cubewp-frontend'),
        );

        return array_merge( $section_fields, $fields );
    }

    add_filter( 'cubewp/settings/section/general-settings', 'cubewp_add_quick_signup_into_settings' );
}

// Adding New Section
if ( ! function_exists('cubewp_add_url_settings_sections')) {
    function cubewp_add_url_settings_sections($sections) {
       $settings['url-settings'] = array(
          'title'  => __('URL Config', 'cubewp'),
          'id'     => 'url-settings',
          'icon'   => ' dashicons-admin-links',
          'fields' => array(
             array(
                'id'       => 'dashboard_page',
                'type'     => 'pages',
                'title'    => __('Frontend Dashboard Page', 'cubewp'),
                'subtitle' => __('This must be an URL.', 'cubewp'),
                'validate' => 'url',
                'desc'     => __('Select the page used for the Frontend Dashboard (Page must include the Dashboard Shortcode)', 'cubewp'),
                'default'  => ''
             ),
             array(
                'id'       => 'price_plan',
                'title'    => __('Price Plan Page', 'cubewp'),
                'subtitle' => __('This must be an URL.', 'cubewp'),
                'validate' => 'url',
                'desc'     => __('Select the page used for Price Plans (Page must include the Price Plan Shortcode)', 'cubewp'),
                'type'     => 'pages',
                'default'  => '',
             ),
             array(
                'id'    => 'submit_edit_page',
                'title' => __('Submit/Edit Post Page', 'cubewp'),
                'desc'  => __('Select the page used for any Custom Post Submission (Page must include the Post Type Submit Shortcode)', 'cubewp'),
                'type'  => 'submit_edit_page',
             ),
          )
       );
       $single_position = array_search('map', array_keys($sections)) + 1;
 
       return array_merge(array_slice($sections, 0, $single_position), $settings, array_slice($sections, $single_position));
    }
 
    add_filter('cubewp/options/sections', 'cubewp_add_url_settings_sections', 9, 1);
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

 if ( ! function_exists( 'cubewp_settings_section_post_settings' ) ) {
	function cubewp_settings_section_post_settings( $section_fields ) {
		$fields    = array();
		$fields[] = array(
			'id'       => 'post_admin_approved',
			'title'    => __( 'Admin Moderation', 'cubewp-framework' ),
			'type'     => 'cwp_types_switch',
			'default'  => 'pending',
		);

		return array_merge( $fields, $section_fields );
	}

	add_filter( 'cubewp/settings/section/post_settings', 'cubewp_settings_section_post_settings', 9 );
}

if ( ! function_exists( "cubewp_setting_type_cwp_types_switch" ) ) {
	function cubewp_setting_type_cwp_types_switch( $output = '', $args = array() ) {
		$args      = ( new CubeWp_Settings_Fields )->default_input_parameters( $args );
		$fieldID = $args['id'];
		$output = apply_filters( "cubewp/settings/heading/field", '', $args );
		$postTypes = CWP_all_post_types('settings');
		$output .= '<td>';
		foreach ($postTypes as $postType => $postTypeLabel) {
			$args['id'] = $fieldID . '[' . $postType . ']';
			$args['options'] = array(
				'pending' => sprintf( esc_html__( 'Enable Moderation For %s', 'cubewp-frontend' ), $postTypeLabel ),
				'publish' => sprintf( esc_html__( 'Disable Moderation For %s', 'cubewp-frontend' ), $postTypeLabel ),
			);
			$value = isset($args['value'][$postType]) ? $args['value'][$postType] : '';
			$output .= '<fieldset id="cwp-' . esc_attr( $args['id'] ) . '" class="cwp-field-container cwp-' . esc_attr( $args['type'] ) . '-container" data-id="' . esc_attr( $args['id'] ) . '" data-type="' . esc_attr( $args['type'] ) . '" style="margin-bottom: 10px;">';
			$field_args = array(
				'id'          => $args['id'],
				'name'        => $args['id'],
				'placeholder' => $args['placeholder'] == '' ? esc_html__( 'Select Option', "cubewp-framework" ) : '',
				'class'       => $args['class'],
				'value'       => $value,
				'options'     => $args['options'],
				'extra_attrs' => $args['extra_attrs'],
			);
			$field_args['class'] = $field_args['class'] . ' cwp-single-select';
			$output .= cwp_render_dropdown_input( $field_args );
			$args['desc'] = sprintf(__( 'Enabling this will require %s to be approved by an admin before publishing. If disabled, %s will automatically publish after submission and payment, if applicable.<br>Note: If paying via Bank Transfer or similar payment method, %s will still require payment approval to be published.', 'cubewp-frontend' ), $postTypeLabel, $postTypeLabel, $postTypeLabel);
			$output .= apply_filters( "cubewp/settings/desc/field", '', $args );
			$output .= '</fieldset>';
		}
		$output .= '</td>';

		return $output;
	}

	add_filter('cubewp/settings/cwp_types_switch/field', 'cubewp_setting_type_cwp_types_switch', 10, 2);
}
 
if ( ! function_exists("cubewp_login_page_template")) {
    function cubewp_login_page_template( $post_templates, $wp_theme, $post, $post_type ) {
       $post_templates['cubewp-template-login.php'] = esc_html__("CubeWP Login", "cubewp-frontend");
 
       return $post_templates;
    }
 
    add_filter( 'theme_page_templates', 'cubewp_login_page_template', 11, 4 );
}
 
if ( ! function_exists("cubewp_login_page_template_output")) {
    function cubewp_login_page_template_output( $page_template ) {
       if ( get_page_template_slug() == 'cubewp-template-login.php' ) {
          $page_template = CUBEWP_FRONTEND_PLUGIN_DIR . 'cube/templates/cubewp-template-login.php';
       }
 
       return $page_template;
    }
 
    add_filter( 'page_template', 'cubewp_login_page_template_output' );
}
/**
 * Method cube_shortcode
 *
 * @param array $args
 *
 * @return string html
 * @since  1.0.0
 */
if (!function_exists('cube_shortcode')) {
    function cube_shortcode($field= array(), $value = '') {
        $output='';
        $output .= '<div class="cwp-single-widget cwp-shortcode-widget '.$field['container_class'].'">';
        $output .= '<div class="'.$field['class'].'">';
        ob_start();
        echo do_shortcode($field['shortcode']);
        $output .= ob_get_clean();
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }
}

if ( ! function_exists( 'cubewp_get_loop_builder_by_post_type' ) ) {
    function cubewp_get_loop_builder_by_post_type( $post_type, $style = false ) {
        $form_options     = CWP()->get_form( 'loop_builder' );
        $loop_layout_html = '';
        if ( ! empty( $form_options[ $post_type ] ) ) {
            $form_options = $form_options[ $post_type ];
            if ( ! $style ) {
                foreach ( $form_options as $_style => $option ) {
                    if ( isset( $option['form']['loop-is-primary'] ) && $option['form']['loop-is-primary'] == 'yes' ) {
                        $style = $_style;
                        break;
                    }
                }
            }
            if ( isset( $form_options[ $style ] ) && ! empty( $form_options[ $style ] ) ) {
                $form_options     = $form_options[ $style ];
                $loop_layout_html = $form_options['loop-layout-html'];
            }
        }
        $string = stripslashes( $loop_layout_html );
        preg_match_all( '/\[loop_[^\]]*\]/', $string, $matches );
        $values = array();
        foreach ( $matches as $fields ) {
            foreach ( $fields as $field ) {
                $field            = trim( $field, '[]' );
                $name             = str_replace( 'loop_', '', $field );
                $values[ $field ] = cubewp_get_loop_builder_shortcode_value( $name );
            }
        }
        foreach ( $values as $field => $value ) {
            $string = str_replace( '[' . $field . ']', (string) $value, $string );
        }
        return $string;
    }
}
if ( ! function_exists( 'cubewp_get_loop_builder_shortcode_value' ) ) {
    function cubewp_get_loop_builder_shortcode_value( $field ) {
        if ( $field == 'the_title' ) {
            return get_the_title();
        } else if ( $field == 'the_excerpt' ) {
            return get_the_excerpt();
        } else if ( $field == 'the_content' ) {
            return get_the_content();
        } else if ( $field == 'post_link' ) {
            return get_the_permalink();
        } else if ( $field == 'the_date' ) {
            return get_the_date();
        } else if ( $field == 'author_name' ) {
            $author_id = get_post_field( 'post_author', get_the_ID() );
            $author    = get_userdata( $author_id );
            if ( ! empty( $author ) && ! is_wp_error( $author ) ) {
                return $author->display_name;
            }
        } else if ( $field == 'author_link' ) {
            $author_id = get_post_field( 'post_author', get_the_ID() );
            return get_author_posts_url( $author_id );
        } else if ( $field == 'author_avatar' ) {
            $author_id = get_post_field( 'post_author', get_the_ID() );
            return get_avatar_url( $author_id );
        } else if ( $field == 'featured_image' ) {
            return cubewp_get_post_thumbnail_url( get_the_ID() );
        } else if ( taxonomy_exists( $field ) ) {
            $terms = wp_get_post_terms( get_the_ID(), $field );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $term = $terms[0];
                return $term->name;
            }
        } else if ( str_contains( $field, '_tax_link' ) ) {
            $taxonomy = str_replace( '_tax_link', '', $field );
            $terms    = wp_get_post_terms( get_the_ID(), $taxonomy, array( 'fields' => 'ids' ) );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $term = $terms[0];
                return get_term_link( $term );
            }
        } else if ( $field == 'post_save' ) {
            ob_start();
            get_post_save_button( get_the_ID() );
            return ob_get_clean();
        } else {
            $return = get_field_value( $field, false, get_the_ID() );

            if ( is_array( $return ) ) {
                return isset( $return['address'] ) && ! empty( $return['address'] ) ? $return['address'] : '';
            }else {
                return $return;
            }
        }
    }
}
if ( ! function_exists( 'if_cubewp_emails_enabled' ) ) {
	function if_cubewp_emails_enabled() {
		global $cwpOptions;
		$cwpOptions = ! empty( $cwpOptions ) ? $cwpOptions : get_option( 'cwpOptions' );

		return isset( $cwpOptions['enable_emails'] ) && $cwpOptions['enable_emails'] == '1';
	}
}

if ( ! function_exists( 'cubewp_get_all_post_type_email_templates' ) ) {
	function cubewp_get_all_post_type_email_templates() {
		$args = array(
			'post_type'   => CubeWp_Emails::$post_type,
			'post_status' => 'publish',
			'fields'      => 'ids',
			'meta_query'  => array(
				'relation' => 'AND',
				array(
					'key'     => 'email_recipient',
					'value'   => '',
					'compare' => '!='
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => 'admin_email_types',
						'value'   => array( 'new-post', 'post-updated' ),
						'compare' => 'IN'
					),
					array(
						'key'     => 'user_email_types',
						'value'   => array( 'new-post', 'post-updated' ),
						'compare' => 'IN'
					)
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => 'admin_email_post_types',
						'value'   => '',
						'compare' => '!='
					),
					array(
						'key'     => 'user_email_post_types',
						'value'   => '',
						'compare' => '!='
					)
				)
			)
		);

		return get_posts( $args );
	}
}

if ( ! function_exists( 'cubewp_get_email_template_by_post_id' ) ) {
	function cubewp_get_email_template_by_post_id( $post_id, $recipient, $email_type ) {
		$post_type = get_post_type( $post_id );
		$args      = array(
			'post_type'      => CubeWp_Emails::$post_type,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'email_recipient',
					'value'   => $recipient,
					'compare' => '=='
				),
				array(
					'key'     => $recipient . '_email_types',
					'value'   => $email_type,
					'compare' => '=='
				),
				array(
					'key'     => $recipient . '_email_post_types',
					'value'   => $post_type,
					'compare' => 'LIKE'
				),
			)
		);
		$templates = get_posts( $args );

		return isset( $templates[0] ) && ! empty( $templates[0] ) ? $templates[0] : false;
	}
}

if ( ! function_exists( 'cubewp_get_email_template_by_user_id' ) ) {
	function cubewp_get_email_template_by_user_id( $user_id, $recipient, $email_type ) {
		$user_obj = get_userdata( $user_id );
		if ( is_wp_error( $user_obj ) || empty( $user_obj ) ) {
			return array();
		}
		$user_roles = $user_obj->roles;
		$args       = array(
			'post_type'      => CubeWp_Emails::$post_type,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'email_recipient',
					'value'   => $recipient,
					'compare' => '=='
				),
				array(
					'key'     => $recipient . '_email_types',
					'value'   => $email_type,
					'compare' => '=='
				),
			)
		);
		if ( ! empty( $user_roles ) ) {
			$meta_query = array(
				'relation' => 'OR'
			);
			foreach ( $user_roles as $role ) {
				$meta_query[] = array(
					'key'     => $recipient . '_email_user_roles',
					'value'   => $role,
					'compare' => 'LIKE'
				);
			}
			$args['meta_query'][] = $meta_query;
		}
		$templates = get_posts( $args );

		return isset( $templates[0] ) && ! empty( $templates[0] ) ? $templates[0] : false;
	}
}