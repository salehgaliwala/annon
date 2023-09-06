<?php

use Elementor\Widgets_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Classified load Class.
 *
 * @class Classified_Icons
 */
final class Classified_Elementor_Widgets {

	/**
	 * Addon Version
	 *
	 * @since 1.0.0
	 * @var string The addon version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the addon.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the addon.
	 */
	const MINIMUM_PHP_VERSION = '7.3';

	/**
	 * Instance
	 *
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @var Classified_Elementor_Widgets The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Constructor
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {
		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', array( $this, 'init_elementor_widgets' ) );
		}
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function is_compatible() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );

			return false;
		}
		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );

			return false;
		}
		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );

			return false;
		}

		return true;
	}

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Classified_Elementor_Widgets An instance of the class.
	 * @since  1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Auto Loading Widgets Files
	 *
	 * Load Elementor widgets files.
	 */
	private static function require_widgets_files( $className ) {
		// If class does not start with our prefix (Classified_Elementor), nothing will return.
		if ( false === strpos( $className, 'Classified_Elementor' ) ) {
			return null;
		}
		// Replace _ with - to match the file name.
		$file_name = str_replace( '_', '-', strtolower( $className ) );
		// Calling class file.
		$files = array(
			CLASSIFIED_PLUGIN_PATH . 'classes/elementor-widgets/class-' . $file_name . '.php'
		);
		// Checking if exists then include.
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				require $file;
			}
		}

		return $className;
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(/* translators: 1: Plugin name 2: Elementor */ esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'cubewp-classified' ), '<strong>' . esc_html__( 'Classified', 'cubewp-classified' ) . '</strong>', '<strong>' . esc_html__( 'Elementor', 'cubewp-classified' ) . '</strong>' );
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */ esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'cubewp-classified' ), '<strong>' . esc_html__( 'Classified', 'cubewp-classified' ) . '</strong>', '<strong>' . esc_html__( 'Elementor', 'cubewp-classified' ) . '</strong>', self::MINIMUM_ELEMENTOR_VERSION );
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(/* translators: 1: Plugin name 2: PHP 3: Required PHP version */ esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'cubewp-classified' ), '<strong>' . esc_html__( 'Classified', 'cubewp-classified' ) . '</strong>', '<strong>' . esc_html__( 'PHP', 'cubewp-classified' ) . '</strong>', self::MINIMUM_PHP_VERSION );
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function init_elementor_widgets() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'elementor_widget_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		spl_autoload_register( array( $this, 'require_widgets_files' ) );
	}

	/**
	 * Widget Categories
	 *
	 * Adding New Widgets Category.
	 */
	public function elementor_widget_category( $widgets_manager ) {
		$widgets_manager->add_category( 'classified', array(
			'title' => __( 'Classified', 'cubewp-classified' )
		) );
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * Fired by `elementor/widgets/register` action hook.
	 *
	 * @param Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		$widgets_manager->register( new Classified_Elementor_Featured_Items_Widget() );
		$widgets_manager->register( new Classified_Elementor_Categories_Widget() );
		$widgets_manager->register( new Classified_Elementor_Browse_By_Widget() );
		$widgets_manager->register( new Classified_Elementor_Items_Widget() );
		$widgets_manager->register( new Classified_Elementor_Author_Widget() );
		$widgets_manager->register( new Classified_Elementor_Reviews_Widget() );
		$widgets_manager->register( new Classified_Elementor_Promotion_Widget() );
		$widgets_manager->register( new Classified_Elementor_Ads_Submission_Widget() );
		$widgets_manager->register( new Classified_Elementor_Multi_Search_Widget() );
		$widgets_manager->register( new Classified_Elementor_Search_Widget() );
		$widgets_manager->register( new Classified_Elementor_Blogs_Widget() );
		if ( classified_is_payment_active() ) {
			$widgets_manager->register( new Classified_Elementor_Pricing_Plans_Widget() );
		}
	}
}