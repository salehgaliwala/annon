<?php
defined( 'ABSPATH' ) || exit;
global $cubewp_frontend, $classified_category_taxonomies;
$col_class   = get_query_var( 'col_class', 'col-12 col-md-6 col-lg-4' );
$recommended = get_query_var( 'recommended', false );
$boosted     = get_query_var( 'boosted', 'check_again' );
$focused     = get_query_var( 'focused', false );
$is_preview  = get_query_var( 'is_preview', false );
$post_id     = get_query_var( 'post_id', get_the_ID() );
$post_type   = get_query_var( 'current_post_type', get_post_type( $post_id ) );

if ( $boosted == 'check_again' ) {
	if ( classified_is_booster_active() ) {
		if ( is_boosted( $post_id ) ) {
			$boosted = true;
		} else {
			$boosted = false;
		}
	} else {
		$boosted = false;
	}
}
$show_preview_class = false;
if ( $is_preview ) {
	$show_preview_class = true;
	$col_class          = 'col-12';
	if ( isset( $_GET['pid'] ) && ! empty( $_GET['pid'] ) ) {
		$post_id    = sanitize_text_field( $_GET['pid'] );
		$is_preview = false;
		$post_type  = get_post_type( $post_id );
	}
}
if ( ! $is_preview && ! cubewp_is_elementor_editing() ) {
	$post_metas = $cubewp_frontend->post_metas( $post_id );
} else {
	$post_metas = array();
}
if ( ! str_contains( $post_type, 'classified' ) ) {
	$condition_name = 'classified_' . str_replace( '-', '_', $post_type ) . '_condition';
} else {
	$condition_name = str_replace( '-', '_', $post_type ) . '_condition';
}
$show_like             = true;
$category              = classified_get_post_terms( $post_id, $classified_category_taxonomies, 1 );
$locations             = classified_get_post_terms( $post_id, array( 'locations' ), 1 );
$SavedText             = esc_html__( "Save", "classified-pro" );
$SavedClass            = 'cwp-save-post';
$category_url          = '';
$category_name         = '';
$location_url          = '';
$location_name         = '';
$classified_item_media = '';
$post_title            = get_the_title( $post_id );
$gallery               = $post_metas['classified_gallery']['meta_value'] ?? '';
$price                 = $post_metas['classified_price']['meta_value'] ?? 0;
$condition             = $post_metas[ $condition_name ]['meta_value'] ?? esc_html__( "N/A", "classified-pro" );
$purpose               = $post_metas['classified_property_ad_purpose']['meta_value'] ?? esc_html__( "N/A", "classified-pro" );
if ( class_exists( 'CubeWp_Saved' ) ) {
	$SavedText  = CubeWp_Saved::is_cubewp_post_saved( $post_id, false, false );
	$SavedClass = CubeWp_Saved::is_cubewp_post_saved( $post_id, false );
}
if ( ! empty( $category ) ) {
	$category      = $category[0];
	$category_url  = get_term_link( $category->term_id );
	$category_name = $category->name;
}
if ( ! empty( $locations ) ) {
	$locations     = $locations[0];
	$location_url  = get_term_link( $locations->term_id );
	$location_name = $locations->name;
}
if ( $is_preview ) {
	$show_like     = false;
	$location_url  = '';
	$location_name = esc_html__( "N/A", "classified-pro" );
	$category_url  = '';
	$category_name = esc_html__( "N/A", "classified-pro" );
	$post_title    = esc_html__( "Your Ad Title", "classified-pro" );
	$condition     = esc_html__( "Used", "classified-pro" );
	$purpose       = esc_html__( "Sale", "classified-pro" );
}
$category_taxonomies_class = '';
if ( ! empty( $classified_category_taxonomies ) && is_array( $classified_category_taxonomies ) ) {
	foreach ( $classified_category_taxonomies as $taxonomy ) {
		$category_taxonomies_class .= 'classified-preview-' . $taxonomy . ' ';
	}
}
$days = 0;
$now  = new DateTime;
$ago  = new DateTime( get_the_date( 'Y-m-d', $post_id ) );
$diff = $now->diff( $ago );
if ( isset( $diff->days ) ) {
	$days = $diff->days;
}
$fire = '';
if ( ! $is_preview ) {
	$fire = ( $days >= 1 && $days < 8 ) ? '<p class="classified-hot" data-classified-tooltip="true" data-bs-placement="right" title="' . esc_html__( 'hot', 'classified-pro' ) . '"><i class="fa-solid fa-fire classified-hot-item" aria-hidden="true"></i></p>' : '';
}
?>
<div <?php post_class( $col_class, $post_id ) ?>>
    <div class="classified-item
    <?php
	if ( $focused ) {
		echo esc_attr( 'classified-focused-item' );
	}
	echo ' ';
	if ( $boosted ) {
		echo esc_attr( 'classified-boosted-item' );
	}
	?>">
        <div class="classified-item-media-and-tags">
			<?php if ( ! $show_preview_class ) { ?>
                <a href="<?php echo get_the_permalink( $post_id ) ?>" class="stretched-link"></a>
			<?php } ?>
            <div class="classified-item-media">
                <img loading="lazy" width="100%" height="100%" class="<?php if ( $show_preview_class ) {
					echo esc_html( "classified-preview-featured_image" );
				} ?>" src="<?php echo classified_get_post_featured_image( $post_id, false, 'classified-grid' ); ?>"
                     alt="<?php echo get_the_post_thumbnail_caption( $post_id ); ?>">
            </div>
			<?php if ( ! empty( $category_url ) || ! empty( $category_name ) ) { ?>
                <div class="classified-item-tags">
                    <a href="<?php echo esc_url( $category_url ); ?>" class="classified-item-tag
                       <?php if ( $show_preview_class ) {
						echo esc_html( $category_taxonomies_class );
					} ?>">
						<?php echo esc_html( $category_name ); ?>
                    </a>
                </div>
			<?php } ?>
			<?php
			if ( $recommended ) {
				?>
                <div class="classified-item-recommended">
					<?php esc_html_e( 'Recommended', 'classified-pro' ); ?>
                </div>
				<?php
			} else if ( $boosted ) {
				?>
                <div class="classified-item-boosted">
					<?php esc_html_e( 'Ad', 'classified-pro' ); ?>
                </div>
				<?php
			}
			if ( $post_type == 'classified-ad' && ( $is_preview || classified_is_item_buyable( $post_id ) ) ) {
				?>
                <div class="classified-item-buyable <?php if ( $is_preview ) {
					echo 'opacity-0';
				} ?>" data-classified-tooltip="true" data-bs-placement="top"
                     title="<?php esc_html_e( 'Shippable', 'classified-pro' ); ?>">
                    <i class="fa-solid fa-truck" aria-hidden="true"></i>
                </div>
				<?php
			}
			?>
        </div>
        <div class="classified-item-content">
            <div class="classified-item-content-top">
                <p class="classified-item-price">
                    <?php
                    if ( $show_preview_class ) {
	                    echo classified_build_price( 0, true );

	                    echo "<text class='classified-preview-classified_price'>";
                        if ( ! empty( $price ) ) {
                            echo classified_build_price( $price, false, false );
                        }else {
                            echo '0';
                        }
						echo "</text>";
					}else {
                        echo classified_build_price( $price );
                    }
                    ?>
                </p>
                <div class="d-flex">
					<?php

					if ( $post_type == 'real-estate' && ! empty( $purpose ) ) {
						?>
                        <p class="classified-item-condition <?php if ( $show_preview_class ) {
							echo esc_html( "classified-preview-classified_property_ad_purpose" );
						} ?>">
							<?php echo esc_html( $purpose ) ?>
                        </p>
						<?php
					} else {
						?>
                        <p class="classified-item-condition <?php if ( $show_preview_class ) {
							echo esc_html( "classified-preview-" . $condition_name );
						} ?>">
							<?php echo esc_html( $condition ) ?>
                        </p>
						<?php
					}
					if ( $show_like ) {
						?>
                        <p class="classified-item-like <?php echo esc_attr( $SavedClass ); ?>"
                           data-pid="<?php echo esc_attr( $post_id ); ?>">
                            <i class="fa-regular fa-heart" aria-hidden="true"></i>
                        </p>
						<?php
					}
					?>
                </div>
            </div>
            <div class="classified-item-content-details">
				<?php if ( ! $show_preview_class ) { ?><a href="<?php echo get_the_permalink( $post_id ) ?>"><?php } ?>
                    <h5 class="classified-item-title <?php if ( $show_preview_class ) {
						echo esc_html( "classified-preview-the_title" );
					} ?>">
						<?php echo esc_html( $post_title ); ?>
						<?php echo cubewp_core_data( $fire ); ?>
                    </h5>
					<?php if ( ! $show_preview_class ) { ?></a><?php } ?>
            </div>
            <div class="classified-item-content-bottom">
				<?php if ( ! empty( $location_url ) || ! empty( $location_name ) ) { ?>
                    <p class="classified-item-content-term">
                        <a href="<?php echo esc_url( $location_url ); ?>">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            <text class="<?php if ( $show_preview_class ) {
								echo esc_html( "classified-preview-locations" );
							} ?>"><?php echo esc_html( $location_name ) ?></text>
                        </a>
                    </p>
					<?php
				}
				$stat = classified_get_setting( $post_type . '_loop_stat' );
				if ( $stat ) {
					$stat_field     = classified_get_setting( $post_type . '_loop_stat_field' );
					$stat_icon      = classified_get_setting( $post_type . '_loop_stat_icon' );
					$stat_field_val = $post_metas[ $stat_field ]['meta_value'] ?? false;
					if ( $stat_field_val && ! is_array( $stat_field_val ) && ! is_object( $stat_field_val ) ) {
						?>
                        <p class="classified-item-stat">
							<?php
							if ( ! empty( $stat_icon ) ) {
								echo classified_get_icon_output( $stat_icon );
							}
							echo esc_html( classified_limit_str_by_words( $stat_field_val, 1 ) );
							?>
                        </p>
						<?php
					}
				}
				if ( ! $show_preview_class ) {
					?>
                    <p class="classified-item-timer">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
						<?php echo classified_time_elapsed_string( get_the_date( 'Y-m-d g:i:s a', $post_id ) ); ?>
                    </p>
				<?php } ?>
            </div>
            <a href="<?php echo get_the_permalink( $post_id ) ?>" class="stretched-link"></a>
        </div>
    </div>
</div>
