<?php

if ( is_front_page() ) {
	$current_loc = 'classified_home_offcanvas';
} else {
	$current_loc = 'classified_inner_offcanvas';
}

$header_logo = classified_get_site_logo_url();

?>
<div class="offcanvas offcanvas-start" id="classified-offcanvas-navigation">
    <div class="offcanvas-header">
        <div class="offcanvas-title">
            <a href="<?php echo home_url() ?>">
                <img loading="lazy" width="100%" height="100%" src="<?php echo esc_url( $header_logo ); ?>" alt="<?php echo get_bloginfo(); ?>">
            </a>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
		<?php echo classified_get_navigation_quicks( 'classified-offcanvas-quick-container' ); ?>
        <div class="classified-offcanvas-menu-navigation">
			<?php echo classified_get_navigation( $current_loc ); ?>
        </div>
    </div>
</div>