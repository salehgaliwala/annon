<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Author.
 *
 * @class Classified_Author_Shortcode
 */
class Classified_Author_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_author_shortcode', array( $this, 'classified_author_callback' ) );
		add_filter( 'classified_author_shortcode_output', array( $this, 'classified_author' ), 10, 2 );
	}

	public static function classified_author( $output, $parameters ) {
		ob_start();
        set_query_var( 'author-style', 'author-style-2' );
		get_template_part( 'templates/author/author-views' );

		return ob_get_clean();
    }

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_author_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_author_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}
}