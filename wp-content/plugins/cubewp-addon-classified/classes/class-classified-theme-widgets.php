<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Widgets.
 *
 * @class Classified_Widgets
 */
class Classified_Theme_Widgets {
	public function __construct() {
		add_action( 'widgets_init', array( $this, 'classified_register_widget_area' ) );
		add_action( 'widgets_init', array( $this, 'classified_register_widgets' ) );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_register_widget_area() {
		$classified_post_types = classified_get_custom_post_types();
		if ( ! empty( $classified_post_types ) && is_array( $classified_post_types ) ) {
			foreach ( $classified_post_types as $post_type => $label ) {
				register_sidebar( array(
					'name'          => sprintf( esc_html__( "%s Sidebar", "cubewp-classified" ), $label ),
					'id'            => 'classified_single_sidebar_' . str_replace( '-', '_', $post_type ),
					'before_widget' => '<div class="classified-widget">',
					'after_widget'  => '</div>',
					'before_title'  => '<h5>',
					'after_title'   => '</h5>'
				) );
			}
		}

		$footer_column = classified_get_setting( 'footer_column' );
		register_sidebar( array(
			'name'          => esc_html__( "Classified Before Footer Columns", "cubewp-classified" ),
			'id'            => 'classified_before_footer_columns_row',
			'before_widget' => '<div class="classified-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5>',
			'after_title'   => '</h5>'
		) );
		register_sidebars( $footer_column, array(
			'name'          => esc_html__( "Classified Footer Column", "cubewp-classified" ),
			'id'            => 'classified_footer_column',
			'before_widget' => '<div class="classified-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5>',
			'after_title'   => '</h5>'
		) );
		register_sidebar( array(
			'name'          => esc_html__( "Classified After Footer Columns", "cubewp-classified" ),
			'id'            => 'classified_after_footer_columns_row',
			'before_widget' => '<div class="classified-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5>',
			'after_title'   => '</h5>'
		) );
	}

	public function classified_register_widgets() {
		$classes = array(
			'Classified_Widget_Call_To_Action',
			'Classified_Widget_Socials',
			'Classified_Widget_Terms'
		);
		foreach ( $classes as $className ) {
			if ( class_exists( $className ) ) {
				register_widget( $className );
			}
		}
	}
}