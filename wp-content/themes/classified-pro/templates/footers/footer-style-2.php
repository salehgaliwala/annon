<?php
defined( 'ABSPATH' ) || exit;

$footer_column = classified_get_setting( 'footer_column' );
$logo_url      = classified_get_setting( 'footer_logo', 'media_url' );
?>
<footer>
    <div id="classified-footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
	                <?php dynamic_sidebar( 'classified_before_footer_columns_row' ); ?>
                </div>
				<?php
                for ( $i = 1; $i <= $footer_column; $i ++ ) {
					$footer_column_name = 'classified_footer_column';
					if ( $i != 1 ) {
						$footer_column_name .= '-' . $i;
					}
					$col_class = 'col-6';
					if ( $footer_column == '3' ) {
						$col_class .= ' col-lg-4';
					}
					if ( $footer_column == '4' ) {
						$col_class .= ' col-lg-3';
					}
					if ( $footer_column == '5' ) {
                        if ( $i == 1 ) {
                            $col_class .= ' col-lg-4';
                        }else {
                            $col_class .= ' col-lg-2';
                        }
					}
					if ( $footer_column == '6' ) {
						$col_class .= ' col-lg-2';
					}
					?>
                    <div class="<?php echo esc_html( $col_class ); ?>">
                        <div class="classified-footer-column">
							<?php dynamic_sidebar( $footer_column_name ); ?>
                        </div>
                    </div>
				    <?php
                }
                ?>
                <div class="col-12">
	                <?php dynamic_sidebar( 'classified_after_footer_columns_row' ); ?>
                </div>
            </div>
        </div>
    </div>
    <div id="classified-sub-footer">
        <div class="container">
            <div class="classified-sub-footer-container">
                <div class="classified-footer-copyright d-flex">
					<?php
					echo sprintf( esc_html__( 'Copyright &copy; %s %s', 'classified-pro' ), date_i18n( _x( 'Y', 'copyright date format', 'classified-pro' ) ), get_bloginfo( 'name' ) );
					?>
                </div>
                <?php 
                if ( ! empty( $logo_url ) ) {
                    ?>
                    <div class="classified-sub-footer-logo">
                        <a href="<?php echo esc_url( home_url() ); ?>">
                            <img width="100%" height="100%" src="<?php echo esc_url( $logo_url ); ?>" loading="lazy" alt="<?php echo get_bloginfo( 'name' ); ?>">
                        </a>
                    </div>
                    <?php
                }
                ?>
				<?php classified_get_navigation( 'classified_sub_footer', true, 'classified-sub-footer-menu' ); ?>
            </div>
        </div>
    </div>
</footer>