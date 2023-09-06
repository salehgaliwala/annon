<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Items.
 *
 * @class Classified_Items_Shortcode
 */
class Classified_Reviews_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_reviews_shortcode', array( $this, 'classified_reviews_callback' ) );
		add_filter( 'classified_reviews_shortcode_output', array( $this, 'classified_reviews' ), 10, 2 );
	}

	public static function classified_reviews( $output, $parameters ) {
		ob_start();
		?>
        <section class="classified-reviews-widget">
            <div class="row">
				<?php
				echo self::classified_reviews_cards( $parameters );
				?>
            </div>
        </section>
		<?php

		return ob_get_clean();
	}

	private static function classified_reviews_cards( $parameters ) {
		$user_role     = $parameters['user_role'];
		$order         = $parameters['order'];
		$no_of_reviews = $parameters['no_of_reviews'];
		$layout        = $parameters['layout'];
		ob_start();
		if ( ! empty( $user_role ) ) {
			$user_role = ! is_array( $user_role ) ? array( $user_role ) : $user_role;
			$user_ids  = get_users( array(
				'fields'   => 'ID',
				'role__in' => $user_role
			) );
			if ( ! empty( $user_ids ) ) {
				$args  = array(
					'post_type'      => 'cwp_reviews',
					'post_status'    => 'publish',
					'author__in'     => $user_ids,
					'order'          => $order,
					'posts_per_page' => $no_of_reviews,
					'meta_query'     => array(
						array(
							'key'     => 'cwp_review_type',
							'value'   => 'user',
							'compare' => '='
						)
					)
				);
				$posts = new WP_Query( $args );
				if ( $posts->have_posts() ) {
					while ( $posts->have_posts() ) {
						$posts->the_post();
						global $post;
						if ( $layout == 'style-2' ) {
							echo self::classified_get_style_2_review_card_template( $post );
						} else {
							echo self::classified_get_style_1_review_card_template( $post );
						}
					}
				}
				wp_reset_postdata();
				wp_reset_query();
			}
		}

		return ob_get_clean();
	}

	private static function classified_get_style_2_review_card_template( $post ) {
		$review_author_id          = $post->post_author;
		$reviewID                  = get_the_ID();
		$post_id                   = get_post_meta( $reviewID, 'cwp_review_associated', true );
		$classified_query          = new Classified_Query();
		$query                     = $classified_query->Query( array(
			'post_status'    => array( 'sold' ),
			'posts_per_page' => '-1',
			'author'         => $post_id
		) );
		$sold_ads                  = $query->post_count;
		$current_user_image        = classified_get_userdata( $review_author_id, 'avatar' );
		$current_user_name         = classified_get_userdata( $review_author_id, 'name' );
		$current_review_author_url = classified_get_userdata( $review_author_id, 'profile_link' );
		$current_author_url        = get_author_posts_url( $post_id );
		$current_author_image      = classified_get_userdata( $post_id, 'avatar' );
		$author_name               = get_the_author_meta( 'display_name', $post_id );
		$rating                    = classified_get_userdata( $post_id, 'rating' );
		ob_start();
		?>
        <div class="col-12 col-md-4">
            <div class="classified-review-box">
                <div class="classified-review-content">
                    <i class="fas fa-quote-left" aria-hidden="true"></i>
					<?php
					$count_des_grid       = get_the_excerpt( $reviewID );
					$count_des_grid_words = strip_tags( $count_des_grid );
					if ( ! empty( $count_des_grid_words ) ) {
						?>
                        <p class="classified-review-des">
							<?php echo wp_trim_words( $count_des_grid_words, 18 ); ?>
                        </p>
						<?php
					}
					?>
                    <a href="<?php echo esc_html( $current_review_author_url ); ?>">
                        <img src="<?php echo esc_html( $current_user_image ); ?>"
                             alt="<?php echo esc_attr( $current_user_name ); ?>"/>
                    </a>
                </div>
                <div class="classified-reviewed-profile">
                    <a href="<?php echo esc_url( $current_author_url ); ?>">
                        <img src="<?php echo esc_url( $current_author_image ); ?>" alt="user"/>
                    </a>
                    <div class="classified-reviewed-profile-name-rating">
                        <a href="<?php echo esc_url( $current_author_url ); ?>">
                            <h3 class="user-name"><?php echo esc_html( $author_name ); ?>
                                <?php
                                if ( classified_is_user_email_verified() ) {
                                    ?>
                                    <i class="fa-regular fa-circle-check" aria-hidden="true"
                                       data-classified-tooltip="true"
                                       data-bs-placement="right"
                                       title="<?php esc_html_e( 'Verified', 'cubewp-classified' ); ?>"></i>
                                    <?php
                                }
                                ?>
                            </h3>
                        </a>
                        <?php
                        if ( $rating ) {
                            ?>
                            <div class="classified-author-rating-stars"
                                 data-classified-tooltip="true"
                                 data-bs-placement="bottom"
                                 title="<?php echo esc_html( $rating ); ?>">
                                <?php echo classified_get_rating_stars_html( $rating ); ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="classified-reviewed-profile-sold-items">
                        <span class="classified-reviewed-profile-sold-item-count"><?php echo esc_html( $sold_ads ); ?></span>
	                    <?php esc_html_e( 'Sold Items', 'cubewp-classified' ); ?>
                    </div>
                </div>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}

	private static function classified_get_style_1_review_card_template( $post ) {
		$review_author_id     = $post->post_author;
		$reviewID             = get_the_ID();
		$post_id              = get_post_meta( $reviewID, 'cwp_review_associated', true );
		$current_author_image = classified_get_userdata( $post_id, 'avatar' );
		$current_author_url   = get_author_posts_url( $post_id );
		$author_name          = get_the_author_meta( 'display_name', $post_id );
		$rating               = classified_get_userdata( $post_id, 'rating' );
		$inbox_resp           = classified_get_userdata( $post_id, 'inbox_short_response' );
		if ( ! $inbox_resp ) {
			return '';
		}
		$resp_time                 = $inbox_resp['response_time'] ?? esc_html__( 'N/A', 'cubewp-classified' );
		$resp_rate                 = $inbox_resp['response_rate'] ?? esc_html__( 'N/A', 'cubewp-classified' );
		$current_user_image        = classified_get_userdata( $review_author_id, 'avatar' );
		$current_review_author_url = classified_get_userdata( $review_author_id, 'profile_link' );
		$classified_query          = new Classified_Query();
		$query                     = $classified_query->Query( array(
			'post_status'    => array( 'sold' ),
			'posts_per_page' => '-1',
			'author'         => $post_id
		) );
		$sold_ads                  = $query->post_count;
		ob_start();
		?>
        <div class="col-12 col-md-4">
            <div class="classified-review-card">
                <div class="classified-review-card-user-info w-50">
                    <div class="classified-review-card-user">
                        <a href="<?php echo esc_url( $current_author_url ); ?>" class="user-image">
                            <img src="<?php echo esc_url( $current_author_image ); ?>" alt="user"/>
                        </a>
                        <a href="<?php echo esc_url( $current_author_url ); ?>">
                            <h3 class="user-name"><?php echo esc_html( $author_name ); ?>
								<?php
								if ( classified_is_user_email_verified() ) {
									?>
                                    <i class="fa-regular fa-circle-check" aria-hidden="true"
                                       data-classified-tooltip="true"
                                       data-bs-placement="right"
                                       title="<?php esc_html_e( 'Verified', 'cubewp-classified' ); ?>"></i>
									<?php
								}
								?>
                            </h3>
                        </a>
						<?php
						if ( $rating ) {
							?>
                            <div class="classified-author-rating-stars"
                                 data-classified-tooltip="true"
                                 data-bs-placement="bottom"
                                 title="<?php echo esc_html( $rating ); ?>">
								<?php echo classified_get_rating_stars_html( $rating ); ?>
                            </div>
							<?php
						}
						?>
                    </div>
                    <div class="classified-review-user-response">
                        <div class="classified-review-user-layout-box">
                            <p class="classified-review-user-status"><?php echo esc_html( $sold_ads ); ?></p>
                            <p class="review-user-layout-box-info">
                                <span><?php echo esc_html__( 'sold', 'cubewp-classified' ); ?></span><br><?php echo esc_html__( 'items', 'cubewp-classified' ); ?>
                            </p>
                        </div>
                        <div class="classified-review-user-layout-box">
                            <p class="classified-review-user-status"><?php echo esc_html( $resp_rate ); ?></p>
                            <p class="review-user-layout-box-info">
                                <span><?php echo esc_html__( 'Response', 'cubewp-classified' ) ?></span><br><?php echo esc_html__( 'Rate', 'cubewp-classified' ) ?>
                            </p>
                        </div>
                        <div class="classified-review-user-layout-box">
                            <p class="classified-review-user-status"><?php echo esc_html( $resp_time ); ?></p>
                            <p class="review-user-layout-box-info">
                                <span><?php echo esc_html__( 'Response', 'cubewp-classified' ) ?></span><br><?php echo esc_html__( 'Time', 'cubewp-classified' ) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="classified-review-card-reviewer-info w-50">
                    <i class="fas fa-quote-left" aria-hidden="true"></i>
					<?php
					$count_des_grid       = get_the_excerpt( $reviewID );
					$count_des_grid_words = strip_tags( $count_des_grid, '<p>' );
					$count                = str_word_count( $count_des_grid_words );

					if ( ! empty( $count_des_grid_words ) ) {
						if ( ! empty( $count && $count > 18 ) ) {
							echo '  <p class="classified-review-des">' . wp_trim_words( $count_des_grid_words, 18 ) . '...</p>';
						} else {
							echo '  <p class="classified-review-des">' . wp_trim_words( $count_des_grid_words, 18 ) . '</p>';
						}
					}
					?>
                    <a href="<?php echo esc_html( $current_review_author_url ); ?>"
                       class="user-image"><img
                                src="<?php echo esc_html( $current_user_image ); ?>"
                                alt="user"/></a>
                </div>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_reviews_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_reviews_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}
}
