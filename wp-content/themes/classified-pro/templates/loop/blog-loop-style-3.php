<?php
defined( 'ABSPATH' ) || exit;

$post_id            = get_the_ID();
$post_time          = get_the_date();
$post_author        = get_post_field( 'post_author', $post_id );
$post_author_name   = get_userdata( $post_author );
$post_author_avatar = get_avatar_url( $post_author );
$post_author_name   = $post_author_name->display_name;
$post_term          = wp_get_post_terms( $post_id, 'category' );
$post_term          = $post_term[0] ?? '';
$blog_sidebar = classified_get_setting( 'blog_sidebar' );

if ( ! is_home() ) {
	$blog_sidebar = false;
}

$col_class = $blog_sidebar ? 'col-lg-4' : 'col-lg-3';
?>
<div class="col-12 <?php echo esc_attr( $col_class ); ?>">
    <div <?php post_class( 'classified-post-grid-style' ) ?>>
        <div class="classified-post-grid-style-thumbnail">
            <a href="<?php echo esc_url( get_permalink() ); ?>">
                <img loading="lazy" width="100%" height="100%"
                     src="<?php echo classified_get_post_featured_image( $post_id, false, 'classified-grid' ); ?>"
                     alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">
            </a>
			<?php if ( ! empty( $post_term ) ) { ?>
                <a href="<?php echo esc_url( get_term_link( $post_term->term_id ) ); ?>"
                   class="classified-post-grid-style-term"><?php echo esc_html( $post_term->name ); ?></a>
			<?php } ?>
        </div>
        <div class="classified-post-grid-style-content">
            <div class="classified-single-post-author">
                <img src="<?php echo esc_url( $post_author_avatar ); ?>"
                     alt="<?php echo esc_html( $post_author_name ); ?>">
                <div class="classified-single-post-author-content">
                    <a href="<?php echo esc_url( get_author_posts_url( $post_author ) ); ?>">
                        <p class="p-lg"><?php echo esc_attr( $post_author_name ); ?></p>
                    </a>
                    <span><?php echo esc_html( $post_time ); ?></span>
                </div>
            </div>
            <a href="<?php echo esc_url( get_permalink() ); ?>"><h2><?php echo substr( get_the_title(), 0, 35 ); ?></h2>
            </a>
            <p class="classified-post-grid-style-desc p-lg">
				<?php
				$content = get_the_excerpt();
				if ( empty( $content ) ) {
					$content = strip_tags( get_the_content() );
					if ( strlen( $content ) > 50 ) {
						$content = substr( $content, 0, 50 ) . '...';
					}
				} else {
					if ( strlen( $content ) > 50 ) {
						$content = substr( $content, 0, 50 ) . '...';
					}
				}
				echo esc_html( $content );

				?>
            </p>
            <p class="classified-post-grid-style-info-date">
				<?php echo '<a href="' . esc_url( get_permalink() ) . '">' . esc_html__( "Read More", "classified-pro" ) . '<i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>'; ?>
            </p>
        </div>
    </div>
</div>