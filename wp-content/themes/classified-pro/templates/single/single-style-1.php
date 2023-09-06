<?php
defined( 'ABSPATH' ) || exit;

global $cubewp_frontend, $post, $classified_category_taxonomies;

$post_id         = get_the_ID() ?? $post->ID;
$post_type       = get_post_type( $post_id );
$single          = $cubewp_frontend->single();
$sidebar_classes = 'classified-sidebar';

if ( is_user_logged_in() ) {
	classified_require_modal( array(
		'report' => $post_id,
	) );
}

$sticky_sidebar = classified_get_setting( 'classified_sticky_sidebar' );

if ( $sticky_sidebar && ! wp_is_mobile() ) {
	$sidebar_classes .= ' classified-sticky-sidebar';
}
?>
<section class="classified-single-page <?php echo sprintf( esc_attr( 'classified-%s-single-page' ), $post_type ) ?>">
    <div class="container">
		<?php echo classified_breadcrumb(); ?>
        <div class="row mb-4">
            <div class="col-12 col-md-12 col-lg-8">
				<?php echo wp_kses_post( $single->get_single_content_area() ); ?>
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <div class="<?php echo esc_attr( $sidebar_classes ) ?>">
					<?php
					// echo wp_kses_post($single->get_single_sidebar_area());
					$form_options = CWP()->get_form( 'single_layout' );
					$sidebar      = $form_options[ $post_type ]['sidebar'] ?? array();
					if ( ! empty( $sidebar ) ) {
						foreach ( $sidebar as $data ) {
							$section['title']  = $data['section_title'] ?? '';
							$section['class']  = $data['section_class'] ?? '';
							$section['fields'] = $data['fields'] ?? array();
							$section['id']     = $data['section_id'] ?? '';
							$layout            = $data['section_layout'] ?? 'classified-bordered-box';
							$section_title     = $data['section_show_title'] ?? 'no';
							$section['class']  .= ' classified-single-section ' . esc_attr( $layout ) . ' classified-single-sidebar-section ';
							echo '<div id="' . $section['id'] . '" class="' . $section['class'] . '">';
							if ( $section_title == 'yes' ) {
								echo '<h2 class="classified-section-title">' . esc_html( $section['title'] ) . '</h2>';
							}
							echo apply_filters( 'cubewp/frontend/single/section/fields', $section['fields'] );
							echo '</div>';
						}
					}
					?>
                </div>
            </div>
        </div>
        <?php
        echo classified_single_related_items( $post_id );
        echo classified_single_author_items( $post_id );
        ?>
    </div>
</section>