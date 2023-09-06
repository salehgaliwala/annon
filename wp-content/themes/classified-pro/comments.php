<?php
defined( 'ABSPATH' ) || exit;

if ( comments_open() ) {
	if ( post_password_required() ) { ?>
        <p class="nocomments"><?php esc_html_e( 'This post is password protected. Enter the password to view comments.', 'classified-pro' ); ?></p>
		<?php
		return;
	}
	if ( have_comments() ) { ?>
        <h3 class="classified-comments-title">
			<?php
			if ( 1 == get_comments_number() ) {
				printf( __( 'One thought on %s', 'classified-pro' ), '&#8220;' . get_the_title() . '&#8221;' );
			} else {
				printf( _n( '%1$s thoughts on %2$s', '%1$s thoughts on %2$s', get_comments_number(), 'classified-pro' ), number_format_i18n( get_comments_number() ), '&#8220;' . get_the_title() . '&#8221;' );
			}
			?>
        </h3>
        <ol class="classified-comments-list">
			<?php wp_list_comments(); ?>
        </ol>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
            <nav class="navigation comment-navigation" role="navigation">
                <h3 class="screen-reader-text section-heading"><?php _e( 'Comment navigation', 'classified-pro' ); ?></h3>
                <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'classified-pro' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'classified-pro' ) ); ?></div>
            </nav><!-- .comment-navigation -->
		<?php endif; // Check for comment navigation ?>
	<?php } else { ?>
		<?php if ( comments_open() ) { ?>
            <p class="classified-no-comment-found"><?php esc_html_e( 'No Comment Found.', 'classified-pro' ); ?></p>
		<?php } else { ?>
            <p class="classified-no-comment-found"><?php esc_html_e( 'Comments are closed.', 'classified-pro' ); ?></p>
		<?php }
	}
	comment_form();
}