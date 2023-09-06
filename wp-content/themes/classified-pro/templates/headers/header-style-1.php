<?php
defined('ABSPATH') || exit;

$header_logo = classified_get_site_logo_url();
?>
<header id="classified-header">
    <nav class="navbar navbar-expand-lg classified-header-top-container">
        <div class="container">
            <a class="navbar-brand" href="<?php echo esc_url(home_url()); ?>">
                <img loading="lazy" width="100%" height="100%" src="<?php echo esc_url($header_logo); ?>" alt="<?php echo get_bloginfo(); ?>">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php
                $args = array(
                    'container' => 'ul',
                    'menu_class' => 'navbar-nav ms-auto mb-2 mb-lg-0',
                    'walker' => new Classified_Walker_Nav_Menu()
                );
                wp_nav_menu($args);
                ?>
            </div>
        </div>
    </nav>
</header>