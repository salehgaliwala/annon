<?php
defined('ABSPATH') || exit;
global $classified_post_types;
?>
<div class="modal fade" id="classified-item-type" tabindex="-1" aria-labelledby="classified-item-type" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <i class="fa-solid fa-xmark classified-close-modal" data-bs-dismiss="modal" aria-label="Close"></i>
            <div class="modal-body p-0">
                <div class="classified-item-types-content-container">
                    <h3 class="text-center classified-top-icon"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></h3>
                    <h3 class="text-center classified-top-title"><?php esc_html_e( 'What do you like to post today?', 'classified-pro' ); ?></h3>
                    <div class="classified-item-types-container">
						<?php
						if ( ! empty( $classified_post_types ) && is_array( $classified_post_types ) ) {
							foreach ( $classified_post_types as $classified_post_type ) {
                                if ( ! post_type_exists( $classified_post_type ) ) {
                                    continue;
                                }
								$post_type = get_post_type_object( $classified_post_type );
								$post_type_icon = classified_get_setting( $classified_post_type . '_icon' );
								$icon = ! empty( $post_type_icon ) ? $post_type_icon : 'dashicons ' . $post_type->menu_icon;
								$label = $post_type->labels->singular_name;
								$ad_submission = classified_get_setting( 'submit_edit_page', 'page_url', $classified_post_type );
                                if ( $ad_submission ) {
                                    ?>
                                    <div class="classified-item-type">
                                        <a href="<?php echo esc_url( $ad_submission ); ?>" class="stretched-link"></a>
                                        <i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
                                        <?php echo esc_html( $label ); ?>
                                    </div>
                                    <?php
                                }
							}
						}
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>