<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Website Promotion Widget.
 *
 * Website Promotion Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Reviews_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_reviews';
	}

	public function get_title() {
		return esc_html__( 'Reviews', 'cubewp-classified' );
	}

	public function get_icon() {
		return 'eicon-star';
	}

	public function get_categories() {
		return array( 'classified' );
	}

	public function get_keywords() {
		return array( 'banner', 'featured', 'classified', 'click', 'Reviews', 'Reviews', 'website' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'classified_widget_setting_section', array(
			'label' => esc_html__( 'Widget Settings', 'cubewp-classified' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'classified_user_role', array(
			'type'     => Controls_Manager::SELECT2,
			'label'    => esc_html__( 'Select User Role', 'cubewp-classified' ),
			'default'  => 'subscriber',
			'options'  => self::get_all_user_roles(),
			'multiple' => true,
		) );
		$this->add_control( 'classified_order', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select Order', 'cubewp-classified' ),
			'default' => 'DESC',
			'options' => array(
				'ASC'  => esc_html__( 'Ascending', 'cubewp-classified' ),
				'DESC' => esc_html__( 'Descending', 'cubewp-classified' ),
			),
		) );
		$this->add_control( 'classified_no_of_reviews', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Show No. of Reviews', 'cubewp-classified' ),
			'options' => array(
				'-1' => esc_html__( 'Show All Reviews', 'cubewp-classified' ),
				'3'  => esc_html__( 'Show 3 Reviews', 'cubewp-classified' ),
				'4'  => esc_html__( 'Show 4 Reviews', 'cubewp-classified' ),
				'5'  => esc_html__( 'Show 5 Reviews', 'cubewp-classified' ),
				'6'  => esc_html__( 'Show 6 Reviews', 'cubewp-classified' ),
				'8'  => esc_html__( 'Show 8 Reviews', 'cubewp-classified' ),
				'9'  => esc_html__( 'Show 9 Reviews', 'cubewp-classified' ),
			),
			'default' => '3'
		) );
		$this->add_control( 'classified_reviews_layout', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select Layout Style', 'cubewp-classified' ),
			'options' => array(
				'style-1' => esc_html__( 'Layout Style 1', 'cubewp-classified' ),
				'style-2' => esc_html__( 'Layout Style 2', 'cubewp-classified' ),
			),
			'default' => 'style-1'
		) );

		$this->end_controls_section();
	}

	private static function get_all_user_roles() {
		global $wp_roles;

		return $wp_roles->get_names();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array(
			'user_role'     => $settings['classified_user_role'],
			'order'         => $settings['classified_order'],
			'no_of_reviews' => $settings['classified_no_of_reviews'],
			'layout'        => $settings['classified_reviews_layout'],
		);

		echo apply_filters( 'classified_reviews_shortcode_output', '', $args );
	}
}