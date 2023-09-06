<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Plugin Setup.
 *
 * @class Classified_Plugin_Setup
 */
class Classified_Plugin_Setup {
	public static $classified_custom_post_types = array();

	public function __construct() {
		$this->classified_default_post_types();
		add_filter( 'cubewp/builder/post_types', array( $this, 'classified_post_types_into_builder' ), 10, 2 );

		// Adding Settings Into CubeWP Settings For Post Type Settings
		add_filter( 'cubewp/settings/section/general-settings', array(
			$this,
			'classified_post_types_into_builder_settings',
		), 13, 1 );

		add_action( 'admin_init', array( $this, 'classified_create_custom_fields' ) );

		add_action( 'cubewp/after/settings/saved', array( $this, 'classified_lock_unlock_groups' ) );
	}

	public function classified_default_post_types() {
		$GLOBALS['classified_post_types'] = array();
		$unlock                           = classified_get_setting( 'classified_post_types' );
		$unlocked                         = array();
		if ( $unlock ) {
			$unlocked = classified_get_setting( 'classified_post_types_unlocked' );
			$unlocked = ! empty( $unlocked ) && is_array( $unlocked ) ? $unlocked : array();
		}
		$_classified_post_types                  = array();
		$_classified_post_types['classified-ad'] = array(
			'label'               => __( 'General Ads', 'cubewp-classified' ),
			'singular'            => __( 'General', 'cubewp-classified' ),
			'icon'                => 'dashicons-store',
			'slug'                => 'classified-ad',
			'description'         => __( 'ADS Post Type For Classified', 'cubewp-classified' ),
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'menu_position'       => 35,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => false,
			'rewrite_slug'        => '',
			'rewrite_withfront'   => true,
			'show_in_rest'        => true,
		);
		$_classified_post_types['real-estate']   = array(
			'label'               => __( 'Property Ads', 'cubewp-classified' ),
			'singular'            => __( 'Property', 'cubewp-classified' ),
			'icon'                => 'dashicons-building',
			'slug'                => 'real-estate',
			'description'         => __( 'Real Estate Ads Post Type For Classified', 'cubewp-classified' ),
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'menu_position'       => 36,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => false,
			'rewrite_slug'        => '',
			'rewrite_withfront'   => true,
			'show_in_rest'        => true,
		);
		$_classified_post_types['automotive']    = array(
			'label'               => __( 'Automotive Ads', 'cubewp-classified' ),
			'singular'            => __( 'Automotive', 'cubewp-classified' ),
			'icon'                => 'dashicons-car',
			'slug'                => 'automotive',
			'description'         => __( 'Real Estate Ads Post Type For Classified', 'cubewp-classified' ),
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'menu_position'       => 36,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => false,
			'rewrite_slug'        => '',
			'rewrite_withfront'   => true,
			'show_in_rest'        => true,
		);
		$_classified_post_types                  = apply_filters( 'classified_default_post_types',
			$_classified_post_types );
		if ( ! is_array( $_classified_post_types ) ) {
			wp_die( sprintf( esc_html__( '%s filter only accept array in return.', 'cubewp-classified' ),
				'<code>classified_default_post_types</code>' ) );
		}
		foreach ( $_classified_post_types as $post_type => $labels ) {
			if ( ! post_type_exists( $post_type ) && in_array( $post_type, $unlocked ) ) {
				$cubewp_post_types = get_option( 'cwp_custom_types' );
				if ( empty( $cubewp_post_types ) || ! is_array( $cubewp_post_types ) ) {
					$cubewp_post_types = array();
				}
				if ( ! isset( $cubewp_post_types[ $post_type ] ) ) {
					$cubewp_post_types[ $post_type ] = $labels;
					update_option( 'cwp_custom_types', $cubewp_post_types );
				}
			}

			if ( array_key_exists( $post_type, cwp_types() ) ) {
				self::$classified_custom_post_types[ $post_type ] = $labels['label'];
				$GLOBALS['classified_post_types'][]               = $post_type;
				add_filter( "cubewp/{$post_type}/single/template", "__return_true" );
				add_filter( "cubewp/{$post_type}/archive/template", "__return_true" );
			}
		}
		if ( classified_get_setting( 'extra_cpt_into_classified' ) ) {
			$extra_post_types = classified_get_setting( 'extra_cpt_for_classified' );
			if ( ! empty( $extra_post_types ) && is_array( $extra_post_types ) ) {
				foreach ( $extra_post_types as $extra_post_type ) {
					$cubewp_post_types = get_option( 'cwp_custom_types' );
					if ( empty( $cubewp_post_types ) || ! is_array( $cubewp_post_types ) ) {
						$cubewp_post_types = array();
					}
					$extra_post_type_obj = $cubewp_post_types[ $extra_post_type ] ?? array();
					if ( empty( $extra_post_type_obj ) ) {
						continue;
					}

					if ( array_key_exists( $extra_post_type, cwp_types() ) ) {
						self::$classified_custom_post_types[ $extra_post_type ] = $extra_post_type_obj['label'];
						$GLOBALS['classified_post_types'][]                     = $extra_post_type;
						add_filter( "cubewp/{$extra_post_type}/single/template", "__return_true" );
						add_filter( "cubewp/{$extra_post_type}/archive/template", "__return_true" );
					}
				}
			}
		}
		self::classified_register_default_taxonomies();
	}

	private static function classified_register_default_taxonomies() {
		global $classified_post_types;
		$GLOBALS['classified_taxonomies']          = array();
		$GLOBALS['classified_category_taxonomies'] = array();
		if ( empty( $classified_post_types ) ) {
			return false;
		}
		$location_taxonomy_labels = array(
			'name'              => _x( 'Locations', 'taxonomy general name', 'cubewp-classified' ),
			'singular_name'     => _x( 'Location', 'taxonomy singular name', 'cubewp-classified' ),
			'search_items'      => __( 'Search Locations', 'cubewp-classified' ),
			'all_items'         => __( 'All Locations', 'cubewp-classified' ),
			'parent_item'       => __( 'Parent Location', 'cubewp-classified' ),
			'parent_item_colon' => __( 'Parent Location:', 'cubewp-classified' ),
			'edit_item'         => __( 'Edit Location', 'cubewp-classified' ),
			'update_item'       => __( 'Update Location', 'cubewp-classified' ),
			'add_new_item'      => __( 'Add New Location', 'cubewp-classified' ),
			'new_item_name'     => __( 'New Location Name', 'cubewp-classified' ),
			'menu_name'         => __( 'Locations', 'cubewp-classified' ),
		);
		$location_taxonomy_args   = array(
			'hierarchical'      => true,
			'labels'            => $location_taxonomy_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'location' ),
		);
		register_taxonomy( 'locations', $classified_post_types, $location_taxonomy_args );
		$GLOBALS['classified_taxonomies'][] = 'locations';
		foreach ( $classified_post_types as $post_type ) {
			$category_taxonomy_labels                    = array(
				'name'              => _x( 'Categories', 'taxonomy general name', 'cubewp-classified' ),
				'singular_name'     => _x( 'Category', 'taxonomy singular name', 'cubewp-classified' ),
				'search_items'      => __( 'Search Categories', 'cubewp-classified' ),
				'all_items'         => __( 'All Categories', 'cubewp-classified' ),
				'parent_item'       => __( 'Parent Category', 'cubewp-classified' ),
				'parent_item_colon' => __( 'Parent Category:', 'cubewp-classified' ),
				'edit_item'         => __( 'Edit Category', 'cubewp-classified' ),
				'update_item'       => __( 'Update Category', 'cubewp-classified' ),
				'add_new_item'      => __( 'Add New Category', 'cubewp-classified' ),
				'new_item_name'     => __( 'New Category Name', 'cubewp-classified' ),
				'menu_name'         => __( 'Categories', 'cubewp-classified' ),
			);
			$category_taxonomy_args                      = array(
				'hierarchical'      => true,
				'labels'            => $category_taxonomy_labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => $post_type . '/category' ),
			);
			$category                                    = $post_type . '_category';
			$GLOBALS['classified_taxonomies'][]          = $category;
			$GLOBALS['classified_category_taxonomies'][] = $category;
			register_taxonomy( $category, $post_type, $category_taxonomy_args );
		}
	}

	public static function classified_create_custom_fields() {
		$unlock   = classified_get_setting( 'classified_custom_fields' );
		$unlocked = array();
		if ( $unlock ) {
			$unlocked = classified_get_setting( 'classified_custom_fields_unlocked' );
			$unlocked = ! empty( $unlocked ) && is_array( $unlocked ) ? $unlocked : array();
		}
		if ( in_array( 'classified_page_settings', $unlocked ) ) {
			self::classified_create_page_custom_fields();
		}
		self::classified_create_post_types_custom_fields();
		if ( in_array( 'classified_category_icon', $unlocked ) ) {
			self::classified_create_taxonomies_custom_fields();
		}
		if ( in_array( 'classified_location_image', $unlocked ) ) {
			self::classified_create_location_custom_fields();
		}
	}

	private static function classified_create_page_custom_fields() {
		$postData = get_posts( array(
			'name'        => 'classified_page_settings',
			'post_type'   => 'cwp_form_fields',
			'post_status' => 'publish',
			'fields'      => 'id',
			'numberposts' => 1,
		) );
		$post_id  = count( $postData ) > 0 ? $postData[0]->ID : '';
		// Create post object
		if ( empty( $post_id ) ) {
			if ( ! $post_id ) {
				$my_post = array(
					'post_title'   => wp_strip_all_tags( __( 'Classified Page Settings', 'cubewp-classified' ) ),
					'post_name'    => 'classified_page_settings',
					'post_content' => 'Custom fields for page settings.',
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'cwp_form_fields',
				);
				// Insert the post into the database
				$post_id = wp_insert_post( $my_post );
				update_post_meta( $post_id, '_cwp_group_visibility', 'secure' );
				update_post_meta( $post_id, '_cwp_group_types', 'page' );
				update_post_meta( $post_id, '_cwp_group_order', 1 );
			}
			$classified_fields        = array(
				'classified_header_top_bar'    => array(
					'label'                => __( 'Disable Header Top Bar On This Page', 'cubewp-classified' ),
					'name'                 => 'classified_header_top_bar',
					'type'                 => 'switch',
					'description'          => '',
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array() ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => false,
					'validation_msg'       => '',
					'id'                   => 'classified_header_top_bar',
					'class'                => '',
					'container_class'      => '',
					'conditional'          => false,
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				),
				'classified_header_bottom_bar' => array(
					'label'                => __( 'Disable Header Bottom Bar On This Page', 'cubewp-classified' ),
					'name'                 => 'classified_header_bottom_bar',
					'type'                 => 'switch',
					'description'          => '',
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array() ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => false,
					'validation_msg'       => '',
					'id'                   => 'classified_header_bottom_bar',
					'class'                => '',
					'container_class'      => '',
					'conditional'          => false,
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				),
				'classified_header_search'     => array(
					'label'                => __( 'Disable Header Search On This Page', 'cubewp-classified' ),
					'name'                 => 'classified_header_search',
					'type'                 => 'switch',
					'description'          => '',
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array() ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => false,
					'validation_msg'       => '',
					'id'                   => 'classified_header_search',
					'class'                => '',
					'container_class'      => '',
					'conditional'          => false,
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				),
				'classified_full_width_header' => array(
					'label'                => __( 'Full Width Header On This Page', 'cubewp-classified' ),
					'name'                 => 'classified_full_width_header',
					'type'                 => 'switch',
					'description'          => '',
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array() ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => false,
					'validation_msg'       => '',
					'id'                   => 'classified_full_width_header',
					'class'                => '',
					'container_class'      => '',
					'conditional'          => false,
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				),
				'classified_page_banner'       => array(
					'label'                => __( 'Enable Banner On This Page', 'cubewp-classified' ),
					'name'                 => 'classified_page_banner',
					'type'                 => 'switch',
					'description'          => '',
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array() ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => false,
					'validation_msg'       => '',
					'id'                   => 'classified_page_banner',
					'class'                => '',
					'container_class'      => '',
					'conditional'          => false,
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				),
				'classified_page_subtitle'     => array(
					'label'                => __( 'Subtitle For banner On This Page', 'cubewp-classified' ),
					'name'                 => 'classified_page_subtitle',
					'type'                 => 'text',
					'description'          => '',
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array() ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => false,
					'validation_msg'       => '',
					'id'                   => 'classified_page_subtitle',
					'class'                => '',
					'conditional'          => true,
					'conditional_field'    => 'classified_page_banner',
					'conditional_operator' => '==',
					'conditional_value'    => 'Yes',
					'group_id'             => $post_id,
				),
			);
			$classified_custom_fields = array();
			foreach ( $classified_fields as $key => $classified_field ) {
				$classified_custom_fields[] = $key;
				CubeWp_Custom_Fields_Processor::set_option( $key, $classified_field );
			}
			update_post_meta( $post_id, '_cwp_group_fields', implode( ',', $classified_custom_fields ) );
		}
	}

	private static function classified_create_post_types_custom_fields() {
		global $classified_post_types;
		$unlock   = classified_get_setting( 'classified_custom_fields' );
		$unlocked = array();
		if ( $unlock ) {
			$unlocked = classified_get_setting( 'classified_custom_fields_unlocked' );
			$unlocked = ! empty( $unlocked ) && is_array( $unlocked ) ? $unlocked : array();
		}
		if ( in_array( 'classified_general_fields', $unlocked ) ) {
			$postData = get_posts( array(
				'name'        => 'classified_general_fields',
				'post_type'   => 'cwp_form_fields',
				'post_status' => 'publish',
				'fields'      => 'id',
				'numberposts' => 1,
			) );
			$post_id  = count( $postData ) > 0 ? $postData[0]->ID : '';
			// Create post object
			if ( empty( $post_id ) ) {
				if ( ! $post_id ) {
					$my_post = array(
						'post_title'   => wp_strip_all_tags( __( 'Classified General Custom Fields',
							'cubewp-classified' ) ),
						'post_name'    => 'classified_general_fields',
						'post_content' => 'Classified mandatory general custom fields.',
						'post_status'  => 'publish',
						'post_author'  => 1,
						'post_type'    => 'cwp_form_fields',
					);
					// Insert the post into the database
					$post_id = wp_insert_post( $my_post );
					update_post_meta( $post_id, '_cwp_group_visibility', 'secure' );
					update_post_meta( $post_id, '_cwp_group_types', implode( ',', $classified_post_types ) );
					update_post_meta( $post_id, '_cwp_group_order', 1 );
				}
				$classified_fields        = self::classified_post_types_custom_fields( $post_id );
				$classified_custom_fields = array();
				foreach ( $classified_fields as $key => $classified_field ) {
					$classified_custom_fields[] = $key;
					CubeWp_Custom_Fields_Processor::set_option( $key, $classified_field );
				}
				update_post_meta( $post_id, '_cwp_group_fields', implode( ',', $classified_custom_fields ) );
			}
		}
		if ( in_array( 'classified_property_fields', $unlocked ) ) {
			// Property Custom Fields
			$postData = get_posts( array(
				'name'        => 'classified_property_fields',
				'post_type'   => 'cwp_form_fields',
				'post_status' => 'publish',
				'fields'      => 'id',
				'numberposts' => 1,
			) );
			$post_id  = count( $postData ) > 0 ? $postData[0]->ID : '';
			// Create post object
			if ( empty( $post_id ) ) {
				if ( ! $post_id ) {
					$my_post = array(
						'post_title'   => wp_strip_all_tags( __( 'Classified Property Custom Fields',
							'cubewp-classified' ) ),
						'post_name'    => 'classified_property_fields',
						'post_content' => 'Classified mandatory property custom fields.',
						'post_status'  => 'publish',
						'post_author'  => 1,
						'post_type'    => 'cwp_form_fields',
					);
					// Insert the post into the database
					$post_id = wp_insert_post( $my_post );
					update_post_meta( $post_id, '_cwp_group_visibility', 'secure' );
					update_post_meta( $post_id, '_cwp_group_types', 'real-estate' );
					update_post_meta( $post_id, '_cwp_group_order', 1 );
				}
				$classified_fields                                   = array();
				$classified_fields['classified_property_ad_purpose'] = array(
					'label'                => __( 'Purpose', 'cubewp-classified' ),
					'name'                 => 'classified_property_ad_purpose',
					'type'                 => 'radio',
					'description'          => '',
					'default_value'        => 'sale',
					'placeholder'          => '',
					'options'              => json_encode( array(
						'label' => array(
							__( 'Sale', 'cubewp-classified' ),
							__( 'Rent', 'cubewp-classified' ),
						),
						'value' => array( 'sale', 'rent' ),
					) ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => 1,
					'validation_msg'       => __( 'Purpose field is mandatory', 'cubewp-classified' ),
					'id'                   => 'classified_property_ad_purpose',
					'class'                => '',
					'container_class'      => '',
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				);
				$classified_custom_fields                            = array();
				foreach ( $classified_fields as $key => $classified_field ) {
					$classified_custom_fields[] = $key;
					CubeWp_Custom_Fields_Processor::set_option( $key, $classified_field );
				}
				update_post_meta( $post_id, '_cwp_group_fields', implode( ',', $classified_custom_fields ) );
			}
		}
		if ( in_array( 'classified_ad_fields', $unlocked ) ) {
			// Classified AD Custom Fields
			$postData = get_posts( array(
				'name'        => 'classified_ad_fields',
				'post_type'   => 'cwp_form_fields',
				'post_status' => 'publish',
				'fields'      => 'id',
				'numberposts' => 1,
			) );
			$post_id  = count( $postData ) > 0 ? $postData[0]->ID : '';
			// Create post object
			if ( empty( $post_id ) ) {
				if ( ! $post_id ) {
					$my_post = array(
						'post_title'   => wp_strip_all_tags( __( 'Classified Ad Custom Fields', 'cubewp-classified' ) ),
						'post_name'    => 'classified_ad_fields',
						'post_content' => 'Classified mandatory ad custom fields.',
						'post_status'  => 'publish',
						'post_author'  => 1,
						'post_type'    => 'cwp_form_fields',
					);
					// Insert the post into the database
					$post_id = wp_insert_post( $my_post );
					update_post_meta( $post_id, '_cwp_group_visibility', 'secure' );
					update_post_meta( $post_id, '_cwp_group_types', 'classified-ad' );
					update_post_meta( $post_id, '_cwp_group_order', 1 );
				}
				$classified_fields                            = array();
				$classified_fields['classified_buyable']      = array(
					'label'                => __( 'Can you ship this item?', 'cubewp-classified' ),
					'name'                 => 'classified_buyable',
					'type'                 => 'radio',
					'description'          => __( 'Enabling shipping increases the chances of selling the item faster.',
						'cubewp-classified' ),
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array(
						'label' => array(
							__( 'Yes', 'cubewp-classified' ),
							__( 'No', 'cubewp-classified' ),
						),
						'value' => array( 'yes', 'no' ),
					) ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => 1,
					'validation_msg'       => __( 'This field is mandatory', 'cubewp-classified' ),
					'id'                   => 'classified_buyable',
					'class'                => '',
					'container_class'      => '',
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				);
				$classified_fields['classified_ad_condition'] = array(
					'label'                => __( 'Condition', 'cubewp-classified' ),
					'name'                 => 'classified_ad_condition',
					'type'                 => 'radio',
					'description'          => '',
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array(
						'label' => array(
							__( 'Used', 'cubewp-classified' ),
							__( 'New', 'cubewp-classified' ),
						),
						'value' => array( 'used', 'new' ),
					) ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => 1,
					'validation_msg'       => __( 'Condition field is mandatory', 'cubewp-classified' ),
					'id'                   => 'classified_ad_condition',
					'class'                => '',
					'container_class'      => '',
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				);
				$classified_custom_fields                     = array();
				foreach ( $classified_fields as $key => $classified_field ) {
					$classified_custom_fields[] = $key;
					CubeWp_Custom_Fields_Processor::set_option( $key, $classified_field );
				}
				update_post_meta( $post_id, '_cwp_group_fields', implode( ',', $classified_custom_fields ) );
			}
		}
		if ( in_array( 'classified_automotive_fields', $unlocked ) ) {
			// Classified Automotive Custom Fields
			$postData = get_posts( array(
				'name'        => 'classified_automotive_fields',
				'post_type'   => 'cwp_form_fields',
				'post_status' => 'publish',
				'fields'      => 'id',
				'numberposts' => 1,
			) );
			$post_id  = count( $postData ) > 0 ? $postData[0]->ID : '';
			// Create post object
			if ( empty( $post_id ) ) {
				if ( ! $post_id ) {
					$my_post = array(
						'post_title'   => wp_strip_all_tags( __( 'Classified Automotive Custom Fields',
							'cubewp-classified' ) ),
						'post_name'    => 'classified_automotive_fields',
						'post_content' => 'Classified mandatory automotive custom fields.',
						'post_status'  => 'publish',
						'post_author'  => 1,
						'post_type'    => 'cwp_form_fields',
					);
					// Insert the post into the database
					$post_id = wp_insert_post( $my_post );
					update_post_meta( $post_id, '_cwp_group_visibility', 'secure' );
					update_post_meta( $post_id, '_cwp_group_types', 'automotive' );
					update_post_meta( $post_id, '_cwp_group_order', 1 );
				}
				$classified_fields                                    = array();
				$classified_fields['classified_automotive_condition'] = array(
					'label'                => __( 'Condition', 'cubewp-classified' ),
					'name'                 => 'classified_automotive_condition',
					'type'                 => 'radio',
					'description'          => '',
					'default_value'        => '',
					'placeholder'          => '',
					'options'              => json_encode( array(
						'label' => array(
							__( 'Used', 'cubewp-classified' ),
							__( 'New', 'cubewp-classified' ),
						),
						'value' => array( 'used', 'new' ),
					) ),
					'filter_post_types'    => '',
					'filter_taxonomy'      => '',
					'filter_user_roles'    => '',
					'appearance'           => '',
					'required'             => 1,
					'validation_msg'       => __( 'Condition field is mandatory', 'cubewp-classified' ),
					'id'                   => 'classified_automotive_condition',
					'class'                => '',
					'container_class'      => '',
					'conditional_operator' => '!empty',
					'conditional_value'    => '',
					'group_id'             => $post_id,
				);
				$classified_custom_fields                             = array();
				foreach ( $classified_fields as $key => $classified_field ) {
					$classified_custom_fields[] = $key;
					CubeWp_Custom_Fields_Processor::set_option( $key, $classified_field );
				}
				update_post_meta( $post_id, '_cwp_group_fields', implode( ',', $classified_custom_fields ) );
			}
		}
	}

	private static function classified_post_types_custom_fields( $post_id ) {
		$classified_fields                       = array();
		$classified_fields['classified_price']   = array(
			'label'                => __( 'Price', 'cubewp-classified' ),
			'name'                 => 'classified_price',
			'type'                 => 'number',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( array() ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => 1,
			'validation_msg'       => __( 'Price field is mandatory', 'cubewp-classified' ),
			'id'                   => 'classified_price',
			'class'                => '',
			'container_class'      => '',
			'conditional_operator' => '!empty',
			'conditional_value'    => '',
			'group_id'             => $post_id,
		);
		$classified_fields['classified_gallery'] = array(
			'label'                => __( 'Gallery', 'cubewp-classified' ),
			'name'                 => 'classified_gallery',
			'type'                 => 'gallery',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( array() ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => '',
			'validation_msg'       => '',
			'id'                   => 'classified_gallery',
			'class'                => '',
			'container_class'      => '',
			'conditional_operator' => '!empty',
			'conditional_value'    => '',
			'group_id'             => $post_id,
		);
		$classified_fields['classified_address'] = array(
			'label'                => __( 'Address', 'cubewp-classified' ),
			'name'                 => 'classified_address',
			'type'                 => 'google_address',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( array() ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => 1,
			'validation_msg'       => __( 'Address field is mandatory', 'cubewp-classified' ),
			'id'                   => 'classified_address',
			'class'                => '',
			'container_class'      => '',
			'conditional_operator' => '!empty',
			'conditional_value'    => '',
			'group_id'             => $post_id,
		);

		return apply_filters( 'classified_default_post_types_custom_fields', $classified_fields, $post_id );
	}

	private static function classified_create_taxonomies_custom_fields() {
		global $classified_category_taxonomies;
		$classified_category_custom_fields[] = array(
			'name'          => esc_html__( 'Category Icon', 'cubewp-classified' ),
			'old_slug'      => 'classified_category_icon',
			'slug'          => 'classified_category_icon',
			'type'          => 'text',
			'description'   => esc_html__( 'You can put fontawesome icon classes.', 'cubewp-classified' ),
			'placeholder'   => esc_html__( 'Category Icon', 'cubewp-classified' ),
			'default_value' => '',
			'taxonomies'    => $classified_category_taxonomies,
		);
		$classified_category_custom_fields   = apply_filters( 'classified_taxonomy_custom_fields', $classified_category_custom_fields );
		$cubewp_taxonomies_custom_fields     = CWP()->get_custom_fields( 'taxonomy' ) ?? array();
		foreach ( $classified_category_custom_fields as $field_options ) {
			foreach ( $field_options['taxonomies'] as $taxonomy ) {
				$custom_field                                                          = $field_options;
				$custom_field['taxonomies']                                            = implode( ',',
					$custom_field['taxonomies'] );
				$cubewp_taxonomies_custom_fields[ $taxonomy ][ $custom_field['slug'] ] = $custom_field;
			}
		}
		CWP()->update_custom_fields( 'taxonomy', $cubewp_taxonomies_custom_fields );
	}

	private static function classified_create_location_custom_fields() {
		$classified_locations_custom_fields[] = array(
			'name'          => esc_html__( 'Location Image', 'cubewp-classified' ),
			'old_slug'      => 'classified_location_image',
			'slug'          => 'classified_location_image',
			'type'          => 'image',
			'description'   => esc_html__( 'Select the image for this location', 'cubewp-classified' ),
			'placeholder'   => esc_html__( 'Location Image', 'cubewp-classified' ),
			'default_value' => '',
			'taxonomies'    => array( 'locations' ),
		);
		$classified_locations_custom_fields   = apply_filters( 'classified_locations_custom_fields',
			$classified_locations_custom_fields );
		$cwp_tax_custom_fields                = CWP()->get_custom_fields( 'taxonomy' ) ?? array();
		foreach ( $classified_locations_custom_fields as $field_options ) {
			foreach ( $field_options['taxonomies'] as $taxonomy ) {
				$custom_field                                                = $field_options;
				$custom_field['taxonomies']                                  = implode( ',',
					$custom_field['taxonomies'] );
				$cwp_tax_custom_fields[ $taxonomy ][ $custom_field['slug'] ] = $custom_field;
			}
		}
		CWP()->update_custom_fields( 'taxonomy', $cwp_tax_custom_fields );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_lock_unlock_groups() {
		$unlock   = classified_get_setting( 'classified_custom_fields' );
		$unlocked = array();
		if ( $unlock ) {
			$unlocked = classified_get_setting( 'classified_custom_fields_unlocked' );
			$unlocked = ! empty( $unlocked ) && is_array( $unlocked ) ? $unlocked : array();
		}
		$all_groups = classified_get_cubewp_groups( 'ID', 'post_name' );
		if ( ! empty( $all_groups ) ) {
			foreach ( $all_groups as $group_id => $group_name ) {
				if ( in_array( $group_name, $unlocked ) ) {
					update_post_meta( $group_id, '_cwp_group_visibility', 'secure' );
				} else {
					update_post_meta( $group_id, '_cwp_group_visibility', 'not-secured' );
				}
			}
		}
	}

	public function classified_post_types_into_builder_settings( $section_fields ) {
		$fields                              = array();
		$cubewp_post_types                   = CWP_types();
		$exclude_post_types['automotive']    = 'automotive';
		$exclude_post_types['classified-ad'] = 'classified-ad';
		$exclude_post_types['real-estate']   = 'real-estate';
		$exclude_post_types['cubewp-report'] = 'cubewp-report';
		$exclude_post_types['cwp_reviews']   = 'cwp_reviews';
		$exclude_post_types['cwp_booster']   = 'cwp_booster';
		$exclude_post_types['attachment']    = 'attachment';
		$exclude_post_types['post']          = 'post';
		$exclude_post_types['page']          = 'page';
		$exclude_post_types                  = apply_filters( 'classified_settings_excluded_extra_post_types',
			$exclude_post_types );
		$cubewp_post_types                   = array_diff_key( $cubewp_post_types, $exclude_post_types );
		$extra_post_types                    = array();
		if ( ! empty( $cubewp_post_types ) && is_array( $cubewp_post_types ) ) {
			foreach ( $cubewp_post_types as $post_type => $cubewp_post_type ) {
				$extra_post_types[ $post_type ] = $cubewp_post_type['label'];
			}
		}
		$fields[] = array(
			'id'      => 'extra_cpt_into_classified',
			'type'    => 'switch',
			'title'   => __( 'Extra Custom Post Types Into Classified', 'classified-pro' ),
			'desc'    => __( 'Enable if you want to add extra custom post types into classified.', 'classified-pro' ),
			'default' => '0',
		);
		$fields[] = array(
			'id'       => 'extra_cpt_for_classified',
			'type'     => 'select',
			'title'    => __( 'Select Post Types', 'cubewp-framework' ),
			'options'  => $extra_post_types,
			'multi'    => true,
			'desc'     => __( 'Select custom post types you want to add into classified.', 'cubewp-framework' ),
			'required' => array(
				array( 'extra_cpt_into_classified', 'equals', '1' ),
			),
		);

		$fields[] = array(
			'id'      => 'classified_post_types',
			'type'    => 'switch',
			'title'   => __( 'Recreate Classified Post Types', 'cubewp-classified' ),
			'desc'    => __( 'Enable if you want to recreate a classified post type.', 'cubewp-classified' ),
			'default' => '1',
		);
		$fields[] = array(
			'id'       => 'classified_post_types_unlocked',
			'type'     => 'select',
			'title'    => __( 'Select Post Types', 'cubewp-classified' ),
			'multi'    => true,
			'desc'     => __( 'Select post types which you want to recreate.', 'cubewp-classified' ),
			'options'  => array(
				'classified-ad' => __( 'General Ads', 'cubewp-classified' ),
				'automotive'    => __( 'Automotive Ads', 'cubewp-classified' ),
				'real-estate'   => __( 'Property Ads', 'cubewp-classified' ),
			),
			'default'  => array(
				'classified-ad',
				'real-estate',
				'automotive',
			),
			'required' => array(
				array( 'classified_post_types', 'equals', '1' ),
			),
		);
		$fields[] = array(
			'id'      => 'classified_custom_fields',
			'type'    => 'switch',
			'title'   => __( 'Recreate Classified Custom Fields', 'cubewp-classified' ),
			'desc'    => __( 'Enable if you want to recreate a classified post type.', 'cubewp-classified' ),
			'default' => '0',
		);
		$fields[] = array(
			'id'       => 'classified_custom_fields_unlocked',
			'type'     => 'select',
			'title'    => __( 'Select Custom Fields', 'cubewp-classified' ),
			'multi'    => true,
			'desc'     => __( 'Select classified custom field which you want to recreate.', 'cubewp-classified' ),
			'options'  => array(
				'classified_automotive_fields' => esc_html__( 'Classified Automotive Custom Fields',
					'cubewp_classified' ),
				'classified_page_settings'     => esc_html__( 'Classified Page Settings', 'cubewp_classified' ),
				'classified_general_fields'    => esc_html__( 'Classified General Custom Fields', 'cubewp_classified' ),
				'classified_property_fields'   => esc_html__( 'Classified Property Custom Fields',
					'cubewp_classified' ),
				'classified_ad_fields'         => esc_html__( 'Classified Ad Custom Fields', 'cubewp_classified' ),
				'classified_category_icon'     => esc_html__( 'Classified Category Icon', 'cubewp_classified' ),
				'classified_location_image'    => esc_html__( 'Classified Location Image', 'cubewp_classified' ),
			),
			'default'  => array(),
			'required' => array(
				array( 'classified_custom_fields', 'equals', '1' ),
			),
		);

		return array_merge( $section_fields, $fields );
	}

	public function classified_post_types_into_builder( $defaults, $request_from ) {
		if ( empty( $defaults ) || ! is_array( $defaults ) ) {
			$defaults = array();
		}
		if ( $request_from == 'dashboard' ) {
			$defaults['all_classified_post_types'] = esc_html__( 'All Classified Post Types', 'cubewp-classified' );
		}

		return array_merge( $defaults, self::$classified_custom_post_types );
	}
}