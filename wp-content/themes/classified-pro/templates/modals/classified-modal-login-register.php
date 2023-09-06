<?php
defined('ABSPATH') || exit;

?>
<div class="modal fade" id="classified-login-register" tabindex="-1" aria-labelledby="classified-login-register" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
            <i class="fa-solid fa-xmark classified-close-modal" data-bs-dismiss="modal" aria-label="Close" aria-hidden="true"></i>
			<div class="modal-body">
                <div class="classified-login-container">
                    <h5><?php esc_html_e("Login to your account", "classified-pro"); ?></h5>
                    <p><?php echo sprintf(esc_html__("Don't have any account? %sSignup%s", "classified-pro"), '<span class="classified-register-trigger">', '</span>'); ?></p>
                    <div class="classified-login-form-container classified-frontend-form-container">
                        <?php echo do_shortcode('[cwpLoginForm]') ?>
                    </div>
                </div>
                <div class="classified-register-container d-none">
                    <h5><?php esc_html_e("Create new account", "classified-pro"); ?></h5>
                    <p><?php echo sprintf(esc_html__("Have an account? %sLogin%s", "classified-pro"), '<span class="classified-login-trigger">', '</span>'); ?></p>
                    <div class="classified-register-form-container classified-frontend-form-container">
	                    <?php echo do_shortcode('[cwpRegisterForm role="subscriber"]') ?>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
