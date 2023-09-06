<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Items.
 *
 * @class Classified_Blogs_Shortcode
 */
class Classified_Blogs_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_blogs_shortcode', array( $this, 'classified_blogs_callback' ) );
		add_filter( 'classified_blogs_shortcode_output', array( $this, 'classified_blogs' ), 10, 2 );
	}

	public static function classified_blogs( $output, $parameters ) {
		$number_of_posts             = $parameters['number_of_posts'];
		$classified_blog_style_items = $parameters['classified_blog_style_items'] ?? 'style_1';
		$args                        = array();
		$args['posts_per_page']      = $number_of_posts ?? 3;
		$args['post_type']           = 'post';
		$query                       = ( new Classified_Query() )->Query( $args );
		ob_start();
		?>
        <section class="classified-blogs-items-container">
            <div class="row flex-column-reverse flex-lg-row">
                <div class="row classified-blogs-items">
					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							if ( $classified_blog_style_items == 'style_1' ) {
								get_template_part( 'templates/loop/blog-loop-style-1' );
							} else {
								get_template_part( 'templates/loop/blog-loop-style-2' );
							}
						}
					}
					wp_reset_postdata();
					wp_reset_query();
					?>
                </div>
            </div>
        </section>
		<?php

		return ob_get_clean();
	}

	public function classified_blogs_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_blogs_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}