<?php
/**
 * Quick SignUP module class.
 *
 * @package cubewp-addon-frontend/cube/classes/
 * @version 1.0.11
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Quick_SignUP
 */
 
class CubeWp_Quick_SignUP {
	public function __construct() {
		global $cwpOptions;
		if( isset($cwpOptions['allow_instant_signup']) && $cwpOptions['allow_instant_signup']  ){
			if( !is_user_logged_in() ){
				add_filter('cubewp/frontend/instant_signup/field', array($this, 'post_type_cube_instant_signup'), 11 , 2);
				$cwp_custom_types = cwp_post_types();
				if ( isset( $cwp_custom_types ) && ! empty( $cwp_custom_types ) ) {
					foreach ( $cwp_custom_types as $slug => $label ) {
						add_action('cubewp/'.$slug.'/before/submit/actions', array($this, 'cubewp_quick_signup_actions') , 7 , 1 );
					}
				}
				add_action( 'wp_ajax_nopriv_cwp_send_verification_email', array($this,'cwp_send_verification_email') );
			}
		}
	}
	
	/**
	 * Method post_type_cube_instant_signup
	 *
	 * @param array $field_options
	 * @param null $output
	 *
	 * @return html
     * @since 1.0.11
	 */
	public function post_type_cube_instant_signup( $output ,  $field_options ){
		global $cwpOptions;
		$output ='
		<div class="cwp-field-container cwp-field-checkbox" data-id="cwp-quick-checkbox-container">
			<div class="cwp-checkbox-container">
				<div class="cwp-field-checkbox-container">
					<div class="cwp-field-checkbox">
						<input type="checkbox" id="cwp_quick_checkbox_field" name="cwp_quick_checkbox_field" class="custom-control-input form-control  ">
						<label for="cwp_quick_checkbox_field">Are you returning user? Login</label>
					</div>
				</div>
			</div>
		</div>
		<div class="cwp-quick-sign-up-container cwp-field-container">
			<div class="cwp-field-container cwp-field-text" data-id="cwp-quick-signup-username">
				<label for="cwp-quick-signup-username">User Name<span class="cwp-required">*</span></label>
				<input type="text" id="cwp-quick-signup-username" name="cwp-quick-signup-username" class="form-control form-control required" value="">
			</div>
			<div class="cwp-field-container cwp-field-text" data-id="cwp-quick-signup-email">
				<label for="cwp-quick-signup-email">Email<span class="cwp-required">*</span></label>
				<input type="email" id="cwp-quick-signup-email" name="cwp-quick-signup-email" class="form-control form-control required" value="">
			</div>';
			//OTP field if enabled
			if( isset($cwpOptions['email_otp_verification']) && $cwpOptions['email_otp_verification']  ){
				$output .= '<a class="cwp_email_verification margin-top-10">Send OTP to verify email</a>';
			}
			//Password and confirm password field if enabled
			if( isset($cwpOptions['allow_password']) && $cwpOptions['allow_password']  ){
				$output .='<div class="cwp-field-container cwp-field-text" data-id="cwp-quick-signup-password">
				<label for="cwp-quick-signup-password">Password<span class="cwp-required">*</span></label>
				<input type="password" id="cwp-quick-signup-password" name="cwp-quick-signup-password" class="form-control form-control required" value="">
			</div>
			<div class="cwp-field-container cwp-field-text" data-id="cwp-quick-signup-confirm-password">
				<label for="cwp-quick-signup-confirm-password">Confirm Password<span class="cwp-required">*</span></label>
				<input type="password" id="cwp-quick-signup-confirm-password" name="cwp-quick-signup-confirm-password" class="form-control form-control required" value="">
			</div>';
			}
			
		$output .='</div>
		<div class="cwp_quick_sign_in_container cwp-field-container" style="display:none">
			<div class="cwp-field-container cwp-field-text" data-id="cwp-quick-signin-username">
				<label for="cwp-quick-signin-username">User Name<span class="cwp-required">*</span></label>
				<input type="text" id="cwp-quick-signin-username" name="cwp-quick-signin-username" class="form-control form-control required" value="">
			</div>
			<div class="cwp-field-container cwp-field-text" data-id="cwp-quick-signin-password">
				<label for="cwp-quick-signin-password">Password<span class="cwp-required">*</span></label>
				<input type="password" id="cwp-quick-signin-password" name="cwp-quick-signin-password" class="form-control form-control required" value="">
			</div>
		</div>';
		return $output;
	}
	
	/**
	 * Method cubewp_quick_signup_actions
	 *
	 * @param array $POST
	 *
	 * @return array json
     * @since 1.0.11
	 */
	public function cubewp_quick_signup_actions( $POST ){
		global $cwpOptions;
		if( isset( $POST['cwp-quick-signin-username'] ) && isset( $POST['cwp-quick-signup-username'] ) ){
			// if returning user
			if( isset( $POST['cwp_quick_checkbox_field'] ) && sanitize_text_field( $POST['cwp_quick_checkbox_field'] ) == 'on' ){
				$username = sanitize_text_field( $POST['cwp-quick-signin-username'] );
				$password = sanitize_text_field( $POST['cwp-quick-signin-password'] );
				// check if username and password are not empty
				if(empty($username)){
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  esc_html__('Sorry! UserName field is empty.', 'cubewp-frontend'),
						)
					);
				}
				if(empty($password)){
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  esc_html__('Sorry! Password field is empty.', 'cubewp-frontend'),
						)
					);
				}
				// check if username and password are verified
				$user = wp_signon( array(
				    'user_login' => $username,
					'user_password' => $password,
					'remember' => true
				) );
				// error if username and password are not matched
				if ( is_wp_error( $user ) ) {
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  $user->get_error_message(),
						)
					);
				}
				//set user cookies and authentication
				wp_set_current_user( $user->ID, $user->user_login );
				wp_set_auth_cookie( $user->ID );
				do_action( 'wp_login', $user->user_login, $user );
				
			}else{
				// if new user signup email verification enabled
				if( isset($cwpOptions['email_otp_verification']) && $cwpOptions['email_otp_verification']  ){
					if( isset($POST['cwp-quick-signup-otp-verification']) ){
						$otp = sanitize_text_field( $POST['cwp-quick-signup-otp-verification'] );
						$cookie_token = isset($_COOKIE['email_verification_token']) ? $_COOKIE['email_verification_token'] : null;
						if ( !$cookie_token  || $cookie_token != $otp ) {
							wp_send_json(
								array(
									'type'        =>  'error',
									'msg'         =>  esc_html__('Please verify your email before post submission.', 'cubewp-frontend'),
								)
							);
						}
					}else{
						wp_send_json(
							array(
								'type'        =>  'error',
								'msg'         =>  esc_html__('Email verification is required for post submission.', 'cubewp-frontend'),
							)
						);
					}
				}
				$username = sanitize_text_field( $POST['cwp-quick-signup-username'] );
				$email = sanitize_text_field( $POST['cwp-quick-signup-email'] );
				$password = '';
				$existinUserId = username_exists( $username );
				if(empty($username)){
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  esc_html__('Sorry! UserName field is empty.', 'cubewp-frontend'),
						)
					);
				}
				if(empty($email)){
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  esc_html__('Sorry! Email field is empty.', 'cubewp-frontend'),
						)
					);
				}
				// check if password field is enabled
				if( isset($cwpOptions['allow_password']) && $cwpOptions['allow_password']  ){
					$password = sanitize_text_field( $POST['cwp-quick-signup-password'] );
					$confpassword = sanitize_text_field( $POST['cwp-quick-signup-confirm-password'] );
					if( empty($password) || empty($confpassword) ){
						wp_send_json(
							array(
								'type'        =>  'error',
								'msg'         =>  esc_html__('Password field is empty.', 'cubewp-frontend'),
							)
						);
					}
					if( $password != $confpassword ){
						wp_send_json(
							array(
								'type'        =>  'error',
								'msg'         =>  esc_html__('Password does not match. Please try again.', 'cubewp-frontend'),
							)
						);
					}
				}
				// check if user name already exists
				if(!empty($existinUserId)){
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  esc_html__('Sorry! UserName already exists.', 'cubewp-frontend'),
						)
					);
				}
				// check if email already exists
				if( email_exists($email)== true  ){
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  esc_html__('Sorry! Email already exists.', 'cubewp-frontend'),
						)
					);
				}
				// random password generated in case of no password set
				if( empty($password) ){
					$password = wp_generate_password( $length=12, $include_standard_special_chars=false );
				}
				// new user registration
				$user_id = wp_create_user( $username, $password, $email );
				if ( is_wp_error( $user_id ) ) {
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  $user_id->get_error_message(),
						)
					);
				}
				// check if username and password are verified
				$creds['user_login'] = $username;
				$creds['user_password'] = $password;
				$creds['remember'] = true;
				$user = wp_signon( $creds, true );
				// error if username and password are not matched
				if ( is_wp_error( $user ) ) {
					wp_send_json(
						array(
							'type'        =>  'error',
							'msg'         =>  $user->get_error_message(),
						)
					);
				}
				// send password in case password is not enabled
				if( !isset($POST['cwp-quick-signup-password']) ){
					$subject           = esc_html__( 'User Registration Password', 'cubewp-frontend' );
					$message           = esc_html__( 'Thanks for registering to ', 'cubewp-frontend' ).site_url().'<br> '.esc_html__( 'UserName: ', 'cubewp-frontend' ).$username.'<br>'.'Password: '.$password;
					cubewp_send_mail( $email, $subject, $message );
				}
				//set user cookies and authentication
				wp_set_current_user( $user->ID, $user->user_login );
				wp_set_auth_cookie( $user->ID );
				do_action( 'wp_login', $user->user_login, $user );
			}
		}else{
			wp_send_json(
				array(
					'type'        =>  'error',
					'msg'         =>  esc_html__('Sorry! Quick SignUp field is enabled so make sure you have added it into the form.', 'cubewp-frontend'),
				)
			);
		}
	}
	
	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
	
	/**
	 * Method cwp_send_verification_email
	 *
	 * Ajax call back function to send OTP email
	 *
	 * @return json array
     * @since 1.0.11
	 */
	public function cwp_send_verification_email(){
		//verify nonce of ajax call
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'cubewp_submit_post_form' ) ) {
			wp_send_json_error( esc_html__( 'Security verification failed', 'cubewp-frontend' ) );
		}
		$email = sanitize_email( $_POST['email'] );
		$process = sanitize_text_field( $_POST['process'] );
		$otp = sanitize_text_field( $_POST['otp'] );
		//send token to provided email
		if( $process == 'send_token' ){
			if ( ! empty( $email ) ) {
				//send email on entered value
				$this->cwp_send_verification_email_call( $email , false );
				wp_send_json_success( esc_html__( 'Email Sent. Please check your inbox.', 'cubewp-frontend' ) );
				
			} else {
				wp_send_json_error( esc_html__( 'Please enter correct email', 'cubewp-frontend' ) );
			}
		}else if( $process == 'verify_token'){
			//verify user token
			$cookie_token = isset($_COOKIE['email_verification_token']) ? $_COOKIE['email_verification_token'] : null;
			if ($cookie_token  && $cookie_token == $otp ) {
				wp_send_json_success( esc_html__( 'Email is verified.', 'cubewp-frontend' ) );
			}else {
				wp_send_json_error( esc_html__( 'Token invalid or expired', 'cubewp-frontend' ) );
			}
		}
	}
	
	/**
	 * Method cwp_send_verification_email_call
	 *
	 * @param string
	 *
     * @since 1.0.11
	 */
	function cwp_send_verification_email_call( $email ) {
		$token = mt_rand(100000, 999999);
		setcookie('email_verification_token', $token, time() + 3600, '/');
		$subject           = esc_html__( 'Email Verification', 'cubewp-frontend' );
		$message           = '<div style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5;">
			<div style="margin: 0 auto;">
				<p>'.esc_html__( 'Hello,', 'cubewp-frontend' ).'</p>
				<p>'.esc_html__( 'Please use the following One-Time Password (OTP) to verify your email address:', 'cubewp-frontend' ).'</p>
				<h2 style="font-size: 24px; font-weight: bold; margin-top: 20px; margin-bottom: 20px;">'.$token.'</h2>
				<p>'.esc_html__( 'If you did not request this verification, please ignore this email.', 'cubewp-frontend' ).'</p>
				<p>'.esc_html__( 'Thank you for using our service!', 'cubewp-frontend' ).'</p>
				<p style="margin-top: 40px;">'.esc_html__( 'Best regards,', 'cubewp-frontend' ).'</p>
				<p>'.get_bloginfo( 'name' ).'</p>
			</div>
		</div>';
		cubewp_send_mail( $email, $subject, $message );
	}
}