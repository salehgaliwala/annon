<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Featured Items.
 *
 * @class Classified_Categories_Shortcode
 */
class Classified_Categories_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_categories_shortcode', array( $this, 'classified_categories_shortcode_callback' ) );
		add_filter( 'classified_categories_shortcode_output', array( $this, 'classified_categories' ), 10, 2 );
	}

	public static function classified_categories( $output, $parameters ) {
		$layout = $parameters['categories_layout'] ?? 'grid-child';
		/**
         * @todo Start
           * Added in 1.0.10
           * Following code is for backward compatibility and can be removed after at least 5 Plugin updates
		 */
		if ( $layout == 'grid-view' ) {
			$parameters['categories_layout'] = $layout = 'grid-child';
		} elseif ( $layout == 'list-view' ) {
			$parameters['categories_layout'] = $layout = 'grid-carousal';
		}
		// @todo End
		ob_start();
		if ( $layout == 'grid-carousal' ) {
			?>
            <section class="classified-category-cards classified-have-carousal">
				<?php echo self::classified_categories_loop( $parameters ); ?>
            </section>
			<?php
		} else {
			?>
            <section class="classified-categories-container <?php echo esc_attr( $layout ); ?>">
                <div class="row">
					<?php echo self::classified_categories_loop( $parameters ); ?>
                </div>
            </section>
			<?php
		}

		return ob_get_clean();
	}

	public static function classified_categories_loop( $parameters ) {
		$taxonomy    = $parameters['classified_taxonomy'] ?? array( 'category' );
		$icon_name   = $parameters['classified_icon_field'] ?? 'classified_category_icon';
		$hide_empty  = $parameters['hide_empty_categories'] ?? false;
		$number      = $parameters['number_of_categories'] ?? false;
		$color       = $parameters['categories_effect_colors'] ?? array();
		$layout      = $parameters['categories_layout'] ?? 'grid-child';
		$color_count = count( $color );
		if ( $hide_empty == 'true' ) {
			$hide_empty = true;
		} else {
			$hide_empty = false;
		}
		$args       = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => $hide_empty,
			'parent'     => 0,
			'number'     => $number,
		);
		$categories = get_terms( $args );
		$counter    = 0;
		ob_start();
		if ( ! empty( $categories ) && is_array( $categories ) && ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$term_id   = $category->term_id;
				$term_name = $category->name;
				$term_icon = get_term_meta( $term_id, $icon_name, true );
				if ( is_array( $term_icon ) ) {
					$term_icon = '';
				}
				$color_eft        = $color[ $counter ]['category_color'] ?? 'var(--primary-color)';
				if ( $layout == 'grid-child' || $layout == 'grid-bg-image' ) {
					?>
                    <div class="col-12 col-md-6 col-lg-3">
					<?php
				}elseif ( $layout == 'grid-ads-count' ) {
                    ?>
                    <div class="col-12 col-md-4 col-lg-3 col-xl-2">
					<?php
				}
				?>
                <div class="classified-category-card">
					<?php
					if ( ! empty( $term_icon ) ) {
						?>
                        <div class="classified-category-icon" style="color: <?php echo esc_attr( $color_eft ); ?>">
                            <?php echo classified_get_icon_output( $term_icon ); ?>
                        </div>
                        <?php
					}
					?>
                    <a href="<?php echo get_term_link( $term_id ); ?>" <?php if ( $layout == 'grid-carousal' || $layout == 'grid-ads-count' || $layout == 'grid-bg-image' ) {
						echo 'class="stretched-link"';
					} ?>>
                        <h4>
							<?php echo esc_html( $term_name ); ?>
                        </h4>
                    </a>
					<?php
					if ( $layout == 'grid-child' ) {
                        $child_args       = array(
                            'taxonomy'   => $taxonomy,
                            'hide_empty' => $hide_empty,
                            'parent'     => $term_id,
                            'number'     => 0,
                        );
                        $child_categories = get_terms( $child_args );
						?>
                        <ul class="<?php if ( count( $child_categories ) > 3 ) {
							echo 'classified-category-card-have-collapse';
						} ?>">
							<?php
							if ( ! empty( $child_categories ) && is_array( $child_categories ) ) {
								foreach ( $child_categories as $child_category ) {
									$child_term_id   = $child_category->term_id;
									$child_term_name = $child_category->name;
									?>
                                    <li>
                                        <a href="<?php echo get_term_link( $child_term_id ); ?>"><?php echo esc_html( $child_term_name ); ?></a>
                                    </li>
								<?php }
							} ?>
                        </ul>
						<?php
						if ( count( $child_categories ) > 3 ) {
							?>
							<p class="classified-see-more-category collapsed"
                                 data-more="<?php esc_attr_e( "See More", "cubewp-classified" ); ?>"
                                 data-less="<?php esc_attr_e( "See Less", "cubewp-classified" ); ?>">
                                <?php esc_html_e( "See More", "cubewp-classified" ); ?>
                            </p>
                            <?php
						}
					}elseif ( $layout == 'grid-ads-count' ) {
                        ?>
                        <p class="classified-category-posts-count">
                            <?php
                            echo sprintf( esc_html__( '(%s)', 'cubewp-classified' ), $category->count );
                            ?>
                        </p>
                        <?php
					}
					?>
                </div>
				<?php
				if ( $layout == 'grid-child' || $layout == 'grid-ads-count' || $layout == 'grid-bg-image' ) {
					?>
                    </div>
					<?php
				}
				$counter ++;
				if ( $counter >= $color_count ) {
					$counter = 0;
				}
			}
		}

		return ob_get_clean();
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_categories_shortcode_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_categories_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}
}