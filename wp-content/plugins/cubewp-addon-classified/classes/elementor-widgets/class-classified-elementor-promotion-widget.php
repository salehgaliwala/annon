<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined('ABSPATH') || exit;

/**
 * Website Promotion Widget.
 *
 * Website Promotion Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Promotion_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_promotion';
	}

	public function get_title() {
		return esc_html__('Promotional', 'cubewp-classified');
	}

	public function get_icon() {
		return 'eicon-click';
	}

	public function get_categories() {
		return array('classified');
	}

	public function get_keywords() {
		return array('banner', 'featured', 'classified', 'click', 'promotion', 'promotional', 'website');
	}

	protected function register_controls() {
		$this->start_controls_section('classified_widget_setting_section', array(
			'label' => esc_html__('Widget Settings', 'cubewp-classified'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		));
		$this->add_control('classified_promotion_heading', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__('Promotion Heading', 'cubewp-classified'),
			'default'     => esc_html__("Quick sell's better when you become a member", "cubewp-classified"),
			'placeholder' => esc_html__("Quick sell's better when you become a member", "cubewp-classified"),
		));
		$this->add_control('classified_promotion_desc', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__('Promotion Description', 'cubewp-classified'),
			'default'     => esc_html__("See more relevant listings about your business, find what you are looking for quicker, and much more!", "cubewp-classified"),
			'placeholder' => esc_html__("See more relevant listings about your business, find what you are looking for quicker, and much more!", "cubewp-classified"),
		));
		$this->add_control('classified_promotion_btn_text', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__('Promotion Button Text', 'cubewp-classified'),
			'default'     => esc_html__("Sell Now", "cubewp-classified"),
			'placeholder' => esc_html__("Sell Now", "cubewp-classified"),
		));
		$this->add_control('classified_promotion_btn_url', array(
			'type'        => Controls_Manager::URL,
			'label'       => esc_html__('Promotion Button Link', 'cubewp-classified'),
			'placeholder' => esc_html__("https://example.com", "cubewp-classified"),
		));
		$this->end_controls_section();

		$this->start_controls_section('classified_widget_styling_setting_section', array(
			'label' => esc_html__('Promotional Card Settings', 'cubewp-classified'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		));
		$this->add_control('classified_promotion_background_color', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__('Promotion Background Color', 'cubewp-classified'),
			'default'   => '#e5edf9',
		));
		$this->add_control('classified_promotion_background_image', array(
			'type'      => Controls_Manager::MEDIA,
			'label'     => esc_html__('Promotion Background Image', 'cubewp-classified'),
		));
		$this->add_control('classified_promotion_text_color', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__('Promotion Text Color', 'cubewp-classified'),
			'default'   => '#ffffff',
		));
		$this->add_control('classified_promotion_btn_bg', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__('Promotion Button Background Color', 'cubewp-classified'),
			'default'   => '#0075ff',
		));
		$this->add_control('classified_promotion_btn_text_color', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__('Promotion Button Text Color', 'cubewp-classified'),
			'default'   => '#ffffff',
		));
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args = array(
			'promotion_heading' => $settings['classified_promotion_heading'],
			'promotion_desc' => $settings['classified_promotion_desc'],
			'promotion_btn_text' => $settings['classified_promotion_btn_text'],
			'promotion_btn_url' => $settings['classified_promotion_btn_url'],
			'promotion_background_color' => $settings['classified_promotion_background_color'],
			'promotion_background_image' => $settings['classified_promotion_background_image'],
			'promotion_text_color' => $settings['classified_promotion_text_color'],
			'promotion_btn_bg' => $settings['classified_promotion_btn_bg'],
			'promotion_btn_text_color' => $settings['classified_promotion_btn_text_color'],
		);

		echo apply_filters('classified_promotion_shortcode_output', '', $args);
	}
}