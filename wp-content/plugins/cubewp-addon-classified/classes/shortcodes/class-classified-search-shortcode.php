<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Search.
 *
 * @class Classified_Search_Shortcode
 */
class Classified_Search_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_search_shortcode', array( $this, 'classified_search_callback' ) );
		add_filter( 'classified_search_shortcode_output', array( $this, 'classified_search' ), 10, 2 );
	}

	public static function classified_search( $output, $parameters ) {
		$search_layout = $parameters['search_layout'] ?? 'style-1';
		if ( $search_layout == 'style-1' ) {
			return self::get_classified_search_style_1( $parameters );
		} else if ( $search_layout == 'style-2' ) {
			return self::get_classified_search_style_2( $parameters );
		}

        return $output;
	}

	private static function get_classified_search_style_2( $parameters ) {
		$post_type = $parameters['post_type'];
		ob_start();
		?>
        <div class="classified-search-style2-container classified-visible-on-load">
			<?php
			echo do_shortcode( '[cwpSearch type="' . $post_type . '"]' );
			?>
        </div>
		<?php

		return ob_get_clean();
	}

	private static function get_classified_search_style_1( $parameters ) {
		$post_type = $parameters['post_type'];
		ob_start();
		?>
        <div class="classified-search-style1-container classified-visible-on-load">
			<?php
			echo do_shortcode( '[cwpSearch type="' . $post_type . '"]' );
			?>
        </div>
		<?php

		return ob_get_clean();
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_search_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_search_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}
}