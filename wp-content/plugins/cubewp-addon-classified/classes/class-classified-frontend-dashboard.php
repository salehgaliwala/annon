<?php

defined( 'ABSPATH' ) || exit;



/**

 * Classified Frontend Dashboard Class.

 *

 * @class Classified_Frontend_Dashboard

 */

class Classified_Frontend_Dashboard {



	private static $user_dashboard = '';



	private static $user_role = '';



	public function __construct() {

		self::$user_dashboard = CWP()->cubewp_options( 'cwp_userdash' ) ?? array();

		self::$user_role      = cwp_get_current_user_roles();

		add_filter( 'cwp/dashboard/tab/headings', array( $this, 'classified_dashboard_tabs' ), 11, 4 );

		add_filter( 'cwp/dashboard/single/tab/content/output', array( $this, 'classified_dashboard_content' ), 11, 4 );



		add_action( 'wp_ajax_classified_ignore_rate_request', array( $this, 'classified_ignore_rate_request' ) );



        add_filter( 'cubewp/cwp_reviews/after/submit/actions', array( $this, 'classified_remove_rate_request' ), 11, 2 );



        if ( get_transient( 'classified_dashboard_activities_transient' ) === false ) {

			self::classified_dashboard_activities_transient();

		}

	}



	private static function classified_dashboard_activities_transient() {

		$user_ids   = array();

		$user_query = new WP_User_Query( array( 'number' => - 1 ) );

		$users      = $user_query->get_results();

		if ( ! empty( $users ) ) {

			foreach ( $users as $user ) {

				classified_calc_user_stats_count( $user );

				classified_calc_user_profile_completion_status( $user );

				$user_ids[] = $user->ID;

			}

		}

		set_transient( 'classified_dashboard_activities_transient', $user_ids, DAY_IN_SECONDS );

	}



	public static function init() {

		$ClassifiedClass = __CLASS__;

		new $ClassifiedClass;

	}



	public function classified_ignore_rate_request() {

		if ( ! is_user_logged_in() ) {

			wp_send_json_error( esc_html__( "Sorry! Please login/register to send offers.", "cubewp-classified" ) );

		}

		if ( ! classified_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'classified_ignore_rate_request_nonce' ) ) {

			wp_send_json_error( esc_html__( "Nonce Security Error", "cubewp-classified" ) );

		}

		$user_id    = get_current_user_id();

		$request_id = sanitize_text_field( $_POST['request_id'] );

		$user_meta  = get_user_meta( $user_id, 'classified_rate_profile_requests', true );

		$user_meta  = is_array( $user_meta ) && ! empty( $user_meta ) ? $user_meta : array();

		if ( ! isset( $user_meta[ $request_id ] ) ) {

			wp_send_json_error( esc_html__( "Request Not Found.", "cubewp-classified" ) );

		}

		unset( $user_meta[ $request_id ] );

		update_user_meta( $user_id, 'classified_rate_profile_requests', $user_meta );

		wp_send_json_success( esc_html__( "Request Deleted", "cubewp-classified" ) );

	}



	public function classified_remove_rate_request( $data, $post ) {

		$user_id    = classified_get_post_author( $post['post_id'] );

		$request_id = get_post_meta( $post['post_id'], 'cwp_review_associated_post_user', true );

		$user_meta  = get_user_meta( $user_id, 'classified_rate_profile_requests', true );

		$user_meta  = is_array( $user_meta ) && ! empty( $user_meta ) ? $user_meta : array();

		if ( isset( $user_meta[ $request_id ] ) ) {

	    	unset( $user_meta[ $request_id ] );

    		update_user_meta( $user_id, 'classified_rate_profile_requests', $user_meta );

        }



        return $data;

	}



	public function classified_dashboard_content( $output, $tab_detail, $tab_id ) {

		global $classified_post_types;

		$content_type = $tab_detail['content_type'] ?? '';

		$content      = $tab_detail['content'] ?? '';

		$user_id      = get_current_user_id();



		if ( $content_type == 'saved' ) {

			$savedPosts             = CubeWp_Saved::cubewp_get_saved_posts();

			$args['post__in']       = $savedPosts;

			$args['post_type']      = $classified_post_types;

			$args['post_status']    = 'publish';

			$args['posts_per_page'] = '-1';

			$posts                  = array();

			if ( ! empty( $savedPosts ) ) {

				$posts = get_posts( $args );

			}

			ob_start();

			?>

            <div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1><?php esc_html_e( "Saved Ads", "cubewp-classified" ); ?></h1>

                    <p><?php esc_html_e( "View and delete your saved ads here", "cubewp-classified" ); ?></p>

                </div>

				<?php echo self::classified_dashboard_header_items(); ?>

            </div>

            <div class="classified-dashboard-tab-content-heading-and-filters d-block d-md-flex justify-content-md-start align-items-md-center">

                <h4 class="classified-dashboard-tab-heading"><?php echo sprintf( esc_html__( "Total Ads: %s", "cubewp-classified" ), count( $posts ) ); ?></h4>

            </div>

			<?php if ( ! empty( $posts ) ) { ?>

                <div class="table-responsive pb-5">

                    <table class="classified-table-items-container">

                        <tr class="classified-table-items-heading">

                            <th class="ps-4"><?php esc_html_e( "Title & Description", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Price", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Condition", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Expire In", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Views", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Actions", "cubewp-classified" ); ?></th>

                        </tr>

						<?php

						global $cubewp_frontend;

						foreach ( $posts as $post ) {

							$post_id    = $post->ID;

							$post_metas = $cubewp_frontend->post_metas( $post_id );

							$item_price = $post_metas['classified_price']['meta_value'] ?? '';

							$post_type  = get_post_type( $post_id );

							if ( ! str_contains( $post_type, 'classified' ) ) {

								$condition_name = 'classified_' . str_replace( '-', '_', $post_type ) . '_condition';

							} else {

								$condition_name = str_replace( '-', '_', $post_type ) . '_condition';

							}

							$item_condition = $post_metas[ $condition_name ]['meta_value'] ?? '';

							$item_views     = classified_get_post_views( $post_id );

							$post_expiry    = classified_get_item_expiry( $post_id );

							?>

                            <tr class="classified-table-item">

                                <td class="d-flex justify-content-start align-items-center">

                                    <img loading="lazy" width="100%" height="100%"

                                         src="<?php echo classified_get_post_featured_image( $post_id ); ?>"

                                         alt="<?php echo get_the_title( $post_id ); ?>">

                                    <div>

                                        <h5><?php echo esc_html( $post->post_title ); ?></h5>

                                        <p><?php echo wp_trim_words( apply_filters( 'the_content', $post->post_content ), 10, '...' ); ?></p>

                                    </div>

                                </td>

                                <td><h6><?php echo classified_build_price( $item_price ); ?></h6></td>

                                <td><h6><?php echo esc_html( $item_condition ); ?></h6></td>

                                <td>

                                    <h6 class="classified-saved-item-expiry">

										<?php

										if ( $post_expiry == 'unlimited' ) {

											?>

                                            <span>

                                                <time><?php esc_html_e( 'Unlimited', 'cubewp-classified' ); ?></time>

                                            </span>

											<?php

										} else if ( $post_expiry == 'expired' ) {

											?>

                                            <span>

                                                <time><?php esc_html_e( 'Expired', 'cubewp-classified' ); ?></time>

                                            </span>

											<?php

										} else {

											?>

                                            <span class="classified-item-remaining-time"

                                                  data-current-date="<?php esc_attr_e( date( "F j, Y H:i", strtotime( "now" ) ) ) ?>"

                                                  data-end-date="<?php esc_attr_e( date( "F j, Y H:i", $post_expiry ) ) ?>">

                                                <i class="fa-regular fa-clock" aria-hidden="true"></i>

                                                <time>00:00:00</time>

                                            </span>

											<?php

										}

										?>

                                    </h6>

                                </td>

                                <td><h6><?php echo esc_html( $item_views ); ?></h6></td>

                                <td>

                                    <div class="btn-group">

                                        <button class="classified-filled-btn position-relative me-3">

                                            <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"

                                               class="stretched-link"></a>

											<?php esc_html_e( "View", "cubewp-classified" ) ?>

                                        </button>

                                        <button class="classified-saved-action cwp-main cwp-saved-post"

                                                data-pid="<?php echo esc_attr( $post->ID ); ?>" data-action="remove">

                                            <i class="fa-regular fa-trash-can" aria-hidden="true"></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

						<?php } ?>

                    </table>

                </div>

				<?php

			} else {

				?>

                <div class="cwp-empty-search">

                    <img loading="lazy" width="100%" height="100%" class="cwp-empty-search-img"

                         src="<?php echo CWP_PLUGIN_URI ?>cube/assets/frontend/images/no-result.png"

                         alt="<?php esc_html_e( 'No Results', 'cubewp-classified' ); ?>">

                    <h2><?php esc_html_e( 'No Posts Found', 'cubewp-classified' ) ?></h2>

                    <p><?php esc_html_e( 'There are no saved posts.', 'cubewp-classified' ) ?></p>

                </div>

				<?php

			}

			$return = ob_get_clean();

		} else if ( $content_type == 'post_types' && ( in_array( $content, $classified_post_types ) || $content == 'all_classified_post_types' ) ) {

			$posts_per_page = classified_get_setting( 'posts_per_page' );

			$paged          = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			if ( $content == 'all_classified_post_types' ) {

				$args['post_type'] = $classified_post_types;

			} else {

				$args['post_type'] = $content;

			}

			$args['post_status']    = array( 'publish', 'sold', 'pending', 'draft' );

			$args['posts_per_page'] = $posts_per_page;

			$args['paged']          = $paged;

			$args['author']         = get_current_user_id();

			$classified_query       = new Classified_Query();

			$query                  = $classified_query->Query( $args );

			ob_start();

			?>

            <div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1><?php esc_html_e( "My Ads", "cubewp-classified" ); ?></h1>

                    <p><?php esc_html_e( "You can View Edit manage and delete your ads here", "cubewp-classified" ); ?></p>

                </div>

				<?php echo self::classified_dashboard_header_items(); ?>

            </div>

            <div class="classified-dashboard-tab-content-heading-and-filters d-block d-md-flex justify-content-md-start align-items-md-center">

                <h4 class="classified-dashboard-tab-heading"><?php echo sprintf( esc_html__( "Total Ads: %s", "cubewp-classified" ), $query->found_posts ); ?></h4>

                <div class="classified-dashboard-tab-filters d-none justify-content-between justify-content-md-start flex-wrap align-items-center ms-0 ms-md-auto">

                    <div class="classified-dashboard-tab-filter ms-0 ms-md-4">

                        <label for="classified_filter_type"><?php esc_html_e( "Ad Type:", "cubewp-classified" ); ?></label>

                        <select name="classified_filter_type" id="classified_filter_type">

                            <option value="all_ads"><?php esc_html_e( "All Ads", "cubewp-classified" ); ?></option>

                        </select>

                    </div>

                </div>

            </div>

			<?php if ( $query->have_posts() ) {

				global $cubewp_frontend; ?>

                <div class="table-responsive pb-6">

                    <table class="classified-table-items-container">

                        <tr class="classified-table-items-heading">

                            <th class="ps-4"><?php esc_html_e( "Title", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Category", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Price", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Stats", "cubewp-classified" ); ?></th>

                            <th><?php esc_html_e( "Actions", "cubewp-classified" ); ?></th>

                        </tr>

						<?php while ( $query->have_posts() ) {

							$query->the_post();

							$post_id        = get_the_ID();

							$post_type      = get_post_type( $post_id );

							$post_metas     = array();

							$post_metas     = $cubewp_frontend->post_metas( $post_id );

							$price          = $post_metas['classified_price']['meta_value'] ?? '';

							$categories     = get_the_terms( $post_id, $post_type . '_category' );

							$published_date = sprintf( esc_html__( 'Submitted %s ago', 'cubewp-classified' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );

							$category_text  = '';

							if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {

								$counter = 1;

								$count   = count( $categories );

								foreach ( $categories as $category ) {

									$category_text .= '<a href="' . get_term_link( $category->term_id ) . '" target="_blank">';

									$style         = 'style="display: inline-block;"';

									if ( $counter > 2 ) {

										$style = 'style="display: none;"';

									}

									$category_text .= '<h6 ' . $style . '>';

									$category_text .= $category->name;

									if ( $counter != $count ) {

										$category_text .= ',&nbsp;';

									}

									$category_text .= '</h6>';

									$category_text .= '</a>';

									$counter ++;

								}

								if ( $count > 2 ) {

									$category_text .= '<a href="javascript:void(0);"><h6 class="classified-table-item-see-categories">' . esc_html__( "See All", "cubewp-classified" ) . '</h6></a>';

								}

							}

							$item_views = classified_get_post_views( $post_id );

							$item_leads = classified_get_post_leads( $post_id );

							?>

                            <tr class="classified-table-item">

                                <td class="classified-table-item-details">

                                    <div class="d-flex justify-content-start align-items-center">

                                        <img loading="lazy" width="100%" height="100%"

                                             src="<?php echo classified_get_post_featured_image( $post_id ); ?>"

                                             alt="<?php echo get_the_title( $post_id ); ?>">

                                        <div class="classified-table-item-details">

											<?php

											$post_expiry = classified_get_item_expiry( $post_id );

											if ( $post_expiry == 'pending' ) {

												$percent_color    = '--grey-700';

												$post_expiry_html = /** @land HTML */

													'<span>

                                                        <i class="fa-regular fa-clock" aria-hidden="true"></i>

                                                        <time>' . esc_html__( "Pending", "cubewp-classified" ) . '</time>

                                                    </span>';

												$percent_used     = 100;

											} else if ( $post_expiry == 'unlimited' ) {

												$percent_color    = '--green-700';

												$post_expiry_html = /** @land HTML */

													'<span>

                                                        <i class="fa-regular fa-clock" aria-hidden="true"></i>

                                                        <time>' . esc_html__( "Unlimited", "cubewp-classified" ) . '</time>

                                                    </span>';

												$percent_used     = 100;

											} else if ( $post_expiry == 'expired' ) {

												$percent_color    = '--red-700';

												$post_expiry_html = /** @land HTML */

													'<span>

                                                        <i class="fa-regular fa-clock" aria-hidden="true"></i>

                                                        <time>' . esc_html__( "Expired", "cubewp-classified" ) . '</time>

                                                    </span>';

												$percent_used     = 100;

											} else {

												$purchase_end  = $post_expiry;

												$now           = strtotime( date( 'F j, Y H:i:s' ) );

												$purchase_date = strtotime( 'now' );

												$percent_used  = ( ( $now - $purchase_date ) / ( $purchase_end - $purchase_date ) ) * 100;

												if ( $percent_used > 100 ) {

													$percent_used = 100;

												}

												if ( $percent_used < 60 ) {

													$percent_color = '--green-700';

												}

												if ( $percent_used < 80 && $percent_used >= 60 ) {

													$percent_color = '--orange-700';

												}

												if ( $percent_used >= 80 ) {

													$percent_color = '--red-700';

												}

												$post_expiry_html = /** @land HTML */

													'<span class="classified-item-remaining-time" data-current-date="' . esc_attr__( date( "F j, Y H:i", strtotime( "now" ) ) ) . '"

                                                      data-end-date="' . esc_attr__( date( "F j, Y H:i", $purchase_end ) ) . '">

                                                    <i class="fa-regular fa-clock" aria-hidden="true"></i>

                                                    <time>00:00:00</time>

                                                </span>';

											}

											?>

                                            <h5><?php echo esc_html( get_the_title( $post_id ) ); ?></h5>

                                            <p><?php echo esc_html( $published_date ); ?></p>

                                            <div class="classified-progress"

                                                 data-value="<?php echo esc_attr( $percent_used ); ?>%"

                                                 style="color: var(<?php echo esc_attr( $percent_color ); ?>)">

                                                <span></span>

                                            </div>

                                            <p class="d-flex align-items-center p-sm mb-0"

                                               style="color: var(<?php echo esc_attr( $percent_color ); ?>);">

                                                <b><?php echo esc_html__( "Expires in:", "cubewp-classified" ); ?></b>

												<?php echo( $post_expiry_html ); ?>

                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <td class="classified-table-item-categories"><?php echo( $category_text ); ?></td>

                                <td class="classified-table-item-price">

                                    <h6><?php echo classified_build_price( $price ) ?></h6></td>

                                <td class="classified-table-item-statistics">

                                    <div class="classified-table-item-stats">

                                        <div class="classified-table-item-stat">

                                            <div class="classified-table-item-stat-icon">

                                                <i class="fa-solid fa-eye" aria-hidden="true"></i>

                                            </div>

                                            <div class="classified-table-item-stat-info">

                                                <p class="p-sm"><?php esc_html_e( "Views", "cubewp-classified" ); ?></p>

                                                <h6><?php echo esc_html( $item_views ); ?></h6>

                                            </div>

                                        </div>

                                        <div class="classified-table-item-stat">

                                            <div class="classified-table-item-stat-icon">

                                                <i class="fa-solid fa-message" aria-hidden="true"></i>

                                            </div>

                                            <div class="classified-table-item-stat-info">

                                                <p class="p-sm"><?php esc_html_e( "Leads", "cubewp-classified" ); ?></p>

                                                <h6><?php echo esc_html( $item_leads ); ?></h6>

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td class="classified-table-item-actions">

									<?php echo classified_dashboard_items_actions( $post_id ); ?>

                                </td>

                            </tr>

						<?php } ?>

                    </table>

					<?php

					$big = 999999999; // need an unlikely integer

					$url = get_pagenum_link( $big );

					$url = explode( '?dashboard', $url );

					$url = add_query_arg( 'tab_id', $tab_id, $url[0] ?? '' );

					echo '<div class="classified-pagination">' . paginate_links( array(

							'base'      => str_replace( $big, '%#%', esc_url( $url ) ),

							'format'    => '?paged=%#%',

							'current'   => max( 1, get_query_var( 'paged' ) ),

							'total'     => $query->max_num_pages,

							'prev_next' => false

						) ) . '</div>'; ?>

                </div>

				<?php

			} else {

				?>

                <div class="cwp-empty-search">

                    <img loading="lazy" width="100%" height="100%" class="cwp-empty-search-img"

                         src="<?php echo CWP_PLUGIN_URI ?>cube/assets/frontend/images/no-result.png"

                         alt="<?php esc_html_e( 'No Results', 'cubewp-classified' ); ?>">

                    <h2><?php esc_html_e( 'No Posts Found!', 'cubewp-classified' ) ?></h2>

                    <p><?php esc_html_e( 'There are no posts associated.', 'cubewp-classified' ) ?></p>

                </div>

				<?php

			}

			wp_reset_postdata();

			wp_reset_query();

			$return = ob_get_clean();

		} else if ( $content_type == 'classified_main_dashboard' ) {

			$classified_query = new Classified_Query();

			// Active ADS

			$query      = $classified_query->Query( array(

				'post_status'    => array( 'publish' ),

				'posts_per_page' => '-1',

				'author'         => $user_id

			) );

			$active_ads = $query->post_count;

			wp_reset_postdata();

			wp_reset_query();

			// Expired ADS

			$expired_ads = 0;

			if ( classified_is_payment_active() ) {

				$query       = $classified_query->Query( array(

					'post_status'    => array( 'expired' ),

					'posts_per_page' => '-1',

					'author'         => $user_id

				) );

				$expired_ads = $query->post_count;

				wp_reset_postdata();

				wp_reset_query();

			}

			// Free ADS

			$query    = $classified_query->Query( array(

				'post_status'    => array( 'sold' ),

				'posts_per_page' => '-1',

				'author'         => $user_id

			) );

			$sold_ads = $query->post_count;

			wp_reset_postdata();

			wp_reset_query();

			// Premium ADS

			$premium_ads       = 0;

			$expiring_campaign = 0;

			if ( classified_is_booster_active() ) {

				$query       = $classified_query->Query( array(

					'post_type'      => array( 'cwp_booster' ),

					'post_status'    => array( 'active' ),

					'posts_per_page' => '-1',

					'author'         => $user_id,

					'is_archive'     => 'false'

				) );

				$premium_ads = $query->post_count;

				$query       = $classified_query->Query( array(

					'post_type'      => array( 'cwp_booster' ),

					'post_status'    => array( 'active' ),

					'posts_per_page' => '-1',

					'author'         => $user_id,

					'is_archive'     => 'false'

				) );

				if ( $query->have_posts() ) {

					while ( $query->have_posts() ) {

						$query->the_post();

						$campaign_id   = get_the_ID();

						$campaign_type = get_post_meta( $campaign_id, 'cwp_boost_type', true );

						$campaign_vol  = 0;

						if ( $campaign_type == 'ppc' ) {

							$campaign_vol = get_post_meta( $campaign_id, 'cwp_booster_budget', true );

						} else if ( $campaign_type == 'ppd' ) {

							$campaign_vol = get_post_meta( $campaign_id, 'cwp_booster_days', true );

						} else {

							continue;

						}

						$campaign_vol_remain = get_post_meta( $campaign_id, 'cwp_remaining_budget', true );

						if ( ! empty( $campaign_vol ) && ! empty( $campaign_vol_remain ) ) {

							$percentage = ( $campaign_vol_remain / $campaign_vol ) * 100;

							if ( $percentage <= 20 ) {

								$expiring_campaign += 1;

							}

						}

					}

				}

				wp_reset_postdata();

				wp_reset_query();

			}

			// ADS About To Expire

			$query        = $classified_query->Query( array(

				'meta_query' => array(

					array(

						'key'     => 'post_expired',

						'value'   => strtotime( '-1 week' ),

						'compare' => '>=',

						'type'    => 'NUMERIC'

					)

				)

			) );

			$expiring_ads = $query->post_count;

			wp_reset_postdata();

			wp_reset_query();

			// Unfinished ADS

			$unfinished_ads = get_user_meta( $user_id, 'classified_unfinished_ads', true );

			$unfinished_ads = ! empty( $unfinished_ads ) && is_array( $unfinished_ads ) ? $unfinished_ads : array();

			// Overall ads views

			$overall_views = get_user_meta( $user_id, 'classified_overall_ads_views', true );

			$overall_views = ! empty( $overall_views ) && is_numeric( $overall_views ) ? $overall_views : 0;

			// Overall ads leads

			$overall_leads = get_user_meta( $user_id, 'classified_overall_ads_leads', true );

			$overall_leads = ! empty( $overall_leads ) && is_numeric( $overall_leads ) ? $overall_leads : 0;

			// User Reviews

			$overall_reviews = 0;

			if ( classified_is_review_active() ) {

				$args            = array(

					'post_type'      => 'cwp_reviews',

					'post_status'    => 'publish',

					'posts_per_page' => - 1,

					'meta_query'     => array(

						'relation' => 'AND',

						array(

							'key'     => 'cwp_review_associated',

							'compare' => 'EXISTS'

						),

						array(

							'key'     => 'cwp_review_type',

							'value'   => 'user',

							'compare' => '='

						),

						array(

							'key'     => 'cwp_review_associated_post_user',

							'value'   => $user_id,

							'compare' => '='

						)

					)

				);

				$reviews         = new WP_Query( $args );

				$overall_reviews = $reviews->post_count;

				wp_reset_postdata();

				wp_reset_query();

			}

			// Profile completion

			$profile_completion       = get_user_meta( $user_id, 'classified_profile_completion_status', true );

			$profile_completion       = ! empty( $profile_completion ) && is_numeric( $profile_completion ) ? $profile_completion : 0;

			$profile_completion_color = '--red-700';

			if ( $profile_completion > 40 && $profile_completion < 75 ) {

				$profile_completion_color = '--orange-700';

			} else if ( $profile_completion >= 75 ) {

				$profile_completion_color = '--green-700';

			}



			ob_start();

			?>

            <div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1><?php esc_html_e( "Dashboard Overview", "cubewp-classified" ); ?></h1>

                    <p><?php echo sprintf( esc_html__( "Today, %s", "cubewp-classified" ), date_i18n( get_option( 'date_format' ) ) ); ?></p>

                </div>

				<?php echo self::classified_dashboard_header_items(); ?>

            </div>

            <div class="row">

                <div class="col-12 col-lg-9">

                    <div class="row">

                        <div class="col-6 col-lg-4 col-xl-3">

                            <div class="classified-dashboard-item-stat">

                                <div class="classified-dashboard-item-stat-icon">

                                    <i class="fa-solid fa-chart-line" aria-hidden="true"></i>

                                </div>

                                <div class="classified-dashboard-item-stat-text">

                                    <p><?php esc_html_e( "Active Ads", "cubewp-classified" ); ?></p>

                                    <h6><?php echo esc_html( $active_ads ); ?></h6>

                                </div>

                            </div>

                        </div>

                        <div class="col-6 col-lg-4 col-xl-3">

                            <div class="classified-dashboard-item-stat">

                                <div class="classified-dashboard-item-stat-icon">

                                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>

                                </div>

                                <div class="classified-dashboard-item-stat-text">

                                    <p><?php esc_html_e( "Expired Ads", "cubewp-classified" ); ?></p>

                                    <h6><?php echo esc_html( $expired_ads ); ?></h6>

                                </div>

                            </div>

                        </div>

                        <div class="col-6 col-lg-4 col-xl-3">

                            <div class="classified-dashboard-item-stat">

                                <div class="classified-dashboard-item-stat-icon">

                                    <i class="fa-solid fa-check" aria-hidden="true"></i>

                                </div>

                                <div class="classified-dashboard-item-stat-text">

                                    <p><?php esc_html_e( "Sold Ads", "cubewp-classified" ); ?></p>

                                    <h6><?php echo esc_html( $sold_ads ); ?></h6>

                                </div>

                            </div>

                        </div>

						<?php

						if ( classified_is_booster_active() ) {

							?>

                            <div class="col-6 col-lg-4 col-xl-3">

                                <div class="classified-dashboard-item-stat">

                                    <div class="classified-dashboard-item-stat-icon">

                                        <i class="fa-solid fa-gem" aria-hidden="true"></i>

                                    </div>

                                    <div class="classified-dashboard-item-stat-text">

                                        <p><?php esc_html_e( "Boosted Ads", "cubewp-classified" ); ?></p>

                                        <h6><?php echo esc_html( $premium_ads ); ?></h6>

                                    </div>

                                </div>

                            </div>

							<?php

						}

						?>

                    </div>

                    <div class="row">

                        <div class="col-12 col-lg-6 col-xl-4">

                            <div class="classified-dashboard-stat">

                                <div class="classified-dashboard-stat-icon" style="color: var(--orange-700)">

                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>

                                </div>

                                <div class="classified-dashboard-stat-text">

                                    <p class="p-md"

                                       style="color: var(--orange-700)"><?php esc_html_e( 'Total Views', 'cubewp-classified' ); ?></p>

                                    <h4><?php echo esc_html( $overall_views ); ?></h4>

                                </div>

                            </div>

                        </div>

                        <div class="col-12 col-lg-6 col-xl-4">

                            <div class="classified-dashboard-stat">

                                <div class="classified-dashboard-stat-icon" style="color: var(--green-700)">

                                    <i class="fa-solid fa-message" aria-hidden="true"></i>

                                </div>

                                <div class="classified-dashboard-stat-text">

                                    <p class="p-md"

                                       style="color: var(--green-700)"><?php esc_html_e( 'Total Leads', 'cubewp-classified' ); ?></p>

                                    <h4><?php echo esc_html( $overall_leads ); ?></h4>

                                </div>

                            </div>

                        </div>

                        <div class="col-12 col-lg-6 col-xl-4">

                            <div class="classified-dashboard-stat">

                                <div class="classified-dashboard-stat-icon" style="color: var(--red-700)">

                                    <i class="fa-regular fa-star" aria-hidden="true"></i>

                                </div>

                                <div class="classified-dashboard-stat-text">

                                    <p class="p-md"

                                       style="color: var(--red-700)"><?php esc_html_e( 'Total Reviews', 'cubewp-classified' ); ?></p>

                                    <h4><?php echo esc_html( $overall_reviews ); ?></h4>

                                </div>

                            </div>

                        </div>

                    </div>



					<?php

					ob_start();

					if ( ! empty( $unfinished_ads ) && is_array( $unfinished_ads ) ) {

						foreach ( $unfinished_ads as $post_id => $progress ) {

							if ( $progress > 90 ) {

								continue;

							}

							$post_type           = get_post_type( $post_id );

							$ad_submission       = classified_get_setting( 'submit_edit_page', 'page_url', $post_type );

							$edit_url            = add_query_arg( 'pid', $post_id, $ad_submission );

							$ad_completion_color = '--red-700';

							if ( $progress > 40 && $progress < 75 ) {

								$ad_completion_color = '--orange-700';

							} else if ( $progress >= 75 ) {

								$ad_completion_color = '--green-700';

							}

							?>

                            <div class="col-12 col-lg-6 col-xl-4 classified-dashboard-incomplete-item-card">

                                <div class="classified-dashboard-activity-card">

                                    <h5><?php esc_html_e( "Complete AD Details", "cubewp-classified" ); ?></h5>

                                    <p class="p-sm d-flex justify-content-between align-items-center">

                                        <a href="<?php echo get_post_permalink( $post_id ); ?>"><?php echo get_the_title( $post_id ); ?></a>

                                        <span><?php echo sprintf( esc_html__( "%s%s Completed", "cubewp-classified" ), $progress, '%' ); ?></span>

                                    </p>

                                    <div class="classified-progress"

                                         data-value="<?php echo esc_attr( $progress ); ?>%"

                                         style="color: var(<?php echo esc_attr( $ad_completion_color ) ?>)">

                                        <span></span></div>

                                    <p class="p-sm"><?php esc_html_e( "Complete this action to get more reach.", "cubewp-classified" ); ?></p>

                                    <button class="classified-filled-btn position-relative">

                                        <a href="<?php echo esc_url( $edit_url ); ?>"

                                           class="stretched-link"></a>

										<?php esc_html_e( "Complete Now", "cubewp-classified" ); ?>

                                    </button>

                                </div>

                            </div>

							<?php

						}

					}

					$rate_requests = get_user_meta( $user_id, 'classified_rate_profile_requests', true );

					$rate_requests = is_array( $rate_requests ) && ! empty( $rate_requests ) ? $rate_requests : array();

					if ( is_array( $rate_requests ) && ! empty( $rate_requests ) ) {

						foreach ( $rate_requests as $requested_by => $time ) {

							?>

                            <div class="col-12 col-lg-6 col-xl-4 classified-dashboard-rate-request-card">

                                <div class="classified-dashboard-activity-card">

                                    <div class="d-flex classified-dashboard-activity-card-user">

                                        <img loading="lazy" width="100%" height="100%"

                                             src="<?php echo classified_get_userdata( $requested_by, 'avatar' ); ?>"

                                             alt="<?php esc_html_e( "Avatar Of This User", "cubewp-classified" ); ?>">

                                        <p class="p-md">

											<?php echo sprintf( esc_html__( '%s Sent a request to you to rate about the item you have purchased or sell.', 'cubewp-classified' ), classified_get_userdata( $requested_by, 'short_name' ) ); ?>

                                        </p>

                                    </div>

                                    <p class="p-sm"><?php esc_html_e( 'If you can take out 2 minutes to share a review, it will help many customers like yourself make the right choice.', 'cubewp-classified' ); ?></p>

                                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                                        <button class="classified-filled-btn position-relative">

                                            <a href="<?php echo esc_url( classified_get_userdata( $requested_by, 'profile_link' ) ); ?>"

                                               class="stretched-link"></a>

											<?php esc_html_e( 'View And Rate Profile', 'cubewp-classified' ); ?>

                                        </button>

                                        <button class="classified-filled-btn classified-disabled-btn position-relative classified-ignore-rate-request"

                                                data-request-id="<?php echo esc_attr( $requested_by ); ?>">

											<?php esc_html_e( 'Ignore', 'cubewp-classified' ); ?>

                                        </button>

                                    </div>

                                </div>

                            </div>

							<?php

						}

					}

					if ( $profile_completion > 90 ) { ?>

                        <div class="col-12 col-lg-6 col-xl-4 classified-dashboard-incomplete-profile-card">

                            <div class="classified-dashboard-activity-card">

                                <div class="d-flex align-items-center classified-dashboard-activity-card-user">

                                    <img loading="lazy" width="100%" height="100%"

                                         src="<?php echo classified_get_userdata( $user_id, 'avatar' ); ?>"

                                         alt="<?php esc_attr_e( "Your Avatar", "cubewp-classified" ); ?>">

                                    <div class="classified-dashboard-activity-card-user-completed">

                                        <p><?php esc_html_e( "Profile completion", "cubewp-classified" ); ?></p>

                                        <h4><?php echo esc_attr( $profile_completion ) ?>%</h4>

                                    </div>

                                </div>

                                <div class="d-block mt-4">

                                    <div class="classified-progress"

                                         data-value="<?php echo esc_attr( $profile_completion ) ?>%"

                                         style="color: var(<?php echo esc_attr( $profile_completion_color ) ?>)">

                                        <span></span></div>

                                </div>

                                <button class="classified-not-filled-btn w-100 classified-complete-author-profile">

									<?php esc_html_e( "Complete Your Profile Now", "cubewp-classified" ); ?>

                                </button>

                            </div>

                        </div>

						<?php

					}

					if ( classified_is_booster_active() ) {

						$boosted_posts       = cubewp_boosted_posts( $classified_post_types );

						$boosted_posts       = ! empty( $boosted_posts ) && is_array( $boosted_posts ) ? $boosted_posts : array();

						$highest_reach_args  = array(

							'post_type'      => $classified_post_types,

							'post_status'    => array( 'publish' ),

							'meta_key'       => 'cubewp_post_views',

							'orderby'        => 'meta_value_num',

							'order'          => 'DESC',

							'author'         => get_current_user_id(),

							'posts_per_page' => 1, // only get the post with the highest views

							'post__not_in'   => $boosted_posts

						);

						$highest_reach_query = new WP_Query( $highest_reach_args );

						if ( $highest_reach_query->have_posts() ) {

							while ( $highest_reach_query->have_posts() ) {

								$highest_reach_query->the_post();

								$views = classified_get_post_views( get_the_ID() );

								$ppc_price = CubeWp_Booster_Fields::booster_type_price_per_click( get_post_type( get_the_ID() ) );

								$ppd_price = CubeWp_Booster_Fields::booster_type_price_per_day( get_post_type( get_the_ID() ) );

								$ppc_price = sprintf( esc_html__( 'Pay Per Click Price is $%.2f/per click', 'cubewp-classified' ), $ppc_price );

								$ppd_price = sprintf( esc_html__( 'Pay Per Day Price is $%.2f/per day', 'cubewp-classified' ), $ppd_price );

								?>

                                <div class="col-12 col-lg-6 col-xl-4 classified-dashboard-boost-item-card">

                                    <div class="classified-dashboard-activity-card">

                                        <h5><?php echo sprintf( esc_html__( '%s  is doing good', 'cubewp-classified' ), get_the_title() ) ?></h5>

                                        <p class="p-sm d-flex justify-content-between align-items-center">

                                            <a><?php esc_html_e( 'Reach', 'cubewp-classified' ); ?></a>

                                            <span><?php echo sprintf( esc_html__( '%s Views', 'cubewp-classified' ), $views ); ?></span>

                                        </p>

                                        <div class="classified-progress" data-value="65%"

                                             style="color: var(--orange-700)"><span></span></div>

                                        <p class="p-sm"><?php esc_html_e( 'Boost now and get even more reach', 'cubewp-classified' ); ?></p>

	                                    <button class="classified-filled-btn classified-dashboard-activity-card-gold-btn classified-boost-item"

                                                data-item-id="<?php echo get_the_ID(); ?>"

                                                data-ppc-desc="<?php echo esc_html( $ppc_price ); ?>"

                                                data-ppd-desc="<?php echo esc_html( $ppd_price ); ?>">

											<?php esc_html_e( 'Boost Now', 'cubewp-classified' ); ?>

                                        </button>

                                    </div>

                                </div>

								<?php

							}

						}

						wp_reset_postdata();

						wp_reset_query();

					}

					$dashboard_actions = ob_get_clean();

					if ( ! empty( $dashboard_actions ) ) {

						?>

                        <div class="classified-dashboard-activity-container">

                            <h5 class="mb-3"><?php esc_html_e( "Action's Required", "cubewp-classified" ); ?></h5>

                            <div class="row">

								<?php echo cubewp_core_data( $dashboard_actions ); ?>

                            </div>

                        </div>

						<?php

					}

					?>

                </div>

                <div class="col-12 col-lg-3">

                    <div class="classified-dashboard-content-sidebar">

                        <div class="classified-dashboard-content-sidebar-stat">

                            <div>

                                <h5><?php esc_html_e( "Expiring Ads", "cubewp-classified" ); ?></h5>

								<?php

								if ( $expiring_ads > 1 ) {

									?>

                                    <p class="p-md"><?php echo sprintf( esc_html__( "There are %s ads about to expire", "cubewp-classified" ), $expiring_ads ); ?></p>

									<?php

								} else if ( $expiring_ads == 1 ) {

									?>

                                    <p class="p-md"><?php echo sprintf( esc_html__( "There is %s ad about to expire", "cubewp-classified" ), $expiring_ads ); ?></p>

									<?php

								} else {

									?>

                                    <p class="p-md"><?php esc_html_e( "There is no ad about to expire", "cubewp-classified" ); ?></p>

									<?php

								}

								?>

                            </div>

                            <h4><?php echo esc_html( $expiring_ads ); ?></h4>

                        </div>

                        <div class="classified-dashboard-content-sidebar-stat">

                            <div>

                                <h5><?php esc_html_e( "Expiring Promotions", "cubewp-classified" ); ?></h5>

								<?php

								if ( $expiring_campaign > 1 ) {

									?>

                                    <p class="p-md"><?php echo sprintf( esc_html__( "There are %s campaings about to expire", "cubewp-classified" ), $expiring_campaign ); ?></p>

									<?php

								} else if ( $expiring_campaign == 1 ) {

									?>

                                    <p class="p-md"><?php echo sprintf( esc_html__( "There is %s campaing about to expire", "cubewp-classified" ), $expiring_campaign ); ?></p>

									<?php

								} else {

									?>

                                    <p class="p-md"><?php esc_html_e( "There is no campaing about to expire", "cubewp-classified" ); ?></p>

									<?php

								}

								?>

                            </div>

                            <h4><?php echo esc_html( $expiring_campaign ); ?></h4>

                        </div>

                        <div class="classified-dashboard-content-sidebar-stat d-none">

                            <div>

                                <h5><?php esc_html_e( "Expiring Packages", "cubewp-classified" ); ?></h5>

                                <p class="p-md"><?php esc_html_e( "Your packages are about to expire", "cubewp-classified" ); ?></p>

                            </div>

                            <h4>02</h4>

                            <div class="w-100">

                                <div class="classified-dashboard-content-sidebar-stat-details">

                                    <div>

                                        <h6><?php esc_html_e( 'Package Name', 'cubewp-classified' ); ?></h6>

                                        <p class="p-md"><?php esc_html_e( "Expire in", "cubewp-classified" ) ?>

                                            <span style="color: var(--secondary-color)">10:15:05</span>

                                        </p>

                                    </div>

                                    <button class="classified-not-filled-btn mt-2 mt-sm-0"><?php esc_html_e( "Renew", "cubewp-classified" ) ?></button>

                                </div>

                                <div class="classified-dashboard-content-sidebar-stat-details">

                                    <div>

                                        <h6><?php esc_html_e( 'Package Name', 'cubewp-classified' ); ?></h6>

                                        <p class="p-md"><?php esc_html_e( "Expire in", "cubewp-classified" ) ?>

                                            <span style="color: var(--secondary-color)">10:15:05</span>

                                        </p>

                                    </div>

                                    <button class="classified-not-filled-btn mt-2 mt-sm-0"><?php esc_html_e( "Renew", "cubewp-classified" ) ?></button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

			<?php

			$return = ob_get_clean();

		} else if ( $content_type == 'classified_user_profile' ) {

			ob_start();

			?>

            <div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1><?php esc_html_e( "User Profile", "cubewp-classified" ); ?></h1>

                    <p><?php esc_html_e( "View and edit your profile here", "cubewp-classified" ); ?></p>

                </div>

				<?php echo self::classified_dashboard_header_items(); ?>

            </div>

			<?php

			set_query_var( 'user_id', get_current_user_id() );

			set_query_var( 'tab_id', $tab_id );

			set_query_var( 'author-style', 'author-style-2' );

			get_template_part( 'templates/author/author-views' );



			$return = ob_get_clean();

		} else if ( $content_type == 'classified_plans' ) {

			ob_start();

			$purchased_plans = classified_get_user_purchased_plans();

			?>

            <div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1><?php esc_html_e( "Pricing and Packages", "cubewp-classified" ); ?></h1>

                    <p><?php esc_html_e( "View and change your packages here", "cubewp-classified" ); ?></p>

                </div>

				<?php echo self::classified_dashboard_header_items(); ?>

            </div>

            <div class="classified-purchased-plans">

				<?php

				if ( ! empty( $purchased_plans ) && is_array( $purchased_plans ) ) {

					?>

                    <h5 class="classified-dashboard-tab-heading"><?php esc_html_e( "Purchased Plans", "cubewp-classified" ); ?></h5>

                    <div class="table-responsive">

                        <table class="classified-purchased-plans-container">

                            <tr class="classified-purchased-plans-heading">

                                <th class="ps-4"><?php esc_html_e( "Plan", "cubewp-classified" ); ?></th>

                                <th><?php esc_html_e( "# Of Ads", "cubewp-classified" ); ?></th>

                                <th><?php esc_html_e( "Duration", "cubewp-classified" ); ?></th>

                                <th><?php esc_html_e( "Ads Type", "cubewp-classified" ); ?></th>

                                <th><?php esc_html_e( "Action", "cubewp-classified" ); ?></th>

                            </tr>

							<?php

							foreach ( $purchased_plans as $purchased_plan ) {

								$order_id      = $purchased_plan->ID ?? 0;

								$plan_id       = $purchased_plan->planID ?? 0;

								$plan_type     = $purchased_plan->planType ?? '';

								$post_type     = get_post_meta( $plan_id, 'plan_post_type', true );

                                $post_type_obj = get_post_type_object( $post_type );

								$no_of_posts   = get_post_meta( $plan_id, 'no_of_posts', true );

								$plan_duration = cwp_plan_duration( $plan_id );

								$percent_age   = 100;

								$remaining_ads = count( classified_get_package_posts( $order_id ) );

								$remaining_ads = (int) $no_of_posts - $remaining_ads;

								$remaining_ads = $remaining_ads > 0 ? $remaining_ads : 1;

								if ( $plan_type == 'package' ) {

									if ( ! empty( $no_of_posts ) ) {

										$percent_age = $remaining_ads / $no_of_posts * 100;

									}

								}

								$percent_age = 100 - $percent_age;

								if ( $percent_age < 51 ) {

									$css_var = '--green-700';

								} else if ( $percent_age < 70 ) {

									$css_var = '--orange-700';

								} else {

									$css_var = '--red-700';

								}

								?>

                                <tr class="classified-purchased-plan">

                                    <td class="ps-4">

                                        <div class="classified-purchased-plan-details">

                                            <h5><?php echo get_the_title( $plan_id ); ?></h5>

											<?php

											if ( $plan_type == 'package' ) {

												$ad_text = esc_html__( 'Ad' );

												if ( $remaining_ads > 1 ) {

													$ad_text = esc_html__( 'Ads' );

												}

												?>

                                                <div class="classified-progress"

                                                     data-value="<?php echo esc_attr( $percent_age ); ?>%"

                                                     style="color: var(<?php echo esc_attr( $css_var ); ?>)">

                                                    <span></span>

                                                </div>

                                                <p><?php echo sprintf( esc_html__( '%s %s Left', 'cubewp-classified' ), $remaining_ads, $ad_text ); ?></p>

												<?php

											}

											?>

                                        </div>

                                    </td>

                                    <td>

                                        <h6>

                                            <span><?php echo esc_html( $remaining_ads ); ?></span><?php

											if ( ! empty( $no_of_posts ) ) {

												echo '/' . esc_html( $no_of_posts );

											}

											?>

                                        </h6>

                                    </td>

                                    <td>

                                        <h6>

                                            <?php

                                            if ( ! empty( $plan_duration ) ) {

	                                            echo sprintf( esc_html__( '%s Days', 'cubewp-classified' ), $plan_duration );

                                            }else {

	                                            esc_html_e( 'Unlimited', 'cubewp-classified' );

                                            }

                                            ?>

                                        </h6>

                                    </td>

                                    <td>

                                        <h6>

                                            <?php

                                                if ( ! empty( $post_type_obj ) ) {

                                                    echo esc_html( $post_type_obj->label );

                                                }

                                            ?>

                                        </h6>

                                    </td>

                                    <td>

                                        <?php

                                        global $cwpOptions;

                                        ?>

                                        <form action="https://classified-dev-2.local/general-ads/">

                                            <input type="hidden" name="plan_id" value="<?php echo isset( $cwpOptions['submit_edit_page'][ $post_type ] ) ? $cwpOptions['submit_edit_page'][ $post_type ] : ''; ?>">

                                            <input type="hidden" name="type" value="<?php echo esc_html( $post_type ); ?>">

                                            <button class="classified-filled-btn classified-filled-red">

		                                        <?php esc_html_e( 'Submit Ad Now', 'cubewp-classified' ); ?>

                                            </button>

                                        </form>

                                    </td>

                                </tr>

								<?php

							}

							?>

                        </table>

                    </div>

					<?php

				} else {

					?>

                    <h5 class="classified-dashboard-tab-heading"><?php esc_html_e( "No Purchased Plan Found.", "cubewp-classified" ); ?></h5>

					<?php

				}

				?>

            </div>

            <div class="classified-plans-container">

				<?php

				echo do_shortcode( '[cwpPricingPlans]' );

				?>

            </div>

			<?php

			$return = ob_get_clean();

		} else if ( $content_type == 'classified_inbox' ) {

			ob_start();

			?>

            <div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1><?php esc_html_e( "Conversations", "cubewp-classified" ); ?></h1>

                    <p><?php esc_html_e( "View all conversations here", "cubewp-classified" ); ?></p>

                </div>

				<?php echo self::classified_dashboard_header_items(); ?>

            </div>

            <div class="classified-inbox-container">

				<?php

				echo do_shortcode( '[cwpInbox]' );

				?>

            </div>

			<?php



			$return = ob_get_clean();

		} else if ( $content_type == 'classified_sales' ) {

			ob_start();

			?>

            <div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1><?php esc_html_e( "Sales", "cubewp-classified" ); ?></h1>

                    <p><?php esc_html_e( "View all received orders here", "cubewp-classified" ); ?></p>

                </div>

				<?php echo self::classified_dashboard_header_items(); ?>

            </div>

            <div class="classified-orders-container">

				<?php

				if ( class_exists( 'WooCommerce' ) ) {

					$show_invoice = false;

					if ( isset( $_GET['order_id'] ) && ! empty( $_GET['order_id'] ) ) {

						$order = wc_get_order( sanitize_text_field( $_GET['order_id'] ) );

						if ( ! empty( $order ) && is_object( $order ) ) {

							$show_invoice = true;

						}

					}

					if ( $show_invoice ) {

						$order_id = sanitize_text_field( $_GET['order_id'] );

						$view_url = cubewp_current_url( array( 'tab_id' => $tab_id ) );

						$view_url = remove_query_arg( "order_id", $view_url );

						?>

                        <div class="woocommerce woocommerce-page">

                            <a class="button"

                               href="<?php echo esc_url( $view_url ); ?>"><?php esc_html_e( "View All Orders", "cubewp-classified" ); ?></a>

							<?php

							$order        = wc_get_order( $order_id );

							$status       = new stdClass();

							$status->name = wc_get_order_status_name( $order->get_status() );

							wc_get_template( 'myaccount/view-order.php', array(

								'status'   => $status, // @deprecated 2.2.

								'order'    => $order,

								'order_id' => $order->get_id(),

							) );

							?>

                        </div>

						<?php

					} else {

						$orders = wc_get_orders( array(

							'limit'              => - 1,

							'_classified_author' => $user_id,

							'return'             => 'ids',

						) );

						if ( ! empty( $orders ) && is_array( $orders ) ) {

							?>

                            <div class="table-responsive pb-6">

                                <table class="classified-table-items-container">

                                    <thead>

                                    <tr class="classified-table-items-heading">

                                        <th class="ps-4"><?php echo esc_html__( "Item Details", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Transaction ID", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Placed At", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Price", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Status", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Actions", "cubewp-classified" ) ?></th>

                                    </tr>

                                    </thead>

                                    <tbody>

									<?php

									foreach ( $orders as $orderID ) {

										$order    = wc_get_order( $orderID );

										$view_url = cubewp_current_url( array(

											'tab_id'   => $tab_id,

											'order_id' => $order->get_id()

										) );

										$post_id  = false;

										foreach ( $order->get_items() as $item ) {

											$product_data = $item->get_data();

											$product_id   = $product_data['product_id'] ?? false;

											if ( $product_id ) {

												if ( get_post_meta( $product_id, '_is_classified_item', true ) ) {

													$post_id = get_post_meta( $product_id, '_classified_item', true );

													break;

												}

											}

										}

										?>

                                        <tr class="classified-table-item">

											<?php

											if ( $post_id ) {

												?>

                                                <td class="position-relative d-flex justify-content-start align-items-center">

                                                    <img loading="lazy" width="100%" height="100%"

                                                         src="<?php echo classified_get_post_featured_image( $post_id ); ?>"

                                                         alt="<?php echo get_the_title( $post_id ); ?>">

                                                    <div>

                                                        <h5><?php echo get_the_title( $post_id ); ?></h5>

                                                        <p><?php echo wp_trim_words( apply_filters( 'the_content', get_the_content( '', '', $post_id ) ), 10, '...' ); ?></p>

                                                    </div>

                                                    <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"

                                                       class="stretched-link" target="_blank"></a>

                                                </td>

												<?php

											} else {

												?>

                                                <td class="d-flex justify-content-start align-items-center">

                                                    <img loading="lazy" width="100%" height="100%"

                                                         src="<?php echo classified_get_post_featured_image(); ?>"

                                                         alt="<?php esc_html_e( 'Item Not Available', 'cubewp-classified' ); ?>">

                                                    <div>

                                                        <h5><?php esc_html_e( 'Item Not Available', 'cubewp-classified' ); ?></h5>

                                                        <p><?php esc_html_e( 'Oops! Items details not available. Perhaps it was deleted?', 'cubewp-classified' ); ?></p>

                                                    </div>

                                                </td>

												<?php

											}

											?>

                                            <td><?php echo sprintf( esc_html__( '#%s', 'cubewp-classified' ), $order->get_id() ) ?></td>

                                            <td><?php echo esc_html( $order->get_date_created()->date_i18n( get_option( "date_format" ) ) ); ?></td>

                                            <td><?php echo esc_html( classified_build_price( $order->get_total() ) ); ?></td>

											<?php

											$is_disputed = false;

											$is_refunded = false;

											if ( classified_is_wallet_active() ) {

												$wallet = CubeWp_Wallet_Processor::get_wallet_transactions_by( 'order_id', $orderID );

												if ( ! empty( $wallet ) && is_array( $wallet ) ) {

													$wallet = $wallet[0];

													$status = $wallet['status'];

													if ( $status == 'disputed' ) {

														$is_disputed = true;

													} else if ( $status == 'refunded' ) {

														$is_refunded = true;

													}

												}

											}

											if ( $is_disputed ) {

												?>

                                                <td class="text-uppercase"><?php esc_html_e( 'Disputed', 'cubewp-classified' ) ?></td>

												<?php

											} else {

												if ( $order->get_payment_method() == 'cod' && ! $is_refunded ) {

													?>

                                                    <td class="text-uppercase">

														<?php esc_html_e( 'COD', 'cubewp-classified' ) ?>

                                                        <i class="fa-solid fa-info-circle ms-2" aria-hidden="true"

                                                           data-classified-tooltip="true" data-bs-placement="right"

                                                           title="<?php esc_html_e( 'Can\'t Process Onsite', 'cubewp-classified' ) ?>"></i>

                                                    </td>

													<?php

												} else if ( $is_refunded ) {

													?>

                                                    <td class="text-uppercase">

														<?php esc_html_e( 'Refunded', 'cubewp-classified' ) ?>

                                                        <i class="fa-solid fa-info-circle ms-2" aria-hidden="true"

                                                           data-classified-tooltip="true" data-bs-placement="right"

                                                           title="<?php esc_html_e( 'Dispute Approved', 'cubewp-classified' ) ?>"></i>

                                                    </td>

													<?php

												} else {

													?>

                                                    <td class="text-uppercase"><?php echo esc_html( $order->get_status() ); ?></td>

													<?php

												}

											}

											?>

                                            <td>

												<?php

												if ( $order->get_payment_method() == 'cod' || $is_refunded ) {

													?>

                                                    <button class="classified-not-filled-btn position-relative px-3">

                                                        <i class="fa-solid fa-eye me-1" aria-hidden="true"></i>

														<?php esc_html_e( 'View Details', 'cubewp-classified' ); ?>

                                                        <a href="<?php echo esc_url( esc_url( $view_url ) ); ?>"

                                                           class="stretched-link"></a>

                                                    </button>

													<?php

												} else {

													if ( $order->get_status() == 'processing' ) {

														?>

                                                        <button class="classified-filled-btn classified-make-order-action px-3"

                                                                data-order-id="<?php echo esc_attr( $order->get_id() ); ?>"

                                                                data-action-type="shipped">

                                                            <i class="fa-solid fa-truck-ramp-box me-1" aria-hidden="true"></i>

															<?php esc_html_e( 'Mark As Shipped', 'cubewp-classified' ); ?>

                                                        </button>

                                                        <div class="ps-3 d-inline-flex justify-content-center align-items-center classified-dropdown"

                                                             type="button">

                                                            <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>

                                                            <div class="classified-dropdown-items drop-left have-indicator">

                                                                <p class="classified-dropdown-item">

                                                                    <a href="<?php echo esc_url( esc_url( $view_url ) ); ?>">

                                                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>

																		<?php esc_html_e( 'View Details', 'cubewp-classified' ); ?>

                                                                    </a>

                                                                </p>

                                                            </div>

                                                        </div>

														<?php

													} else {

														?>

                                                        <button class="classified-not-filled-btn position-relative px-3">

                                                            <i class="fa-solid fa-eye me-1" aria-hidden="true"></i>

															<?php esc_html_e( 'View Details', 'cubewp-classified' ); ?>

                                                            <a href="<?php echo esc_url( esc_url( $view_url ) ); ?>"

                                                               class="stretched-link"></a>

                                                        </button>

														<?php

														if ( classified_is_wallet_active() ) {

															$wallet = CubeWp_Wallet_Processor::get_wallet_transactions_by( 'order_id', $orderID );

															if ( ! empty( $wallet ) && is_array( $wallet ) ) {

																$wallet = $wallet[0];

																$status = $wallet['status'];

																if ( $status == 'on-hold' ) {

																	$wallet_data = maybe_unserialize( $wallet['data'] );

																	$hold_period = $wallet_data['hold_period'];

																	$release_on  = strtotime( $wallet['created_at'] . " +$hold_period days" );

																	$now         = strtotime( 'NOW' );

																	if ( $release_on >= $now ) {

																		?>

                                                                        <p class="mt-1">

																			<?php

																			esc_html_e( 'Funds added into wallet and currently on hold until', 'cubewp-classified' );

																			echo '&nbsp;<b>';

																			echo date_i18n( get_option( 'date_format' ), $release_on );

																			echo '</b>';

																			?>

                                                                        </p>

																		<?php

																	}

																}

															}

														}

													}

												}

												?>

                                            </td>

                                        </tr>

										<?php

									}

									?>

                                    </tbody>

                                </table>

                            </div>

							<?php

						} else {

							?>

                            <div class="cwp-empty-search">

                                <img loading="lazy" width="100%" height="100%" class="cwp-empty-search-img"

                                     src="<?php echo CWP_PLUGIN_URI ?>cube/assets/frontend/images/no-result.png"

                                     alt="<?php esc_html_e( 'No Results', 'cubewp-classified' ); ?>">

                                <h2><?php esc_html_e( 'No Purchase History Found', 'cubewp-classified' ) ?></h2>

                            </div>

							<?php

						}

					}

				}

				?>

            </div>

			<?php

			$return = ob_get_clean();

		} else if ( $content_type == 'classified_purchased_history' ) {

			ob_start();

			?>

            <div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1><?php esc_html_e( "Purchase History", "cubewp-classified" ); ?></h1>

                    <p><?php esc_html_e( "View all purchases here", "cubewp-classified" ); ?></p>

                </div>

				<?php echo self::classified_dashboard_header_items(); ?>

            </div>

            <div class="classified-orders-container">

				<?php

				if ( class_exists( 'WooCommerce' ) ) {

					$show_invoice = false;

					if ( isset( $_GET['order_id'] ) && ! empty( $_GET['order_id'] ) ) {

						$order = wc_get_order( sanitize_text_field( $_GET['order_id'] ) );

						if ( ! empty( $order ) && is_object( $order ) ) {

							$show_invoice = true;

						}

					}

					if ( $show_invoice ) {

						$order_id = sanitize_text_field( $_GET['order_id'] );

						$view_url = cubewp_current_url( array( 'tab_id' => $tab_id ) );

						$view_url = remove_query_arg( "order_id", $view_url );

						?>

                        <div class="woocommerce woocommerce-page">

                            <a class="button"

                               href="<?php echo esc_url( $view_url ); ?>"><?php esc_html_e( "View All Orders", "cubewp-classified" ); ?></a>

							<?php

							$order        = wc_get_order( $order_id );

							$status       = new stdClass();

							$status->name = wc_get_order_status_name( $order->get_status() );

							wc_get_template( 'myaccount/view-order.php', array(

								'status'   => $status, // @deprecated 2.2.

								'order'    => $order,

								'order_id' => $order->get_id(),

							) );

							?>

                        </div>

						<?php

					} else {

						$orders = wc_get_orders( array(

							'limit'       => - 1,

							'customer_id' => $user_id,

							'return'      => 'ids',

						) );

						if ( ! empty( $orders ) && is_array( $orders ) ) {

							?>

                            <div class="table-responsive pb-6">

                                <table class="classified-table-items-container">

                                    <thead>

                                    <tr class="classified-table-items-heading">

                                        <th class="ps-4"><?php echo esc_html__( "Item Details", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Transaction ID", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Placed At", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Price", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Tracking", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Status", "cubewp-classified" ) ?></th>

                                        <th><?php echo esc_html__( "Actions", "cubewp-classified" ) ?></th>

                                    </tr>

                                    </thead>

                                    <tbody>

									<?php

									foreach ( $orders as $orderID ) {

										$order    = wc_get_order( $orderID );

										$view_url = cubewp_current_url( array(

											'tab_id'   => $tab_id,

											'order_id' => $order->get_id()

										) );

										$post_id  = false;

										foreach ( $order->get_items() as $item ) {

											$product_data = $item->get_data();

											$product_id   = $product_data['product_id'] ?? false;

											if ( $product_id ) {

												if ( get_post_meta( $product_id, '_is_classified_item', true ) ) {

													$post_id = get_post_meta( $product_id, '_classified_item', true );

													break;

												}

											}

										}

										?>

                                        <tr class="classified-table-item">

											<?php

											if ( $post_id ) {

												?>

                                                <td class="position-relative d-flex justify-content-start align-items-center">

                                                    <img loading="lazy" width="100%" height="100%"

                                                         src="<?php echo classified_get_post_featured_image( $post_id ); ?>"

                                                         alt="<?php echo get_the_title( $post_id ); ?>">

                                                    <div>

                                                        <h5><?php echo get_the_title( $post_id ); ?></h5>

                                                        <p><?php echo wp_trim_words( apply_filters( 'the_content', get_the_content( '', '', $post_id ) ), 10, '...' ); ?></p>

                                                    </div>

                                                    <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"

                                                       class="stretched-link" target="_blank"></a>

                                                </td>

												<?php

											} else {

												?>

                                                <td class="d-flex justify-content-start align-items-center">

                                                    <img loading="lazy" width="100%" height="100%"

                                                         src="<?php echo classified_get_post_featured_image(); ?>"

                                                         alt="<?php esc_html_e( 'Item Not Available', 'cubewp-classified' ); ?>">

                                                    <div>

                                                        <h5><?php esc_html_e( 'Item Not Available', 'cubewp-classified' ); ?></h5>

                                                        <p><?php esc_html_e( 'Oops! Items details not available. Perhaps it was deleted?', 'cubewp-classified' ); ?></p>

                                                    </div>

                                                </td>

												<?php

											}

											?>

                                            <td><?php echo sprintf( esc_html__( '#%s', 'cubewp-classified' ), $order->get_id() ) ?></td>

                                            <td><?php echo esc_html( $order->get_date_created()->date_i18n( get_option( "date_format" ) ) ); ?></td>

                                            <td><?php echo esc_html( classified_build_price( $order->get_total() ) ); ?></td>

                                            <td>

												<?php

												$tracking_info = $order->get_meta( '_classified_tracking_details' );

												if ( ! empty( $tracking_info ) ) {

													echo esc_html( $tracking_info );

												} else {

													esc_html_e( 'Information Not Provided Yet', 'cubewp-classified' );

												}

												?>

                                            </td>

											<?php

											$is_disputed        = false;

											$is_refunded        = false;

											$can_create_dispute = false;

											if ( classified_is_wallet_active() ) {

												$wallet = CubeWp_Wallet_Processor::get_wallet_transactions_by( 'order_id', $orderID );

												if ( ! empty( $wallet ) && is_array( $wallet ) ) {

													$wallet = $wallet[0];

													$status = $wallet['status'];

													if ( $status == 'on-hold' ) {

														$wallet_data = maybe_unserialize( $wallet['data'] );

														$hold_period = $wallet_data['hold_period'];

														$release_on  = strtotime( $wallet['created_at'] . " +$hold_period days" );

														$now         = strtotime( 'NOW' );

														if ( $release_on >= $now ) {

															$can_create_dispute = true;

														}

													} else if ( $status == 'disputed' ) {

														$is_disputed = true;

													} else if ( $status == 'refunded' ) {

														$is_refunded = true;

													}

												}

											}

											if ( $is_disputed ) {

												?>

                                                <td class="text-uppercase"><?php esc_html_e( 'Disputed', 'cubewp-classified' ) ?></td>

												<?php

											}else if ( $is_refunded ) {

												?>

                                                <td class="text-uppercase">

													<?php esc_html_e( 'Refunded', 'cubewp-classified' ) ?>

                                                    <i class="fa-solid fa-info-circle ms-2" aria-hidden="true"

                                                       data-classified-tooltip="true" data-bs-placement="right"

                                                       title="<?php esc_html_e( 'Dispute Approved', 'cubewp-classified' ) ?>"></i>

                                                </td>

												<?php

											} else {

												if ( $order->get_payment_method() == 'cod' ) {

													?>

                                                    <td class="text-uppercase">

														<?php esc_html_e( 'COD', 'cubewp-classified' ) ?>

                                                        <i class="fa-solid fa-info-circle ms-2" aria-hidden="true"

                                                           data-classified-tooltip="true" data-bs-placement="right"

                                                           title="<?php esc_html_e( 'Can\'t Process Onsite', 'cubewp-classified' ) ?>"></i>

                                                    </td>

													<?php

												} else {

													?>

                                                    <td class="text-uppercase"><?php echo esc_html( $order->get_status() ); ?></td>

													<?php

												}

											}

											?>

                                            <td>

												<?php

												if ( $order->get_payment_method() == 'cod' || $is_refunded ) {

													?>

                                                    <button class="classified-not-filled-btn position-relative px-3">

                                                        <i class="fa-solid fa-eye me-1" aria-hidden="true"></i>

														<?php esc_html_e( 'View Details', 'cubewp-classified' ); ?>

                                                        <a href="<?php echo esc_url( esc_url( $view_url ) ); ?>"

                                                           class="stretched-link"></a>

                                                    </button>

													<?php

												} else {

													if ( $order->get_status() == 'shipped' && ! $is_disputed ) {

														?>

                                                        <button class="classified-filled-btn classified-make-order-action px-3"

                                                                data-order-id="<?php echo esc_attr( $order->get_id() ); ?>"

                                                                data-action-type="received">

                                                            <i class="fa-solid fa-box me-1" aria-hidden="true"></i>

															<?php esc_html_e( 'Mark As Received', 'cubewp-classified' ); ?>

                                                        </button>

                                                        <div class="ps-3 d-inline-flex justify-content-center align-items-center classified-dropdown"

                                                             type="button">

                                                            <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>

                                                            <div class="classified-dropdown-items drop-left have-indicator">

                                                                <?php

																if ( $can_create_dispute ) {

																	?>

		                                                            <p class="classified-dropdown-item classified-make-order-action"

	                                                                   data-order-id="<?php echo esc_attr( $order->get_id() ); ?>"

	                                                                   data-action-type="dispute">

	                                                                    <i class="fa-regular fa-circle-xmark" aria-hidden="true"></i>

																		<?php esc_html_e( 'Create Dispute', 'cubewp-classified' ); ?>

	                                                                </p>

																	<?php

																}

                                                                ?>

                                                                <p class="classified-dropdown-item">

                                                                    <a href="<?php echo esc_url( esc_url( $view_url ) ); ?>">

                                                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>

																		<?php esc_html_e( 'View Details', 'cubewp-classified' ); ?>

                                                                    </a>

                                                                </p>

                                                            </div>

                                                        </div>

														<?php

													} else if ( $can_create_dispute ) {

														?>

                                                        <button class="classified-not-filled-btn classified-make-order-action px-3"

                                                                data-order-id="<?php echo esc_attr( $order->get_id() ); ?>"

                                                                data-action-type="dispute">

                                                            <i class="fa-solid fa-circle-xmark me-1" aria-hidden="true"></i>

															<?php esc_html_e( 'Create Dispute', 'cubewp-classified' ); ?>

                                                        </button>

                                                        <div class="ps-3 d-inline-flex justify-content-center align-items-center classified-dropdown"

                                                             type="button">

                                                            <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>

                                                            <div class="classified-dropdown-items drop-left have-indicator">

                                                                <p class="classified-dropdown-item">

                                                                    <a href="<?php echo esc_url( esc_url( $view_url ) ); ?>">

                                                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>

																		<?php esc_html_e( 'View Details', 'cubewp-classified' ); ?>

                                                                    </a>

                                                                </p>

                                                            </div>

                                                        </div>

														<?php

													} else {

														?>

                                                        <button class="classified-not-filled-btn position-relative px-3">

                                                            <i class="fa-solid fa-eye me-1" aria-hidden="true"></i>

															<?php esc_html_e( 'View Details', 'cubewp-classified' ); ?>

                                                            <a href="<?php echo esc_url( esc_url( $view_url ) ); ?>"

                                                               class="stretched-link"></a>

                                                        </button>

														<?php

													}

													if ( $can_create_dispute ) {

														?>

                                                        <p class="w-100 mt-1">

															<?php

															esc_html_e( 'You can create dispute until', 'cubewp-classified' );

															echo '<br><b>';

															echo date_i18n( get_option( 'date_format' ), $release_on );

															echo '</b>';

															?>

                                                        </p>

														<?php

													}

												}

												?>

                                            </td>

                                        </tr>

										<?php

									}

									?>

                                    </tbody>

                                </table>

                            </div>

							<?php

						} else {

							?>

                            <div class="cwp-empty-search">

                                <img loading="lazy" width="100%" height="100%" class="cwp-empty-search-img"

                                     src="<?php echo CWP_PLUGIN_URI ?>cube/assets/frontend/images/no-result.png"

                                     alt="<?php esc_html_e( 'No Results', 'cubewp-classified' ); ?>">

                                <h2><?php esc_html_e( 'No Purchase History Found', 'cubewp-classified' ) ?></h2>

                            </div>

							<?php

						}

					}

				}

				?>

            </div>

			<?php

			$return = ob_get_clean();

		} else if ( $content_type == 'campaigns' ) {

			$posts_per_page = classified_get_setting( 'posts_per_page' );

			$paged          = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			$args['post_type'] = 'cwp_booster';

			$args['post_status']    = array( 'pending', 'active', 'expired');

			$args['posts_per_page'] = $posts_per_page;

			$args['paged']          = $paged;

			$args['author']         = get_current_user_id();

			$classified_query       = new Classified_Query();

			$query                  = $classified_query->Query( $args );

			$campaings = '<div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1>'.esc_html__( "My Campaigns", "cubewp-classified" ).'</h1>

                    <p>'.esc_html__( "You can View the detail of your campaigns here.", "cubewp-classified" ).'</p>

                </div>'.self::classified_dashboard_header_items().'

            </div>

            <div class="classified-dashboard-tab-content-heading-and-filters d-block d-md-flex justify-content-md-start align-items-md-center">

                <h4 class="classified-dashboard-tab-heading">'. sprintf( esc_html__( "Total Campaigns: %s", "cubewp-classified" ), $query->found_posts ).'</h4>

            </div>';



			$return = $campaings . $output;

		} else if ( $content_type == 'reviews' ) {

            $posts_per_page = classified_get_setting( 'posts_per_page' );

			$paged          = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

            $args['post_type'] = 'cwp_reviews';

			$args['post_status']    = array( 'pending', 'publish');

			$args['posts_per_page'] = $posts_per_page;

			$args['paged']          = $paged;

			$args['author']         = get_current_user_id();

			$classified_query       = new Classified_Query();

			$query                  = $classified_query->Query( $args );

            $reviews = '';

            $reviews = '<div class="classified-dashboard-header d-block d-lg-flex justify-content-lg-between align-items-center">

                <div class="classified-dashboard-header-heading mb-3 mb-lg-0">

                    <h1>'.esc_html__( "My Reviews", "cubewp-classified" ).'</h1>

                    <p>'.esc_html__( "You can View the detail of your reviews here.", "cubewp-classified" ).'</p>

                </div>'.self::classified_dashboard_header_items().'

            </div>

            <div class="classified-dashboard-tab-content-heading-and-filters d-block d-md-flex justify-content-md-start align-items-md-center">

                <h4 class="classified-dashboard-tab-heading">'. sprintf( esc_html__( "Total Reviews: %s", "cubewp-classified" ), $query->found_posts ).'</h4>

            </div>';

            $output = $reviews.$output;

			

            return $output;

        } else {

			$return = $output;

		}



		return $return;

	}



	public static function classified_dashboard_header_items() {

		$help_url = classified_get_setting( 'classified_help_page', 'page_url' ) ?? home_url();



		return '<div class="classified-dashboard-header-items d-flex justify-content-start">

            <button class="classified-dashboard-header-item mb-3 mb-lg-0 position-relative" data-classified-tooltip="true" data-bs-placement="bottom" title="' . esc_html__( 'Need Help?', 'cubewp-classified' ) . '">

                <a href="' . esc_url( $help_url ) . '" class="stretched-link"></a>

                <i class="fa-solid fa-circle-question" aria-hidden="true"></i>

            </button>

            <button class="classified-dashboard-header-item mb-3 mb-lg-0 classified-dashboard-open-edit-profile" data-classified-tooltip="true" data-bs-placement="bottom" title="' . esc_html__( 'Edit Profile', 'cubewp-classified' ) . '">

                <i class="fa-solid fa-user-pen" aria-hidden="true"></i>

            </button>

            <img loading="lazy" width="100%" height="100%" src="' . classified_get_userdata( get_current_user_id(), "avatar" ) . '" alt="' . sprintf( esc_html__( "Avatar Of %s", "cubewp-classified" ), classified_get_userdata( get_current_user_id(), "name" ) ) . '" class="classified-dashboard-header-item mb-3 mb-lg-0">

        </div>';

	}



	public function classified_dashboard_tabs( $output, $tab_ids, $tabs_detail ) {

		ob_start();

		if ( ! empty( $tabs_detail ) && is_array( $tabs_detail ) ) {

			$header_logo = classified_get_site_logo_url();

			?>

            <div class="classified-dashboard-sidebar-tab classified-dashboard-expand-tabs" data-target="expander">

                <div class="position-relative classified-dashboard-sidebar-logo">

                    <a class="stretched-link" href="<?php echo home_url() ?>">

                        <img loading="lazy" width="100%" height="100%" src="<?php echo esc_url( $header_logo ); ?>"

                             alt="<?php echo get_bloginfo(); ?>">

                    </a>

                </div>

                <i class="fa-solid fa-bars" aria-hidden="true"></i>

            </div>

			<?php

			$counter = 1;

			foreach ( $tabs_detail as $tab_id => $tab_detail ) {

				if ( ! self::check_dependency( $tab_id ) ) {

					continue;

				}

				$tab_title        = $tab_detail['title'] ?? '';

				$tab_icon         = $tab_detail['icon'] ?? 'fa-solid fa-cube';

				$tab_user_role    = $tab_detail['user_role'] ?? '';

				$tab_content_type = $tab_detail['content_type'] ?? '';

				$tab_content      = $tab_detail['content'] ?? '';

				$dashboard_url    = classified_get_setting( 'dashboard_page', 'page_url' ) ?? home_url();

				$current_tab      = sanitize_text_field( $_GET['tab_id'] ?? '' );

				if ( $counter == 1 && empty( $current_tab ) ) {

					wp_redirect( add_query_arg( 'tab_id', $tab_id, $dashboard_url ) );

				}



				$target    = '#' . $tab_id;

				$permalink = add_query_arg( 'tab_id', $tab_id, $dashboard_url );

				if ( $tab_content_type === 'logout' ) {

					$target    = 'href';

					$permalink = wp_logout_url( home_url() );

				}

				?>

                <div class="classified-dashboard-sidebar-tab <?php echo esc_attr( $tab_content_type ); ?> <?php classified_active_helper( $tab_id, $current_tab, '', 'classified-active-tab', true ); ?>"

                     data-permalink="<?php echo $permalink; ?>"

                     data-target="<?php echo esc_attr( $target ); ?>">

                    <i class="<?php echo esc_attr( $tab_icon ); ?>" aria-hidden="true"></i>

                    <p class="classified-dashboard-sidebar-tab-tooltip p-sm"><?php echo esc_html( $tab_title ); ?></p>

                    <p class="classified-dashboard-sidebar-tab-text p-sm"><?php echo esc_html( $tab_title ); ?></p>

                </div>

				<?php

				$counter ++;

			}

		}



		return ob_get_clean();

	}



	public function check_dependency( $tab_id = '' ) {

		if ( self::$user_dashboard[ $tab_id ]['user_role'] == self::$user_role || self::$user_dashboard[ $tab_id ]['user_role'] == '' ) {

			return true;

		}



		return false;

	}

}