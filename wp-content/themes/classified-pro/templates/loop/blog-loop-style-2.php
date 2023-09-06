<?php
defined( 'ABSPATH' ) || exit;

$post_id          = get_the_ID();
$post_time        = get_the_date();
$post_author      = get_post_field( 'post_author', $post_id );
$post_author_name = get_userdata( $post_author );
$post_term        = wp_get_post_terms( $post_id, 'category' );
$post_term        = $post_term[0] ?? '';
$blog_sidebar = classified_get_setting( 'blog_sidebar' );

if ( ! is_home() ) {
	$blog_sidebar = false;
}

$col_class = $blog_sidebar ? 'col-lg-6' : 'col-lg-4';
?>
<div class="col-12 <?php echo esc_attr( $col_class ); ?>">
    <div <?php post_class( 'classified-post-element-grid' ) ?>>
        <div class="classified-post-grid-thumbnail">
            <a class="classified-post-grid-thumbnail-url" href="<?php echo esc_url( get_permalink() ); ?>">
                <img loading="lazy" width="100%" height="100%"
                     src="<?php echo classified_get_post_featured_image( $post_id, false, 'classified-grid' ); ?>"
                     alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">
            </a>
			<?php if ( ! empty( $post_term ) ) { ?>
                <a href="<?php echo esc_url( get_term_link( $post_term->term_id ) ); ?>"
                   class="classified-post-element-grid-term"><?php echo esc_html( $post_term->name ); ?></a>
			<?php } ?>
        </div>
        <div class="classified-post-element-grid-content">
            <a href="<?php echo esc_url( get_permalink() ); ?>"><h2 title="<?php echo get_the_title(); ?>"><?php echo get_the_title(); ?></h2></a>
            <p class="classified-post-element-grid-info">
                <span class="classified-post-element-date"><?php echo esc_attr( $post_time ); ?></span>
                <span class="classified-post-element-grid-separator">/</span>
                <a href="<?php echo esc_url( get_author_posts_url( $post_author ) ) ?>"><span
                            class="classified-post-element-author"><?php echo sprintf( esc_html__( "By %s", "classified-pro" ), $post_author_name->display_name ); ?></span></a>
                <span class="classified-post-element-grid-separator">/</span>
                <span class="classified-post-element-read-time"><?php echo sprintf( esc_html__( "%s min read", "classified-pro" ), classified_estimate_reading_time( get_the_content() ) ); ?></span>
            </p>
        </div>
    </div>
</div>