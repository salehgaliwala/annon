<?php
defined( 'ABSPATH' ) || exit;
global $cubewp_frontend, $classified_category_taxonomies;
$col_class          = get_query_var( 'col_class', 'col-12 col-md-6 col-lg-4' );
$recommended        = get_query_var( 'recommended', false );
$is_preview         = get_query_var( 'is_preview', false );
$post_id            = get_query_var( 'post_id', get_the_ID() );
$show_preview_class = false;
$post_type          = get_post_type( $post_id );

if ( $is_preview ) {
	$show_preview_class = true;
	$col_class          = 'col-12';
	if ( isset( $_GET['pid'] ) && ! empty( $_GET['pid'] ) ) {
		$post_id    = sanitize_text_field( $_GET['pid'] );
		$is_preview = false;
	}
}
if ( ! $is_preview && ! cubewp_is_elementor_editing() ) {
	$post_metas = $cubewp_frontend->post_metas( $post_id );
} else {
	$post_metas = array();
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
$post_content          = classified_limit_str_by_words( strip_tags( get_the_content( '', '', $post_id ) ), 20 );
$gallery               = $post_metas['classified_gallery']['meta_value'] ?? '';
$price                 = $post_metas['classified_price']['meta_value'] ?? 0;
if ( ! str_contains( $post_type, 'classified' ) ) {
	$condition_name = 'classified_' . str_replace( '-', '_', $post_type ) . '_condition';
}else {
	$condition_name = str_replace( '-', '_', $post_type ) . '_condition';
}
$condition             = $post_metas[ $condition_name ]['meta_value'] ?? esc_html__( "N/A", "classified-pro" );

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
	$post_content  = esc_html__( "Your ad details", "classified-pro" );
}
$category_taxonomies_class = '';
foreach ( $classified_category_taxonomies as $taxonomy ) {
	$category_taxonomies_class .= 'classified-preview-' . $taxonomy . ' ';
}

?>
<div <?php post_class( $col_class ) ?>>
    <div class="classified-item-style-2 <?php if ( $recommended ) {
		echo esc_attr( 'classified-focused-item' );
	} ?>">
        <div class="classified-item-style-2-media-and-tags">
			<?php if ( ! $show_preview_class ) { ?>
                <a href="<?php echo get_the_permalink( $post_id ) ?>" class="stretched-link"></a>
			<?php } ?>
            <div class="classified-item-style-2-media">
                <img loading="lazy" width="100%" height="100%" class="<?php if ( $show_preview_class ) {
					echo esc_html( "classified-preview-featured_image" );
				} ?>" src="<?php echo classified_get_post_featured_image( $post_id, false, 'classified-grid' ); ?>"
                     alt="<?php echo get_the_post_thumbnail_caption( $post_id ); ?>">
            </div>
			<?php if ( ! empty( $category_url ) || ! empty( $category_name ) ) { ?>
                <div class="classified-item-style-2-tags">
                    <a href="<?php echo esc_url( $category_url ); ?>" class="classified-item-style-2-tag
                       <?php if ( $show_preview_class ) {
						echo esc_html( $category_taxonomies_class );
					} ?>">
						<?php echo esc_html( $category_name ); ?>
                    </a>
                </div>
			<?php } ?>
			<?php if ( $show_like ) { ?>
                <p class="classified-item-style-2-like <?php echo esc_attr( $SavedClass ); ?>"
                   data-pid="<?php echo esc_attr( $post_id ); ?>">
                    <i class="fa-regular fa-heart" aria-hidden="true"></i>
                </p>
			<?php } ?>
			<?php if ( $recommended ) { ?>
                <div class="classified-item-style-2-recommended">
					<?php esc_html_e( 'Recommended', 'classified-pro' ); ?>
                </div>
			<?php } ?>
        </div>
        <div class="classified-item-style-2-content">
            <h5 class="classified-item-style-2-title position-relative <?php if ( $show_preview_class ) {
				echo esc_html( "classified-preview-the_title" );
			} ?>">
				<?php
				if ( ! $show_preview_class ) { ?><a href="<?php echo get_the_permalink( $post_id ) ?>"
                                                    class="stretched-link"></a><?php }
				echo esc_html( $post_title );
				?>
            </h5>
            <p class="classified-item-style-2-price">
				<?php
				echo classified_build_price( $price );
				if ( $show_preview_class ) {
					echo "<text class='classified-preview-classified_price'></text>";
				}
				?>
            </p>
			<?php if ( ! empty( $location_url ) || ! empty( $location_name ) ) { ?>
                <p class="classified-item-style-2-content-term">
                    <a href="<?php echo esc_url( $location_url ); ?>">
                        <text class="<?php if ( $show_preview_class ) {
							echo esc_html( "classified-preview-locations" );
						} ?>"><?php echo esc_html( $location_name ) ?></text>
                    </a>
                </p>
				<?php
			}
			?>
            <a href="<?php echo get_the_permalink( $post_id ) ?>" class="stretched-link"></a>
        </div>
    </div>
</div>