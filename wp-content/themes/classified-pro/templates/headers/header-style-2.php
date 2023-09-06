<?php
defined( 'ABSPATH' ) || exit;
global $post;
$post_id = $post->ID ?? get_the_ID();

CubeWp_Enqueue::enqueue_style( 'classified-header-styles' );

$sticky_header   = '';
$header_class    = '';
$container_class = 'container';

$header_layout = classified_get_setting( 'header_layout' );
$full_width    = get_post_meta( $post_id, 'classified_full_width_header', true );
if ( $full_width == 'Yes' || $header_layout == 'full-width' ) {
	$container_class = 'container-fluid px-5';
}

if ( is_home() || is_front_page() ) {
	$current_loc             = 'classified_home_header';
	$sticky_header           = classified_get_setting( 'sticky_header_home' );
	$top_bar                 = classified_get_setting( 'home_header_top_bar' );
	$bottom_bar              = classified_get_setting( 'home_header_bottom_bar' );
	$header_search           = classified_get_setting( 'home_header_search' );
	$header_search_shortcode = classified_get_setting( 'home_header_search_shortcode' );
	$show_all_cats           = classified_get_setting( 'header_cats_home' );
} else {
	$current_loc             = 'classified_inner_header';
	$sticky_header           = classified_get_setting( 'sticky_header_inner' );
	$top_bar                 = classified_get_setting( 'inner_header_top_bar' );
	$bottom_bar              = classified_get_setting( 'inner_header_bottom_bar' );
	$header_search           = classified_get_setting( 'inner_header_search' );
	$header_search_shortcode = classified_get_setting( 'inner_header_search_shortcode' );
	$show_all_cats           = classified_get_setting( 'header_cats_inner' );
}

if ( classified_is_singular() ) {
	$header_search_shortcode = '[cwpSearch type="' . get_post_type() . '"]';
}

if ( $top_bar ) {
	$topbar = get_post_meta( $post_id, 'classified_header_top_bar', true );
	if ( $topbar == 'Yes' ) {
		$top_bar = false;
	}
}

if ( $bottom_bar ) {
	$bottom_bar = get_post_meta( $post_id, 'classified_header_bottom_bar', true );
	if ( $bottom_bar == 'Yes' ) {
		$bottom_bar = false;
	}
}

if ( $header_search ) {
	$_header_search = get_post_meta( $post_id, 'classified_header_search', true );
	if ( $_header_search == 'Yes' ) {
		$header_search = false;
	}
}

if ( $sticky_header ) {
	$header_class .= ' classified-sticky-header ';
}

$header_logo = classified_get_site_logo_url();

?>
<div class="classified-header-container <?php echo esc_attr( $header_class ); ?>">
    <header class="classified-header">
		<?php
		if ( $top_bar ) {
			?>
            <div class="classified-header-top-bar d-none d-lg-block">
                <div class="<?php echo esc_attr( $container_class ); ?> d-flex justify-content-between flex-wrap position-relative">
					<?php
					$landing_links = classified_get_setting( 'header_top_bar_landing_pages' );
					if ( $landing_links ) {
						?>
                        <div class="classified-header-top-bar-landing-links me-auto">
                            <div class="classified-dropdown position-static d-flex">
                                <?php
                                if ( $show_all_cats ) {
	                                ?>
		                            <a href="#"
	                                   class="d-flex align-items-center text-decoration-none classified-nav-all-categories">
	                                    <i class="fa-solid fa-bars me-2" aria-hidden="true"></i>
										<?php esc_html_e( "All Categories", "classified-pro" ); ?>
	                                </a>
		                            <?php
                                }
								?>
                                <div class="classified-dropdown-items">
                                    <div class="classified-nav-all-categories-container">
                                        <div class="row"
                                             data-masonry='{"percentPosition": true, "horizontalOrder": true}'>
											<?php
											global $classified_category_taxonomies;
											$args       = array(
												'taxonomy'   => $classified_category_taxonomies,
												'hide_empty' => false,
												'parent'     => 0,
												'number'     => 10
											);
											$categories = get_terms( $args );
											if ( is_front_page() || is_home() ) {
												$show        = classified_get_setting( 'header_cats_home' );
												$banner_type = classified_get_setting( 'home_header_cats_banner' );
												$image_url   = classified_get_setting( 'home_cats_banner_image' );
												$link        = classified_get_setting( 'home_cats_banner_img_link' );
												$adsense     = classified_get_setting( 'home_cats_banner_ads' );
												$location    = 'classified_home_header';
											} else {
												$location    = 'classified_inner_header';
												$banner_type = classified_get_setting( 'inner_header_cats_banner' );
												$show        = classified_get_setting( 'header_cats_inner' );
												$image_url   = classified_get_setting( 'inner_cats_banner_image' );
												$link        = classified_get_setting( 'inner_cats_banner_img_link' );
												$adsense     = classified_get_setting( 'inner_cats_banner_ads' );
											}
											$header_cats_number = classified_get_setting( 'header_cats_number' );
											if ( empty( $header_cats_number ) ) {
												$header_cats_number = 6;
											}
											if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
												if ( ! wp_is_mobile() ) {
													wp_enqueue_script( 'classified-masonry-scripts' );
												}
												$counter = 0;
												foreach ( $categories as $category ) {
													if ( $counter >= $header_cats_number ) {
														break;
													}
													$counter ++;
													$term_icon = get_term_meta( $category->term_id, 'classified_category_icon', true );
													?>
                                                    <div class="col-12 col-md-6 col-lg-3 col-xxl-3"
                                                         style="margin: 0 0 43px 0;">
                                                        <a href="<?php echo esc_url( get_term_link( $category->term_id ) ) ?>">
                                                            <h5>
																<?php
																echo classified_get_icon_output( $term_icon );
																echo esc_html( $category->name );
																?>
                                                            </h5>
                                                        </a>
														<?php
														$child_args       = array(
															'taxonomy'   => $classified_category_taxonomies,
															'hide_empty' => false,
															'parent'     => $category->term_id
														);
														$child_categories = get_terms( $child_args );
														if ( ! empty( $child_categories ) && ! is_wp_error( $child_categories ) ) {
															foreach ( $child_categories as $child_category ) {
																?>
                                                                <a href="<?php echo esc_url( get_term_link( $child_category->term_id ) ) ?>">
                                                                    <p class="p-md">
                                                                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
																		<?php echo esc_html( $child_category->name ); ?>
                                                                    </p>
                                                                </a>
																<?php
															}
														}
														?>
                                                    </div>
													<?php
													if ( $counter == 3 && ! empty( $banner_type ) ) {
														?>
                                                        <div class="col-12 col-md-6 col-lg-3 col-xxl-3"
                                                             style="margin: 0 0 43px 0;">
															<?php
															if ( $banner_type == "static_banner" ) {
																if ( ! empty( $image_url ) ) {
																	$attach_image_url = wp_get_attachment_url( $image_url );
																	if ( ! empty( $attach_image_url ) ) {
																		?>
                                                                        <a href="<?php echo esc_url( $link ); ?>"
                                                                           target="_blank" class="m-0">
                                                                            <img loading="lazy" width="100%"
                                                                                 class="m-0" height="100%"
                                                                                 src="<?php echo esc_url( $attach_image_url ); ?>"
                                                                                 alt="<?php esc_html_e( 'Advertisement', 'classified-pro' ); ?>"></a>
																		<?php
																	}
																}
															} else if ( $banner_type == "google_adsense" ) {
																if ( ! empty( $adsense ) ) {
																	echo cubewp_core_data( $adsense );
																}
															}
															?>
                                                        </div>
														<?php
													}
												}
											}
											?>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<?php
							global $classified_post_types;
							if ( ! empty( $classified_post_types ) ) {
                                foreach ( $classified_post_types as $post_type ) {
	                                if ( ! post_type_exists( $post_type ) ) {
		                                continue;
	                                }
                                    $post_type_obj = get_post_type_object( $post_type );
	                                $link = classified_get_setting( 'header_top_bar_landing_pages_' . $post_type );
	                                if ( ! empty( $link ) ) {
		                                $link = get_permalink( $link );
                                        $post_type_icon = classified_get_setting( $post_type . '_icon' );
		                                $post_type_icon = ! empty( $post_type_icon ) ? $post_type_icon : 'dashicons ' . $post_type_obj->menu_icon;
		                                ?>
                                        <a href="<?php echo esc_url( $link ) ?>">
                                            <i class="<?php echo esc_attr( $post_type_icon ); ?>" aria-hidden="true"></i>
			                                <?php echo esc_html( $post_type_obj->label ); ?>
                                        </a>
		                                <?php
	                                }
                                }
                            }
							?>
                        </div>
						<?php
					}
					classified_get_navigation( 'classified_header_topbar', true, 'classified-navigation-nav' );
					?>
                </div>
            </div>
			<?php
		}
		?>
        <div class="classified-header-top-container">
            <div class="<?php echo esc_attr( $container_class ); ?>">
                <div class="classified-header-top">
                    <div class="classified-header-logo">
                        <a href="<?php echo home_url() ?>">
                            <img loading="lazy" width="100%" height="100%" src="<?php echo esc_url( $header_logo ); ?>"
                                 alt="<?php echo get_bloginfo(); ?>">
                        </a>
                    </div>
                    <button class="classified-not-filled-btn d-flex d-lg-none classified-offcanvas-menu-btn"
                            data-bs-toggle="offcanvas" data-bs-target="#classified-offcanvas-navigation">
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </button>
					<?php
					if ( ! classified_is_archive() && $header_search ) { ?>
                        <div class="classified-header-search classified-visible-on-load">
							<?php echo do_shortcode( $header_search_shortcode ); ?>
                        </div>
					<?php } ?>
                    <button class="classified-not-filled-btn d-none d-lg-flex d-xl-none classified-offcanvas-menu-btn"
                            data-bs-toggle="offcanvas" data-bs-target="#classified-offcanvas-navigation">
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </button>
					<?php echo classified_get_navigation_quicks( 'd-none d-xl-flex classified-visible-on-load' ); ?>
                </div>
            </div>
        </div>
		<?php
		if ( $bottom_bar ) {
			$classified_navigation_menu = classified_get_navigation( $current_loc );
			if ( ! empty( $classified_navigation_menu ) ) {
				?>
                <div class="classified-header-bottom-container d-none d-lg-block">
                    <div class="<?php echo esc_attr( $container_class ); ?>">
                        <div class="position-relative">
							<?php echo cubewp_core_data( $classified_navigation_menu ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
		?>
    </header>
</div>