<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified load Class.
 *
 * @class CubeWp_Classified_Load
 */
final class CubeWp_Classified_Load {

	/**
	 * The single instance of the class.
	 *
	 * @var CubeWp_Classified_Load
	 */
	protected static $Load = null;

	public function __construct() {
		self::includes();
	}

	public function includes() {
		add_action( 'init', array( 'Classified_Plugin_Setup', 'init' ), - 1 );
		add_action( 'init', array( 'Classified_Report', 'init' ), 6 );
		add_action( 'init', array( 'Classified_Email_Verification', 'init' ), 6 );
		$files = array(
			'include/helper.php',
			'include/classified-ajax.php',
		);
		// Checking if exists then require.
		foreach ( $files as $file ) {
			$file = CLASSIFIED_PLUGIN_PATH . $file;
			if ( file_exists( $file ) ) {
				require $file;
			}
		}
		add_action( 'init', array( 'Classified_Theme_Widgets', 'init' ), 0 );
		add_action( 'init', array( 'Classified_Offer_Buy_Item', 'init' ), 5 );
		add_action( 'init', array( 'Classified_Personalization', 'init' ) );
		add_action( 'init', array( 'Classified_Icons', 'init' ) );
		add_action( 'plugins_loaded', array( 'Classified_Elementor_Widgets', 'init' ) );
		if ( CWP()->is_request( 'frontend' ) || cubewp_is_elementor_editing() ) {
			add_action( 'init', array( 'Classified_Frontend_Enqueue', 'init' ) );
			add_action( 'init', array( 'Classified_Frontend_Dashboard', 'init' ) );
			add_action( 'init', array( 'Classified_Featured_Items_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Categories_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Author_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Items_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Browse_By_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Multi_Search_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Search_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Promotion_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Reviews_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Pricing_Plans_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Submit_Edit_Shortcode', 'init' ) );
			add_action( 'init', array( 'Classified_Blogs_Shortcode', 'init' ) );
		}
	}

	public static function instance() {
		if ( is_null( self::$Load ) ) {
			self::$Load = new self();
		}

		return self::$Load;
	}
}