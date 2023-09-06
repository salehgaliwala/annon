<?php

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Browse By Widget.
 *
 * Browse By Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Browse_By_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_browse_by';
	}

	public function get_title() {
		return esc_html__( 'Browse By', 'cubewp-classified' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return array( 'classified' );
	}

	public function get_keywords() {
		return array(
			'banner',
			'Browse By',
			'Categories',
			'Locations',
			'classified',
			'items',
			'featured',
			'ads',
			'posts'
		);
	}

	protected function register_controls() {
		global $classified_taxonomies;
		if ( ! empty( $classified_taxonomies ) && is_array( $classified_taxonomies ) ) {
			$classified_taxonomies = array_combine( $classified_taxonomies, $classified_taxonomies );
		}else {
			$classified_taxonomies = array();
		}
		$this->start_controls_section( 'classified_widget_setting_section', array(
			'label' => esc_html__( 'Widget Settings', 'cubewp-classified' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'classified_browse_by_layout', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Layout', 'cubewp-classified' ),
			'options' => array(
				'style-1' => esc_html__( 'Style 1', 'cubewp-classified' ),
				'style-2' => esc_html__( 'Style 2', 'cubewp-classified' ),
			),
			'default' => 'style-1',
		) );
		$this->add_control( 'classified_browse_by_col', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Columns Per Row', 'cubewp-classified' ),
			'options' => array(
				'col-12'                  => esc_html__( '1 Columns', 'cubewp-classified' ),
				'col-6'                   => esc_html__( '2 Columns', 'cubewp-classified' ),
				'col-6 col-lg-4'          => esc_html__( '3 Columns', 'cubewp-classified' ),
				'col-6 col-lg-3'          => esc_html__( '4 Columns', 'cubewp-classified' ),
				'col-6 col-lg-3 col-xl-2' => esc_html__( '6 Columns', 'cubewp-classified' ),
			),
			'default' => 'col-6 col-lg-3 col-xl-2',
		) );
		$this->add_control( 'classified_browse_by_style_2_bg_color', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Grid Background Color', 'cubewp-classified' ),
			'default'   => '#F8F9FF',
			'selectors' => array(
				'{{WRAPPER}} .classified-browse-by-card .classified-browse-by-card-item' => 'background-color: {{VALUE}};',
			),
			'condition' => array(
				'classified_browse_by_layout' => 'style-2',
			),
		) );
		$this->add_control( 'classified_browse_by_style_2_bg_color:hover', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Grid Background Color On Hover', 'cubewp-classified' ),
			'default'   => '#ffffff',
			'selectors' => array(
				'{{WRAPPER}} .classified-browse-by-card .classified-browse-by-card-item:hover' => 'background-color: {{VALUE}};',
			),
			'condition' => array(
				'classified_browse_by_layout' => 'style-2',
			),
		) );
		$this->add_control( 'classified_browse_by_style_2_text_color', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Grid Text Color', 'cubewp-classified' ),
			'default'   => '#2c3e50',
			'selectors' => array(
				'{{WRAPPER}} .classified-browse-by-card .classified-browse-by-card-item a' => 'color: {{VALUE}};',
			),
			'condition' => array(
				'classified_browse_by_layout' => 'style-2',
			),
		) );
		$this->add_control( 'classified_browse_by_style_2_text_color:hover', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Grid Text Color On Grid Hover', 'cubewp-classified' ),
			'default'   => '#2c3e50',
			'selectors' => array(
				'{{WRAPPER}} .classified-browse-by-card .classified-browse-by-card-item:hover a' => 'color: {{VALUE}};',
			),
			'condition' => array(
				'classified_browse_by_layout' => 'style-2',
			),
		) );
		$repeater = new Repeater();
		$repeater->add_control( 'classified_browse_by_tab_text', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Browse By Tab Text', 'cubewp-classified' ),
			'placeholder' => esc_html__( "Enter tab text here", "cubewp-classified" ),
			'label_block' => true,
		) );
		$repeater->add_control( 'classified_browse_by_post_type', array(
			'type'        => Controls_Manager::SELECT,
			'label'       => esc_html__( 'Post Types', 'cubewp-classified' ),
			'description' => esc_html__( 'Select Post Types.', 'cubewp-classified' ),
			'options'     => classified_get_custom_post_types(),
			'default'     => 'classified-ad',
		) );
		$repeater->add_control( 'classified_browse_by_tab_type', array(
			'type'        => Controls_Manager::SELECT,
			'multiple'    => false,
			'label'       => esc_html__( 'Content Type', 'cubewp-classified' ),
			'description' => esc_html__( 'Select the content type you want to show in tab content.', 'cubewp-classified' ),
			'options'     => array(
				''            => esc_html__( 'Select Tab Content', 'cubewp-classified' ),
				'terms'       => esc_html__( 'Taxonomy Terms', 'cubewp-classified' ),
				'fields'      => esc_html__( 'Custom Field Options', 'cubewp-classified' ),
				'field-terms' => esc_html__( 'Taxonomy Terms With Custom Fields', 'cubewp-classified' ),
			),
			'default'     => ''
		) );
		$repeater->add_control( 'classified_browse_by_taxonomy', array(
			'type'      => Controls_Manager::SELECT2,
			'multiple'  => true,
			'label'     => esc_html__( 'Select Taxonomies', 'cubewp-classified' ),
			'options'   => $classified_taxonomies,
			'default'   => array( 'classified-ad_category', 'real-estate_category', 'automotive_category' ),
			'condition' => array(
				'classified_browse_by_tab_type' => array( 'field-terms', 'terms' ),
			),
		) );
		$repeater->add_control( 'classified_browse_by_custom_fields', array(
			'type'      => Controls_Manager::SELECT2,
			'multiple'  => true,
			'label'     => esc_html__( 'Select Custom Fields', 'cubewp-classified' ),
			'options'   => classified_get_choices_custom_fields(),
			'condition' => array(
				'classified_browse_by_tab_type' => 'fields',
			),
		) );
		$repeater->add_control( 'classified_browse_by_max_options', array(
			'type'      => Controls_Manager::NUMBER,
			'label'     => esc_html__( 'Max Options', 'cubewp-classified' ),
			'default'   => 16,
		) );
		$repeater->add_control( 'classified_browse_by_custom_field', array(
			'type'      => Controls_Manager::TEXT,
			'multiple'  => true,
			'label'     => esc_html__( 'Enter Custom Fields Name', 'cubewp-classified' ),
			'condition' => array(
				'classified_browse_by_tab_type' => 'field-terms'
			),
		) );
		$repeater->add_control( 'classified_browse_by_custom_field_value', array(
			'type'        => Controls_Manager::TEXT,
			'multiple'    => true,
			'label'       => esc_html__( 'Enter Custom Field Value', 'cubewp-classified' ),
			'description' => esc_html__( 'Enter the value of entered custom field.', 'cubewp-classified' ),
			'condition'   => array(
				'classified_browse_by_custom_field!' => '',
				'classified_browse_by_tab_type'      => 'field-terms'
			),
		) );
		$this->add_control( 'classified_browse_by', array(
			'label'       => esc_html__( 'Browse By Tabs', 'cubewp-classified' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array(
					'classified_browse_by_tab_text'  => esc_html__( "Location", "cubewp-classified" ),
					'classified_browse_by_post_type' => 'classified-ad',
					'classified_browse_by_tab_type'  => 'terms',
					'classified_browse_by_taxonomy'  => 'locations',
				)
			),
			'title_field' => '{{{ classified_browse_by_tab_text }}}'
		) );
		$this->add_control( 'classified_browse_by_icon_accent', array(
			'type'      => Controls_Manager::SWITCHER,
			'multiple'  => true,
			'label'     => esc_html__( 'Accent Colors On Icons', 'cubewp-classified' ),
			'default'   => 'yes',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array(
			'browse_by'                    => $settings['classified_browse_by'],
			'browse_by_col'                => $settings['classified_browse_by_col'],
			'browse_by_layout'             => $settings['classified_browse_by_layout'],
			'browse_by_style_2_bg_color'   => $settings['classified_browse_by_style_2_bg_color'],
			'browse_by_style_2_text_color' => $settings['classified_browse_by_style_2_text_color'],
			'browse_by_icon_accent'        => $settings['classified_browse_by_icon_accent']
		);

		echo apply_filters( 'classified_browse_by_shortcode_output', '', $args );
	}
}