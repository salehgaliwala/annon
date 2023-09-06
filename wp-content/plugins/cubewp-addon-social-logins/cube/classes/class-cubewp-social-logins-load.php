<?php
defined( 'ABSPATH' ) || exit;

/**
 * CubeWp load Class.
 *
 * @class CubeWp_Social_Logins_Load
 */
final class CubeWp_Social_Logins_Load {
	/**
	 * The single instance of the class.
	 *
	 * @var CubeWp_Social_Logins_Load
	 */
	protected static $Load = null;

	public static function instance() {
		if ( is_null( self::$Load ) ) {
			self::$Load = new self();
		}

		return self::$Load;
	}

	/**
     * CubeWp_Social_Logins_Load Constructor.
     */
	public function __construct() {
		self::includes();
		add_action( 'init', array( 'CubeWp_Social_Logins_Settings', 'init' ) );
		$social_login = cwp_sl_get_settings( 'cubewp_social_logins' );
		if ( $social_login ) {
			add_action( 'init', array( 'CubeWp_Social_Logins_Display', 'init' ) );
			add_action( 'init', array( 'CubeWp_Social_Processing', 'init' ) );
		}
		// Set up localisation.
        self::load_plugin_textdomain();
	}

	/**
     * Include required core files used in admin and on the frontend.
     */
	public static function includes() {
		$files = array(
			'cube/helpers/functions.php',
		);
		// Checking if exists then require.
		foreach ( $files as $file ) {
			$file = CWP_SL_PLUGIN_PATH . $file;
			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	}

	/**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     * - WP_LANG_DIR/cubewp/cubewp-LOCALE.mo
     * - WP_LANG_DIR/plugins/cubewp-LOCALE.mo
     */
    public function load_plugin_textdomain() {
        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            // @todo Remove when start supporting WP 5.0 or later.
            $locale = is_admin() ? get_user_locale() : get_locale();
        }

        $locale = apply_filters('plugin_locale', $locale, 'cubewp-social-logins');

        unload_textdomain('cubewp-social-logins');
        load_textdomain('cubewp-social-logins', WP_LANG_DIR . '/cubewp-addon-social-logins/cubewp-social-logins-' . $locale . '.mo');
        load_plugin_textdomain('cubewp-social-logins', false, plugin_basename(dirname(CUBEWP_SL_PLUGIN_FILE)) . '/languages');
    }
}