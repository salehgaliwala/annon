<?php

/**
 * Elementor widgets related to pricing plans.
 *
 * @package cubewp-addon-payments/cube/classes/page-builders/elementor-widgets
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * CubeWp_Elementor_Pricing_Plans_Widget
 */
class CubeWp_Elementor_Pricing_Plans_Widget extends Widget_Base {
	
	/**
	 * Method get_name
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_name() {
		return 'cubewp_pricing_plans';
	}
	
	/**
	 * Method get_title
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_title() {
		return esc_html__('CubeWP Pricing Plans', 'cubewp-payments');
	}
	
	/**
	 * Method get_icon
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_icon() {
		return 'eicon-product-price';
	}
	
	/**
	 * Method get_categories
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function get_categories() {
		return array('cubewp');
	}
	
	/**
	 * Method get_keywords
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function get_keywords() {
		return array(
			'cubewp',
			'Pricing',
			'Plans',
			'Pricing Plans',
			'Pricing Plan',
			'Woo',
			'featured',
			'elements',
			'widgets',
			'terms',
			'taxonomy',
			'category',
			'categories',
			'term',
			'taxonomies',
			'posts',
			'post',
			'archive',
			'locations'
		);
	}

	private static function get_post_types() {
		$post_types = cwp_post_types();
		unset( $post_types['elementor_library'] );
		unset( $post_types['e-landing-page'] );
		unset( $post_types['attachment'] );
		unset( $post_types['page'] );

		return $post_types;
	}

	/**
	 * Method register_controls
	 *
	 * @return void
	 */
	protected function register_controls() {
		$post_types = self::get_post_types();
		$this->start_controls_section( 'cubewp_widgets_section', array(
			'label' => esc_html__( 'Widget Options', 'cubewp-payments' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'show_plans_by', array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Show Plans', 'cubewp-payments' ),
			'options'   => array(
				'post_type'  => esc_html__( 'By Post Type', 'cubewp-payments' ),
				'all_plans' => esc_html__( 'All Plans', 'cubewp-payments' ),
			),
			'default'   => 'all_plans',
		) );
		$this->add_control( 'post_type', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select Post Type', 'cubewp-payments' ),
			'options' => $post_types,
			'default'   => 'post',
			'condition' => array(
				'show_plans_by' => 'post_type',
			),
		) );
		$this->end_controls_section();
	}
	
	/**
	 * Method render
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$show_plans_by = $settings['show_plans_by'];
        $attrs = '';
        if ($show_plans_by == "post_type") {
	        $post_type = $settings['post_type'];
	        $attrs = 'post_type=' . $post_type;
        }
		?>
		<div class="cwp-row">
			<?php
			echo do_shortcode('[cwpPricingPlans ' . $attrs . ']');
			?>
		</div>
		<?php
	}
}