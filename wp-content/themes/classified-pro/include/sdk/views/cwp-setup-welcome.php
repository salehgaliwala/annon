<?php
$CWP_License_verification = new CWP_License_verification();
$PATH_URL                 = $CWP_License_verification->PATH_URL;
?>
<!---* You can design your own welcome screen of setup process here *-->
<div class="importer-tab-started-main">
    <header class="importer-step-form-lisance-header">
        <img src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/renter-logo.png' ); ?>" alt="">
    </header>
    <img src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/step_2_header.png' ); ?>" alt="">
    <h2 class="welcome-text"><?php echo esc_html__( 'Classified Pro - ReCommerce Classified WordPress Theme', 'classified-pro' ); ?></h2>
    <p class="welcome-des"><?php echo esc_html__( 'Thank you so much for purchasing Classified Pro WordPress Theme built with CubeWP Framework. We are very grateful for your support and with this setup wizard we hope to make your journey a little bit easier.', 'classified-pro' ); ?></p>
    <div class="get-started-features">
        <div class="cube-started-features">
            <img src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/thumb-search.png' ); ?>" alt="">
            <h5>
                <span><?php echo esc_html__( 'Verify', 'classified-pro' ); ?></span><br><?php echo esc_html__( 'License Key', 'classified-pro' ); ?>
            </h5>
        </div>
        <div class="cube-started-features">
            <img src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/thumb-search1.png' ); ?>" alt="">
            <h5>
                <span><?php echo esc_html__( 'Install', 'classified-pro' ); ?></span><br><?php echo esc_html__( 'Required Plugins', 'classified-pro' ); ?>
            </h5>
        </div>
        <div class="cube-started-features">
            <img src="<?php echo esc_url( $PATH_URL . '/sdk/assets/images/importer/thumb-search3.png' ); ?>" alt="">
            <h5>
                <span><?php echo esc_html__( 'Import', 'classified-pro' ); ?></span><br><?php echo esc_html__( 'Dummy Data', 'classified-pro' ); ?>
            </h5>
        </div>
    </div>
    <div class="next-step-import"
         id="next-step-import"><?php echo esc_html__( 'get started', 'classified-pro' ); ?></div>
</div>