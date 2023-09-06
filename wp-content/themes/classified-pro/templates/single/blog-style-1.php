<?php
defined( 'ABSPATH' ) || exit;


if ( comments_open() ) {
	wp_enqueue_script( 'comment-reply' );
}
wp_enqueue_style( 'classified-blog-styles' );

$post_id            = get_the_ID();
$post_time          = human_time_diff( strtotime( get_the_date() ) );
$post_author        = get_post_field( 'post_author', $post_id );
$post_author_name   = get_userdata( $post_author );
$post_author_avatar = get_avatar_url( $post_author );
$post_author_name   = $post_author_name->display_name;
$post_thumbnail     = get_the_post_thumbnail_url( $post_id );

if ( empty( $post_thumbnail ) ) {
	$post_thumbnail = CLASSIFIED_URL . 'assets/images/placeholder.png';
}
?>
<div id="classified-single-post">
    <div class="classified-single-post-banner">
        <img src="<?php echo esc_url( $post_thumbnail ); ?>"
             alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"
             class="classified-single-post-banner-image">
        <div class="classified-single-post-banner-content container">
            <h1><?php echo get_the_title( $post_id ); ?></h1>
            <p class="classified-single-post-info p-lg">
                <span class="classified-single-post-date"><?php echo sprintf( esc_html__( "%s ago", "classified-pro" ), $post_time ); ?></span>
                <span class="classified-single-post-separator">|</span>
                <span class="classified-single-post-read-time"><?php echo sprintf( esc_html__( "%s min read", "classified-pro" ), classified_estimate_reading_time( get_the_content() ) ); ?></span>
            </p>
        </div>
    </div>
    <div class="container">
        <div class="classified-single-post-content">
            <div class="row">
                <div class="col-12 col-lg-8 mx-auto">
                    <div class="classified-single-post-author">
                        <img src="<?php echo esc_url( $post_author_avatar ); ?>"
                             alt="<?php echo esc_html( $post_author_name ); ?>">
                        <a href="<?php echo esc_url( get_author_posts_url( $post_author ) ); ?>">
                            <p class="p-lg"><?php echo sprintf( esc_html__( "By %s", "classified-pro" ), $post_author_name ); ?></p>
                        </a>
                    </div>
                    <div class="classified-single-post-content-container">
						<?php
						the_content();
						?>
                    </div>
                    <div class="classified-single-post-pagination">
						<?php
						wp_link_pages( array(
							'before'      => '<nav class="post-nav-links my-3" aria-label="' . esc_attr__( 'Page', 'classified-pro' ) . '"><span class="p-lg label">' . __( 'Pages:', 'classified-pro' ) . '</span>',
							'after'       => '</nav>',
							'link_before' => '<span class="page-number p-lg">',
							'link_after'  => '</span>',
						) );
						?>
                    </div>
                    <div class="classified-single-post-tags">
						<?php
						the_tags( '', '', '' );
						?>
                    </div>
                    <div class="classified-single-post-next-prev">
                        <div class="classified-single-post-prev"><?php previous_post_link(); ?></div>
                        <div class="classified-single-post-next"><?php next_post_link(); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="classified-single-post-comments">
            <div class="row">
                <div class="col-12 col-lg-8 mx-auto">
					<?php
					comments_template();
					?>
                </div>
            </div>
        </div>
    </div>
</div>