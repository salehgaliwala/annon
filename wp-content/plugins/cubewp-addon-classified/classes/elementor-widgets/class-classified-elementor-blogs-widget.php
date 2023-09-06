<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined('ABSPATH') || exit;

/**
 * Website Blogs Widget.
 *
 * Website Blogs Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Blogs_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_blogs';
	}

	public function get_title() {
		return esc_html__('Classified Blogs', 'cubewp-classified');
	}

	public function get_icon() {
		return 'eicon-click';
	}

	public function get_categories() {
		return array('classified');
	}

	public function get_keywords() {
		return array('banner', 'website', 'classified', 'post', 'blogs', 'blog', 'news');
	}

	protected function register_controls() {
		$this->start_controls_section('classified_widget_setting_section', array(
			'label' => esc_html__('Widget Settings', 'cubewp-classified'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		));
		$this->add_control( 'classified_blog_style_items', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Number Of Blogs', 'cubewp-classified' ),
			'options' => array(
				'style_1' => esc_html__( 'Style 1', 'cubewp-classified' ),
				'style_2'  => esc_html__( 'Style 2', 'cubewp-classified' ),
			),
			'default' => 'style_1',
		) );
		$this->add_control( 'classified_number_of_items', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Number Of Blogs', 'cubewp-classified' ),
			'options' => array(
				'-1' => esc_html__( 'Show All Blogs', 'cubewp-classified' ),
				'2'  => esc_html__( 'Show 2 Blogs', 'cubewp-classified' ),
				'3'  => esc_html__( 'Show 3 Blogs', 'cubewp-classified' ),
				'4'  => esc_html__( 'Show 4 Blogs', 'cubewp-classified' ),
				'5'  => esc_html__( 'Show 5 Blogs', 'cubewp-classified' ),
				'6'  => esc_html__( 'Show 6 Blogs', 'cubewp-classified' ),
				'7'  => esc_html__( 'Show 7 Blogs', 'cubewp-classified' ),
				'8'  => esc_html__( 'Show 8 Blogs', 'cubewp-classified' ),
				'9'  => esc_html__( 'Show 9 Blogs', 'cubewp-classified' ),
				'10' => esc_html__( 'Show 10 Blogs', 'cubewp-classified' ),
			),
			'default' => '4',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args = array(
			'number_of_posts' => $settings['classified_number_of_items'],
			'classified_blog_style_items' => $settings['classified_blog_style_items'],
		);

		echo apply_filters('classified_blogs_shortcode_output', '', $args);
	}
}