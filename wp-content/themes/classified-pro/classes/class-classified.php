<?php
defined( 'ABSPATH' ) || exit;

class Classified {

	public static $is_theme_uptodate = false;

	/**
	 * The single instance of the class.
	 *
	 * @var Classified
	 */
	protected static $Load = null;

	public function __construct() {
		self::includes();

		if ( false === self::$is_theme_uptodate = get_transient( 'is_classified_theme_uptodate' ) ) {
			set_transient( 'is_classified_theme_uptodate', self::check_if_theme_update_available(), DAY_IN_SECONDS );
		}

		if ( ! self::$is_theme_uptodate ) {
			new CubeWp_Admin_Notice( 'classified_update_available', self::classified_update_available_admin_notice_ui(), 'info', false );
		}
	}

	private static function includes() {
		$files = array(
			'include/settings.php',
			'include/helper.php',
			'include/dynamic-css.php',
		);
		// Checking if exists then include.
		foreach ( $files as $file ) {
			$file = CLASSIFIED_PATH . $file;
			if ( file_exists( $file ) ) {
				require $file;
			}
		}

		if ( CWP()->is_request( 'frontend' ) || cubewp_is_elementor_editing() ) {
			add_action( 'init', array( 'Classified_Frontend_Fields', 'init' ) );
			add_action( 'init', array( 'Classified_Theme_Frontend_Enqueue', 'init' ) );
		}

		add_action( 'init', array( 'Classified_Theme_Single_Widgets', 'init' ) );
		add_action( 'init', array( 'Classified_Theme_Frontend_Fields', 'init' ) );
	}

	private static function check_if_theme_update_available() {
		$latest_version  = 0;
		$api_key         = 'e3DQwOD3l7XYDbn4BMV7ZfmRRY14rgxG';
		$purchase_code   = get_option( 'purchase-code' );
		$parent_theme    = wp_get_theme( get_template() );
		$current_version = $parent_theme->get( 'Version' );
		$api_url = "https://api.envato.com/v3/market/author/sale?code={$purchase_code}";
		$headers = array(
			'Authorization' => "Bearer {$api_key}",
			'User-Agent'    => 'Your User Agent'
		);
		$response = wp_remote_get( $api_url, array( 'headers' => $headers ) );
		$response = @json_decode( wp_remote_retrieve_body( $response ), 1 );
		if ( isset( $response['item'] ) ) {
			$latest_version = $response['item']['wordpress_theme_metadata']['version'];
		}

		if ( version_compare( $current_version, $latest_version, '>=' ) ) {
			self::$is_theme_uptodate = true;
			return true;
		} else {
			self::$is_theme_uptodate = false;
			return false;
		}
	}

	private static function classified_update_available_admin_notice_ui() {
		?>
		<h2><?php esc_html_e( 'Important Notice! New ClassifiedPro Update Available.', 'classified-pro' ); ?></h2>
		<p><?php esc_html_e( 'A new update of ClassifiedPro Theme is now available.', 'classified-pro' ); ?></p>
		<p><?php esc_html_e( 'To update your theme, simply log in to your Themeforest account and navigate to your downloads section. From there, you can download the latest version of your theme and follow the installation instructions included in the package.', 'classified-pro' ); ?></p>
		<p><?php esc_html_e( 'Please note that updating your theme may overwrite any customizations you have made or language translation. We recommend that you create a backup of your current version before updating.', 'classified-pro' ); ?></p>
		<?php

		return ob_get_clean();
	}

	public static function instance() {
		if ( is_null( self::$Load ) ) {
			self::$Load = new self();
		}

		return self::$Load;
	}
}