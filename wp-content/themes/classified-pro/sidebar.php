<?php
defined( 'ABSPATH' ) || exit;
?>
<section id="classified-sidebar">
    <?php
    if( ! dynamic_sidebar('default-sidebar')) {
        esc_html_e("There is no widget. You should add your widgets into", "classified-pro");
        ?>
        <strong>
            <?php esc_html_e("Default Sidebar.", "classified-pro"); ?>
        </strong>
        <?php
    }
    ?>
</section>