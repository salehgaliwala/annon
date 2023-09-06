<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
    <main class="container">
        <div id="section-inner thin " class="classified-error404-content">
            <div class="classified-error-main-content">
                <div class="error-image">
                    <img loading="lazy" width="100%" height="100%" src="<?php echo esc_url( CLASSIFIED_URL . 'assets/images/404.png' ); ?>" alt="<?php esc_html_e( 'Page Not Found', 'classified-pro' ); ?>">
                </div>
                <div class="classified-error-text">
                    <h1 class="entry-title"><?php esc_html_e( 'Oops!', 'classified-pro' ); ?></h1>
                    <div class="intro-text"><p><?php esc_html_e( '404 - PAGE NOT FOUND', 'classified-pro' ); ?></p></div>
                </div>
            </div>
        </div>
    </main>
<?php
get_footer();