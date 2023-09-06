<?php
defined( 'ABSPATH' ) || exit;

global $classified_post_types;
$user_id = get_query_var( 'user_id', get_queried_object_id() );
$tab_id  = get_query_var( 'tab_id', false );
$rating = classified_get_userdata( $user_id, 'rating' );
if ( $rating > 3.5 ) {
	$css_var = '--green-700';
} else if ( $rating <= 3.5 && $rating > 2 ) {
	$css_var = '--orange-700';
} else {
	$css_var = '--red-700';
}
$posts_per_page         = classified_get_setting( 'posts_per_page' );
$paged                  = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$args['post_type']      = $classified_post_types;
$args['posts_per_page'] = $posts_per_page;
$args['paged']          = $paged;
$args['author']         = $user_id;
$classified_query       = new Classified_Query();
$query                  = $classified_query->Query( $args );
?>
<div class="classified-author-profile">
    <div class="classified-view-profile-container">
        <div class="row">
            <div class="col-12 col-lg-3">
                <div class="classified-author-sidebar mb-4 mb-lg-0">
                    <div class="classified-author-box">
                        <div class="classified-author-avatar">
                            <img loading="lazy" width="100%" height="100%" src="<?php echo classified_get_userdata( $user_id, 'avatar' ); ?>"
                                 alt="<?php echo classified_get_userdata( $user_id, 'name' ); ?>">
                        </div>
                        <h2 class="classified-author-name"><?php echo classified_get_userdata( $user_id, 'name' ); ?></h2>
                        <?php
                        if ( $rating ) {
                            ?>
                            <div class="classified-author-rating"
                                 style="color: var(<?php echo esc_attr( $css_var ); ?>)">
                                <p><?php echo esc_html( $rating ); ?></p>
                                <div class="classified-author-rating-stars">
                                    <?php echo classified_get_rating_stars_html( $rating, '<div class="classified-author-rating-star">', '</div>' ); ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <p class="classified-author-detail"><?php esc_html_e( "Member Since:", "classified-pro" ); ?>
                            <b><?php echo classified_get_userdata( $user_id, 'joined' ); ?></b></p>
                        <p class="classified-author-detail"><?php esc_html_e( "Ads Posted:", "classified-pro" ); ?>
                            <b><?php echo classified_get_userdata( $user_id, 'ads_count' ); ?></b></p>
                    </div>
					<?php
					$facebook    = classified_get_userdata( $user_id, 'facebook' );
					$instagram   = classified_get_userdata( $user_id, 'instagram' );
					$twitter     = classified_get_userdata( $user_id, 'twitter' );
					$linkedin    = classified_get_userdata( $user_id, 'linkedin' );
					$youtube     = classified_get_userdata( $user_id, 'youtube' );
					$addi_social = apply_filters( 'classified_author_social_profile', '', $user_id );
					if ( ! empty( $facebook ) || ! empty( $addi_social ) || ! empty( $instagram ) || ! empty( $twitter ) || ! empty( $linkedin ) || ! empty( $youtube ) ) {
						?>
                        <div class="classified-author-socials">
                            <h6><?php esc_html_e( "Socials Link", "classified-pro" ); ?></h6>
							<?php
							if ( ! empty( $facebook ) ) {
								?>
                                <div class="classified-author-social">
                                    <a href="<?php echo esc_url( $facebook ); ?>" class="stretched-link"
                                       target="_blank"></a>
									<?php echo classified_get_svg( 'facebook' ); ?>
                                </div>
								<?php
							}
							if ( ! empty( $instagram ) ) {
								?>
                                <div class="classified-author-social">
                                    <a href="<?php echo esc_url( $instagram ); ?>" class="stretched-link"
                                       target="_blank"></a>
									<?php echo classified_get_svg( 'instagram' ); ?>
                                </div>
								<?php
							}
							if ( ! empty( $twitter ) ) {
								?>
                                <div class="classified-author-social">
                                    <a href="<?php echo esc_url( $twitter ); ?>" class="stretched-link"
                                       target="_blank"></a>
									<?php echo classified_get_svg( 'twitter' ); ?>
                                </div>
								<?php
							}
							if ( ! empty( $linkedin ) ) {
								?>
                                <div class="classified-author-social">
                                    <a href="<?php echo esc_url( $linkedin ); ?>" class="stretched-link"
                                       target="_blank"></a>
									<?php echo classified_get_svg( 'linkedin' ); ?>
                                </div>
								<?php
							}
							if ( ! empty( $youtube ) ) {
								?>
                                <div class="classified-author-social">
                                    <a href="<?php echo esc_url( $youtube ); ?>" class="stretched-link"
                                       target="_blank"></a>
									<?php echo classified_get_svg( 'youtube' ); ?>
                                </div>
								<?php
							}
							if ( ! empty( $addi_social ) ) {
								echo cubewp_core_data( $addi_social );
							}
							?>
                        </div>
						<?php
					}
					if ( classified_is_review_active() ) {
                        ?>
                        <div class="classified-author-profile-actions">
                            <div class="classified-author-profile-action" data-target="#classified-author-reviews">
                                <i class="fa-solid fa-star" aria-hidden="true"></i>
                                <p><?php esc_html_e( 'Reviews', 'classified-pro' ); ?></p>
                            </div>
                        </div>
                        <?php
					}
					if ( $user_id == get_current_user_id() ) {
						?>
                        <div class="classified-author-profile-actions">
                            <div class="classified-author-profile-action" data-target="#classified-edit-profile">
                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                <p><?php esc_html_e( 'Edit Profile', 'classified-pro' ); ?></p>
                            </div>
                        </div>
						<?php
					}
					?>
                </div>
            </div>
            <div class="col-12 col-lg-9">
                <div class="classified-author-profile-action-target classified-active-section classified-author-ads"
                     id="classified-published-ads">
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
						$big = 999999999; // need an unlikely integer
						$url = get_pagenum_link( $big );
						$url = explode( '?dashboard', $url );
						$url = $url[0] ?? '';
						if ( $tab_id ) {
							$url = add_query_arg( 'tab_id', $tab_id, $url );
						}
						echo '<div class="classified-pagination">' . paginate_links( array(
								'base'      => str_replace( $big, '%#%', esc_url( $url ) ),
								'format'    => '?paged=%#%',
								'current'   => max( 1, get_query_var( 'paged' ) ),
								'total'     => $query->max_num_pages,
								'prev_next' => false
							) ) . '</div>';
					}
                    else {
                        ?>
                        <h4><?php esc_html_e( "No Ad Found", "classified-pro" ); ?></h4>
					    <?php
                    }
					wp_reset_postdata();
					wp_reset_query();
                    ?>
                </div>
                <?php
                if ( classified_is_review_active() ) {
                    ?>
                    <div class="classified-author-profile-action-target classified-author-review-container classified-frontend-form-container"
                         id="classified-author-reviews">
                        <?php
                        echo CubeWp_Reviews_Stats::get_current_post_reviews( 'user', $user_id );
                        if ( is_author() ) {
                            CubeWp_Enqueue::enqueue_style( 'cwp-review-single' );
                            $role = classified_get_userdata( $user_id, 'role' );
                            echo do_shortcode( '[cwpForm type="cwp_reviews" content="review_type_' . $role . '"]' );
                        }
                        ?>
                    </div>
                    <?php
                }
                if ( $user_id == get_current_user_id() ) {
                    ?>
                    <div class="classified-author-profile-action-target classified-edit-profile-container classified-frontend-form-container"
                         id="classified-edit-profile">
						<?php
						echo do_shortcode( '[cwpProfileForm]' );
						?>
                    </div>
				<?php } ?>
            </div>
        </div>
    </div>
</div>