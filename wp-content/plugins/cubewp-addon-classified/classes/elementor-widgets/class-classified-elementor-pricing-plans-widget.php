<?php
// @todo Delete This Widget
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined('ABSPATH') || exit;

/**
 * Pricing Plans Widget.
 *
 * Pricing Plans Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Pricing_Plans_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_pricing_plans';
	}

	public function get_title() {
		return esc_html__('Pricing Plans', 'cubewp-classified');
	}

	public function get_icon() {
		return 'eicon-cart-medium';
	}

	public function get_categories() {
		return array('classified');
	}

	public function get_keywords() {
		return array('classified', 'pricing', 'plans', 'pricing plan', 'pricing plans');
	}

	protected function register_controls() {
		$this->start_controls_section('classified_pricing_plans_section', array(
			'label' => esc_html__('Pricing Plans Settings', 'cubewp-classified'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		));
        $options = array(
            "" => esc_html__("Show All Plans")
        );
        $this->add_control('classified_post_type', array(
            'label'       => esc_html__('Select The Post Type.', 'cubewp-classified'),
            'type'        => Controls_Manager::SELECT,
            'description' => esc_html__('Select Classified Post Type of pricing plans you want to show.', 'cubewp-classified'),
            'options'     => array_merge($options, classified_get_custom_post_types()),
            'default'     => '',
        ));
        $this->add_control('classified_plans_per_row', array(
            'type'    => Controls_Manager::SELECT,
            'label'   => esc_html__('Plans Per Row', 'cubewp-classified'),
            'options' => array(
                '1'  => esc_html__('1 Plans Per Row', 'cubewp-classified'),
                '2'  => esc_html__('2 Plans Per Row', 'cubewp-classified'),
                '3'  => esc_html__('3 Plans Per Row', 'cubewp-classified'),
                '4'  => esc_html__('4 Plans Per Row', 'cubewp-classified'),
                '6'  => esc_html__('6 Plans Per Row', 'cubewp-classified'),
            ),
            'default' => '3',
        ));
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        $attrs = '';
        $post_type = $settings['classified_post_type'];
        $cols_per_row = $settings['classified_plans_per_row'];
        if ( ! empty($post_type))  {
            $attrs .= 'post_type=' . $post_type;
        }
        if ( ! empty($cols_per_row))  {
            $attrs .= ' column_per_row=' . $cols_per_row;
        }
        wp_enqueue_style('classified-shortcode-pricing-plans');
        echo do_shortcode('[cwpPricingPlans ' . $attrs . ']');
	}
}