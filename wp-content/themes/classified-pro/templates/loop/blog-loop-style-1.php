<?php
defined( 'ABSPATH' ) || exit;

$post_id          = get_the_ID();
$post_time        = human_time_diff( strtotime( get_the_date() ) );
$post_author      = get_post_field( 'post_author', $post_id );
$post_author_name = get_userdata( $post_author );
$post_term        = wp_get_post_terms( $post_id, 'category' );
$post_term        = $post_term[0] ?? '';
?>
<div class="col-12 col-lg-6">
    <div <?php post_class( 'classified-post-grid' ) ?>>
        <div class="classified-post-grid-thumbnail">
            <a href="<?php echo esc_url( get_permalink() ); ?>" class="stretched-link"></a>
            <img loading="lazy" width="100%" height="100%" src="<?php echo classified_get_post_featured_image( $post_id, false, 'classified-grid' ); ?>"
                 alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">
			<?php if ( ! empty( $post_term ) ) { ?>
                <a href="<?php echo esc_url( get_term_link( $post_term->term_id ) ); ?>"
                   class="classified-post-grid-term"><?php echo esc_html( $post_term->name ); ?></a>
			<?php } ?>
        </div>
        <div class="classified-post-grid-content">
            <a href="<?php echo esc_url( get_permalink() ); ?>"><h2><?php echo get_the_title(); ?></h2></a>
            <p class="classified-post-grid-info">
                <span class="classified-post-date"><?php echo sprintf( esc_html__( "%s ago", "classified-pro" ), $post_time ); ?></span>
                <span class="classified-post-grid-separator">/</span>
                <a href="<?php echo esc_url( get_author_posts_url( $post_author ) ) ?>"><span
                            class="classified-post-author"><?php echo sprintf( esc_html__( "By %s", "classified-pro" ), $post_author_name->display_name ); ?></span></a>
                <span class="classified-post-grid-separator">/</span>
                <span class="classified-post-read-time"><?php echo sprintf( esc_html__( "%s min read", "classified-pro" ), classified_estimate_reading_time( get_the_content() ) ); ?></span>
            </p>
            <p class="classified-post-grid-desc p-lg">
				<?php
				$content = get_the_excerpt();
				if ( empty( $content ) ) {
					$content = strip_tags( get_the_content() );
					if ( str_word_count( $content ) > 40 ) {
						$words   = str_word_count( $content, 2 );
						$pos     = array_keys( $words );
						$content = substr( $content, 0, $pos[40] ) . '...';
					}
				}
				echo esc_html( $content );
				?>
            </p>
            <div class="d-flex justify-content-center align-items-center">
                <a class="classified-post-grid-read-more"
                   href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html__( "Read more", "classified-pro" ); ?></a>
            </div>
            <div class="classified-post-grid-content-comment">
                <i class="fa-regular fa-comments" aria-hidden="true"></i><span><?php echo get_comments_number(); ?></span>
            </div>
        </div>
    </div>
</div>