<?php

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Author Widget.
 *
 * Author Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Author_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_author';
	}

	public function get_title() {
		return esc_html__( 'Author', 'cubewp-classified' );
	}

	public function get_icon() {
		return 'eicon-user-circle-o';
	}

	public function get_categories() {
		return array( 'classified' );
	}

	public function get_keywords() {
		return array(
			'user',
			'author',
			'classified',
			'banner',
			'featured ads',
			'classified',
			'items',
			'featured',
			'ads',
			'posts'
		);
	}

	protected function register_controls() {}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array();

		echo apply_filters( 'classified_author_shortcode_output', '', $args );
	}
}