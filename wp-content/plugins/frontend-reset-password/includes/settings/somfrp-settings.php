<?php
/**
 * Frontend Reset Password - Settings
 * 
 * @version	1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function somfrp_get_plugin_link() {
	return apply_filters( 'somfrp_plugin_link', 'options-general.php?page=somfrp_options_page' );
}

function somfrp_get_donate_link() {
	return 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VAYF6G99MCMHU';
}

add_action ('after_setup_theme', 'somfrp_after_setup_plugin');
function somfrp_after_setup_plugin() {
	add_filter( 'plugin_action_links_' . SOMFRP_PLUGIN_BASENAME, 'somfrp_settings_link' );
}
function somfrp_settings_link( $links ) {
	$url = get_admin_url() . somfrp_get_plugin_link();
	$settings_link = '<a href="' . $url . '">' . esc_html__( 'Settings', 'frontend-reset-password' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

add_filter( 'plugin_row_meta', 'somfrp_plugin_row_meta', 10, 2 );
function somfrp_plugin_row_meta( $links, $file ) {
	if ( SOMFRP_PLUGIN_BASENAME == $file ) {
		$new_links = array(
			'donate' => '<a href="' . somfrp_get_donate_link() . '" target="_blank">Donate</a>',
			'more' => '<a href="https://profiles.wordpress.org/wpenhanced/" target="_blank">More Plugins</a>'
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}

add_action( 'admin_enqueue_scripts', 'somfrp_admin_load_scripts' );
function somfrp_admin_load_scripts() {

	if ( ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'somfrp_options_page' ) ) {
		/**
		 * If the current admin page is this plugin's settings page
		 */
		wp_register_style( 'somfrp-settings-style', plugins_url('/assets/css/somfrp-settings-style.css', SOMFRP_FILE ) );
		wp_enqueue_style( 'somfrp-settings-style' );
	}

}

add_action('admin_menu', 'somfrp_main_admin_menu', 95);
function somfrp_main_admin_menu() {

	add_options_page(
		'Frontend Reset Password',
		'Frontend Reset Password',
		'manage_options',
		'somfrp_options_page',
		'somfrp_options_page'
	);

}

function somfrp_options_page() {

	somfrp_get_settings_header_content();

	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings';
	
	$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'general';

	if ( $active_tab == 'home' ) {
	
		//som-settings_settings_home();

		//do_action( 'somfrp_settings_after_home', $active_tab );

	}

	if ( $active_tab == 'settings' ) {
	
		$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'general';
		
		if ( 'general' == $active_section ) {
	
			somfrp_gen_settings_content();
			
		}
	
		if ( 'security' == $active_section ) {
	
			somfrp_security_settings_content();
			
		}

		do_action( 'somfrp_settings_page_content', $active_section );
	
	}

	if ( $active_tab == 'support' ) {
	
		somfrp_support_guide();

		do_action( 'somfrp_support_page_content', $active_section );

	}

	if ( $active_tab == 'more' ) {
	
		somfrp_settings_more();

		do_action( 'somfrp_more_page_content', $active_section );

	}

	do_action( 'somfrp_settings_after_more', $active_tab );

	do_action( 'somfrp_settings_bottom' );

}

add_action( 'somfrp_settings_bottom', 'somfrp_get_settings_bottom_content', 10 );
function somfrp_get_settings_bottom_content() { ?>

	<div class="som-settings-container som-settings-message-footer">
		<div class="som-settings-row">
			<div class="som-settings-col-12">
				<p>If you like this plugin please leave us a <a href="https://wordpress.org/plugins/frontend-reset-password/reviews/#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> review on WordPress.org!</p>
			</div>
		</div>
	</div>

<?php

}

function somfrp_get_settings_header_content() {

	somfrp_get_admin_header(); ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
			<div class="som-settings-col-12 som-main-plugin-content">
				<h1>Frontend Reset Password</h1>
			</div>
		</div>
	</div>

	<div id="somfrp-admin-notices" class="som-settings-container som-settings-errors">
		<div class="som-settings-row">
			<div class="som-settings-col-12">
				<?php settings_errors(); ?>
			</div>
		</div>
	</div>
	
	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
			
				<?php somfrp_get_settings_tabs(); ?>
				
				<?php somfrp_get_settings_sub_tabs(); ?>
	
			</div>
		</div>
	</div>

<?php

}

function somfrp_get_settings_tabs() {

	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings'; ?>
		
	<h2 class="nav-tab-wrapper">
		<?php
		/*
		<a href="<?php echo somfrp_get_plugin_link(); ?>&tab=home" class="nav-tab <?php echo $active_tab == 'home' ? 'nav-tab-active' : ''; ?>">Home</a>
			<?php do_action( 'somfrp_settings_tabs_after_home', $active_tab ); ?>
		*/
			?>
		<a href="<?php echo somfrp_get_plugin_link(); ?>&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
			<?php do_action( 'somfrp_settings_tabs_after_settings', $active_tab ); ?>
		<a href="<?php echo somfrp_get_plugin_link(); ?>&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
			<?php do_action( 'somfrp_settings_tabs_after_more', $active_tab ); ?>
		<a href="<?php echo somfrp_get_plugin_link(); ?>&tab=more" class="nav-tab <?php echo $active_tab == 'more' ? 'nav-tab-active' : ''; ?>">More</a>
			<?php do_action( 'somfrp_settings_tabs_after_more', $active_tab ); ?>
	</h2>

<?php

}

function somfrp_get_settings_sub_tabs() {

	if ( ! isset( $_GET[ 'tab' ] ) || $_GET[ 'tab' ] == 'settings' ) {

		$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'general'; ?>

		<ul class="subsubsub">
			<li><a href="<?php echo somfrp_get_plugin_link(); ?>&tab=settings&section=general" class="<?php echo $active_section == 'general' ? 'current' : ''; ?>">General</a> | </li>
				<?php do_action( 'somfrp_settings_subtabs_after_general', $active_section ); ?>
			<li><a href="<?php echo somfrp_get_plugin_link(); ?>&tab=settings&section=security" class="<?php echo $active_section == 'security' ? 'current' : ''; ?>">Security</a></li>
				<?php do_action( 'somfrp_settings_subtabs_after_security', $active_section ); ?>
		</ul>
			
	<?php

	}

}

function somfrp_gen_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="somfrp-gen-settings-form-wrap">
			
					<?php
						settings_fields( 'somfrp_gen_settings' );
						do_settings_sections( 'somfrp_gen_settings' );
						submit_button();
					?>
			
					</div>
			
				</form>
		
			</div>

		</div>
	</div>

<?php

}

function somfrp_security_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="somfrp-gen-settings-form-wrap">
			
					<?php
						settings_fields( 'somfrp_security_settings' );
						do_settings_sections( 'somfrp_security_settings' );
						submit_button();
					?>
			
					</div>
			
				</form>
		
			</div>

		</div>
	</div>

<?php

}

function somfrp_support_guide() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7 som-settings-guide som-settings-guide-features">
	
				<h2>Quick Start Guide</h2>
				<p>You'll be up and running in the flashiest of flashes.</p>

				<ul>
					<li>
						<h3>Step 1: Add The Shortcode</h3>
						<p>Put the reset form shortcode in one of your pages, either existing or a brand new one!</p>
						<p style="font-weight: bold;"><code>[reset_password]</code></p>
					</li>
					<li>
						<h3>Step 2: Set the Reset Password Page Setting</h3>
						<p>Go to the plugin <a href="<?php echo somfrp_get_plugin_link(); ?>&tab=settings">settings</a> tab and select your reset password page from the dropdown box labelled "Reset Password Page".</p>
					</li>
					<li>
						<h3>Step 3: Customise! (optional)</h3>
						<p>Have a play with the settings to customise your form however you like.</p>
					</li>
				</ul>
		
			</div>

		</div>
	</div>


<?php }

function somfrp_settings_more() {

	$somdn_logo = plugins_url( '/assets/images/somdn.jpg', SOMFRP_FILE );
	$responsive_youtube = plugins_url( '/assets/images/responsive-youtube.jpg', SOMFRP_FILE );


	?>

	<div class="som-settings-container" style="padding-top: 10px;">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12 som-settings-guide">

				<p style="padding-bottom: 25px;">Looking for more plugins by <strong>WP Enhanced?</strong></p>

				<div class="som-settings-plugin-other-wrap">

					<div class="som-settings-plugin-other">
						<a class="som-settings-plugin-other-link" href="https://wordpress.org/plugins/download-now-for-woocommerce/" target="_blank">
							<div class="som-settings-plugin-other-img">
								<img src="<?php echo $somdn_logo; ?>">
							</div>
							<div class="som-settings-plugin-other-bottom">
								<h3>Free Downloads - WooCommerce</h3>
							</div>
						</a>
					</div>
					
					<div class="som-settings-plugin-other">
						<a class="som-settings-plugin-other-link" href="https://wordpress.org/plugins/responsive-youtube-videos/" target="_blank">
							<div class="som-settings-plugin-other-img">
								<img src="<?php echo $responsive_youtube; ?>">
							</div>
							<div class="som-settings-plugin-other-bottom">
								<h3>Responsive Videos</h3>
							</div>	
						</a>
					</div>
					

				</div>

			</div>

		</div>
	</div>

<?php

}

function somfrp_get_admin_header() {
	include_once( SOMFRP_PATH . 'includes/settings/somfrp-settings-header.php' );
}

function somfrp_get_admin_footer() {
	include_once( SOMFRP_PATH . 'includes/settings/somfrp-settings-footer.php' );
}

add_action( 'admin_footer', 'somfrp_settings_footer' );
function somfrp_settings_footer() {

	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'somfrp_options_page' ) {
		somfrp_get_admin_footer();
	}

}

add_action( 'admin_init', 'somfrp_settings_init' );
function somfrp_settings_init() {

	register_setting( 'somfrp_gen_settings', 'somfrp_gen_settings' );

	add_settings_section(
		'somfrp_gen_settings_section',
		__( 'General Settings', 'frontend-reset-password' ),
		'somfrp_gen_settings_section_callback',
		'somfrp_gen_settings'
	);

	add_settings_field(
		'somfrp_reset_page',
		__( 'Page Settings', 'frontend-reset-password' ),
		'somfrp_reset_page_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field( 
		'somfrp_request_success_page',
		NULL,
		'somfrp_request_success_page_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section',
		array( 'class' => 'somfrp-settings-table-no-top' )
	);

	add_settings_field( 
		'somfrp_reset_success_page',
		NULL,
		'somfrp_reset_success_page_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section',
		array( 'class' => 'somfrp-settings-table-no-top' )
	);

	add_settings_field( 
		'somfrp_login_page',
		NULL,
		'somfrp_login_page_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section',
		array( 'class' => 'somfrp-settings-table-no-top' )
	);

	add_settings_field(
		'somfrp_reset_form_title',
		__( 'Form Title', 'frontend-reset-password' ),
		'somfrp_reset_form_title_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field(
		'somfrp_reset_lost_message',
		__( 'Form Text', 'frontend-reset-password' ),
		'somfrp_reset_lost_message_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field(
		'somfrp_reset_new_message',
		NULL,
		'somfrp_reset_new_message_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field(
		'somfrp_reset_button_text',
		__( 'Button Text', 'frontend-reset-password' ),
		'somfrp_reset_button_text_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field(
		'somfrp_notice_bg',
		__( 'Notice Background', 'frontend-reset-password' ),
		'somfrp_notice_bg_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field(
		'somfrp_email_message',
		__( 'Emails', 'frontend-reset-password' ),
		'somfrp_email_message_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field(
		'somfrp_email_subject',
		__( 'Email Subject', 'frontend-reset-password' ),
		'somfrp_email_subject_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field(
		'somfrp_from_name',
		__( 'Email Sender', 'frontend-reset-password' ),
		'somfrp_from_name_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	add_settings_field(
		'somfrp_email_address',
		NULL,
		'somfrp_email_address_render',
		'somfrp_gen_settings',
		'somfrp_gen_settings_section'
	);

	register_setting( 'somfrp_security_settings', 'somfrp_security_settings' );
	add_settings_section(
		'somfrp_security_settings_section',
		__( 'Security Settings', 'frontend-reset-password' ),
		'somfrp_security_settings_section_callback',
		'somfrp_security_settings'
	);

	add_settings_field(
		'somfrp_pass_length', 
		__( 'Minimum Length', 'frontend-reset-password' ),
		'somfrp_pass_length_render',
		'somfrp_security_settings',
		'somfrp_security_settings_section'
	);

}

function somfrp_email_message_render() {

	$options = get_option( 'somfrp_gen_settings' );
	$value = isset( $options['somfrp_email_message'] ) ? $options['somfrp_email_message'] : '' ;
	$default = esc_html__( 'Please enter your email address or username. You will receive a link to create a new password via email.', 'frontend-reset-password' ); ?>

	<div class="somfrp-gen-settings-wrap">

		<p style="margin-bottom: 15px;"><strong>Customise the email sent to your user.</strong></p>
		<p style="margin-bottom: 15px;">Default:</p>

		<div class="somfrp-email-temp-wrap">
			<p>Someone requested that the password be reset for the following account:</p>
			<p>Username: {username}</p>
			<p>If this was a mistake, just ignore this email and nothing will happen. To reset your password, visit the following address:</p>
			<p>{reset_link}</p>
		</div>

		<div class="somfrp-email-content-options">

			<p><strong>Use the following codes to show the relevant info in the email:</strong></p>
			<ul>
				<li><strong>Username:</strong> <input onClick="this.setSelectionRange(0, this.value.length)" type="text" class="somfrp-inline-input" value="{username}" readonly></li>
				<li><strong>Reset URL:</strong> <input onClick="this.setSelectionRange(0, this.value.length)" type="text" class="somfrp-inline-input" value="{reset_link}" readonly></li>
			</ul>

		</div>

		<?php

			$editor_id = 'somfrp_email_message';
			$settings = array(
				'media_buttons' => false,
				'tinymce'=> array(
					'toolbar1' => 'bold,italic,underline,alignleft,aligncenter,alignright,alignjustify,link,undo,redo',
					'toolbar2'=> false
				),
				'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
				'editor_class' => 'required',
				'teeny' => true,
				'editor_height' => 150,
				'textarea_name' => 'somfrp_gen_settings[somfrp_email_message]'
			);
			$content = $value;

			wp_editor( $content, $editor_id, $settings );

		?>

		<hr class="somfrp-gen-settings-hr">

	</div>

	<?php

}

function somfrp_from_name_render() { 

	$options = get_option( 'somfrp_gen_settings' );
	$default = esc_html__( 'WordPress', 'frontend-reset-password' );
	$value = ( isset( $options['somfrp_from_name'] ) && $options['somfrp_from_name'] ) ? esc_html($options['somfrp_from_name']) : '' ;

	?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Sender Email</strong></p>
	<p class="description" style="margin-bottom: 20px;">Both must be completed to work.</p>

	<p style="margin-bottom: 15px;"><strong>Customise the name the email is sent from.</strong></p>
	
	<p class="description" style="margin-bottom: 15px;">Default: <strong><?php echo $default; ?></strong></p>
	
	<input type="text" name="somfrp_gen_settings[somfrp_from_name]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;" placeholder="Email Name">

	<hr class="somfrp-gen-settings-hr w-300">

	<?php

}

function somfrp_email_address_render() { 

	$options = get_option( 'somfrp_gen_settings' );
	$default = esc_html__( 'wordpress@yoursite.com', 'frontend-reset-password' );
	$value = ( isset( $options['somfrp_email_address'] ) && $options['somfrp_email_address'] ) ? esc_html($options['somfrp_email_address']) : '' ;

	?>

	<?php /*<span class="html_test"><?php echo esc_html( '*&wordpress@yoursite.com&$' ) ; ?></span>*/ ?>

		<p><strong>Customise the email address the email is sent from.</strong></p>
		<p class="description" style="margin-bottom: 15px;">Default: <strong><?php echo $default; ?></strong></p>
	
	<input type="text" name="somfrp_gen_settings[somfrp_email_address]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;" placeholder="Email Address">
	<?php

}

function somfrp_gen_settings_section_callback() { 
	_e( 'General plugin settings', 'frontend-reset-password' );
}

function somfrp_security_settings_section_callback() { 
	_e( 'Settings for password security', 'frontend-reset-password' );
}

function somfrp_notice_bg_render() {

	$options = get_option( 'somfrp_gen_settings' );
	$value = ( isset( $options['somfrp_notice_bg'] ) && $options['somfrp_notice_bg'] ) ? esc_html($options['somfrp_notice_bg']) : '#2679ce' ; ?>

	<p style="margin-bottom: 15px;"><strong>Set the background colour for form notices and errors.</strong></p>

	<div class="somfrp-wp-picker-container">
		<input type="text" name="somfrp_gen_settings[somfrp_notice_bg]" id="somfrp-notice-bg-colour" value="<?php echo $value; ?>" class="somfrp-colour-picker" data-default-color="#2679ce">
	</div>

	<?php

}

function somfrp_pass_length_render() { 

	$options = get_option( 'somfrp_security_settings' );
	$value = ( isset( $options['somfrp_pass_length'] ) && $options['somfrp_pass_length'] ) ? esc_html($options['somfrp_pass_length']) : '' ;
	
	?>

	<p><strong>Set a minimum password length.</strong></p>
	<p class="description" style="margin-bottom: 15px;">Default: 0<br>Recommended: 8</strong></p>
	<input type="number" name="somfrp_security_settings[somfrp_pass_length]" value="<?php echo $value; ?>" style="width: 60px;" min="0" max="100">
	
	<?php

}

function somfrp_reset_page_render() {

	$options = get_option( 'somfrp_gen_settings' );
	$value = ( isset( $options['somfrp_reset_page'] ) && $options['somfrp_reset_page'] ) ? esc_html($options['somfrp_reset_page']): '' ;

	$args = array(
		'selected' => $value,
		'show_option_none' => 'Please choose...',
		'name' => 'somfrp_gen_settings[somfrp_reset_page]',
		'id' => 'somfrp_gen_settings[somfrp_reset_page]'
	); ?>

	<div class="somfrp-gen-settings-wrap">

		<p style="margin-bottom: 15px; font-size: 16px;"><strong>Reset Password Page</strong></p>
		<p>Select which page your <code>[reset_password]</code> shortcode is on.</p>

		<div class="somfrp-setting-input-wrap"><?php wp_dropdown_pages($args); ?></div>

		<p class="description">By default everything can be handled by your reset password page, but you can customise other pages below.</p>

		<hr class="somfrp-gen-settings-hr">

	</div>

<?php }

function somfrp_request_success_page_render() {

	$options = get_option( 'somfrp_gen_settings' );
	$value = ( isset( $options['somfrp_request_success_page'] ) && $options['somfrp_request_success_page'] ) ? esc_html($options['somfrp_request_success_page']): '' ;

	$args = array(
		'selected' => $value,
		'show_option_none' => 'Please choose...',
		'name' => 'somfrp_gen_settings[somfrp_request_success_page]',
		'id' => 'somfrp_gen_settings[somfrp_request_success_page]'
	); ?>

	<div class="somfrp-gen-settings-wrap">

		<p style="margin-bottom: 15px; font-size: 16px;"><strong>Reset Email Sent</strong></p>
		<p>Select a custom page to redirect your user to if the email is sent successfully.</p>

		<div class="somfrp-setting-input-wrap"><?php wp_dropdown_pages($args); ?></div>

		<p class="description">If the email isn't sent, for example if they have entered an incorrect username, they will always be returned to the reset password page.</p>

		<hr class="somfrp-gen-settings-hr">

	</div>

<?php }

function somfrp_reset_success_page_render() {

	$options = get_option( 'somfrp_gen_settings' );
	$value = ( isset( $options['somfrp_reset_success_page'] ) && $options['somfrp_reset_success_page'] ) ? esc_html($options['somfrp_reset_success_page']): '' ;

	$args = array(
		'selected' => $value,
		'show_option_none' => 'Please choose...',
		'name' => 'somfrp_gen_settings[somfrp_reset_success_page]',
		'id' => 'somfrp_gen_settings[somfrp_reset_success_page]'
	); ?>

	<div class="somfrp-gen-settings-wrap">

		<p style="margin-bottom: 15px; font-size: 16px;"><strong>New Password Saved</strong></p>
		<p>Select a custom page to redirect your user to if they have successfully changed their password.</p>

		<div class="somfrp-setting-input-wrap"><?php wp_dropdown_pages($args); ?></div>

		<p class="description">If the password change wasn't accepted, they will always be returned to the reset password page.</p>

		<hr class="somfrp-gen-settings-hr">

	</div>

<?php }

function somfrp_login_page_render() {

	$options = get_option( 'somfrp_gen_settings' );
	$value = ( isset( $options['somfrp_login_page'] ) && $options['somfrp_login_page'] ) ? esc_html($options['somfrp_login_page']): '' ;

	$args = array(
		'selected' => $value,
		'show_option_none' => 'Please choose...',
		'name' => 'somfrp_gen_settings[somfrp_login_page]',
		'id' => 'somfrp_gen_settings[somfrp_login_page]'
	); ?>

	<div class="somfrp-gen-settings-wrap">

		<p style="margin-bottom: 15px; font-size: 16px;"><strong>Custom Login Page</strong></p>
		<p>Select your website login page.</p>

		<div class="somfrp-setting-input-wrap"><?php wp_dropdown_pages($args); ?></div>

		<p class="description">Note: If blank the default wp-login page will be used.</p>

		<hr class="somfrp-gen-settings-hr">

	</div>

<?php }

function somfrp_reset_form_title_render() { 

	$options = get_option( 'somfrp_gen_settings' );
	$default = esc_html__( 'Reset Password', 'frontend-reset-password' );
	$value = ( isset( $options['somfrp_reset_form_title'] ) && $options['somfrp_reset_form_title'] ) ? esc_html($options['somfrp_reset_form_title']) : '' ;
	
	?>

		<p><strong>Customise the form title.</strong></p>
		<p class="description" style="margin-bottom: 15px;">Default: <strong><?php echo $default; ?></strong></p>
	
	<input type="text" name="somfrp_gen_settings[somfrp_reset_form_title]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<?php

}

function somfrp_reset_lost_message_render() {

	$options = get_option( 'somfrp_gen_settings' );
	$default = esc_html__( 'Please enter your email address or username. You will receive a link to create a new password via email.', 'frontend-reset-password' );
	$value = ( isset( $options['somfrp_reset_lost_message'] ) && $options['somfrp_reset_lost_message'] ) ? $options['somfrp_reset_lost_message'] : '' ;

	?>

	<div class="somfrp-gen-settings-wrap">

		<p><strong>Customise the main lost password form text.</strong></p>
		<p class="description" style="margin-bottom: 15px;">Default: <strong><?php echo $default; ?></strong></p>

		<?php

			$editor_id = 'somfrp_reset_lost_message';
			$settings = array(
				'media_buttons' => false,
				'tinymce'=> array(
					'toolbar1' => 'bold,italic,link,undo,redo',
					'toolbar2'=> false
				),
				'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
				'editor_class' => 'required',
				'teeny' => true,
				'editor_height' => 150,
				'textarea_name' => 'somfrp_gen_settings[somfrp_reset_lost_message]'
			);
			$content = stripslashes( $value );

			wp_editor( $content, $editor_id, $settings );

		?>

		<hr class="somfrp-gen-settings-hr">

	</div>

	<?php
}

function somfrp_reset_new_message_render() {

	$options = get_option( 'somfrp_gen_settings' );
	$default = esc_html__( 'Please enter a new password.', 'frontend-reset-password' );
	$default_min = esc_html__( 'Please enter a new password. Minimum X characters.', 'frontend-reset-password' );
	$value = ( isset( $options['somfrp_reset_new_message'] ) && $options['somfrp_reset_new_message'] ) ? $options['somfrp_reset_new_message'] : '' ;

	?>

	<div class="somfrp-gen-settings-wrap">

		<p><strong>Customise the new password form text.</strong></p>
		<p class="description" style="margin-bottom: 15px;">Default: <strong><?php echo $default; ?></strong></p>
		<p class="description" style="margin-bottom: 15px;">If password minimum length required:<br>Default: <strong><?php echo $default_min; ?></strong></p>

		<?php

			$editor_id = 'somfrp_reset_new_message';
			$settings = array(
				'media_buttons' => false,
				'tinymce'=> array(
					'toolbar1' => 'bold,italic,link,undo,redo',
					'toolbar2'=> false
				),
				'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
				'editor_class' => 'required',
				'teeny' => true,
				'editor_height' => 150,
				'textarea_name' => 'somfrp_gen_settings[somfrp_reset_new_message]'
			);
			$content = stripslashes( $value );

			wp_editor( $content, $editor_id, $settings );

		?>

		<hr class="somfrp-gen-settings-hr">

	</div>

	<?php
}

function somfrp_reset_button_text_render() { 

	$options = get_option( 'somfrp_gen_settings' );
	$default = esc_html__( 'Reset Password', 'frontend-reset-password' );
	$value = ( isset( $options['somfrp_reset_button_text'] ) && $options['somfrp_reset_button_text'] ) ? esc_html($options['somfrp_reset_button_text']) : '' ;

	?>

		<p><strong>Customise the button text.</strong></p>
		<p class="description" style="margin-bottom: 15px;">Default: <strong><?php echo $default; ?></strong></p>
	
	<input type="text" name="somfrp_gen_settings[somfrp_reset_button_text]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<?php

}

function somfrp_email_subject_render() { 

	$options = get_option( 'somfrp_gen_settings' );
	$default = esc_html__( 'Account Password Reset', 'frontend-reset-password' );
	$value = ( isset( $options['somfrp_email_subject'] ) && $options['somfrp_email_subject'] ) ? esc_html($options['somfrp_email_subject']) : '' ;
	
	?>

		<p><strong>Customise the email subject line.</strong></p>
		<p class="description" style="margin-bottom: 15px;">Default: <strong><?php echo $default; ?></strong></p>
	
	<input type="text" name="somfrp_gen_settings[somfrp_email_subject]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<?php

}