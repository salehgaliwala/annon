<?php

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Website Promotion Widget.
 *
 * Website Promotion Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Multi_Search_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_multi_search';
	}

	public function get_title() {
		return esc_html__( 'Multi Search', 'cubewp-classified' );
	}

	public function get_icon() {
		return 'eicon-search-bold';
	}

	public function get_categories() {
		return array( 'classified' );
	}

	public function get_keywords() {
		return array(
			'banner',
			'featured',
			'classified',
			'click',
			'promotion',
			'promotional',
			'website',
			'search',
			'searches',
			'multi'
		);
	}

	protected function register_controls() {
		$this->start_controls_section( 'classified_widget_setting_section', array(
			'label' => esc_html__( 'Widget Settings', 'cubewp-classified' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'classified_multi_search_layout', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Layout', 'cubewp-classified' ),
			'options' => array(
				'style-1' => esc_html__( 'Style 1', 'cubewp-classified' ),
				'style-2' => esc_html__( 'Style 2', 'cubewp-classified' ),
				'style-3' => esc_html__( 'Style 3', 'cubewp-classified' ),
			),
			'default' => 'style-1',
		) );
		$repeater = new Repeater();
		$repeater->add_control( 'classified_multi_search_post_type', array(
			'type'        => Controls_Manager::SELECT,
			'multiple'    => false,
			'label'       => esc_html__( 'Post Types', 'cubewp-classified' ),
			'description' => esc_html__( 'Select Post Types Whom Search You Want To Show.', 'cubewp-classified' ),
			'options'     => classified_get_custom_post_types(),
			'default'     => 'classified-ad'
		) );
		$repeater->add_control( 'classified_multi_search_tab_text', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Search Tab Text', 'cubewp-classified' ),
			'default'     => esc_html__( "Any", "cubewp-classified" ),
			'placeholder' => esc_html__( "Enter tab text here", "cubewp-classified" ),
			'label_block' => true,
		) );
		$repeater->add_control( 'classified_multi_search_tab_info', array(
			'type'        => Controls_Manager::WYSIWYG,
			'label'       => esc_html__( 'Search Tab Info', 'cubewp-classified' ),
			'default'     => esc_html__( 'Items available for shipping will be tagged with', "cubewp-classified" ) . '&nbsp;&nbsp;<i class="fa-solid fa-truck" aria-hidden="true"></i>',
			'placeholder' => esc_html__( "Enter tab Info here", "cubewp-classified" ),
			'description' => esc_html__( 'Only works for layout style 2.', 'cubewp-classified' ),
		) );
		$repeater->add_control( 'classified_multi_search_tab_icon', array(
			'type'        => Controls_Manager::ICON,
			'label'       => esc_html__( 'Search Tab Icon', 'cubewp-classified' ),
			'default'     => '',
			'placeholder' => esc_html__( "Enter tab icon class here", "cubewp-classified" ),
		) );
		$repeater->add_control( 'classified_multi_search_tab_bg_image', array(
			'type'        => Controls_Manager::MEDIA,
			'label'       => esc_html__( 'Search Tab Container Background Image', 'cubewp-classified' ),
			'description' => esc_html__( "Select the background image for the search widget elementor container.", "cubewp-classified" ),
		) );
		$this->add_control( 'classified_multi_search', array(
			'label'       => esc_html__( 'Multi Search Tabs', 'cubewp-classified' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array(
					'classified_multi_search_post_type'    => 'classified-ad',
					'classified_multi_search_tab_icon'     => 'fa-solid fa-couch',
					'classified_multi_search_tab_bg_image' => array(),
					'classified_multi_search_tab_text'     => esc_html__( "Any", "cubewp-classified" ),
				),
				array(
					'classified_multi_search_post_type'    => 'real-estate',
					'classified_multi_search_tab_icon'     => 'fa-solid fa-house',
					'classified_multi_search_tab_bg_image' => array(),
					'classified_multi_search_tab_text'     => esc_html__( "Homes", "cubewp-classified" ),
				),
				array(
					'classified_multi_search_post_type'    => 'automotive',
					'classified_multi_search_tab_icon'     => 'fa-solid fa-car-side',
					'classified_multi_search_tab_bg_image' => array(),
					'classified_multi_search_tab_text'     => esc_html__( "Auto", "cubewp-classified" ),
				)
			),
			'title_field' => '{{{ classified_multi_search_tab_text }}}'
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array(
			'search_layout' => $settings['classified_multi_search_layout'],
			'search_tabs'   => $settings['classified_multi_search']
		);

		echo apply_filters( 'classified_multi_search_shortcode_output', '', $args );
	}
}