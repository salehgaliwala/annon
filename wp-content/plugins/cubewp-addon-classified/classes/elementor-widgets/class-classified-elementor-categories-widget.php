<?php

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined('ABSPATH') || exit;

/**
 * Featured Items Widget.
 *
 * Featured Items Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Categories_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'classified_categories';
    }

    public function get_title()
    {
        return esc_html__('Classified Categories', 'cubewp-classified');
    }

    public function get_icon()
    {
        return 'eicon-theme-builder';
    }

    public function get_categories()
    {
        return array('classified');
    }

    public function get_keywords()
    {
        return array('category', 'ads', 'classified', 'ads categories', 'categories');
    }

    protected function register_controls()
    {
        global $classified_taxonomies;
        if (!empty($classified_taxonomies) && is_array($classified_taxonomies)) {
            $classified_taxonomies = array_combine($classified_taxonomies, $classified_taxonomies);
        }
        $this->start_controls_section('classified_widget_setting_section', array(
            'label' => esc_html__('Widget Settings', 'cubewp-classified'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ));
        $this->add_control('classified_taxonomy', array(
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'label' => esc_html__('Select Taxonomy', 'cubewp-classified'),
            'options' => $classified_taxonomies,
            'default' => array('classified-ad_category', 'real-estate_category', 'automotive_category'),
        ));
        $this->add_control('classified_icon_field', array(
            'type' => Controls_Manager::TEXT,
            'label' => esc_html__('Enter Term Icon Custom Field Name', 'cubewp-classified'),
            'default' => 'classified_category_icon',
        ));
        $this->add_control('classified_number_of_categories', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Number Of Categories', 'cubewp-classified'),
            'options' => array(
                '0' => esc_html__('Show All Categories', 'cubewp-classified'),
                '2' => esc_html__('Show 2 Categories', 'cubewp-classified'),
                '4' => esc_html__('Show 4 Categories', 'cubewp-classified'),
                '6' => esc_html__('Show 6 Categories', 'cubewp-classified'),
                '8' => esc_html__('Show 8 Categories', 'cubewp-classified'),
                '10' => esc_html__('Show 10 Categories', 'cubewp-classified'),
                '12' => esc_html__('Show 12 Categories', 'cubewp-classified'),
                '14' => esc_html__('Show 14 Categories', 'cubewp-classified'),
                '16' => esc_html__('Show 16 Categories', 'cubewp-classified'),
                '18' => esc_html__('Show 18 Categories', 'cubewp-classified'),
                '20' => esc_html__('Show 20 Categories', 'cubewp-classified'),
            ),
            'default' => '4',
        ));
        $this->add_control('classified_hide_empty_categories', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Empty Categories', 'cubewp-classified'),
            'options' => array(
                'false' => esc_html__('Show Empty Categories', 'cubewp-classified'),
                'true' => esc_html__('Hide Empty Categories', 'cubewp-classified'),
            ),
            'default' => 'false',
        ));
        $this->add_control('classified_categories_layout', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Layout', 'cubewp-classified'),
            'options' => array(
                'grid-child' => esc_html__('Girds With Children', 'cubewp-classified'),
                'grid-ads-count' => esc_html__('Grid With Ads Count', 'cubewp-classified'),
                'grid-carousal' => esc_html__('Grid Carousal', 'cubewp-classified'),
                'grid-bg-image' => esc_html__('Grid With Background Image', 'cubewp-classified'),
            ),
            'default' => 'grid-child',
        ));
        $this->end_controls_section();
        $this->start_controls_section('classified_widget_style_settings', array(
            'label' => esc_html__('Style Settings', 'cubewp-classified'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => array(
                'classified_categories_layout!' => 'grid-carousal',
            ),
        ));
	    $this->add_control( 'classified_categories_layout_grid_bg_image_color', array(
		    'type'      => Controls_Manager::COLOR,
		    'label'     => esc_html__( 'Card Background Color', 'cubewp-classified' ),
		    'default'   => '#d4dcff',
		    'selectors' => array(
			    '{{WRAPPER}} .classified-category-card' => 'background-color: {{VALUE}};',
		    ),
		    'condition' => array(
			    'classified_categories_layout' => 'grid-bg-image',
		    ),
	    ) );
        $repeater = new Repeater();
        $repeater->add_control('classified_categories_effect', array(
            'label' => esc_html__('Color', 'cubewp-classified'),
            'type' => Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}'
            ),
            'label_block' => true,
        ));
        $this->add_control('classified_categories_effect_colors', array(
            'label' => esc_html__('Categories Colors', 'cubewp-classified'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => array(
                array(
                    'category_color' => '#FFBB00',
                ),
                array(
                    'category_color' => '#3579F0',
                ),
                array(
                    'category_color' => '#F90F58',
                ),
                array(
                    'category_color' => '#34A853',
                ),
                array(
                    'category_color' => '#8138E1',
                ),
            ),
            'title_field' => '{{{ category_color }}}',
        ));
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $args = array(
            'classified_taxonomy' => $settings['classified_taxonomy'],
            'classified_icon_field' => $settings['classified_icon_field'],
            'number_of_categories' => $settings['classified_number_of_categories'],
            'categories_effect_colors' => $settings['classified_categories_effect_colors'],
            'hide_empty_categories' => $settings['classified_hide_empty_categories'],
            'categories_layout' => $settings['classified_categories_layout'],
        );

        echo apply_filters('classified_categories_shortcode_output', '', $args);
    }
}