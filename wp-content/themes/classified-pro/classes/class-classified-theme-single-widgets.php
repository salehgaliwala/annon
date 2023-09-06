<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Theme Single Widgets.
 *
 * @class Classified_Theme_Single_Widgets
 */
class Classified_Theme_Single_Widgets {
	public function __construct() {
		self::include_widgets_callback();
		add_filter( 'cubewp/builder/section/fields', array( $this, 'classified_single_section_fields' ), 11, 2 );
		add_filter( 'cubewp/builder/single/custom/cubes', array( $this, 'classified_single_widgets' ), 11, 2 );
		add_filter( 'cubewp/builder/cubes/fields', array( $this, 'classified_single_cubes_fields' ), 11, 2 );
	}

	private static function include_widgets_callback() {
		$file = CLASSIFIED_PATH . 'include/classified-single-widgets-callback.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}

	public function classified_single_section_fields( $fields, $builder_type ) {
		if ( $builder_type == 'single_layout' ) {
			$fields['section_layout']     = array(
				'class'   => 'section-field form-control',
				'label'   => esc_html__( "Section Layout", "classified-pro" ),
				'name'    => 'section_layout',
				'id'      => 'section_layout',
				'type'    => 'dropdown',
				'options' => array(
					'classified-bordered-box'            => esc_html__( 'Bordered Box', 'classified-pro' ),
					'classified-highlighted-section'     => esc_html__( 'Highlighted', 'classified-pro' ),
					'classified-without-styling-section' => esc_html__( 'No Styling', 'classified-pro' )
				),
			);
			$fields['section_show_title'] = array(
				'class'   => 'section-field form-control',
				'label'   => esc_html__( "Section Title", "classified-pro" ),
				'name'    => 'section_show_title',
				'id'      => 'section_show_title',
				'type'    => 'dropdown',
				'options' => array(
					'yes' => esc_html__( 'Show Section Title', 'classified-pro' ),
					'no'  => esc_html__( 'Hide Section Title', 'classified-pro' )
				),
			);
		}

		return $fields;
	}

	public function classified_single_cubes_fields( $fields, $cube ) {
		$unset     = false;
		$cube_type = $cube['type'] ?? '';
		if ( $cube_type == 'classified_ad_id_and_price' ) {
			$fields['classified_show_ad_id'] = array(
				'class'       => 'group-field field-classified_show_ad_id',
				'name'        => 'classified_show_ad_id',
				'label'       => esc_html__( 'Show ID in widget', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_ad_id'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Yes", 'classified-pro' ),
					'no'  => esc_html__( "No", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_ad_id"',
			);
			$unset                           = true;
		} else if ( $cube_type == 'classified_ad_title_and_desc' ) {
			$fields['classified_description_type'] = array(
				'class'       => 'group-field field-classified_description_type',
				'name'        => 'classified_description_type',
				'label'       => esc_html__( 'Description Type', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_description_type'] ?? 'excerpt',
				'options'     => array(
					'excerpt' => esc_html__( "Ad Excerpt", 'classified-pro' ),
					'content' => esc_html__( "Ad Content ( 20 Words )", 'classified-pro' ),
					'hide'    => esc_html__( "Hide Description", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_description_type"',
			);
			$unset                                 = true;
		} else if ( $cube_type == 'classified_ad_single_actions' ) {
			$fields['classified_show_ad_share']  = array(
				'class'       => 'group-field field-classified_show_ad_share',
				'name'        => 'classified_show_ad_share',
				'label'       => esc_html__( 'Ad Share', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_ad_share'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_ad_share"',
			);
			$fields['classified_show_ad_report'] = array(
				'class'       => 'group-field field-classified_show_ad_report',
				'name'        => 'classified_show_ad_report',
				'label'       => esc_html__( 'Ad Report', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_ad_report'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_ad_report"',
			);
			$fields['classified_show_ad_save']   = array(
				'class'       => 'group-field field-classified_show_ad_save',
				'name'        => 'classified_show_ad_save',
				'label'       => esc_html__( 'Ad Save', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_ad_save'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_ad_save"',
			);
			$unset                               = true;
		} else if ( $cube_type == 'author' ) {
			$fields['classified_show_author_website'] = array(
				'class'       => 'group-field field-classified_show_author_website',
				'name'        => 'classified_show_author_website',
				'label'       => esc_html__( 'Author Website', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_author_website'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_author_website"',
			);
			$fields['classified_show_author_phone']   = array(
				'class'       => 'group-field field-classified_show_author_phone',
				'name'        => 'classified_show_author_phone',
				'label'       => esc_html__( 'Author Phone', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_author_phone'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_author_phone"',
			);
			$fields['classified_show_author_stats']   = array(
				'class'       => 'group-field field-classified_show_author_stats',
				'name'        => 'classified_show_author_stats',
				'label'       => esc_html__( 'Author Stats', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_author_stats'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_author_stats"',
			);
			$unset                                    = true;
		} else if ( $cube_type == 'classified_ad_single_quick_tip' ) {
			$fields['classified_quick_tip_title']     = array(
				'class'       => 'group-field field-classified_quick_tip_title',
				'name'        => 'classified_quick_tip_title',
				'label'       => esc_html__( 'Quick Tip Heading', 'classified-pro' ),
				'type'        => 'text',
				'value'       => $cube['classified_quick_tip_title'] ?? esc_html__( 'Safety Tips', 'classified-pro' ),
				'extra_attrs' => 'data-name="classified_quick_tip_title"',
			);
			$fields['classified_quick_tip_desc']      = array(
				'class'       => 'group-field field-classified_quick_tip_desc',
				'name'        => 'classified_quick_tip_desc',
				'label'       => esc_html__( 'Quick Tip Description', 'classified-pro' ),
				'type'        => 'text',
				'value'       => $cube['classified_quick_tip_desc'] ?? esc_html__( 'Buy and sell safely with Us !', 'classified-pro' ),
				'extra_attrs' => 'data-name="classified_quick_tip_desc"',
			);
			$fields['classified_quick_tip_link_text'] = array(
				'class'       => 'group-field field-classified_quick_tip_link_text',
				'name'        => 'classified_quick_tip_link_text',
				'label'       => esc_html__( 'Quick Tip Link Text', 'classified-pro' ),
				'type'        => 'text',
				'value'       => $cube['classified_quick_tip_link_text'] ?? esc_html__( 'Read our Safety Tips', 'classified-pro' ),
				'extra_attrs' => 'data-name="classified_quick_tip_link_text"',
			);
			$fields['classified_quick_tip_link']      = array(
				'class'       => 'group-field field-classified_quick_tip_link',
				'name'        => 'classified_quick_tip_link',
				'label'       => esc_html__( 'Quick Tip Link', 'classified-pro' ),
				'type'        => 'text',
				'value'       => $cube['classified_quick_tip_link'] ?? esc_html__( '#', 'classified-pro' ),
				'extra_attrs' => 'data-name="classified_quick_tip_link"',
			);
			$fields['classified_quick_tip_icon']      = array(
				'class'       => 'group-field field-classified_quick_tip_icon',
				'name'        => 'classified_quick_tip_icon',
				'label'       => esc_html__( 'Quick Tip Icon', 'classified-pro' ),
				'type'        => 'text',
				'value'       => $cube['classified_quick_tip_icon'] ?? 'fa-solid fa-user-shield',
				'extra_attrs' => 'data-name="classified_quick_tip_icon"',
			);
			$unset                                    = true;
		} else if ( $cube_type == 'classified_ad_single_stats' ) {
			$fields['classified_show_ad_views']        = array(
				'class'       => 'group-field field-classified_show_ad_views',
				'name'        => 'classified_show_ad_views',
				'label'       => esc_html__( 'Ad Views', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_ad_views'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_ad_views"',
			);
			$fields['classified_show_ad_posted_date']  = array(
				'class'       => 'group-field field-classified_show_ad_posted_date',
				'name'        => 'classified_show_ad_posted_date',
				'label'       => esc_html__( 'Ad Posted Date', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_ad_posted_date'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_ad_posted_date"',
			);
			$fields['classified_show_ad_updated_date'] = array(
				'class'       => 'group-field field-classified_show_ad_updated_date',
				'name'        => 'classified_show_ad_updated_date',
				'label'       => esc_html__( 'Ad Updated Date', 'classified-pro' ),
				'type'        => 'dropdown',
				'value'       => $cube['classified_show_ad_updated_date'] ?? 'yes',
				'options'     => array(
					'yes' => esc_html__( "Show", 'classified-pro' ),
					'no'  => esc_html__( "Hide", 'classified-pro' ),
				),
				'extra_attrs' => 'data-name="classified_show_ad_updated_date"',
			);
			$unset                                     = true;
		} else if ( $cube_type == 'classified_ad_the_content' || $cube_type == 'classified_ad_buy_btn' || $cube_type == 'classified_ad_offer_btn' || $cube_type == 'classified_ad_single_sections_tabs' ) {
			$unset = true;
		} else {
			$fields['classified_field_icon'] = array(
				'class'       => 'group-field field-classified_field_icon',
				'name'        => 'classified_field_icon',
				'label'       => esc_html__( 'Icon', 'classified-pro' ),
				'type'        => 'text',
				'value'       => $cube['classified_field_icon'] ?? 'fa-solid fa-check',
				'extra_attrs' => 'data-name="classified_field_icon"',
			);
		}
		if ( $unset ) {
			$fields['label']['type'] = 'hidden';
			unset( $fields['class'] );
		}

		return $fields;
	}

	public function classified_single_widgets( $cubes, $post_type ) {
		global $classified_post_types;
		if ( ! in_array( $post_type, $classified_post_types ) ) {
			return $cubes;
		}

		return array_merge( $cubes, array(
			'classified_ad_the_content'              => array(
				'label' => __( "The Content", "classified-pro" ),
				'name'  => 'classified_ad_the_content',
				'type'  => 'classified_ad_the_content',
			),
			'classified_ad_id_and_price'             => array(
				'label' => __( "Ad ID And Price Details", "classified-pro" ),
				'name'  => 'classified_ad_id_and_price',
				'type'  => 'classified_ad_id_and_price',
			),
			'classified_ad_title_and_desc'           => array(
				'label' => __( "Ad Title And Description", "classified-pro" ),
				'name'  => 'classified_ad_title_and_desc',
				'type'  => 'classified_ad_title_and_desc',
			),
			'classified_ad_single_actions'           => array(
				'label' => __( "Ad Actions", "classified-pro" ),
				'name'  => 'classified_ad_single_actions',
				'type'  => 'classified_ad_single_actions',
			),
			'classified_ad_single_quick_tip'         => array(
				'label' => __( "Ad Single Quick Tip", "classified-pro" ),
				'name'  => 'classified_ad_single_quick_tip',
				'type'  => 'classified_ad_single_quick_tip',
			),
			'classified_ad_single_sections_tabs'     => array(
				'label' => __( "Ad Single Sections Tabs", "classified-pro" ),
				'name'  => 'classified_ad_single_sections_tabs',
				'type'  => 'classified_ad_single_sections_tabs',
			),
			'classified_ad_single_stats'             => array(
				'label' => __( "Ad stats", "classified-pro" ),
				'name'  => 'classified_ad_single_stats',
				'type'  => 'classified_ad_single_stats',
			),
			'classified_ad_single_property_type'     => array(
				'label' => __( "Property Ad Type", "classified-pro" ),
				'name'  => 'classified_ad_single_property_type',
				'type'  => 'classified_ad_single_property_type',
			),
			'classified_ad_single_wordpress_sidebar' => array(
				'label' => __( "WordPress Sidebar", "classified-pro" ),
				'name'  => 'classified_ad_single_wordpress_sidebar',
				'type'  => 'classified_ad_single_wordpress_sidebar',
			)
		) );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}