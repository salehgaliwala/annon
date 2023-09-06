<?php
/**
 * Settings Header
 *
 * Branded WP Enhanced header
 *
 * @version	1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$logo = plugins_url( '/assets/images/logo.png', SOMFRP_FILE );
	
	?>

<div class="som-settings-nav">
	<a href="https://wpenhanced.com" target="_blank" rel="nofollow">
		<img class="som-brand-img" src="<?php echo $logo ?>">
		<h1 class="som-brand-name">WP Enhanced</h1>
	</a>
	<a href="https://profiles.wordpress.org/wpenhanced/" target="_blank"><div class="dashicons dashicons-wordpress"></div></a>
	<a href="https://wpenhanced.com" target="_blank"><div class="dashicons dashicons-desktop"></div></a>
</div>

<div class="som-settings-settings-spacer"></div>