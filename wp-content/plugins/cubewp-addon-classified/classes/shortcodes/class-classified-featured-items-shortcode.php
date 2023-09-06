<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Featured Items.
 *
 * @class Classified_Featured_Items_Shortcodes
 */
class Classified_Featured_Items_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_featured_items_shortcode', array( $this, 'classified_featured_items_callback' ) );
		add_filter( 'classified_featured_items_shortcode_output', array( $this, 'classified_featured_items' ), 10, 2 );
	}

	public static function classified_featured_items( $output, $parameters ) {
		$style = $parameters['featured_items_style'] ?? 'masonry';
		if ( $style == 'sidebar' ) {
			return self::classified_featured_items_with_sidebar( $parameters );
		} else {
			return self::classified_featured_items_masonry( $parameters );
		}
	}

	private static function classified_featured_items_with_sidebar( $parameters ) {
		$preview_bg     = $parameters['items_preview_bg'] ?? '';
		$preview_btn_bg = $parameters['items_preview_btn_bg'] ?? '';
		$sidebar        = $parameters['items_sidebar'] ?? '';
		if ( $sidebar ) {
			$content_class = 'col-12 col-lg-8 col-xxl-9';
		} else {
			$content_class = 'col-12';
		}
		ob_start();
		?>
        <section class="classified-featured-items-container">
            <div class="row flex-column-reverse flex-lg-row">
                <div class="<?php echo esc_attr( $content_class ); ?>">
                    <div class="classified-featured-items"
                         style="background-color: <?php echo esc_attr( $preview_bg ); ?>">
                        <div class="classified-featured-items-slider-progress">
                            <div class="classified-featured-items-progress-bar"
                                 style="background: <?php echo esc_attr( $preview_btn_bg ); ?>;"></div>
                        </div>
						<?php echo self::classified_featured_items_loop( $parameters ); ?>
                    </div>
                </div>
				<?php if ( $sidebar ) { ?>
                    <div class="col-12 col-lg-4 col-xxl-3 mb-4 mb-lg-0">
						<?php echo self::classified_featured_sidebar( $parameters ); ?>
                    </div>
				<?php } ?>
            </div>
        </section>
		<?php

		return ob_get_clean();
	}

	public static function classified_featured_items_loop( $parameters ) {
		$post_types             = $parameters['featured_items_post_types'] ?? 'classified-ad';
		$preview_color          = $parameters['items_preview_text'] ?? '';
		$preview_btn_bg         = $parameters['items_preview_btn_bg'] ?? '';
		$preview_btn_color      = $parameters['items_preview_btn_color'] ?? '';
		$btn_text               = $parameters['items_preview_btn_text'] ?? '';
		$posts_per_page         = $parameters['number_of_items'] ?? '-1';
		$items_posts_type       = $parameters['type_of_items'];
		$args['post_type']      = $post_types;
		$args['posts_per_page'] = $posts_per_page;
		if ( $items_posts_type == 'boosted' ) {
			if ( classified_is_booster_active() ) {
				$boosted_posts = cubewp_boosted_posts( $args['post_type'] );
				if ( ! empty( $boosted_posts ) && is_array( $boosted_posts ) ) {
					shuffle( $boosted_posts );
					$args['post__in'] = $boosted_posts;
				}
			}
		} else if ( $items_posts_type == 'purchasable' ) {
			$args['meta_query'][] = array(
				'key'   => 'classified_buyable',
				'value' => 'yes',
			);
		}
		$classified_query = new Classified_Query();
		$query            = $classified_query->Query( $args );
		ob_start();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();
				$price   = get_post_meta( $post_id, 'classified_price', true );
				?>
                <div class="classified-featured-item">
                    <div class="d-flex justify-content-lg-start align-items-lg-center flex-column-reverse flex-lg-row">
                        <div class="classified-featured-item-details me-lg-5 mt-3 mt-lg-0"
                             style="color: <?php echo esc_attr( $preview_color ); ?>;">
                            <h1><?php echo get_the_title(); ?></h1>
                            <h2><?php echo classified_build_price( $price ); ?></h2>
                            <p class="p-lg"><?php echo esc_html( classified_limit_str_by_words( strip_tags( get_the_content( '', '', $post_id ) ), 35 ) ); ?></p>
                            <style>
                                <?php
								$class = 'classified-temp-' . classified_rand();
								echo '.' . $class . ' {
									color: ' . $preview_btn_color . ';
									background-color: ' . $preview_btn_bg . ';
									border-color: ' . $preview_btn_bg . ';
								}
								.' . $class . ':hover {
									color: ' . $preview_btn_bg . ';
									background-color: ' . $preview_btn_color . ';
									border-color: ' . $preview_btn_color . ';
								}';
								?>
                            </style>
                            <button class="classified-filled-btn position-relative mb-4 mb-lg-0 <?php echo esc_attr( $class ); ?>">
                                <a href="<?php echo get_permalink( $post_id ); ?>" class="stretched-link"></a>
								<?php echo esc_html( $btn_text ); ?>
                            </button>
                        </div>
                        <div class="w-100 position-relative">
                            <div class="classified-featured-item-vectors"
                                 style="color: <?php echo esc_attr( $preview_color ); ?>;">
								<?php echo classified_get_svg( 'fi-vector-2' ); ?>
								<?php echo classified_get_svg( 'fi-vector-3' ); ?>
								<?php echo classified_get_svg( 'fi-vector-4' ); ?>
								<?php echo classified_get_svg( 'fi-vector-5' ); ?>
                            </div>
                            <a href="<?php echo get_permalink( $post_id ); ?>" class="stretched-link"></a>
                            <img loading="lazy" width="100%" height="100%"
                                 src="<?php echo classified_get_post_featured_image( $post_id ); ?>"
                                 alt="<?php echo get_the_title( $post_id ); ?>" class="classified-featured-item-thumb">
                        </div>
                    </div>
                </div>
				<?php
			}
		}
		wp_reset_postdata();
		wp_reset_query();

		return ob_get_clean();
	}

	public static function classified_featured_sidebar( $parameters ) {
		$type = $parameters['items_sidebar_option'];
		ob_start();
		if ( $type == 'ad' ) {
			$media   = $parameters['items_sidebar_ad_card_media'];
			$heading = $parameters['items_sidebar_ad_card_heading'];
			$desc    = $parameters['items_sidebar_ad_card_desc'];
			$url     = $parameters['items_sidebar_ad_card_link'];
			if ( ! isset( $media['alt'] ) ) {
				$media['alt'] = '';
			}
			if ( ! isset( $media['url'] ) ) {
				$media['url'] = '';
			}
			if ( ! isset( $url['url'] ) ) {
				$url['url'] = '';
			}
			if ( ! isset( $url['url'] ) ) {
				$url['url'] = '';
			}
			if ( ! isset( $url['is_external'] ) ) {
				$url['is_external'] = '';
			}
			if ( ! isset( $url['nofollow'] ) ) {
				$url['nofollow'] = '';
			}
			$link        = $url['url'];
			$is_external = $url['is_external'];
			$nofollow    = $url['nofollow'];
			if ( $is_external == 'on' ) {
				$is_external = '_blank';
			} else {
				$is_external = '';
			}
			if ( $nofollow == 'on' ) {
				$nofollow = 'nofollow';
			} else {
				$nofollow = '';
			}
			?>
            <div class="classified-featured-item-sidebar">
                <div class="w-100 position-relative">
                    <a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $is_external ); ?>"
                       class="stretched-link" rel="<?php echo esc_attr( $nofollow ); ?>"></a>
                    <img loading="lazy" width="100%" height="100%" src="<?php echo esc_url( $media['url'] ); ?>"
                         alt="<?php echo esc_attr( $media['alt'] ); ?>">
                </div>
                <h3 class="d-inline-block position-relative">
                    <a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $is_external ); ?>"
                       class="stretched-link" rel="<?php echo esc_attr( $nofollow ); ?>"></a>
					<?php echo esc_html( $heading ); ?>
                </h3>
                <p><?php echo esc_attr( $desc ); ?></p>
            </div>
			<?php
		} else if ( $type == 'shortcode' ) {
			$code = $parameters['items_sidebar_code'];
			echo do_shortcode( $code );
		} else if ( $type == 'html' ) {
			$code = $parameters['items_sidebar_code'];
			echo( $code );
		}


		return ob_get_clean();
	}

	private static function classified_featured_items_masonry( $parameters ) {
		$preview_bg             = $parameters['items_preview_bg'] ?? '';
		$preview_color          = $parameters['items_preview_text'] ?? '';
		$post_types             = $parameters['featured_items_post_types'] ?? 'classified-ad';
		$posts_per_page         = - 1;
		$args['post_type']      = $post_types;
		$args['posts_per_page'] = $posts_per_page;
		$classified_query       = new Classified_Query();
		$query                  = $classified_query->Query( $args );
		ob_start();
		if ( $query->have_posts() ) {
			$height_class = 'classified-featured-item-full-height';
			?>
            <div class="classified-featured-items-container"
                 style="background-color: <?php echo esc_attr( $preview_bg ); ?>">
                <div class="classified-featured-items-masonry" style="display:none">
                    <div class="classified-featured-item <?php echo esc_attr( $height_class ); ?>">
						<?php
						$counter            = 1;
						$grid_reset_counter = 1;
						while ( $query->have_posts() ) {
							$query->the_post();
							$post_id = get_the_ID();
							$price   = get_post_meta( $post_id, 'classified_price', true );
							?>
                            <div class="classified-featured-items-masonry-grid">
                                <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="stretched-link"></a>
                                <img loading="lazy" width="100%" height="100%"
                                     src="<?php echo esc_url( classified_get_post_featured_image( $post_id, false, '' ) ); ?>"
                                     alt="<?php echo get_the_title(); ?>">
                                <div class="classified-featured-items-masonry-grid-content">
                                    <p class="classified-item-price"
                                       style="color: <?php echo esc_attr( $preview_color ); ?>;"><?php echo esc_attr( classified_build_price( $price ) ); ?></p>
                                    <h3 class="classified-item-title"
                                        style="color: <?php echo esc_attr( $preview_color ); ?>;">
										<?php echo esc_attr( get_the_title( $post_id ) ); ?>
                                    </h3>
                                </div>
                            </div>
							<?php
							$counter ++;
							$grid_reset_counter ++;
							if ( $grid_reset_counter == 2 ) {
								if ( $counter == $query->found_posts ) {
									echo '</div><div class="classified-featured-item ' . esc_attr( $height_class ) . '">';
								} else {
									echo '</div><div class="classified-featured-item">';
								}
							}
							if ( $grid_reset_counter == 4 ) {
								echo '</div><div class="classified-featured-item ' . esc_attr( $height_class ) . '">';
								$grid_reset_counter = 1;
							}
						}
						?>
                    </div>
                </div>
            </div>
			<?php
		}
		wp_reset_postdata();
		wp_reset_query();

		return ob_get_clean();
	}

	public function classified_featured_items_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_featured_items_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}