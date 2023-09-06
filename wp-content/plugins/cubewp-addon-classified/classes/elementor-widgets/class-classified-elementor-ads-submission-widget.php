<?php
// @todo Delete This Widget
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined('ABSPATH') || exit;

/**
 * Ads Submission Widget.
 *
 * Ads Submission Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Ads_Submission_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_ads_submission';
	}

	public function get_title() {
		return esc_html__('Ads Submission', 'cubewp-classified');
	}

	public function get_icon() {
		return 'eicon-plus';
	}

	public function get_categories() {
		return array('classified');
	}

	public function get_keywords() {
		return array('classified', 'ads', 'submission', 'listing', 'submit', 'submit listing', 'submit ads', 'submit ad');
	}

	protected function register_controls() {
		$this->start_controls_section('classified_ads_submission_section', array(
			'label' => esc_html__('Ads Submission Settings', 'cubewp-classified'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		));
        $options = classified_get_custom_post_types();
        reset($options);
        $first_key = key($options);
        $this->add_control('classified_post_type', array(
            'label'       => esc_html__('Select The Post Type.', 'cubewp-classified'),
            'type'        => Controls_Manager::SELECT,
            'description' => esc_html__('Select Classified Post Type of pricing plans you want to show.', 'cubewp-classified'),
            'options'     => classified_get_custom_post_types(),
            'default'     => $first_key,
        ));
        $this->add_control('classified_submission_sidebar', array(
            'label'       => esc_html__('Submission Sidebar.', 'cubewp-classified'),
            'type'        => Controls_Manager::SWITCHER,
            'description' => esc_html__('Enable if you want to show sidebar on submission.', 'cubewp-classified'),
            'default'     => 'yes',
        ));
        $this->add_control('classified_submission_live_preview', array(
            'label'       => esc_html__('Sidebar Live Preview.', 'cubewp-classified'),
            'type'        => Controls_Manager::SWITCHER,
            'description' => esc_html__('Enable if you want to show live preview of submission within sidebar.', 'cubewp-classified'),
            'default'     => 'yes',
            'condition'   => array(
                'classified_submission_sidebar' => 'yes'
            )
        ));
        $this->add_control('classified_submission_quicktip', array(
            'label'       => esc_html__('Sidebar Quick Tip.', 'cubewp-classified'),
            'type'        => Controls_Manager::SWITCHER,
            'description' => esc_html__('Enable if you want to show quick tip within sidebar.', 'cubewp-classified'),
            'default'     => 'yes',
            'condition'   => array(
                'classified_submission_sidebar' => 'yes'
            )
        ));
        $this->add_control('classified_submission_quicktip_heading', array(
            'label'       => esc_html__('Quick Tip Heading.', 'cubewp-classified'),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__("Quick Tip", "cubewp-classified"),
            'condition'   => array(
                'classified_submission_sidebar' => 'yes',
                'classified_submission_quicktip' => 'yes'
            )
        ));
        $this->add_control('classified_submission_quicktip_desc', array(
            'label'       => esc_html__('Quick Tip Description.', 'cubewp-classified'),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => esc_html__("By Entering additional information your ads get maximum reach", "cubewp-classified"),
            'condition'   => array(
                'classified_submission_sidebar' => 'yes',
                'classified_submission_quicktip' => 'yes'
            )
        ));
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array(
            'classified_post_type' => $settings['classified_post_type'],
            'classified_submission_sidebar' => $settings['classified_submission_sidebar'],
            'classified_submission_live_preview' => $settings['classified_submission_live_preview'],
            'classified_submission_quicktip' => $settings['classified_submission_quicktip'],
            'classified_submission_quicktip_heading' => $settings['classified_submission_quicktip_heading'],
            'classified_submission_quicktip_desc' => $settings['classified_submission_quicktip_desc'],
		);
		echo apply_filters('classified_ads_submission_shortcode_output', '', $args);
	}
}