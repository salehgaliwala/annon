<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Featured Items Widget.
 *
 * Featured Items Elementor Widget For Classified.
 *
 * @since 1.0.0
 */
class Classified_Elementor_Featured_Items_Widget extends Widget_Base {

	public function get_name() {
		return 'featured_items';
	}

	public function get_title() {
		return esc_html__( 'Featured Ad', 'cubewp-classified' );
	}

	public function get_icon() {
		return 'eicon-pro-icon';
	}

	public function get_categories() {
		return array( 'classified' );
	}

	public function get_keywords() {
		return array( 'banner', 'featured ads', 'classified', 'items', 'featured', 'ads' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'classified_widget_setting_section', array(
			'label' => esc_html__( 'Widget Settings', 'cubewp-classified' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'classified_featured_items_post_types', array(
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'label'       => esc_html__( 'Post Types', 'cubewp-classified' ),
			'description' => esc_html__( 'Select Post Types Whom Posts You Want To Show.', 'cubewp-classified' ),
			'options'     => classified_get_custom_post_types(),
			'default'     => array( 'classified-ad', 'real-estate', 'automotive' ),
		) );
		$this->add_control( 'classified_featured_items_style', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Style', 'cubewp-classified' ),
			'options' => array(
				'sidebar' => esc_html__( 'Sidebar', 'cubewp-classified' ),
				'masonry' => esc_html__( 'Masonry', 'cubewp-classified' )
			),
			'default' => 'masonry',
		) );
		$this->add_control( 'classified_type_of_items', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Type Of Ads', 'cubewp-classified' ),
			'options' => array(
				'boosted'     => esc_html__( 'Boosted', 'cubewp-classified' ),
				'purchasable' => esc_html__( 'Purchasable', 'cubewp-classified' ),
				'latest'      => esc_html__( 'Latest', 'cubewp-classified' )
			),
			'default' => 'boosted',
		) );
		$this->add_control( 'classified_number_of_items', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Number Of Ads', 'cubewp-classified' ),
			'options' => array(
				'-1' => esc_html__( 'Show All Ads', 'cubewp-classified' ),
				'2'  => esc_html__( 'Show 2 Ads', 'cubewp-classified' ),
				'3'  => esc_html__( 'Show 3 Ads', 'cubewp-classified' ),
				'4'  => esc_html__( 'Show 4 Ads', 'cubewp-classified' ),
				'5'  => esc_html__( 'Show 5 Ads', 'cubewp-classified' ),
				'6'  => esc_html__( 'Show 6 Ads', 'cubewp-classified' ),
				'7'  => esc_html__( 'Show 7 Ads', 'cubewp-classified' ),
				'8'  => esc_html__( 'Show 8 Ads', 'cubewp-classified' ),
				'9'  => esc_html__( 'Show 9 Ads', 'cubewp-classified' ),
				'10' => esc_html__( 'Show 10 Ads', 'cubewp-classified' ),
			),
			'default' => '8',
			'condition' => array(
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_preview_btn_text', array(
			'type'      => Controls_Manager::TEXT,
			'label'     => esc_html__( 'Ads Preview Button Text', 'cubewp-classified' ),
			'default'   => esc_html__( 'Start Chat', 'cubewp-classified' ),
			'condition' => array(
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_sidebar', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Show Sidebar', 'cubewp-classified' ),
			'default'   => 'yes',
			'condition' => array(
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_sidebar_option', array(
			'label'       => esc_html__( 'Content Type', 'cubewp-classified' ),
			'description' => esc_html__( 'Select The Content Type For Sidebar', 'cubewp-classified' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				'ad'        => esc_html__( "Advertisement Card", "cubewp-classified" ),
				'shortcode' => esc_html__( "Shortcode", "cubewp-classified" ),
				'html'      => esc_html__( "HTML", "cubewp-classified" )
			),
			'default'     => 'ad',
			'condition'   => array(
				'classified_items_sidebar'        => 'yes',
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_sidebar_ad_card_media', array(
			'type'      => Controls_Manager::MEDIA,
			'label'     => esc_html__( 'Promotion Background Image', 'cubewp-classified' ),
			'condition' => array(
				'classified_items_sidebar_option' => 'ad',
				'classified_items_sidebar'        => 'yes',
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_sidebar_ad_card_heading', array(
			'type'      => Controls_Manager::TEXT,
			'label'     => esc_html__( 'Enter Your Heading', 'cubewp-classified' ),
			'default'   => esc_html__( 'Post Your Ad Now', 'cubewp-classified' ),
			'condition' => array(
				'classified_items_sidebar_option' => 'ad',
				'classified_items_sidebar'        => 'yes',
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_sidebar_ad_card_desc', array(
			'type'      => Controls_Manager::TEXTAREA,
			'label'     => esc_html__( 'Enter Your Description', 'cubewp-classified' ),
			'default'   => esc_html__( 'Est minim aute sit nostrud commodo deserunt exercitation.', 'cubewp-classified' ),
			'condition' => array(
				'classified_items_sidebar_option' => 'ad',
				'classified_items_sidebar'        => 'yes',
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_sidebar_ad_card_link', array(
			'type'      => Controls_Manager::URL,
			'label'     => esc_html__( 'Enter Your URL', 'cubewp-classified' ),
			'condition' => array(
				'classified_items_sidebar_option' => 'ad',
				'classified_items_sidebar'        => 'yes',
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_sidebar_code', array(
			'type'      => Controls_Manager::WYSIWYG,
			'label'     => esc_html__( 'Enter Your HTML/ShortCode', 'cubewp-classified' ),
			'condition' => array(
				'classified_items_sidebar_option' => array( 'shortcode', 'html' ),
				'classified_items_sidebar'        => 'yes',
				'classified_featured_items_style' => 'sidebar',
			),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'classified_widget_additional_setting_section', array(
			'label'     => esc_html__( 'Promotional Card Settings', 'cubewp-classified' ),
			'tab'       => Controls_Manager::TAB_CONTENT,
			'condition' => array(
				'classified_featured_items_style!' => 'masonry',
			),
		) );

		$this->add_control( 'classified_promotional_card', array(
			'type'    => Controls_Manager::SWITCHER,
			'label'   => esc_html__( 'Show Website Promotional Card', 'cubewp-classified' ),
			'default' => 'yes',
		) );
		$this->add_control( 'classified_promotional_card_count', array(
			'type'        => Controls_Manager::NUMBER,
			'label'       => esc_html__( 'Promotional Card Count', 'cubewp-classified' ),
			'placeholder' => '2',
			'default'     => '2',
			'min'         => '1',
			'max'         => '10',
			'description' => esc_html__( 'How many cards you want to show?', 'cubewp-classified' ),
			'condition'   => array(
				'classified_promotional_card' => 'yes',
			),
		) );
		$this->add_control( 'classified_promotional_card_heading', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Promotional Card Heading', 'cubewp-classified' ),
			'default'     => esc_html__( "Boost Your Business with us", "cubewp-classified" ),
			'placeholder' => esc_html__( "Boost Your Business with us", "cubewp-classified" ),
			'condition'   => array(
				'classified_promotional_card' => 'yes',
			),
		) );
		$this->add_control( 'classified_promotional_card_desc', array(
			'type'        => Controls_Manager::TEXTAREA,
			'label'       => esc_html__( 'Promotional Card Description', 'cubewp-classified' ),
			'default'     => esc_html__( "Make some extra money by selling things in your community. Go on its quick and easy", "cubewp-classified" ),
			'placeholder' => esc_html__( "Make some extra money by selling things in your community. Go on its quick and easy", "cubewp-classified" ),
			'condition'   => array(
				'classified_promotional_card' => 'yes',
			),
		) );
		$this->add_control( 'classified_promotional_card_btn_text', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Promotional Card Button Text', 'cubewp-classified' ),
			'default'     => esc_html__( "Start Now", "cubewp-classified" ),
			'placeholder' => esc_html__( "Start Now", "cubewp-classified" ),
			'condition'   => array(
				'classified_promotional_card' => 'yes',
			),
		) );
		$this->add_control( 'classified_promotional_card_btn_url', array(
			'type'        => Controls_Manager::URL,
			'label'       => esc_html__( 'Promotional Card Button Link', 'cubewp-classified' ),
			'placeholder' => esc_html__( "https://example.com", "cubewp-classified" ),
			'condition'   => array(
				'classified_promotional_card' => 'yes',
			),
		) );
		$this->add_control( 'classified_promotional_card_icon', array(
			'type'        => Controls_Manager::TEXTAREA,
			'label'       => esc_html__( 'Icon For Promotional Card', 'cubewp-classified' ),
			'default'     => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#ffffff"><g><rect fill="none" height="24" width="24"></rect></g><g><g><path d="M9.19,6.35c-2.04,2.29-3.44,5.58-3.57,5.89L2,10.69l4.05-4.05c0.47-0.47,1.15-0.68,1.81-0.55L9.19,6.35L9.19,6.35z M11.17,17c0,0,3.74-1.55,5.89-3.7c5.4-5.4,4.5-9.62,4.21-10.57c-0.95-0.3-5.17-1.19-10.57,4.21C8.55,9.09,7,12.83,7,12.83 L11.17,17z M17.65,14.81c-2.29,2.04-5.58,3.44-5.89,3.57L13.31,22l4.05-4.05c0.47-0.47,0.68-1.15,0.55-1.81L17.65,14.81 L17.65,14.81z M9,18c0,0.83-0.34,1.58-0.88,2.12C6.94,21.3,2,22,2,22s0.7-4.94,1.88-6.12C4.42,15.34,5.17,15,6,15 C7.66,15,9,16.34,9,18z M13,9c0-1.1,0.9-2,2-2s2,0.9,2,2s-0.9,2-2,2S13,10.1,13,9z"></path></g></g></svg>',
			'placeholder' => esc_html__( "Put Promotional Card Icon SVG Here", "cubewp-classified" ),
			'condition'   => array(
				'classified_promotional_card' => 'yes',
			),
		) );
		$this->add_control( 'classified_promotional_card_bg', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Promotional Card Background Color', 'cubewp-classified' ),
			'default'   => '#FB295B',
			'condition' => array(
				'classified_promotional_card' => 'yes',
			),
		) );
		$this->add_control( 'classified_promotional_card_color', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Promotional Card Text Color', 'cubewp-classified' ),
			'default'   => '#ffffff',
			'condition' => array(
				'classified_promotional_card' => 'yes',
			),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'classified_widget_style_settings', array(
			'label' => esc_html__( 'Style Settings', 'cubewp-classified' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'classified_items_preview_bg', array(
			'type'    => Controls_Manager::COLOR,
			'label'   => esc_html__( 'Ads Preview Background Color', 'cubewp-classified' ),
			'default' => '#000000',
		) );
		$this->add_control( 'classified_items_preview_text', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Ads Preview Text Color', 'cubewp-classified' ),
			'default'   => '#FB295B',
		) );
		$this->add_control( 'classified_items_preview_btn_bg', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Ads Preview Button Background Color', 'cubewp-classified' ),
			'default'   => '#F8B849',
			'condition' => array(
				'classified_featured_items_style' => 'sidebar',
			),
		) );
		$this->add_control( 'classified_items_preview_btn_color', array(
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Ads Preview Button Text Color', 'cubewp-classified' ),
			'default'   => '#2C3E50',
			'condition' => array(
				'classified_featured_items_style' => 'sidebar',
			),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array(
			'featured_items_post_types'     => $settings['classified_featured_items_post_types'],
			'featured_items_style'          => $settings['classified_featured_items_style'],
			'type_of_items'                 => $settings['classified_type_of_items'],
			'number_of_items'               => $settings['classified_number_of_items'],
			'items_preview_btn_text'        => $settings['classified_items_preview_btn_text'],
			'items_sidebar'                 => $settings['classified_items_sidebar'],
			'items_sidebar_option'          => $settings['classified_items_sidebar_option'],
			'items_sidebar_ad_card_media'   => $settings['classified_items_sidebar_ad_card_media'],
			'items_sidebar_ad_card_heading' => $settings['classified_items_sidebar_ad_card_heading'],
			'items_sidebar_ad_card_desc'    => $settings['classified_items_sidebar_ad_card_desc'],
			'items_sidebar_ad_card_link'    => $settings['classified_items_sidebar_ad_card_link'],
			'items_sidebar_code'            => $settings['classified_items_sidebar_code'],
			'promotional_card'              => $settings['classified_promotional_card'],
			'promotional_card_count'        => $settings['classified_promotional_card_count'],
			'promotional_card_heading'      => $settings['classified_promotional_card_heading'],
			'promotional_card_desc'         => $settings['classified_promotional_card_desc'],
			'promotional_card_btn_text'     => $settings['classified_promotional_card_btn_text'],
			'promotional_card_btn_url'      => $settings['classified_promotional_card_btn_url'],
			'promotional_card_icon'         => $settings['classified_promotional_card_icon'],
			'promotional_card_bg'           => $settings['classified_promotional_card_bg'],
			'promotional_card_color'        => $settings['classified_promotional_card_color'],
			'items_preview_bg'              => $settings['classified_items_preview_bg'],
			'items_preview_text'            => $settings['classified_items_preview_text'],
			'items_preview_btn_bg'          => $settings['classified_items_preview_btn_bg'],
			'items_preview_btn_color'       => $settings['classified_items_preview_btn_color']
		);

		echo apply_filters( 'classified_featured_items_shortcode_output', '', $args );
	}
}