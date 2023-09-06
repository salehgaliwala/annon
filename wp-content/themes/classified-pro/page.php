<?php
defined( 'ABSPATH' ) || exit;

get_header(); ?>
    <div class="container">
        <div class="classified-page-content-container">
			<?php
			while ( have_posts() ): the_post();
				the_content();
				wp_link_pages();
				comments_template('', true);
			endwhile;
			?>
        </div>
    </div>
<?php
get_footer();
