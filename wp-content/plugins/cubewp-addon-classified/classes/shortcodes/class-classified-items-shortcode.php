<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Items.
 *
 * @class Classified_Items_Shortcode
 */
class Classified_Items_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_items_shortcode', array( $this, 'classified_items_callback' ) );
		add_filter( 'classified_items_shortcode_output', array( $this, 'classified_items' ), 10, 2 );
	}

	public static function classified_items( $output, $parameters ) {
		global $classified_category_taxonomies;
		$categories_for_items = $parameters['categories_for_items'] ?? array();
		if ( ! empty( $categories_for_items ) && is_array( $categories_for_items ) ) {
			$_categories_for_items = array();
			foreach ( $categories_for_items as $category ) {
				if ( is_numeric( $category ) ) {
					$term = get_term( $category );
				} else {
					if ( ! empty( $classified_category_taxonomies ) && is_array( $classified_category_taxonomies ) ) {
						foreach ( $classified_category_taxonomies as $taxonomy ) {
							if ( $term = get_term_by( 'slug', $category, $taxonomy ) ) {
								break;
							}
						}
					}
				}
				if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
					$_categories_for_items[] = $term->term_id;
				}
			}
			$categories_for_items = $parameters['categories_for_items'] = $_categories_for_items;
		}
		$use_recommended_cats = $parameters['use_recommended_cats'] ?? 'no';
		$tab_view             = $parameters['items_tabs'] ?? 'no';
		$items_recommended    = $parameters['items_recommended'] ?? 'no';
		$rand_id              = classified_rand();
		if ( $use_recommended_cats == 'yes' ) {
			if ( class_exists( 'Classified_Personalization' ) && classified_get_setting( 'classified_personalization' ) ) {
				$personalized_cats = Classified_Personalization::classified_get_personalized_terms( 'categories' );
				if ( ! empty( $personalized_cats ) && is_array( $personalized_cats ) ) {
					$categories_for_items = $personalized_cats;
				}
			}
		}
		if ( $items_recommended == 'yes' ) {
			$items_recommended = 'no';
			if ( class_exists( 'Classified_Personalization' ) && classified_get_setting( 'classified_personalization' ) ) {
				if ( Classified_Personalization::classified_check_personalized_terms_exists() ) {
					$items_recommended = 'yes';
				}
			}
		}

		if ( $items_recommended == 'yes' ) {
			$tab_text = esc_html__( 'Recommended', 'cubewp-classified' );
		} else {
			$tab_text = esc_html__( 'All Recent', 'cubewp-classified' );
		}
		ob_start();
		?>
        <section class="classified-items-container">
			<?php if ( $tab_view == 'yes' ) { ?>
                <ul class="nav nav-tabs classified-items-tabs">
                    <li class="nav-item classified-items-tab">
                        <button class="nav-link active classified-items-tab-btn"
                                id="classified-term-all-<?php echo esc_attr( $rand_id ); ?>" data-bs-toggle="tab"
                                data-bs-target="#classified-term-all-<?php echo esc_attr( $rand_id ); ?>-target"
                                type="button"><?php echo esc_html( $tab_text ); ?></button>
                    </li>
					<?php
					if ( ! empty( $categories_for_items ) && is_array( $categories_for_items ) ) {
						$icon_colors  = array(
							'var(--orange-700)',
							'var(--purple-700)',
							'var(--red-700)',
							'var(--green-700)',
							'var(--cyan-700)'
						);
						$icon_counter = 0;
						foreach ( $categories_for_items as $category ) {
							if ( $icon_counter >= count( $icon_colors ) ) {
								$icon_counter = 0;
							}
							$term = get_term( $category );
							if ( empty( $term ) || is_wp_error( $term ) ) {
								continue;
							}
							$term_icon = get_term_meta( $term->term_id, 'classified_category_icon', true );
							$unique_id = 'classified-term-' . $category . '-' . $rand_id;
							?>
                            <li class="nav-item classified-items-tab">
                                <button class="nav-link classified-items-tab-btn"
                                        id="<?php echo esc_attr( $unique_id ); ?>" data-bs-toggle="tab"
                                        data-bs-target="#<?php echo esc_attr( $unique_id . '-target' ); ?>"
                                        type="button">
									<?php
									if ( ! empty( $term_icon ) ) {
										$term_icon_color = $icon_colors[ $icon_counter ];
										$icon_counter ++;
										echo classified_get_icon_output( $term_icon, 'style="color: ' . esc_attr( $term_icon_color ) . ';"' );
									}
									echo esc_html( $term->name );
									?>
                                </button>
                            </li>
							<?php
						}
					}
					?>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="classified-term-all-<?php echo esc_attr( $rand_id ); ?>-target"
                         role="tabpanel">
						<?php
						$all_parameters    = $parameters;
						$items_recommended = $all_parameters['items_recommended'] ?? 'no';
						if ( $items_recommended == 'yes' ) {
							$all_parameters['items_posts_type'] = 'recommended';
						}
						self::get_classified_items( $all_parameters );
						?>
                    </div>
					<?php
					if ( ! empty( $categories_for_items ) && is_array( $categories_for_items ) ) {
						foreach ( $categories_for_items as $category ) {
							$term = get_term( $category );

							if ( empty( $term ) || is_wp_error( $term ) ) {
								continue;
							}
							$taxonomy   = get_taxonomy( $term->taxonomy );
							$post_types = $taxonomy->object_type;
							$unique_id  = 'classified-term-' . $category . '-' . $rand_id;
							?>
                            <div class="tab-pane" id="<?php echo esc_attr( $unique_id . '-target' ); ?>"
                                 role="tabpanel">
								<?php
								$query_parameters                         = $parameters;
								$query_parameters['categories_for_items'] = array( $term->term_id );
								$query_parameters['items_post_types']     = $post_types;
								self::get_classified_items( $query_parameters );
								?>
                            </div>
							<?php
						}
					}
					?>
                </div>
			<?php } else {
				self::get_classified_items( $parameters );
			} ?>
        </section>
		<?php

		return ob_get_clean();
	}

	public static function get_classified_items( $parameters ) {
		global $classified_post_types;
		$items_post_types           = $parameters['items_post_types'];
		$items_posts_type           = $parameters['items_posts_type'];
		$posts_per_page             = $parameters['number_of_items'];
		$promotional_card           = $parameters['promotional_card'];
		$layout_style               = $parameters['layout_style'] ?? 'style-1';
		$enable_items_by_categories = $parameters['enable_items_by_categories'];
		$show_load_more             = $parameters['show_load_more'];
		$number_of_items_fold       = $parameters['number_of_items_fold'];
		$categories_for_items       = $parameters['categories_for_items'] ?? array();
		$enable_items_by_meta       = $parameters['enable_items_by_meta'] ?? 'no';
		$meta_key_for_items         = $parameters['meta_key_for_items'] ?? '';
		$meta_value_for_items       = $parameters['meta_value_for_items'] ?? '';
		?>
        <div class="classified-items-widgets">
            <div class="row <?php echo sprintf( esc_attr( 'classified-layout-%s' ), $layout_style ) ?>">
				<?php
				$args = array();
				if ( $enable_items_by_categories == 'yes' && ! empty( $categories_for_items ) ) {
					$args['categories_terms'] = $categories_for_items;
				}
				$args['post_type'] = $items_post_types ?? $classified_post_types;
				if ( $posts_per_page == '-1' && $show_load_more == 'yes' ) {
					$posts_per_page = $number_of_items_fold;
				}
				$args['posts_per_page'] = $posts_per_page;
				$recommended            = false;
				$boosted                = false;

				if ( $items_posts_type == 'boosted' ) {
					$boosted_posts = array();
					// @todo make booster mandatory
					if ( classified_is_booster_active() ) {
						$boosted_posts = cubewp_boosted_posts( $args['post_type'] );
					}
					if ( ! empty( $boosted_posts ) && is_array( $boosted_posts ) ) {
						shuffle( $boosted_posts );
						$args['post__in'] = $boosted_posts;
						$boosted          = true;
					}
				} else if ( $items_posts_type == 'recommended' ) {
					if ( class_exists( 'Classified_Personalization' ) && classified_get_setting( 'classified_personalization' ) ) {
						if ( Classified_Personalization::classified_check_personalized_terms_exists() ) {
							$recommended         = true;
							$args['recommended'] = true;
							?>
                            <input type="hidden" class="d-none classified-section-have-content">
							<?php
						}
					}
				} else if ( $items_posts_type == 'purchasable' ) {
					$args['meta_query'][] = array(
						'key'   => 'classified_buyable',
						'value' => 'yes',
					);
				}
				if ( $enable_items_by_meta == 'yes' && ! empty( $meta_key_for_items ) ) {
					$args['meta_query'][] = array(
						'key'   => $meta_key_for_items,
						'value' => $meta_value_for_items,
					);
				}
				$col_class = 'col-12 col-md-6 col-lg-4 col-xl-3';
				$query     = ( new Classified_Query() )->Query( $args );
				if ( $query->have_posts() ) {
					$counter        = 1;
					$posts_per_page = $args['posts_per_page'];
					while ( $query->have_posts() ) {
						$query->the_post();
						set_query_var( 'layout_style', $layout_style );
						set_query_var( 'col_class', $col_class );
						if ( $recommended ) {
							set_query_var( 'boosted', false );
							set_query_var( 'recommended', true );
						} else if ( $boosted ) {
							set_query_var( 'recommended', false );
							set_query_var( 'boosted', true );
						} else {
							set_query_var( 'recommended', false );
							set_query_var( 'boosted', false );
						}
						get_template_part( 'templates/loop/loop-views' );
						if ( $promotional_card == 'yes' ) {
							echo self::classified_promotional_card( $parameters, $counter );
						}
						$counter ++;
					}
				}
				wp_reset_postdata();
				wp_reset_query();
				?>
            </div>
			<?php
			$items_per_page   = $parameters['number_of_items'] ?? 3;
			$items_categories = $args['categories_terms'] ?? array();
			if ( $enable_items_by_categories != 'yes' || empty( $items_categories ) ) {
				$items_categories = array();
			}
			if ( ! empty( $items_categories ) && is_array( $items_categories ) ) {
				$items_categories = implode( ',', $items_categories );
			} else {
				$items_categories = '';
			}
			$post_types = implode( ',', $args['post_type'] );
			if ( $show_load_more == 'yes' && $items_per_page == '-1' && $query->max_num_pages > 1 ) { ?>
                <div class="d-flex align-items-center justify-content-center mt-5">
                    <button class="classified-not-filled-btn classified-items-load-more"
                            data-page-num="<?php echo esc_attr( '2' ); ?>"
                            data-recommended="<?php echo esc_attr( $recommended ? 'yes' : 'no' ); ?>"
                            data-boosted="<?php echo esc_attr( $boosted ? 'yes' : 'no' ); ?>"
                            data-layout-style="<?php echo esc_attr( $layout_style ); ?>"
                            data-col-class="<?php echo esc_attr( $col_class ); ?>"
                            data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
                            data-post-types="<?php echo esc_attr( $post_types ); ?>"
                            data-categories-terms="<?php echo esc_attr( $items_categories ); ?>">
						<?php esc_html_e( 'Load More', 'cubewp-classified' ); ?>
                    </button>
                </div>
			<?php } ?>
        </div>
		<?php
	}

	public static function classified_promotional_card( $parameters, $counter ) {
		$promotional_cards = $parameters['promotional_cards'];
		ob_start();
		if ( ! empty( $promotional_cards ) && is_array( $promotional_cards ) ) {
			foreach ( $promotional_cards as $promotional_card ) {
				$position = $promotional_card['classified_promotional_card_position'];
				if ( $position != $counter ) {
					continue;
				}
				$promotional_card_heading  = $promotional_card['classified_promotional_card_heading'];
				$promotional_card_desc     = $promotional_card['classified_promotional_card_desc'];
				$promotional_card_btn_text = $promotional_card['classified_promotional_card_btn_text'];
				$promotional_card_btn_url  = $promotional_card['classified_promotional_card_btn_url'];
				$promotional_card_icon     = $promotional_card['classified_promotional_card_icon'];
				$promotional_card_bg       = $promotional_card['classified_promotional_card_bg'];
				$promotional_card_color    = $promotional_card['classified_promotional_card_color'];
				$is_url_external           = $promotional_card_btn_url['is_external'] ?? 'off';
				$promotional_card_btn_url  = $promotional_card_btn_url['url'] ?? '';
                if ( $is_url_external == 'on' ) {
	                $is_url_external = 'target="_blank"';
                }
				?>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="classified-website-promotion-card"
                         style="background-color: <?php echo esc_attr( $promotional_card_bg ); ?>;color: <?php echo esc_attr( $promotional_card_color ); ?>;">
						<?php if ( ! empty( $promotional_card_icon ) ) { ?>
                            <div class="classified-website-promotion-card-bg">
								<?php
								echo classified_get_icon_output( $promotional_card_icon );
								?>
                            </div>
                            <div class="classified-website-promotion-card-icon">
								<?php
								echo classified_get_icon_output( $promotional_card_icon );
								?>
                            </div>
						<?php } ?>
                        <h5><?php echo esc_html( $promotional_card_heading ); ?></h5>
						<?php if ( ! empty( $promotional_card_desc ) ) { ?>
                            <p class="p-md"><?php echo esc_html( $promotional_card_desc ); ?></p>
						<?php } ?>
                        <style>
                            <?php
							$class = 'classified-temp-' . classified_rand();
							echo '.' . $class . ' {
								color: ' . $promotional_card_color . ';
								background-color: ' . $promotional_card_bg . ';
								border-color: ' . $promotional_card_color . ';
							}
							.' . $class . ':hover {
								color: ' . $promotional_card_bg . ';
								background-color: ' . $promotional_card_color . ';
								border-color: ' . $promotional_card_bg . ';
							}';
							?>
                        </style>
                        <button class="classified-not-filled-btn position-relative w-100 <?php echo esc_attr( $class ); ?>">
                            <a href="<?php echo esc_url( $promotional_card_btn_url ); ?>" <?php echo esc_attr( $is_url_external ); ?> class="stretched-link"></a>
							<?php echo esc_html( $promotional_card_btn_text ); ?>
                        </button>
                    </div>
                </div>
				<?php
			}
		}

		return ob_get_clean();
	}

	public function classified_items_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_items_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}