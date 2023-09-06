<?php
defined( 'ABSPATH' ) || exit;
get_header();
if ( if_theme_can_load() ) {
	?>
    <div class="container">
		<?php
		get_template_part( 'templates/author/author-views' );
		?>
    </div>
	<?php
} else {

	wp_enqueue_style( 'classified-dynamic-styles' );
	wp_enqueue_style( 'classified-blogs-styles' );
	$blog_sidebar      = classified_get_setting( 'blog_sidebar' );
	$blog_banner       = classified_get_setting( 'blog_banner' );
	if ( ! $blog_banner ) {
		global $cwpOptions;
		if ( empty( $cwpOptions ) || ! is_array( $cwpOptions ) ) {
			$cwpOptions = get_option( 'cwpOptions' );
		}
		if ( ! isset( $cwpOptions['blog_banner'] ) ) {
			$blog_banner = true;
		}
	}
	$queried_object = get_queried_object();
    $blog_banner_title = $queried_object->display_name;
	?>
    <section class="classified-blogs">
		<?php if ( $blog_banner ) {
			$blog_banner_image = classified_get_setting( 'blog_banner_image' );
			$banner_image_url  = wp_get_attachment_url( $blog_banner_image );
			if ( empty( $banner_image_url ) ) {
				$banner_image_url = CLASSIFIED_URL . 'assets/images/banner-blog-default.webp';
			}
			?>
            <div id="classified-blog-banner"
                 style="background-image: url(<?php echo esc_url( $banner_image_url ); ?>);">
                <div class="container">
                    <div class="classified-blog-banner-content">
                        <h1><?php echo esc_html( $blog_banner_title ); ?></h1>
                    </div>
                </div>
            </div>
			<?php
		}
		?>
        <div class="container">
            <div class="row">
                <div class="col-12 <?php echo esc_attr( $blog_sidebar ? 'col-md-9' : 'col-md-12' ) ?>">
                    <div class="row">
						<?php
						if ( have_posts() ) {
							while ( have_posts() ) {
								the_post();
								$blog_default_style = classified_get_setting( 'blog_default_style' ) ? classified_get_setting( 'blog_default_style' ) : 'style_2';
								if ( $blog_default_style == 'style_1' ) {
									get_template_part( 'templates/loop/blog-loop-style-1' );
								} else if ( $blog_default_style == 'style_3' ) {
									get_template_part( 'templates/loop/blog-loop-style-3' );
								} else {
									get_template_part( 'templates/loop/blog-loop-style-2' );
								}
							}
							the_posts_pagination( array( 'class' => 'classified-pagination' ) );
						} else {
							?>
                            <div>
                                <h2><?php esc_html_e( 'No Results', 'classified-pro' ); ?></h2>
                                <p><?php esc_html_e( 'Sorry! There is no post available.', 'classified-pro' ); ?></p>
                            </div>
							<?php
						}
						?>
                    </div>
                </div>
				<?php if ( $blog_sidebar ) { ?>
                    <div class="col-12 col-md-3">
						<?php get_sidebar(); ?>
                    </div>
				<?php } ?>
            </div>
        </div>
    </section>
	<?php
}
get_footer();