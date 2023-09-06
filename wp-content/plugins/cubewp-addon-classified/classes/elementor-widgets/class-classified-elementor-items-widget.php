<?php

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Featured Items Widget.
 *
 * Featured Items Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Items_Widget extends Widget_Base {

	public function get_name() {
		return 'classified_items';
	}

	public function get_title() {
		return esc_html__( 'Ads', 'cubewp-classified' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'classified' );
	}

	public function get_keywords() {
		return array( 'banner', 'featured ads', 'classified', 'items', 'featured', 'ads', 'posts' );
	}

	protected function register_controls() {
		global $classified_category_taxonomies;
		$categories_list = get_terms( array(
			'taxonomy'   => $classified_category_taxonomies,
			'hide_empty' => false,
		) );
		$categories      = array();
		if ( ! empty( $categories_list ) && ! is_wp_error( $categories_list ) ) {
			foreach ( $categories_list as $taxonomy ) {
				$categories[ $taxonomy->slug ] = $taxonomy->name;
			}
		}

		$this->start_controls_section( 'classified_widget_setting_section', array(
			'label' => esc_html__( 'Widget Settings', 'cubewp-classified' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'classified_items_layout_style', array(
			'type'        => Controls_Manager::SELECT,
			'label'       => esc_html__( 'Layout Style', 'cubewp-classified' ),
			'description' => esc_html__( 'Select item layout style.', 'cubewp-classified' ),
			'options'     => array(
				'style-1'             => esc_html__( 'Style 1', 'cubewp-classified' ),
				'style-1-focused'     => esc_html__( 'Style 1 ( Focused )', 'cubewp-classified' ),
				'style-1-rounded'     => esc_html__( 'Style 1 ( Rounded )', 'cubewp-classified' ),
				'style-2'             => esc_html__( 'Style 2', 'cubewp-classified' ),
				'cubewp-loop-builder' => esc_html__( 'CubeWP Loop Builder', 'cubewp-classified' )
			),
			'default'     => 'style-1',
		) );
		$this->add_control( 'classified_items_post_types', array(
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'label'       => esc_html__( 'Post Types', 'cubewp-classified' ),
			'description' => esc_html__( 'Select Post Types Whom Posts You Want To Show.', 'cubewp-classified' ),
			'options'     => classified_get_custom_post_types(),
			'default'     => array( 'classified-ad', 'real-estate', 'automotive' ),
		) );
		$this->add_control( 'classified_items_posts_type', array(
			'type'        => Controls_Manager::SELECT,
			'label'       => esc_html__( 'Posts Type', 'cubewp-classified' ),
			'description' => esc_html__( 'Which Posts you want to show.', 'cubewp-classified' ),
			'options'     => array(
				'boosted'     => esc_html__( 'Boosted', 'cubewp-classified' ),
				'recommended' => esc_html__( 'Recommended', 'cubewp-classified' ),
				'purchasable' => esc_html__( 'Purchasable', 'cubewp-classified' ),
				'latest'      => esc_html__( 'Latest', 'cubewp-classified' )
			),
			'default'     => 'latest',
		) );
		$this->add_control( 'classified_enable_items_by_categories', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Enable Items By Categories', 'cubewp-classified' ),
			'default'   => 'no',
			'condition' => array(
				'classified_items_posts_type!' => array( 'recommended', 'boosted' ),
			),
		) );
		$this->add_control( 'classified_categories_for_items', array(
			'label'     => esc_html__( 'Select The Categories For Item', 'cubewp-classified' ),
			'type'      => Controls_Manager::SELECT2,
			'multiple'  => true,
			'options'   => $categories,
			'condition' => array(
				'classified_items_posts_type!'          => array( 'recommended', 'boosted' ),
				'classified_enable_items_by_categories' => 'yes',
			),
		) );
		$this->add_control( 'classified_items_recommended_cats', array(
			'type'        => Controls_Manager::SWITCHER,
			'label'       => esc_html__( 'Recommended Categories', 'cubewp-classified' ),
			'description' => esc_html__( 'Use recommended categories if available.', 'cubewp-classified' ),
			'default'     => 'no',
			'condition'   => array(
				'classified_items_posts_type!'          => 'recommended',
				'classified_enable_items_by_categories' => 'yes',
				'classified_items_tabs'                 => 'yes',
			),
		) );
		$this->add_control( 'classified_enable_items_by_meta', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Enable Items By Meta\'s', 'cubewp-classified' ),
			'default'   => 'no',
			'condition' => array(
				'classified_items_posts_type!' => array( 'recommended', 'boosted' ),
			),
		) );
		$this->add_control( 'classified_meta_key_for_items', array(
			'label'       => esc_html__( 'Meta Key', 'cubewp-classified' ),
			'description' => esc_html__( 'Enter The Meta Key For Item', 'cubewp-classified' ),
			'type'        => Controls_Manager::TEXT,
			'condition'   => array(
				'classified_items_posts_type!'    => 'recommended',
				'classified_enable_items_by_meta' => 'yes',
			),
		) );
		$this->add_control( 'classified_meta_value_for_items', array(
			'label'       => esc_html__( 'Meta Value', 'cubewp-classified' ),
			'description' => esc_html__( 'Enter The Meta Value For Items', 'cubewp-classified' ),
			'type'        => Controls_Manager::TEXT,
			'condition'   => array(
				'classified_items_posts_type!'    => 'recommended',
				'classified_enable_items_by_meta' => 'yes',
			),
		) );
		$this->add_control( 'classified_items_tabs', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Show Items In Tabs', 'cubewp-classified' ),
			'default'   => 'yes',
			'condition' => array(
				'classified_items_posts_type!'          => 'recommended',
				'classified_enable_items_by_categories' => 'yes',
			),
		) );
		$this->add_control( 'classified_items_recommended', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'First Tab Content', 'cubewp-classified' ),
			'default'   => 'no',
			'label_on'  => __( 'Recommended', 'cubewp-classified' ),
			'label_off' => __( 'Recent', 'cubewp-classified' ),
			'condition' => array(
				'classified_items_posts_type!'          => 'recommended',
				'classified_enable_items_by_categories' => 'yes',
				'classified_items_tabs'                 => 'yes',
			),
		) );
		$this->add_control( 'classified_number_of_items', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Number Of Ads', 'cubewp-classified' ),
			'options' => array(
				'-1' => esc_html__( 'Show All Ads', 'cubewp-classified' ),
				'3'  => esc_html__( 'Show 3 Ads', 'cubewp-classified' ),
				'4'  => esc_html__( 'Show 4 Ads', 'cubewp-classified' ),
				'6'  => esc_html__( 'Show 6 Ads', 'cubewp-classified' ),
				'8'  => esc_html__( 'Show 8 Ads', 'cubewp-classified' ),
				'12' => esc_html__( 'Show 12 Ads', 'cubewp-classified' ),
				'15' => esc_html__( 'Show 15 Ads', 'cubewp-classified' ),
				'20' => esc_html__( 'Show 20 Ads', 'cubewp-classified' ),
				'25' => esc_html__( 'Show 25 Ads', 'cubewp-classified' ),
			),
			'default' => '3',
		) );
		$this->add_control( 'classified_show_load_more', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Enable Load More', 'cubewp-classified' ),
			'default'   => 'yes',
			'condition' => array(
				'classified_number_of_items' => '-1',
			),
		) );
		$this->add_control( 'classified_number_of_items_fold', array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Number Of Ads Per Fold', 'cubewp-classified' ),
			'options'   => array(
				'2'  => esc_html__( 'Show 2 Ads', 'cubewp-classified' ),
				'3'  => esc_html__( 'Show 3 Ads', 'cubewp-classified' ),
				'4'  => esc_html__( 'Show 4 Ads', 'cubewp-classified' ),
				'5'  => esc_html__( 'Show 5 Ads', 'cubewp-classified' ),
				'6'  => esc_html__( 'Show 6 Ads', 'cubewp-classified' ),
				'7'  => esc_html__( 'Show 7 Ads', 'cubewp-classified' ),
				'8'  => esc_html__( 'Show 8 Ads', 'cubewp-classified' ),
				'9'  => esc_html__( 'Show 9 Ads', 'cubewp-classified' ),
				'10' => esc_html__( 'Show 10 Ads', 'cubewp-classified' ),
				'11' => esc_html__( 'Show 11 Ads', 'cubewp-classified' ),
				'12' => esc_html__( 'Show 12 Ads', 'cubewp-classified' ),
			),
			'default'   => '9',
			'condition' => array(
				'classified_show_load_more'  => 'yes',
				'classified_number_of_items' => '-1',
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'classified_widget_additional_setting_section', array(
			'label' => esc_html__( 'Promotional Card Settings', 'cubewp-classified' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'classified_promotional_card', array(
			'type'    => Controls_Manager::SWITCHER,
			'label'   => esc_html__( 'Show Promotional Cards', 'cubewp-classified' ),
			'default' => 'yes',
		) );
		$repeater = new Repeater();
		$repeater->add_control( 'classified_promotional_card_heading', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Promotional Card Heading', 'cubewp-classified' ),
			'default'     => esc_html__( "Boost Your Business with us", "cubewp-classified" ),
			'placeholder' => esc_html__( "Boost Your Business with us", "cubewp-classified" ),
			'label_block' => true,
		) );
		$repeater->add_control( 'classified_promotional_card_desc', array(
			'type'        => Controls_Manager::TEXTAREA,
			'label'       => esc_html__( 'Promotional Card Description', 'cubewp-classified' ),
			'default'     => esc_html__( "Make some extra money by selling things in your community. Go on its quick and easy", "cubewp-classified" ),
			'placeholder' => esc_html__( "Make some extra money by selling things in your community. Go on its quick and easy", "cubewp-classified" ),
		) );
		$repeater->add_control( 'classified_promotional_card_btn_text', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Promotional Card Button Text', 'cubewp-classified' ),
			'default'     => esc_html__( "Start Now", "cubewp-classified" ),
			'placeholder' => esc_html__( "Start Now", "cubewp-classified" ),
		) );
		$repeater->add_control( 'classified_promotional_card_btn_url', array(
			'type'        => Controls_Manager::URL,
			'label'       => esc_html__( 'Promotional Card Button Link', 'cubewp-classified' ),
			'placeholder' => esc_html__( "https://example.com", "cubewp-classified" ),
		) );
		$repeater->add_control( 'classified_promotional_card_icon', array(
			'type'        => Controls_Manager::ICON,
			'label'       => esc_html__( 'Icon For Promotional Card', 'cubewp-classified' ),
			'default'     => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#ffffff"><g><rect fill="none" height="24" width="24"></rect></g><g><g><path d="M9.19,6.35c-2.04,2.29-3.44,5.58-3.57,5.89L2,10.69l4.05-4.05c0.47-0.47,1.15-0.68,1.81-0.55L9.19,6.35L9.19,6.35z M11.17,17c0,0,3.74-1.55,5.89-3.7c5.4-5.4,4.5-9.62,4.21-10.57c-0.95-0.3-5.17-1.19-10.57,4.21C8.55,9.09,7,12.83,7,12.83 L11.17,17z M17.65,14.81c-2.29,2.04-5.58,3.44-5.89,3.57L13.31,22l4.05-4.05c0.47-0.47,0.68-1.15,0.55-1.81L17.65,14.81 L17.65,14.81z M9,18c0,0.83-0.34,1.58-0.88,2.12C6.94,21.3,2,22,2,22s0.7-4.94,1.88-6.12C4.42,15.34,5.17,15,6,15 C7.66,15,9,16.34,9,18z M13,9c0-1.1,0.9-2,2-2s2,0.9,2,2s-0.9,2-2,2S13,10.1,13,9z"></path></g></g></svg>',
			'placeholder' => esc_html__( "Put Promotional Card Icon SVG Here", "cubewp-classified" ),
		) );
		$repeater->add_control( 'classified_promotional_card_bg', array(
			'type'    => Controls_Manager::COLOR,
			'label'   => esc_html__( 'Promotional Card Background Color', 'cubewp-classified' ),
			'default' => '#FB295B',
		) );
		$repeater->add_control( 'classified_promotional_card_color', array(
			'type'    => Controls_Manager::COLOR,
			'label'   => esc_html__( 'Promotional Card Text Color', 'cubewp-classified' ),
			'default' => '#ffffff',
		) );
		$repeater->add_control( 'classified_promotional_card_position', array(
			'type'        => Controls_Manager::NUMBER,
			'label'       => esc_html__( 'Promotional Card Position', 'cubewp-classified' ),
			'default'     => esc_html__( "3", "cubewp-classified" ),
			'placeholder' => esc_html__( "3", "cubewp-classified" ),
			'min'         => '1',
		) );
		$this->add_control( 'classified_promotional_cards', array(
			'label'       => esc_html__( 'Promotional Cards', 'cubewp-classified' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array(
					'classified_promotional_card_heading'  => esc_html__( "Boost Your Business with us", "cubewp-classified" ),
					'classified_promotional_card_desc'     => esc_html__( "Make some extra money by selling things in your community. Go on its quick and easy", "cubewp-classified" ),
					'classified_promotional_card_btn_text' => esc_html__( "Start Now", "cubewp-classified" ),
					'classified_promotional_card_btn_url'  => esc_html__( "https://example.com", "cubewp-classified" ),
					'classified_promotional_card_icon'     => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#ffffff"><g><rect fill="none" height="24" width="24"></rect></g><g><g><path d="M9.19,6.35c-2.04,2.29-3.44,5.58-3.57,5.89L2,10.69l4.05-4.05c0.47-0.47,1.15-0.68,1.81-0.55L9.19,6.35L9.19,6.35z M11.17,17c0,0,3.74-1.55,5.89-3.7c5.4-5.4,4.5-9.62,4.21-10.57c-0.95-0.3-5.17-1.19-10.57,4.21C8.55,9.09,7,12.83,7,12.83 L11.17,17z M17.65,14.81c-2.29,2.04-5.58,3.44-5.89,3.57L13.31,22l4.05-4.05c0.47-0.47,0.68-1.15,0.55-1.81L17.65,14.81 L17.65,14.81z M9,18c0,0.83-0.34,1.58-0.88,2.12C6.94,21.3,2,22,2,22s0.7-4.94,1.88-6.12C4.42,15.34,5.17,15,6,15 C7.66,15,9,16.34,9,18z M13,9c0-1.1,0.9-2,2-2s2,0.9,2,2s-0.9,2-2,2S13,10.1,13,9z"></path></g></g></svg>',
					'classified_promotional_card_bg'       => '#FB295B',
					'classified_promotional_card_color'    => '#ffffff',
					'classified_promotional_card_position' => esc_html__( "3", "cubewp-classified" ),
				)
			),
			'title_field' => '{{{ classified_promotional_card_heading }}}',
			'condition'   => array(
				'classified_promotional_card' => 'yes',
			),
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array(
			'layout_style'               => $settings['classified_items_layout_style'],
			'items_post_types'           => $settings['classified_items_post_types'],
			'items_posts_type'           => $settings['classified_items_posts_type'],
			'enable_items_by_categories' => $settings['classified_enable_items_by_categories'],
			'categories_for_items'       => $settings['classified_categories_for_items'],
			'use_recommended_cats'       => $settings['classified_items_recommended_cats'],
			'items_tabs'                 => $settings['classified_items_tabs'],
			'enable_items_by_meta'       => $settings['classified_enable_items_by_meta'],
			'meta_key_for_items'         => $settings['classified_meta_key_for_items'],
			'meta_value_for_items'       => $settings['classified_meta_value_for_items'],
			'items_recommended'          => $settings['classified_items_recommended'],
			'number_of_items'            => $settings['classified_number_of_items'],
			'show_load_more'             => $settings['classified_show_load_more'],
			'number_of_items_fold'       => $settings['classified_number_of_items_fold'],
			'promotional_card'           => $settings['classified_promotional_card'],
			'promotional_cards'          => $settings['classified_promotional_cards'],
		);

		echo apply_filters( 'classified_items_shortcode_output', '', $args );
	}
}