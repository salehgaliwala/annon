<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified User Verification Class.
 *
 * @class Classified_Email_Verification
 */
class Classified_Email_Verification {

	public function __construct() {
		$email_verification = classified_get_setting( 'user_email_verification' );
		if ( $email_verification ) {
			add_action( 'user_register', array( $this, 'classified_send_verification_email' ) );
			add_filter( 'cubewp/email/types', array( $this, 'classified_email_types' ) );
			add_action( 'wp_head', array( $this, 'classified_verify_email' ) );
			add_filter( 'cubewp/email/shortcodes', array( $this, 'cubewp_email_shortcodes' ) );
			add_filter( 'cubewp/email/render/shortcodes', array( $this, 'cubewp_email_render_shortcodes' ), 10, 2 );
			add_action( 'wp_ajax_classified_resend_verification_email', array(
				$this,
				'classified_resend_verification_email'
			) );
			if ( ! classified_is_user_email_verified() ) {
				$post_types = CWP_all_post_types();
				if ( ! empty( $post_types ) && is_array( $post_types ) ) {
					foreach ( $post_types as $post_type => $name ) {
						add_filter( "cubewp/frontend/form/{$post_type}", array(
							$this,
							'classified_email_verification_form'
						) );
					}
				}
			}
		}
		add_filter( 'cubewp/options/sections', array( $this, 'classified_email_verification_settings' ) );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_email_verification_settings( $sections ) {
		// Adding Email Verification Settings Section
		$single_settings['classified_email_verification'] = array(
			'title'  => __( 'Email Verification', 'cubewp-classified' ),
			'id'     => 'classified_email_verification',
			'icon'   => 'dashicons dashicons-email-alt2',
			'fields' => array(
				array(
					'id'      => 'user_email_verification',
					'title'   => __( 'Enable User\'s Email Verification', 'cubewp-classified' ),
					'desc'    => __( 'Enable if you want to restrict user to verify their emails before submitting any cubewp post type form.', 'cubewp-classified' ),
					'type'    => 'switch',
					'default' => '0',
				),
			),
		);

		return classified_add_into_array_after_key( $sections, $single_settings, 'general-settings' );
	}

	public function classified_resend_verification_email() {
		if ( ! classified_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'classified_resend_verification_email' ) ) {
			wp_send_json_error( esc_html__( 'Security verification failed', 'cubewp-classified' ) );
		}
		$email = sanitize_email( $_POST['email'] );
		if ( ! empty( $email ) ) {
			$user         = get_user_by( 'email', $email );
			$current_user = wp_get_current_user();
			if ( ! $user || $current_user->user_email != $email ) {
				wp_send_json_error( esc_html__( 'Email mismatch, Please enter your correct email', 'cubewp-classified' ) );
			} else {
				$this->classified_send_verification_email( get_current_user_id(), false );
				wp_send_json_success( esc_html__( 'Email Sent. Please check your inbox.', 'cubewp-classified' ) );
			}
		} else {
			wp_send_json_error( esc_html__( 'Please enter correct email', 'cubewp-classified' ) );
		}
	}

	public function classified_email_types( $email_types ) {
		$new_types = array(
			array(
				'name'      => 'email-verification',
				'label'     => esc_html__( 'Email Verification', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'user_roles'
			),
		);

		return array_merge( $email_types, $new_types );
	}

	public function classified_send_verification_email( $user_id, $init_request = true ) {
		if ( $init_request ) {
			update_user_meta( $user_id, 'is_email_verified', 'no' );
		}
		$template = cubewp_get_email_template_by_user_id( $user_id, 'user', 'email-verification' );
		if ( $template ) {
			CubeWp_Emails::cubewp_send_email( false, $template, $user_id, false );
		}
	}

	public function cubewp_email_shortcodes( $shortcodes ) {
		$shortcodes[] = array(
			'label'     => esc_html__( 'Email Verification Link', 'cubewp-classified' ),
			'shortcode' => '{email_verification_link}',
		);

		return $shortcodes;
	}

	public function cubewp_email_render_shortcodes( $content, $user_id ) {
		if ( $user_id ) {
			$user_obj = get_userdata( $user_id );
			if ( ! is_wp_error( $user_obj ) && ! empty( $user_obj ) ) {
				$token = md5( uniqid( $user_id, true ) );
				update_user_meta( $user_id, 'email_verification_token', $token );
				$email             = $user_obj->user_email;
				$verification_link = add_query_arg( array(
					'action' => 'verify_email',
					'email'  => $email,
					'token'  => $token
				), esc_url( home_url( '/' ) ) );

				$content = str_replace( '{email_verification_link}', $verification_link, $content );
			}
		}

		return $content;
	}

	public function classified_email_verification_form() {
		ob_start();
		echo cwp_alert_ui( sprintf( esc_html__( 'Your email must be verified.', 'cubewp-classified' ), '<br>' ), 'info' );
		?>
        <form method="post" id="classified-verify-email">
			<?php
			wp_nonce_field( 'classified_resend_verification_email', 'nonce' )
			?>
            <div class="mb-3">
                <label for="classified-confirm-email"
                       class="form-label"><?php esc_html_e( 'Confirm Your Email', 'cubewp-classified' ); ?></label>
                <input type="email" id="classified-confirm-email" name="classified-confirm-email" class="form-control"
                       required
                       placeholder="<?php esc_html_e( 'Enter your email here', 'cubewp-classified' ); ?>">
            </div>
            <button class="classified-filled-btn" type="submit">
				<?php esc_html_e( 'Resend Verification Email', 'cubewp-classified' ); ?>
            </button>
        </form>
		<?php

		return ob_get_clean();
	}

	public function classified_verify_email() {
		if ( isset( $_GET['email'] ) && isset( $_GET['token'] ) ) {
			$email = sanitize_email( $_GET['email'] );
			$token = sanitize_text_field( $_GET['token'] );
			$user  = get_user_by( 'email', $email );
			if ( ! $user ) {
				echo cwp_alert_ui( esc_html__( 'Email does not exist.', 'cubewp-classified' ) );

				return false;
			}
			$email_verified = get_user_meta( $user->ID, 'is_email_verified', true );
			if ( $email_verified == 'yes' ) {
				echo cwp_alert_ui( esc_html__( 'Email already verified.', 'cubewp-classified' ), 'info' );

				return false;
			}
			$user_token = get_user_meta( $user->ID, 'email_verification_token', true );
			if ( $user_token == $token ) {
				update_user_meta( $user->ID, 'is_email_verified', 'yes' );
				delete_user_meta( $user->ID, 'email_verification_token' );
				echo cwp_alert_ui( esc_html__( 'Email verified.', 'cubewp-classified' ), 'success' );
			} else {
				echo cwp_alert_ui( esc_html__( 'Invalid request.', 'cubewp-classified' ) );
			}
		}

		return false;
	}
}