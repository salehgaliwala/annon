<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( "classified_dynamic_css" ) ) {
	function classified_dynamic_css() {
		$file_path = CLASSIFIED_PATH . 'assets/css/';
		$file_name = 'dynamic-css.css';

		//Options
		$primary_color        = classified_get_setting( 'primary_color' );
		$primary_text_color   = classified_get_setting( 'primary_alt_color' );
		$secondary_color      = classified_get_setting( 'secondary_color' );
		$secondary_text_color = classified_get_setting( 'secondary_alt_color' );

		$home_header_bg         = classified_get_setting( 'header_bg_color_home' );
		$home_top_bar_header_bg = classified_get_setting( 'header_top_bg_color_home' );
		$home_bottom_header_bg  = classified_get_setting( 'header_bottom_bg_color_home' );
		$header_bg              = classified_get_setting( 'header_bg_color_inner' );
		$top_bar_header_bg      = classified_get_setting( 'header_top_bg_color_inner' );
		$bottom_header_bg       = classified_get_setting( 'header_bottom_bg_color_inner' );
		$home_header_shadow     = classified_get_setting( 'header_shadow_color_home' );
		$inner_header_shadow    = classified_get_setting( 'header_shadow_color_inner' );

		$home_header_color       = classified_get_setting( 'header_text_color_home' );
		$home_header_color_hover = classified_get_setting( 'header_text_color_home:hover' );
		$header_color            = classified_get_setting( 'header_text_color_inner' );
		$header_color_hover      = classified_get_setting( 'header_text_color_inner:hover' );
		$a_tag_hover             = classified_get_setting( 'typography-a:hover' );
		$transition              = classified_get_setting( 'transition' );
		$overlay_opacity         = classified_get_setting( 'overlay_opacity' );
		$footer_min_height       = classified_get_setting( 'footer_min_height' );
		$footer_color            = classified_get_setting( 'footer_color' );
		$footer_color_hover      = classified_get_setting( 'footer_color:hover' );
		$footer_bg_color         = classified_get_setting( 'footer_bg_color' );
		$footer_bg_image         = classified_get_setting( 'footer_bg_img', 'media_url' );
		$footer_bg_overlay       = classified_get_setting( 'footer_bg_overlay' );

		$sub_footer_bg_color    = classified_get_setting( 'sub_footer_bg_color' );
		$sub_footer_text_color  = classified_get_setting( 'sub_footer_color' );
		$sub_footer_hover_color = classified_get_setting( 'sub_footer_color:hover' );

		$secondary_color_44 = $secondary_color . '44';
		//Options
		$footer_overlay = '';
		if ( $footer_bg_overlay ) {
			$footer_overlay = '#classified-footer:after {
				content: "";
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				z-index: 0;
				background: ' . $footer_bg_color . ';
				opacity: ' . $overlay_opacity . ';
			}';
		}

		$typography = '';
		$typos      = array(
			'body'  => true,
			'h1'    => true,
			'h2'    => true,
			'h3'    => true,
			'h4'    => true,
			'h5'    => true,
			'h6'    => true,
			'p'     => true,
			'label' => true,
			'a'     => true,
			'p-sm'  => false,
			'p-md'  => false,
			'p-lg'  => false
		);
		foreach ( $typos as $tag => $is_tag ) {
			$setting_id  = 'typography-' . $tag;
			$settings    = classified_get_setting( $setting_id );
			$font_family = $settings["font-family"];
			$font_weight = $settings["font-weight"];
			$sub_sets    = $settings["subsets"];
			$font_size   = $settings["font-size"];
			$line_height = $settings["line-height"];
			$color       = $settings["color"];
			if ( ! $is_tag ) {
				$tag = "." . $tag;
			}
			$typography .= $tag . '{';
			$typography .= 'font-size: ' . $font_size . ';';
			$typography .= 'line-height: ' . $line_height . ';';
			$typography .= 'font-weight: ' . $font_weight . ';';
			$typography .= 'font-family: ' . $font_family . ';';
			if ( $tag !== 'span' ) {
				$typography .= 'color: ' . $color . ';';
			}
			$typography .= 'margin: 0 0 0 0;';
			$typography .= '}';
		}
		$body_typography = classified_get_setting( 'typography-body' )['font-family'] ?? '';

		$dynamic_css = /** @lang CSS */
			<<<CSS
		:host,
		:root,
		html,
		::after,
		::before {
			--primary-color: $primary_color;
			--primary-font-color: $primary_text_color;
			--secondary-color: $secondary_color;
			--secondary-font-color: $secondary_text_color;
			---secondary-color: $secondary_color_44;
			--a-hover-color: $a_tag_hover;
			--heading-color: #2c3e50;
			--descprition-color: #6e7da3;
			--deaf-font-color: #8894ad;
			--deaf-font-color-600: #8894ad1f;
			--deaf-font-color-400: #8894ad44;
			--label-font-color: #151e42;
			--input-font-color-700: #6d7ca3;
			--input-font-color-500: #6d7ca388;
			---input-font-color-500: #484f5f;
			--input-border-color: #d4dcff;
			--faded-font-color: #525c84;
			--primary-disabled: #0075ff22;
			--primary-border-color: #e5edf9;
			--primary-border-color-600: #e5edf966;
			---primary-border-color: #eaeef7;
			--grid-text-color: #424857;
			---grid-text-color: #838eaa;
			--overlay-color: #000000;
			--stats-bg-color: #fcfdfe;
			--stars-color: #d1e4fa;
			--dashboard-secondary-bg-color: #f5f9fd;
			--black-700: #000000;
			--white-700: #ffffff;
			--white-200: #ffffff22;
			--orange-700: #f8b849;
			--purple-700: #4339f2;
			--red-700: #fb295b;
			--green-700: #34b53a;
			--green-500: #5dc461;
			--grey-700: #808080;
			--cyan-700: #28F8C0;
			--input-radius: 10px;
			--border-radius-lg: 20px;
			--border-radius: 12px;
			--border-radius-md: 16px;
			--border-radius-xs: 4px;
			--primary-font: $body_typography, sans-serif;
			--icons-font: "Font Awesome 6 Free", emoji;
			--transition: $transition;
			--overlay-opacity: $overlay_opacity;
		}
		$typography
		a,
		.classified-navigation-nav li a
		{
			text-decoration: none;
		}
		a,
		a > *,
		.cubewp-address-manually,
		.classified-seller-additional-details p span,
		.classified-quick-container .classified-quick > *,
		.classified-categories-container .classified-category-card h4,
		.classified-archive-content-header .classified-archive-popular-searches a,
		.classified-search-filters-container .classified-search-filters-title-and-reset p,
		.classified-item .classified-item-content .classified-item-content-bottom .classified-item-content-term a
		{
			transition: $transition;
			cursor: pointer;
		}
		
		a:hover,
		.classified-seller-additional-details p span:hover,
		.classified-quick-container .classified-quick:not(.classified-quick-views):hover > *,
		.classified-categories-container .classified-category-card h4:hover,
		.classified-archive-content-header .classified-archive-popular-searches a:hover,
		.classified-search-filters-container .classified-search-filters-title-and-reset p:hover,
		.classified-item .classified-item-content .classified-item-content-details .classified-item-title:hover,
		.classified-item .classified-item-content .classified-item-content-bottom .classified-item-content-term a:hover,
		.cubewp-address-manually:hover
		{
			color: $a_tag_hover;
		}
		.classified-header-top-container {
			background: $header_bg;
			box-shadow: 0 4px 4px $inner_header_shadow;
		}
		.classified-header-top-bar {
            background: $top_bar_header_bg;
		}
		.classified-header-bottom-container,
		.classified-header-bottom-container .classified-dropdown-items
		{
            background: $bottom_header_bg;
		}
		.home .classified-header-top-container {
			background: $home_header_bg;
		    box-shadow: 0 4px 4px $home_header_shadow;
		}
		.home .classified-header-top-bar {
            background: $home_top_bar_header_bg;
		}
		.home .classified-header-bottom-container,
		.home .classified-header-bottom-container .classified-dropdown-items
		{
            background: $home_bottom_header_bg;
		}
		.classified-navigation-nav li a,
		.classified-nav-all-categories-container p,
		.classified-nav-all-categories-container h5,
		.classified-header-top-bar-landing-links a
		{
			color: $header_color;
		}
		.classified-navigation-nav li a:hover,
		.classified-navigation-nav li a:hover > *,
		.classified-nav-all-categories-container p:hover,
		.classified-nav-all-categories-container h5:hover,
		.classified-header-top-bar-landing-links a:hover
		{
			color: $header_color_hover;
		}
		.home .classified-navigation-nav li a,
		.home .classified-nav-all-categories-container p,
		.home .classified-nav-all-categories-container h5,
		.home .classified-header-top-bar-landing-links a
		{
			color: $home_header_color;
		}
		.home .classified-navigation-nav li a:hover,
		.home .classified-navigation-nav li a:hover > *,
		.home .classified-nav-all-categories-container p:hover,
		.home .classified-nav-all-categories-container h5:hover,
		.home .classified-header-top-bar-landing-links a:hover
		{
			color: $home_header_color_hover;
		}
		
		.classified-header-search .search-form-fields .cwp-search-field ::placeholder,
		.classified-header-search .search-form-fields .cwp-search-field :-ms-input-placeholder,
		.classified-header-search .search-form-fields .cwp-search-field ::-webkit-input-placeholder 
		 {
			color: $header_color;
		}
		.classified-header-search .search-form-fields .cwp-search-field input,
		.classified-header-search .search-form-fields .cwp-search-field select,
		.classified-header-search .search-form-fields .cwp-search-field .select2-container--default .select2-selection--single {
			color: $header_color;
		}
		.classified-header-search .cwp-submit-search {
			background: $header_color;
			border: 1px solid $header_color;
			color: $header_color;
		}
		body:not(.home) .classified-header-container .classified-navigation-quick-container .classified-navigation-quick > *:not(button) {
		    color: $header_color;
		}
		body:not(.home) .classified-header-container .classified-navigation-quick-container .classified-navigation-quick > *:not(button):hover {
		    color: $header_color_hover;
		}
		body:not(.home) .classified-header-container .classified-navigation-quick-container .classified-not-filled-btn i, body:not(.home) .classified-navigation-quick-container .classified-filled-btn:hover i {
		    color: $header_color;
		}
		.classified-header .classified-header-top .classified-navigation-quick .classified-not-filled-btn:hover i {
			color: $header_color_hover;
		}
		body:not(.home) .classified-header-container .classified-header .classified-header-top .classified-navigation-quick .classified-unread-chat-count:hover {
		    color: $header_color_hover;
		}
		.home .classified-header-search .cwp-submit-search {
			background: var(--primary-color);
			border: 1px solid var(--primary-color);
			color: var(--primary-color);
		}

		.classified-navigation-quick-container .classified-navigation-quick > * {
			color: $header_color;
		}
		.home .classified-navigation-quick-container .classified-navigation-quick > * {
			color: $home_header_color;
		}

		.classified-navigation-quick-container .classified-navigation-quick > * > i {
			color: $header_color;
		}
		.home .classified-navigation-quick-container .classified-navigation-quick > * > i {
			color: $home_header_color;
		}

		.classified-navigation-quick-container .classified-navigation-quick > *:hover {
			color: $header_color_hover;
		}
		.home .classified-navigation-quick-container .classified-navigation-quick > *:hover {
			color: $home_header_color_hover;
		}

		.classified-navigation-quick-container .classified-navigation-quick > *:hover > i {
			color: $header_color_hover;
		}
		.home .classified-navigation-quick-container .classified-navigation-quick > *:hover > i {
			color: $home_header_color_hover;
		}

		.classified-website-promotion-container:before {
			opacity: $overlay_opacity;
		}
		
		.classified-header-search .search-form-fields .cwp-search-field input,
		.classified-header-search .search-form-fields .cwp-search-field select,
		.classified-header-search .search-form-fields .cwp-search-field .select2-container--default .select2-selection--single {
			background: var(--white-700);
			box-shadow: 0 1px 2px $inner_header_shadow;
		}
		
		.home .classified-header-search .search-form-fields .cwp-search-field input,
		.home .classified-header-search .search-form-fields .cwp-search-field select,
		.home .classified-header-search .search-form-fields .cwp-search-field .select2-container--default .select2-selection--single {
			background: var(--white-700);
			box-shadow: 0 1px 2px $home_header_shadow;
		}
		
		.classified-header-search .cwp-submit-search {
			box-shadow: 0 1px 2px $inner_header_shadow;
		}
		.home .classified-header-search .cwp-submit-search {
			box-shadow: 0 1px 2px $home_header_shadow;
		}
		
		#classified-footer {
		    min-height: $footer_min_height;
		    background-color: $footer_bg_color;
		    background-image: url("$footer_bg_image");
			background-size: auto;
		    background-position: center 101%;
		    background-repeat: repeat-x;
		}
		$footer_overlay
		#classified-sub-footer {
		 	background-color: $sub_footer_bg_color;
		}
		#classified-sub-footer p,
		#classified-sub-footer div,
		#classified-sub-footer a
		{
			color: $sub_footer_text_color;
		}
		#classified-sub-footer a:hover,
		#classified-sub-footer a:hover p
		{
			color: $sub_footer_hover_color;	
		}
		#classified-footer .classified-widget h5,
		#classified-footer .classified-widget a
		{
			color: $footer_color;
		}
		#classified-footer .classified-widget a:hover,
		.classified-widget .classified-social-widget i:hover
		{
			color: $footer_color_hover;
		}
CSS;
		$dynamic_css .= apply_filters( 'classified_dynamic_css', '' );
		$dynamic_css = str_replace( array( "\r", "\n", "\t" ), '', $dynamic_css );
		$dynamic_css = "/**\n* Classified Dynamic CSS\n* Note: This file contains dynamically generated CSS so if you make any changes within this file you will lose it.\n*/\n" . $dynamic_css;
		classified_file_force_contents( $file_path . $file_name, $dynamic_css );
	}
}
add_action( 'cubewp/after/settings/saved', 'classified_dynamic_css' );