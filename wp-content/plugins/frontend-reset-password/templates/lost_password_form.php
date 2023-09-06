<?php
/**
 * Frontend Reset Password - Main lost password form
 * 
 * @version 1.1.91
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="password-lost-form-wrap">

	<?php if ( ! empty( $errors ) ) : ?>

		<?php if ( is_array( $errors ) ) : ?>

			<?php foreach ( $errors as $error ) : ?>
				<p class="som-password-sent-message som-password-error-message">
					<span><?php echo esc_html( $error ); ?></span>
				</p>
			<?php endforeach; ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php if ( $email_confirmed ) : ?>
		<?php $confirmed_text = esc_html__( 'An email has been sent. Please check your inbox.' , 'frontend-reset-password' ); ?>
		<p class="som-password-sent-message">
			<span><?php echo esc_html( $confirmed_text ); ?></span>
		</p>
	<?php endif; ?>

	<form id="lostpasswordform" method="post" class="account-page-form">
		<fieldset>
			<legend><?php echo $form_title; ?></legend>

			<div class="somfrp-lost-pass-form-text">
				<?php echo $lost_text_output;?>
			</div>

			<p class="no-margin">
				<label for="somfrp_user_info"><?php _e( 'Email Address or Username', 'frontend-reset-password' ); ?></label>
				<input type="text" name="somfrp_user_info" id="somfrp_user_info" required autocomplete="username">
			</p>
			

			<div class="lostpassword-submit">
				<?php wp_nonce_field( 'somfrp_lost_pass', 'somfrp_nonce' ); ?>
				<input type="hidden" name="submitted" id="submitted" value="true">
				<input type="hidden" name="somfrp_action" id="somfrp_post_action" value="somfrp_lost_pass">
				<button type="submit" id="reset-pass-submit" name="reset-pass-submit" class="button big-btn"><?php echo $button_text; ?></button>
			</div>

		</fieldset>
	</form>

</div>