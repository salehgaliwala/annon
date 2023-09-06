<?php
defined( 'ABSPATH' ) || exit;

global $classified_post_types;
$default_user = get_current_user_id();
if ( is_author() ) {
	$default_user = get_queried_object_id();
}
$user_id     = get_query_var( 'user_id', $default_user );
$tab_id      = get_query_var( 'tab_id', false );
$avatar      = classified_get_userdata( $user_id, 'avatar' );
$name        = classified_get_userdata( $user_id, 'name' );
$rating      = classified_get_userdata( $user_id, 'rating' );
$join_period = classified_get_userdata( $user_id, 'join_period' );
$inbox_resp  = classified_get_userdata( $user_id, 'inbox_response' );
$resp_time   = $inbox_resp['response_time'];
$resp_rate   = $inbox_resp['response_rate'];

$paged                  = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$posts_per_page         = classified_get_setting( 'posts_per_page' );
$args['post_type']      = $classified_post_types;
$args['posts_per_page'] = $posts_per_page;
$args['paged']          = $paged;
$args['author']         = $user_id;
$classified_query       = new Classified_Query();
$query                  = $classified_query->Query( $args );

if ( is_user_logged_in() ) {
	classified_require_modal( array(
		'report' => array(
			'type' => 'user_id',
			'id'   => $user_id
		),
	) );
}
?>
<div class="classified-author-profile">
    <div class="row">
        <div class="col-12 col-lg-4 col-xl-3 px-0">
            <div class="classified-author-profile-info">
                <div class="classified-author-avatar">
                    <img loading="lazy" width="100%" height="100%" src="<?php echo esc_url( $avatar ); ?>"
                         alt="<?php echo esc_html( $name ); ?>">
                </div>
                <h1 class="classified-author-name">
					<?php
					echo esc_html( $name );
					if ( classified_is_user_email_verified( $user_id ) ) {
						?>
                        <i class="fa-regular fa-circle-check" aria-hidden="true" data-classified-tooltip="true" data-bs-placement="right"
                           title="<?php esc_html_e( 'Verified', 'classified-pro' ); ?>"></i>
						<?php
					}
					?>
                </h1>
				<?php
				if ( $rating ) {
					?>
                    <div class="classified-author-rating-stars">
						<?php echo classified_get_rating_stars_html( $rating ); ?>
                    </div>
					<?php
				}
				?>
                <div class="classified-author-profile-actions">
                    <div class="classified-author-profile-action classified-author-share-profile classified-dropdown">
						<?php echo classified_get_svg( 'classified_share' ); ?>
                        <div class="classified-dropdown-items classified-social-share">
							<?php echo classified_get_socials_share( $user_id, true ); ?>
                        </div>
                    </div>
					<?php
					if ( is_user_logged_in() ) {
						$modal_target = '#classified-report-modal-' . $user_id;
					} else {
						$modal_target = '#classified-login-register';
					}
					?>
                    <div class="classified-author-profile-action classified-author-report-profile" type="button"
                         data-bs-toggle="modal"
                         data-bs-target="<?php echo esc_attr( $modal_target ); ?>">
						<?php echo classified_get_svg( 'classified_flag' ); ?>
                    </div>
                </div>
				<?php
				if ( $user_id == get_current_user_id() ) {
					$default_text = '<i class=\'fa-solid fa-pen-to-square\' aria-hidden=\'true\'></i>' . esc_html__( 'Edit Profile', 'classified-pro' );
					$active_text  = '<i class=\'fa-solid fa-xmark\' aria-hidden=\'true\'></i>' . esc_html__( 'Cancel Editing', 'classified-pro' );
					?>
                    <p class="classified-author-edit-profile"
                       data-default-text="<?php echo cubewp_core_data( $default_text ); ?>"
                       data-active-text="<?php echo cubewp_core_data( $active_text ); ?>">
						<?php echo cubewp_core_data( $default_text ); ?>
                    </p>
					<?php
				}
				?>
                <div class="classified-author-stats">
                    <i class="fa-solid fa-envelope-open-text" aria-hidden="true"></i>
                    <div class="classified-author-stat">
                        <h6><?php esc_html_e( 'Response Rate', 'classified-pro' ); ?></h6>
                        <p><?php echo esc_html( $resp_rate ); ?></p>
                    </div>
                    <div class="classified-author-stat">
                        <h6><?php esc_html_e( 'Typically Replies In', 'classified-pro' ); ?></h6>
                        <p><?php echo esc_html( $resp_time ); ?></p>
                    </div>
                </div>
                <div class="classified-author-linked-platforms">
                    <h4><?php esc_html_e( 'Linked with', 'classified-pro' ); ?></h4>
                    <p class="classified-author-linked-platform">
                        <i class="fa-solid fa-envelope classified-author-linked-platform-icon" aria-hidden="true"></i>
						<?php esc_html_e( 'Email Confirmed', 'classified-pro' ); ?>
                        <i class="fa-regular fa-circle-check classified-author-linked-platform-status
                            <?php echo classified_is_user_email_verified( $user_id ) ? 'classified-status-verified' : '' ?>" aria-hidden="true"></i>
                    </p>
                    <?php
                    if ( function_exists( 'cwp_check_if_sl_connected' ) ) {
                        ?>
                        <p class="classified-author-linked-platform">
                            <i class="fa-brands fa-google classified-author-linked-platform-icon" aria-hidden="true"></i>
		                    <?php esc_html_e( 'Google Connected', 'classified-pro' ); ?>
                            <i class="fa-regular fa-circle-check classified-author-linked-platform-status
                            <?php echo cwp_check_if_sl_connected( 'google', $user_id ) ? 'classified-status-verified' : '' ?>" aria-hidden="true"></i>
                        </p>
                        <p class="classified-author-linked-platform">
                            <i class="fa-brands fa-facebook classified-author-linked-platform-icon" aria-hidden="true"></i>
		                    <?php esc_html_e( 'Facebook Connected', 'classified-pro' ); ?>
                            <i class="fa-regular fa-circle-check classified-author-linked-platform-status
                            <?php echo cwp_check_if_sl_connected( 'facebook', $user_id ) ? 'classified-status-verified' : '' ?>" aria-hidden="true"></i>
                        </p>
                        <?php
                    }
                    ?>
                </div>
                <p class="classified-author-member-since"><?php echo sprintf( esc_html__( 'Member Since %s', 'classified-pro' ), $join_period ); ?></p>
            </div>
        </div>
        <div class="col-12 col-lg-8 col-xl-9 px-0">
            <div class="classified-author-container">
                <div class="classified-author-tabs-container">
                    <ul class="nav nav-tabs classified-author-tabs" role="tablist">
                        <li class="nav-item classified-author-tab" role="presentation">
                            <button class="nav-link active classified-author-items-tab-btn" type="button" role="tab"
                                    id="classified-author-items-btn-tab" data-bs-toggle="tab" aria-selected="true"
                                    data-bs-target="#classified-author-items"
                                    aria-controls="<?php esc_html_e( 'All Ads', 'classified-pro' ); ?>">
                                <i class="fa-solid fa-list-ul" aria-hidden="true"></i>
								<?php esc_html_e( 'All Ads', 'classified-pro' ); ?>
                            </button>
                        </li>
						<?php
						if ( classified_is_review_active() ) {
							?>
                            <li class="nav-item classified-author-tab" role="presentation">
                                <button class="nav-link classified-author-reviews-tab-btn" type="button" role="tab"
                                        id="classified-author-reviews-btn-tab" data-bs-toggle="tab" aria-selected="true"
                                        data-bs-target="#classified-author-reviews"
                                        aria-controls="<?php esc_html_e( 'All Reviews', 'classified-pro' ); ?>">
									<?php
									echo classified_get_svg( 'classified_star_list' );
									esc_html_e( 'All Reviews', 'classified-pro' );
									?>
                                </button>
                            </li>
							<?php
						}
						?>
                    </ul>
                    <div class="tab-content classified-author-profile-tab-target">
                        <div class="tab-pane active" id="classified-author-items" role="tabpanel">
                            <div class="classified-author-items">
								<?php if ( $query->have_posts() ) { ?>
                                    <h4><?php esc_html_e( "Published Ads", "classified-pro" ); ?></h4>
                                    <div class="row">
										<?php
										while ( $query->have_posts() ) {
											$query->the_post();
											$post_id = get_the_ID();
											set_query_var( 'col_class', 'col-12 col-lg-6 col-xl-4' );
											set_query_var( 'post_id', $post_id );
											set_query_var( 'boosted', false );
											get_template_part( 'templates/loop/loop-views' );
										}
										?>
                                    </div>
									<?php
									if ( $query->max_num_pages > 1 ) {
										$post_types = implode( ',', $classified_post_types );
										?>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <button class="classified-not-filled-btn classified-items-load-more"
                                                    data-page-num="<?php echo esc_attr( '2' ); ?>"
                                                    data-author="<?php echo esc_attr( $user_id ); ?>"
                                                    data-boosted="no"
                                                    data-col-class="col-12 col-lg-6 col-xl-4"
                                                    data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
                                                    data-post-types="<?php echo esc_attr( $post_types ); ?>">
												<?php esc_html_e( 'Load More', 'classified-pro' ); ?>
                                            </button>
                                        </div>
										<?php
									}
								} else {
									?>
                                    <div class="cwp-empty-search">
                                        <img loading="lazy" width="100%" height="100%" class="cwp-empty-search-img"
                                             src="<?php echo CWP_PLUGIN_URI ?>cube/assets/frontend/images/no-result.png"
                                             alt="<?php esc_html_e( 'No Results', 'classified-pro' ); ?>">
                                        <h2><?php esc_html_e( 'No Ads Found', 'classified-pro' ) ?></h2>
                                    </div>
									<?php
								}
								wp_reset_postdata();
								wp_reset_query();
								?>
                            </div>
                        </div>
                        <div class="tab-pane" id="classified-author-reviews" role="tabpanel">
							<?php
							if ( classified_is_review_active() ) {
								?>
                                <div class="classified-author-reviews classified-frontend-form-container">
									<?php
									echo CubeWp_Reviews_Stats::get_current_post_reviews( 'user', $user_id );
									CubeWp_Enqueue::enqueue_style( 'cwp-review-single' );
									if ( is_author() ) {
										$role = classified_get_userdata( $user_id, 'role' );
										echo do_shortcode( '[cwpForm type="cwp_reviews" content="review_type_' . $role . '"]' );
									}
									?>
                                </div>
								<?php
							}else {
                                ?>
                                <div class="cwp-empty-search">
                                    <img loading="lazy" width="100%" height="100%" class="cwp-empty-search-img"
                                         src="<?php echo CWP_PLUGIN_URI ?>cube/assets/frontend/images/no-result.png"
                                         alt="<?php esc_html_e( 'No Results', 'classified-pro' ); ?>">
                                    <h2><?php esc_html_e( 'No Reviews Found', 'classified-pro' ) ?></h2>
                                </div>
                                <?php
                            }
							?>
                        </div>
                    </div>
                </div>
				<?php
				if ( $user_id == get_current_user_id() ) {
					?>
                    <div class="classified-author-edit-profile classified-frontend-form-container"
                         style="display: none;">
						<?php echo do_shortcode( '[cwpProfileForm]' ); ?>
                    </div>
					<?php
				}
				?>
            </div>
        </div>
    </div>
</div>