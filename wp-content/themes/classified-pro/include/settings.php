<?php
defined( 'ABSPATH' ) || exit;

// Adding New Section
if ( ! function_exists( 'classified_settings_new_sections' ) ) {
	function classified_settings_new_sections( $sections ) {
		global $classified_post_types;
		// Adding Typography Section
		$single_settings['classified_typography'] = array(
			'title'  => __( 'Typography', 'classified-pro' ),
			'id'     => 'classified_typography',
			'icon'   => 'dashicons dashicons-edit',
			'fields' => array(
				array(
					'id'      => 'typography-h1',
					'title'   => __( 'Heading 1 (h1)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'color'       => '#2C3E50',
						'font-size'   => '24px',
						'font-family' => 'Source Sans Pro',
						'font-weight' => '700',
						'line-height' => '1.5em',
						'subsets'     => 'latin'
					)
				),
				array(
					'id'      => 'typography-h2',
					'title'   => __( 'Heading 2 (h2)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'color'       => '#2C3E50',
						'font-size'   => '22px',
						'font-family' => 'Source Sans Pro',
						'font-weight' => '700',
						'line-height' => '1.5em',
						'subsets'     => 'latin'
					)
				),
				array(
					'id'      => 'typography-h3',
					'title'   => __( 'Heading 3 (h3)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'color'       => '#2C3E50',
						'font-size'   => '20px',
						'font-family' => 'Source Sans Pro',
						'font-weight' => '700',
						'line-height' => '1.5em',
						'subsets'     => 'latin'
					)
				),
				array(
					'id'      => 'typography-h4',
					'title'   => __( 'Heading 4 (h4)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'color'       => '#2C3E50',
						'font-size'   => '18px',
						'font-family' => 'Source Sans Pro',
						'font-weight' => '700',
						'line-height' => '1.5em',
						'subsets'     => 'latin'
					)
				),
				array(
					'id'      => 'typography-h5',
					'title'   => __( 'Heading 5 (h5)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'color'       => '#2C3E50',
						'font-size'   => '16px',
						'font-family' => 'Source Sans Pro',
						'font-weight' => '700',
						'line-height' => '1.5em',
						'subsets'     => 'latin'
					)
				),
				array(
					'id'      => 'typography-h6',
					'title'   => __( 'Heading 6 (h6)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'color'       => '#2C3E50',
						'font-size'   => '14px',
						'font-family' => 'Source Sans Pro',
						'font-weight' => '700',
						'line-height' => '1.5em',
						'subsets'     => 'latin'
					)
				),
				array(
					'id'      => 'typography-p-sm',
					'title'   => __( 'Small Paragraph (.p-sm)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'font-family' => 'Source Sans Pro',
						'font-weight' => '400',
						'color'       => '#838EAA',
						'subsets'     => 'latin',
						'line-height' => '1.5em',
						'font-size'   => '11px'
					)
				),
				array(
					'id'      => 'typography-p-md',
					'title'   => __( 'Medium Paragraph (.p-md)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'font-family' => 'Source Sans Pro',
						'font-weight' => '400',
						'color'       => '#838EAA',
						'subsets'     => 'latin',
						'line-height' => '1.5em',
						'font-size'   => '12px'
					)
				),
				array(
					'id'      => 'typography-p',
					'title'   => __( 'Paragraph (p)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'font-family' => 'Source Sans Pro',
						'font-weight' => '400',
						'color'       => '#838EAA',
						'subsets'     => 'latin',
						'line-height' => '1.5em',
						'font-size'   => '13px'
					)
				),
				array(
					'id'      => 'typography-p-lg',
					'title'   => __( 'Large Paragraph (.p-lg)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'font-family' => 'Source Sans Pro',
						'font-weight' => '400',
						'color'       => '#838EAA',
						'subsets'     => 'latin',
						'line-height' => '1.5em',
						'font-size'   => '14px'
					)
				),
				array(
					'id'      => 'typography-label',
					'title'   => __( 'Label', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'font-family' => 'Source Sans Pro',
						'font-weight' => '400',
						'color'       => '#2C3E50',
						'subsets'     => 'latin',
						'line-height' => '1.5em',
						'font-size'   => '13px'
					)
				),
				array(
					'id'      => 'typography-body',
					'title'   => __( 'Overall Font', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'font-family' => 'Source Sans Pro',
						'font-weight' => '400',
						'color'       => '#838EAA',
						'subsets'     => 'latin',
						'line-height' => '1.5em',
						'font-size'   => '13px'
					)
				),
				array(
					'id'      => 'typography-a',
					'title'   => __( 'Anchor Tag (a)', 'classified-pro' ),
					'type'    => 'typography',
					'default' => array(
						'font-family' => 'Source Sans Pro',
						'font-weight' => '400',
						'color'       => '#838EAA',
						'subsets'     => 'latin',
						'line-height' => '1.5em',
						'font-size'   => '13px'
					)
				),
				array(
					'id'      => 'typography-a:hover',
					'title'   => __( 'Anchor Tag (a) Hover Color', 'classified-pro' ),
					'desc'    => __( 'Please select the text color on hover state.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#ff5656',
				)
			),
		);
		// Adding Header Section
		$single_settings['classified_header'] = array(
			'title'  => __( 'Header', 'classified-pro' ),
			'id'     => 'classified_header',
			'icon'   => 'dashicons dashicons-archive',
			'fields' => array(
				array(
					'id'      => 'header_layout',
					'title'   => __( 'Header Layout', 'classified-pro' ),
					'desc'    => __( 'Select header layout full-width or boxed.', 'classified-pro' ),
					'type'    => 'select',
					'options' => array(
						'boxed'      => esc_html__( "Boxed View", "classified-pro" ),
						'full-width' => esc_html__( "Full Width", "classified-pro" )
					),
					'default' => 'boxed',
				),
				array(
					'id'      => 'home_header_search',
					'title'   => __( 'Header Search On Home', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to header search on home page.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'       => 'home_header_search_shortcode',
					'title'    => __( 'Home Header Search Shortcode', 'classified-pro' ),
					'desc'     => __( 'Please put CubeWP search shortcode here.', 'classified-pro' ),
					'type'     => 'text',
					'default'  => '',
					'required' => array(
						array( 'home_header_search', 'equals', '1' )
					)
				),
				array(
					'id'      => 'inner_header_search',
					'title'   => __( 'Header Search On Pages', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to header search on inner pages.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'       => 'inner_header_search_shortcode',
					'title'    => __( 'Pages Header Search Shortcode', 'classified-pro' ),
					'desc'     => __( 'Please put CubeWP search shortcode here.', 'classified-pro' ),
					'type'     => 'text',
					'default'  => '',
					'required' => array(
						array( 'inner_header_search', 'equals', '1' )
					)
				),
				array(
					'id'    => 'home_page_logo',
					'title' => __( 'Home Logo', 'classified-pro' ),
					'desc'  => __( 'Please select logo image for home page.', 'classified-pro' ),
					'type'  => 'media',
				),
				array(
					'id'    => 'inner_pages_logo',
					'title' => __( 'Pages Logo', 'classified-pro' ),
					'desc'  => __( 'Please select logo image for pages(Other then home page).', 'classified-pro' ),
					'type'  => 'media',
				),
				array(
					'id'      => 'home_header_top_bar',
					'title'   => __( 'Header Top Bar On Home', 'classified-pro' ),
					'desc'    => __( 'Enable if you want a header top bar on home page.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'      => 'inner_header_top_bar',
					'title'   => __( 'Header Top Bar On Pages', 'classified-pro' ),
					'desc'    => __( 'Enable if you want a header top bar on pages.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'home_header_bottom_bar',
					'title'   => __( 'Header Bottom Bar On Home', 'classified-pro' ),
					'desc'    => __( 'Enable if you want a header bottom bar on home page.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'inner_header_bottom_bar',
					'title'   => __( 'Header Bottom Bar On Pages', 'classified-pro' ),
					'desc'    => __( 'Enable if you want a header Bottom bar on pages.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'header_quick_link_inbox',
					'title'   => __( 'Show Inbox Quick Link', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show inbox quick link in header.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'      => 'header_quick_link_saved',
					'title'   => __( 'Show Favorites Quick Link', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show favorites quick link in header.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'      => 'header_bg_color_home',
					'title'   => __( 'Header Background Color For Home', 'classified-pro' ),
					'desc'    => __( 'Select the color for header area on home page.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#F8F8F8',
				),
				array(
					'id'      => 'header_shadow_color_home',
					'title'   => __( 'Header Shadow Color For Home', 'classified-pro' ),
					'desc'    => __( 'Select the shadow color for header area on home page.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#00000026',
				),
				array(
					'id'       => 'header_top_bg_color_home',
					'title'    => __( 'Header Top Background Color For Home', 'classified-pro' ),
					'desc'     => __( 'Select the color for header top area on home page.', 'classified-pro' ),
					'type'     => 'color',
					'default'  => '#ffffff',
					'required' => array(
						array( 'home_header_top_bar', 'equals', '1' )
					)
				),
				array(
					'id'       => 'header_bottom_bg_color_home',
					'title'    => __( 'Header Bottom Background Color For Home', 'classified-pro' ),
					'desc'     => __( 'Select the color for header bottom area on home page.', 'classified-pro' ),
					'type'     => 'color',
					'default'  => '#ffffff',
					'required' => array(
						array( 'home_header_bottom_bar', 'equals', '1' )
					)
				),
				array(
					'id'      => 'header_bg_color_inner',
					'title'   => __( 'Header Background Color For Pages', 'classified-pro' ),
					'desc'    => __( 'Select the color for header area on inner pages.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#F8F8F8',
				),
				array(
					'id'      => 'header_shadow_color_inner',
					'title'   => __( 'Header Shadow Color For Pages', 'classified-pro' ),
					'desc'    => __( 'Select the shadow color for header area on inner pages.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#00000026',
				),
				array(
					'id'       => 'header_top_bg_color_inner',
					'title'    => __( 'Header Top Background Color For Pages', 'classified-pro' ),
					'desc'     => __( 'Select the color for header top area on inner pages.', 'classified-pro' ),
					'type'     => 'color',
					'default'  => '#ffffff',
					'required' => array(
						array( 'inner_header_top_bar', 'equals', '1' )
					)
				),
				array(
					'id'       => 'header_bottom_bg_color_inner',
					'title'    => __( 'Header Bottom Background Color For Pages', 'classified-pro' ),
					'desc'     => __( 'Select the color for header bottom area on inner pages.', 'classified-pro' ),
					'type'     => 'color',
					'default'  => '#ffffff',
					'required' => array(
						array( 'inner_header_bottom_bar', 'equals', '1' )
					)
				),
				array(
					'id'      => 'header_text_color_home',
					'title'   => __( 'Header Navigation Color For Home', 'classified-pro' ),
					'desc'    => __( 'Select the color for navigation text on home header.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#2C3E50',
				),
				array(
					'id'      => 'header_text_color_inner',
					'title'   => __( 'Header Navigation Color For Pages', 'classified-pro' ),
					'desc'    => __( 'Select the color for navigation text on inner pages header.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#2C3E50',
				),
				array(
					'id'      => 'header_text_color_home:hover',
					'title'   => __( 'Header Navigation Hover State Color For Home', 'classified-pro' ),
					'desc'    => __( 'Select the hover state color for navigation text on home header.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#0075ff',
				),
				array(
					'id'      => 'header_text_color_inner:hover',
					'title'   => __( 'Header Navigation Hover State Color For Pages', 'classified-pro' ),
					'desc'    => __( 'Select the hover state color for navigation text on inner pages header.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#0075ff',
				),
				array(
					'id'      => 'sticky_header_home',
					'title'   => __( 'Sticky Header On Home', 'classified-pro' ),
					'desc'    => __( 'Enable if you want sticky header on home page.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'sticky_header_inner',
					'title'   => __( 'Sticky Header On Inner Pages', 'classified-pro' ),
					'desc'    => __( 'Enable if you want sticky header on all inner pages.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'header_cats_home',
					'title'   => __( 'All Categories On Home Page Header', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show all categories navigation item on home page header.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'       => 'home_header_cats_banner',
					'title'    => __( 'Home Page Header Google AdSense / Static Image', 'classified-pro' ),
					'desc'     => __( 'You can select Either Google ads or Static Banner image with link.', 'classified-pro' ),
					'type'     => 'select',
					'options'  => array(
						'google_adsense' => esc_html__( "Google AdSense", "classified-pro" ),
						'static_banner'  => esc_html__( "Static Banner", "classified-pro" )
					),
					'default'  => 'static_banner',
					'required' => array(
						array( 'header_cats_home', 'equals', '1' )
					)
				),
				array(
					'id'       => 'home_cats_banner_image',
					'title'    => __( 'Home All Categories Dropdown Ad Image', 'classified-pro' ),
					'desc'     => __( 'Please select banner ads image for categories dropdown', 'classified-pro' ),
					'type'     => 'media',
					'required' => array(
						array( 'home_header_cats_banner', 'equals', 'static_banner' )
					)
				),
				array(
					'id'       => 'home_cats_banner_img_link',
					'type'     => 'text',
					'title'    => __( 'Home Header Link to Ads Banner Image', 'classified-pro' ),
					'default'  => home_url(),
					'desc'     => __( 'Home Category Dropdown Banner Image Link', 'classified-pro' ),
					'required' => array(
						array( 'home_header_cats_banner', 'equals', 'static_banner' )
					)
				),
				array(
					'id'       => 'home_cats_banner_ads',
					'type'     => 'text',
					'title'    => __( 'Google AdSense Code', 'classified-pro' ),
					'default'  => '',
					'desc'     => __( 'Place your google AdSense code here for homepage category dropdown', 'classified-pro' ),
					'required' => array(
						array( 'home_header_cats_banner', 'equals', 'google_adsense' )
					)
				),
				array(
					'id'      => 'header_cats_inner',
					'title'   => __( 'All Categories On Inner Pages Header', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show all categories navigation item on all inner pages header.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'       => 'inner_header_cats_banner',
					'title'    => __( 'Inner Pages Header Google AdSense / Static Image', 'classified-pro' ),
					'desc'     => __( 'You can select Either Google ads or Static Banner image with link.', 'classified-pro' ),
					'type'     => 'select',
					'options'  => array(
						'google_adsense' => esc_html__( "Google AdSense", "classified-pro" ),
						'static_banner'  => esc_html__( "Static Banner", "classified-pro" )
					),
					'default'  => 'static_banner',
					'required' => array(
						array( 'header_cats_inner', 'equals', '1' )
					)
				),
				array(
					'id'       => 'inner_cats_banner_image',
					'title'    => __( 'Inner Page All Categories Dropdown Ad Image', 'classified-pro' ),
					'desc'     => __( 'Please select banner ads image for categories dropdown', 'classified-pro' ),
					'type'     => 'media',
					'required' => array(
						array( 'inner_header_cats_banner', 'equals', 'static_banner' )
					)
				),
				array(
					'id'       => 'inner_cats_banner_img_link',
					'type'     => 'text',
					'title'    => __( 'Inner Page Header Link to Ads Banner Image', 'classified-pro' ),
					'default'  => home_url(),
					'desc'     => __( 'Home Category Dropdown Banner Image Link', 'classified-pro' ),
					'required' => array(
						array( 'inner_header_cats_banner', 'equals', 'static_banner' )
					)
				),
				array(
					'id'       => 'inner_cats_banner_ads',
					'type'     => 'text',
					'title'    => __( 'Inner Page Header Google AdSense Code', 'classified-pro' ),
					'default'  => '',
					'desc'     => __( 'Place your google AdSense code here for homepage category dropdown', 'classified-pro' ),
					'required' => array(
						array( 'inner_header_cats_banner', 'equals', 'google_adsense' )
					)
				),
				array(
					'id'      => 'header_cats_number',
					'title'   => __( 'Number of Categories', 'classified-pro' ),
					'desc'    => __( 'Total Number of Categories to display in header all categories dropdown', 'classified-pro' ),
					'type'    => 'text',
					'default' => '6',
				),
			),
		);
		if ( ! empty( $classified_post_types ) ) {
			$single_settings['classified_header']['fields'][] = array(
				'id'      => 'header_top_bar_landing_pages',
				'title'   => __( 'Post Type Landing Page Links', 'classified-pro' ),
				'desc'    => __( 'Enable if you want a top post type landing pages link on header top bar.', 'classified-pro' ),
				'type'    => 'switch',
				'default' => '0',
			);
			foreach ( $classified_post_types as $post_type ) {
				$single_settings['classified_header']['fields'][] = array(
					'id'       => 'header_top_bar_landing_pages_' . $post_type,
					'title'    => sprintf( __( 'Landing Page For %s', 'classified-pro' ), $post_type ),
					'subtitle' => __( 'This must be an URL.', 'classified-pro' ),
					'validate' => 'url',
					'desc'     => __( 'This option empty if you dont want to show this post type in header top bar.', 'classified-pro' ),
					'type'     => 'pages',
					'default'  => '',
					'required' => array(
						array( 'header_top_bar_landing_pages', 'equals', '1' )
					)
				);
			}
		}
		// Adding Single Page Settings
		$single_settings['classified_single'] = array(
			'title'  => __( 'Ads Detail Page', 'classified-pro' ),
			'id'     => 'classified_single',
			'icon'   => 'dashicons dashicons-admin-post',
			'fields' => array(
				array(
					'id'      => 'classified_sticky_sidebar',
					'title'   => __( 'Sticky Sidebar.', 'classified-pro' ),
					'desc'    => __( 'Enable if you want sidebar to stick in the parent.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'classified_author_items',
					'title'   => __( 'Show Author Items On Item Details Page.', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show all Author items.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'       => 'classified_author_items_to_show',
					'title'    => __( 'How Many Author Items To Show?', 'classified-pro' ),
					'desc'     => __( 'Enter the amount of author items you want to show on the detail page. Note layout will convert into carousal if items to display are more then 4.', 'classified-pro' ),
					'type'     => 'text',
					'default'  => '4',
					'required' => array(
						array( 'classified_author_items', 'equals', '1' )
					)
				),
				array(
					'id'      => 'classified_related_items',
					'title'   => __( 'Show Related Items On Item Details Page.', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show related items based on item Locations and Categories.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'       => 'classified_related_items_to_show',
					'title'    => __( 'How Many Related Items To Show?', 'classified-pro' ),
					'desc'     => __( 'Enter the amount of related items you want to show on the detail page. Note layout will convert into carousal if items to display are more then 4.', 'classified-pro' ),
					'type'     => 'text',
					'default'  => '4',
					'required' => array(
						array( 'classified_related_items', 'equals', '1' )
					)
				),
			),
		);
		// Adding Loop Settings
		$single_settings['classified_loop'] = array(
			'title'  => __( 'Loop Layout', 'classified-pro' ),
			'id'     => 'classified_loop',
			'icon'   => 'dashicons dashicons-grid-view',
			'fields' => array(),
		);
		if ( ! empty( $classified_post_types ) ) {
			foreach ( $classified_post_types as $post_type ) {
				$single_settings['classified_loop']['fields'][] = array(
					'id'      => $post_type . '_loop_stat',
					'title'   => sprintf( __( 'Custom Field On %s Loop.', 'classified-pro' ), $post_type ),
					'desc'    => sprintf( __( 'Enable if you want to show a custom field value on %s loop layout.', 'classified-pro' ), $post_type ),
					'type'    => 'switch',
					'default' => '0',
				);
				$single_settings['classified_loop']['fields'][] = array(
					'id'       => $post_type . '_loop_stat_field',
					'title'    => sprintf( __( 'Custom Field Name For %s Loop.', 'classified-pro' ), $post_type ),
					'desc'     => sprintf( __( 'Select the custom field you want to show in the %s loop layout.', 'classified-pro' ), $post_type ),
					'type'     => 'select',
					'options'  => classified_get_custom_fields_by_post_type( $post_type ),
					'required' => array(
						array( $post_type . '_loop_stat', 'equals', '1' )
					)
				);
				$single_settings['classified_loop']['fields'][] = array(
					'id'       => $post_type . '_loop_stat_icon',
					'title'    => sprintf( __( 'Icon For Custom Field In %s Loop.', 'classified-pro' ), $post_type ),
					'desc'     => sprintf( __( 'Enter the Font Awesome icon classes for custom field in the %s loop layout.', 'classified-pro' ), $post_type ),
					'type'     => 'text',
					'required' => array(
						array( $post_type . '_loop_stat', 'equals', '1' )
					)
				);
			}
		}
		// Adding Blog Section
		$single_settings['classified_blog'] = array(
			'title'  => __( 'Blog', 'classified-pro' ),
			'id'     => 'classified_blog',
			'icon'   => 'dashicons dashicons-archive',
			'fields' => array(
				array(
					'id'      => 'blog_banner',
					'title'   => __( 'Banner on Blog Page', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show Banner on blog page.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'      => 'blog_default_style',
					'title'   => __( 'Blog Grid Style', 'classified-pro' ),
					'desc'    => __( 'Please select blog style for index or blog page.', 'classified-pro' ),
					'type'    => 'select',
					'options' => array(
						'style_1' => esc_html__( "Style 1", "classified-pro" ),
						'style_2' => esc_html__( "Style 2", "classified-pro" ),
						'style_3' => esc_html__( "Style 3", "classified-pro" ),
					),
					'default' => 'style_2',
				),
				array(
					'id'       => 'blog_banner_image',
					'title'    => __( 'Blog Banner Background Image', 'classified-pro' ),
					'desc'     => __( 'Please select image for blog page banner background.', 'classified-pro' ),
					'type'     => 'media',
					'required' => array(
						array( 'blog_banner', 'equals', '1' )
					)
				),
				array(
					'id'      => 'blog_sidebar',
					'title'   => __( 'Enable/Disable Sidebar on Blog Page', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show sidebar on blog page.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'blog_banner_title',
					'title'   => __( 'Blog Banner Title', 'classified-pro' ),
					'desc'    => __( 'Specify your banner title.', 'classified-pro' ),
					'type'    => 'text',
					'default' => __( 'Home', 'classified-pro' ),
				)
			),
		);
		// Adding Watermark Section
		$single_settings['classified_watermark'] = array(
			'title'  => __( 'Watermark', 'classified-pro' ),
			'id'     => 'classified_watermark',
			'icon'   => 'dashicons dashicons-images-alt2',
			'fields' => array(
				array(
					'id'      => 'classified_watermark',
					'title'   => __( 'Watermark', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to add watermark to gallery uploaded images.<br>Please note that enabling the option will result in increased ad submission time.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'    => 'classified_watermark_image',
					'title' => __( 'Watermark Image', 'classified-pro' ),
					'desc'  => __( 'Please select an image to use as watermark. Must be a PNG image.', 'classified-pro' ),
					'type'  => 'media',
					'required' => array(
						array( 'classified_watermark', 'equals', '1' )
					)
				),
				array(
					'id'      => 'classified_watermark_position',
					'title'   => __( 'Watermark Position', 'classified-pro' ),
					'desc'    => __( 'Please select the position of watermark on image.', 'classified-pro' ),
					'type'    => 'select',
					'options' => array(
						'center'       => esc_html__( "Center", "classified-pro" ),
						'top-right'    => esc_html__( "Top Right", "classified-pro" ),
						'top-left'     => esc_html__( "Top Left", "classified-pro" ),
						'bottom-left'  => esc_html__( "Bottom Left", "classified-pro" ),
						'bottom-right' => esc_html__( "Bottom Right", "classified-pro" ),
					),
					'default' => 'center',
					'required' => array(
						array( 'classified_watermark', 'equals', '1' )
					)
				),
			),
		);
		// Adding Footer Section
		$sections['classified_footer'] = array(
			'title'  => __( 'Footer', 'classified-pro' ),
			'id'     => 'classified_footer',
			'icon'   => 'dashicons dashicons-art',
			'fields' => array(
				array(
					'id'    => 'footer_logo',
					'title' => __( 'Footer Logo', 'classified-pro' ),
					'desc'  => __( 'Please select logo image for footer.', 'classified-pro' ),
					'type'  => 'media',
				),
				array(
					'id'      => 'footer_min_height',
					'title'   => __( 'Footer Minimum Height', 'classified-pro' ),
					'desc'    => __( 'Please enter the minimum height for footer.', 'classified-pro' ),
					'type'    => 'text',
					'default' => '600px',
				),
				array(
					'id'      => 'footer_bg_color',
					'title'   => __( 'Footer Background Color', 'classified-pro' ),
					'desc'    => __( 'Please select the background color for footer.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#f9fbff',
				),
				array(
					'id'    => 'footer_bg_img',
					'title' => __( 'Footer Background Image', 'classified-pro' ),
					'desc'  => __( 'Please select the background image for footer if any.', 'classified-pro' ),
					'type'  => 'media',
				),
				array(
					'id'      => 'footer_bg_overlay',
					'title'   => __( 'Show Overlay On Background Image', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to show an overlay on footer background image.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'      => 'footer_color',
					'title'   => __( 'Footer Text Color', 'classified-pro' ),
					'desc'    => __( 'Please select the text color for footer.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#353B4A',
				),
				array(
					'id'      => 'footer_color:hover',
					'title'   => __( 'Footer Text Hover State Color', 'classified-pro' ),
					'desc'    => __( 'Please select the hover state color for links on footer.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#0075ff',
				),
				array(
					'id'      => 'footer_column',
					'title'   => __( 'Footer Columns', 'classified-pro' ),
					'desc'    => __( 'Please select the quantity of columns on footer.', 'classified-pro' ),
					'type'    => 'select',
					'options' => array(
						'1' => esc_html__( "Show 1 Column On Footer", "classified-pro" ),
						'2' => esc_html__( "Show 2 Columns On Footer", "classified-pro" ),
						'3' => esc_html__( "Show 3 Columns On Footer", "classified-pro" ),
						'4' => esc_html__( "Show 4 Columns On Footer", "classified-pro" ),
						'5' => esc_html__( "Show 5 Columns On Footer", "classified-pro" ),
						'6' => esc_html__( "Show 6 Columns On Footer", "classified-pro" ),
					),
					'default' => '4',
				),
				array(
					'id'      => 'sub_footer',
					'title'   => __( 'Sub Footer', 'classified-pro' ),
					'desc'    => __( 'Enable if you want to add sub footer.', 'classified-pro' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'      => 'sub_footer_bg_color',
					'title'   => __( 'Sub Footer Background Color', 'classified-pro' ),
					'desc'    => __( 'Please select the background color for sub footer.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#0075FF',
				),
				array(
					'id'      => 'sub_footer_color',
					'title'   => __( 'Sub Footer Text Color', 'classified-pro' ),
					'desc'    => __( 'Please select the text color for sub footer.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#ffffff',
				),
				array(
					'id'      => 'sub_footer_color:hover',
					'title'   => __( 'Sub Footer Text Hover State Color', 'classified-pro' ),
					'desc'    => __( 'Please select the hover state color for links on sub footer.', 'classified-pro' ),
					'type'    => 'color',
					'default' => '#FF5656',
				),
			),
		);

		return classified_add_into_array_after_key( $sections, $single_settings, 'general-settings' );
	}

	add_filter( 'cubewp/options/sections', 'classified_settings_new_sections', 11, 1 );
}

// Adding Settings In General Settings
if ( ! function_exists( 'classified_adding_general_settings' ) ) {
	function classified_adding_general_settings( $section_fields ) {
		global $classified_post_types;

		$fields   = array();
		$fields[] = array(
			'id'      => 'primary_color',
			'title'   => __( 'Primary Color', 'classified-pro' ),
			'type'    => 'color',
			'desc'    => __( 'Please select primary color for the text, hover or active states etc.', 'classified-pro' ),
			'default' => '#0075FF',
		);
		$fields[] = array(
			'id'      => 'primary_alt_color',
			'title'   => __( 'Primary Alternative Color', 'classified-pro' ),
			'type'    => 'color',
			'desc'    => __( 'Please select alternative color of primary color for the text, hover or active states etc.', 'classified-pro' ),
			'default' => '#ffffff',
		);
		$fields[] = array(
			'id'      => 'secondary_color',
			'title'   => __( 'Secondary Color', 'classified-pro' ),
			'type'    => 'color',
			'desc'    => __( 'Please select secondary color for the text, hover or active states etc.', 'classified-pro' ),
			'default' => '#FF5656',
		);
		$fields[] = array(
			'id'      => 'secondary_alt_color',
			'title'   => __( 'Secondary Alternative Color', 'classified-pro' ),
			'type'    => 'color',
			'desc'    => __( 'Please select alternative color of secondary color for the text, hover or active states etc.', 'classified-pro' ),
			'default' => '#ffffff',
		);
		$fields[] = array(
			'id'      => 'transition',
			'type'    => 'select',
			'title'   => __( 'Transition Time', 'classified-pro' ),
			'desc'    => __( 'Please select the transition time for hover effect all over the website. (Below 500 Millisecond Recommended).', 'classified-pro' ),
			'options' => array(
				'100ms' => esc_html__( "100 Millisecond", "classified-pro" ),
				'200ms' => esc_html__( "200 Millisecond", "classified-pro" ),
				'300ms' => esc_html__( "300 Millisecond", "classified-pro" ),
				'400ms' => esc_html__( "400 Millisecond", "classified-pro" ),
				'500ms' => esc_html__( "500 Millisecond", "classified-pro" ),
				'600ms' => esc_html__( "600 Millisecond", "classified-pro" ),
				'700ms' => esc_html__( "700 Millisecond", "classified-pro" ),
				'800ms' => esc_html__( "800 Millisecond", "classified-pro" ),
				'900ms' => esc_html__( "900 Millisecond", "classified-pro" ),
				'1s'    => esc_html__( "1 Second", "classified-pro" ),
				'2s'    => esc_html__( "2 Second", "classified-pro" ),
			),
			'default' => '300ms',
		);
		$fields[] = array(
			'id'    => 'default_featured_image',
			'title' => __( 'Default Featured Image', 'classified-pro' ),
			'desc'  => __( 'Please select the default image for ads to be used if no image available.', 'classified-pro' ),
			'type'  => 'media',
		);
		$fields[] = array(
			'id'      => 'overlay_opacity',
			'title'   => __( 'Website Overlay Opacity', 'classified-pro' ),
			'desc'    => __( 'Please select the opacity value for overlay all over the website.', 'classified-pro' ),
			'type'    => 'select',
			'options' => array(
				'0'   => esc_html__( "Hidden Overlay", "classified-pro" ),
				'0.1' => esc_html__( "10% Overlay Opacity", "classified-pro" ),
				'0.2' => esc_html__( "20% Overlay Opacity", "classified-pro" ),
				'0.3' => esc_html__( "30% Overlay Opacity", "classified-pro" ),
				'0.4' => esc_html__( "40% Overlay Opacity", "classified-pro" ),
				'0.5' => esc_html__( "50% Overlay Opacity", "classified-pro" ),
				'0.6' => esc_html__( "60% Overlay Opacity", "classified-pro" ),
				'0.7' => esc_html__( "70% Overlay Opacity", "classified-pro" ),
				'0.8' => esc_html__( "80% Overlay Opacity", "classified-pro" ),
				'0.9' => esc_html__( "90% Overlay Opacity", "classified-pro" ),
				'1'   => esc_html__( "Solid Overlay", "classified-pro" ),
			),
			'default' => '0.3',
		);
		$fields[] = array(
			'id'      => 'posts_per_page',
			'title'   => __( 'Items Per Page', 'classified-pro' ),
			'desc'    => __( 'How many items you want to show on per page.', 'classified-pro' ),
			'type'    => 'text',
			'default' => '6',
		);

		if ( ! empty( $classified_post_types ) ) {
			foreach ( $classified_post_types as $post_type ) {
				$fields[] = array(
					'id'      => $post_type . '_icon',
					'title'   => sprintf( __( 'Icon For %s', 'classified-pro' ), $post_type ),
					'desc'    => __( 'Enter fontawesome icon class for this post type.', 'classified-pro' ),
					'type'    => 'text',
					'default' => '',
				);
			}
		}

		return array_merge( $section_fields, $fields );
	}

	add_filter( 'cubewp/settings/section/general-settings', 'classified_adding_general_settings' );
}

// Adding Help Page Option In URL Settings
if ( ! function_exists( 'classified_adding_url_settings' ) ) {
	function classified_adding_url_settings( $section_fields ) {
		$fields   = array();
		$fields[] = array(
			'id'       => 'classified_help_page',
			'type'     => 'pages',
			'title'    => __( 'Dashboard Help Page', 'classified-pro' ),
			'validate' => 'url',
			'desc'     => __( 'Select the page to show on used Frontend Dashboard help button.', 'classified-pro' ),
			'default'  => ''
		);

		return array_merge( $fields, $section_fields );
	}

	add_filter( 'cubewp/settings/section/url-settings', 'classified_adding_url_settings' );
}