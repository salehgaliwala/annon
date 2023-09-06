<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Classified Search Widget.
 *
 * Single Search Widget For Classified.
 *
 * @since 1.0.10
 */
class Classified_Elementor_Search_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_search';
	}

	public function get_title() {
		return esc_html__( 'Search', 'cubewp-classified' );
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
		$this->add_control( 'classified_search_layout', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Layout', 'cubewp-classified' ),
			'options' => array(
				'style-1' => esc_html__( 'Style 1', 'cubewp-classified' ),
				'style-2' => esc_html__( 'Style 2', 'cubewp-classified' ),
			),
			'default' => 'style-1',
		) );
        $this->add_control( 'classified_search_post_type', array(
            'type'        => Controls_Manager::SELECT,
            'multiple'    => false,
            'label'       => esc_html__( 'Post Types', 'cubewp-classified' ),
            'description' => esc_html__( 'Select Post Types Whom Search You Want To Show.', 'cubewp-classified' ),
            'options'     => classified_get_custom_post_types(),
            'default'     => 'classified-ad'
        ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array(
			'search_layout' => $settings['classified_search_layout'] ?? 'style-1',
			'post_type'     => $settings['classified_search_post_type']
		);

		echo apply_filters( 'classified_search_shortcode_output', '', $args );
	}
}