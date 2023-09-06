<?php

use Elementor\Plugin;

class CWP_License_verification {


	/**
	 * Redirect User to defined page after completion of setup.
	 *
	 * @var string
	 */
	public $setup_after_url;

	/**
	 * Root folder of the sdk.
	 *
	 * @var string
	 */
	public $root = __DIR__;

	/**
	 * Whether licensing process is enabled or not.
	 *
	 * @var bool
	 */
	public $licensing = true;

	/**
	 * id or class selector to append response of import process
	 *
	 * @var string
	 */
	public $selector = '.cwp-importer-success';

	/**
	 * URL Path for images and stles
	 *
	 * @var string
	 */
	public $PATH_URL;

	/**
	 * Class constructor, actions, filters and method allocation
	 *
	 */
	function __construct() {
		$this->load_resources();
		add_action( 'wp_ajax_cwp_license_verification', 'cwp_license_verification_callback' );
		add_action( 'admin_menu', array( $this, 'cwp_license_sdk_view' ) );
		add_action( 'admin_post_cwp_license_submit', 'cwp_license_form_response' );
		add_action( 'wp_ajax_cwp_activate_license', 'cwp_activate_license_cb' );
		add_action( 'wp_ajax_cwp_activate_required_addons', 'cwp_activate_required_addons_cb' );
		add_filter( 'cubewp/import/content/path', array( $this, 'cwp_path_for_import_cubewp_content' ), 15 );
		add_filter( 'cubewp/after/import/redirect', array( $this, 'cwp_redirect_import_cubewp_content' ), 15 );
		add_filter( 'cubewp/after/import/success_message', array( $this, 'cwp_import_cubewp_content_success' ), 15 );
		add_action( 'after_setup_theme', array( $this, 'cwp_redirect_after_theme_activation' ) );
		add_action( 'admin_init', array( $this, 'cwp_woocommerce_no_redirect_on_activation' ) );
		add_action( 'cwp_actions_after_demo_imported', array( $this, 'cwp_actions_after_demo_imported_callback' ) );
		if ( function_exists( 'cwp_theme_required_plugins' ) ) {
			$theme_required = cwp_theme_required_plugins();

			if ( ! empty( $theme_required ) && is_array( $theme_required ) ) {
				add_action( 'admin_notices', array( $this, 'cwp_admin_notice_required_plugins' ) );
			}
		}
		add_action( 'admin_init', function() {
			if ( did_action( 'elementor/loaded' ) ) {
				remove_action( 'admin_init', [
					Plugin::$instance->admin,
					'maybe_redirect_to_getting_started'
				] );
			}
		}, 1 );
	}

	/**
	 * Class method, including required files and calling script method
	 *
	 */
	public function load_resources() {
		$this->setup_after_url = admin_url( 'admin.php?page=cube_wp_dashboard' );
		$this->PATH_URL        = get_template_directory_uri() . '/include';
		$this->cwp_license_scripts();
		require_once( $this->root . '/helpers.php' );
		require_once( $this->root . '/manager.php' );
		require_once( $this->root . '/views/cwp-view.php' );
	}

	/**
	 * Class method, calling of another method based on respective screen
	 *
	 */
	public function cwp_license_scripts() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'cwp_license_manager' ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'cwp_admin_license_scripts' ) );
		}
	}

	/**
	 * Class method, define folder path for import files
	 *
	 */
	public function cwp_path_for_import_cubewp_content( $path ) {
		return $this->root . '/import/';
	}

	/**
	 * Class method, no redirection after import process success
	 *
	 */
	public function cwp_redirect_import_cubewp_content( $path ) {
		return null;
	}

	/**
	 * Class method, passing selector and message after import success.
	 *
	 */
	public function cwp_import_cubewp_content_success( $path ) {
		return array( 'selecter' => $this->selector, 'message' => 'Your Content has been been imported successfully!' );
	}

	/**
	 * Class method, registration, localization and enqueing of scripts,styles.
	 *
	 */
	public function cwp_admin_license_scripts() {
		wp_enqueue_style( 'cwp_admin_license_style', get_template_directory_uri() . '/include/sdk/assets/css/cwp_license.css' ); //enqueue style
		wp_register_script( 'cwp_admin_license_script', get_template_directory_uri() . '/include/sdk/assets/js/cwp_license.js' );//register script
		wp_localize_script( 'cwp_admin_license_script', 'cwp_admin_license_params', [
			'ajax_url'       => admin_url( 'admin-ajax.php' ),
			'security_nonce' => wp_create_nonce( "cubewp-admin-nonce" )
		] );//localize script
		wp_enqueue_script( 'cwp_admin_license_script' );
		wp_enqueue_script( 'cwp_vars' );
	}

	/**
	 * Class method, add new theme page for license management screen
	 *
	 */
	public function cwp_license_sdk_view() {
		add_theme_page( 'Setup', 'Setup', 'manage_options', 'cwp_license_manager', 'cwp_license_manager_cb', );
	}

	/**
	 * Class method, redirect to setup page after theme activation
	 *
	 */
	public function cwp_redirect_after_theme_activation() {
		global $pagenow;
		if ( "themes.php" == $pagenow && is_admin() && isset( $_GET['activated'] ) ) {
			wp_redirect( esc_url_raw( add_query_arg( 'page', 'cwp_license_manager', admin_url( 'admin.php' ) ) ) );
		}
	}

	/**
	 * Class method, stop redirection after woo commerce activation
	 *
	 */

	public function cwp_woocommerce_no_redirect_on_activation() {
		delete_transient( '_wc_activation_redirect' );
	}

	/**
	 * Class method, admin notice of required plugins
	 *
	 */
	public function cwp_admin_notice_required_plugins() {
		$theme_required  = cwp_theme_required_plugins();
		$counter         = 1;
		$first_occurance = true;
		$last_occurance  = false;
		foreach ( $theme_required as $theme_req ) {
			if ( isset( $theme_req['slug'] ) && isset( $theme_req['base'] ) && isset( $theme_req['name'] ) ) {
				if ( ( isset( $theme_req['class_exists'] ) && ! class_exists( $theme_req['class_exists'] ) ) && ( isset( $theme_req['required'] ) && $theme_req['required'] == 'yes' ) ) {
					if ( $first_occurance ) {
						?>
                        <div class="notice notice-warning is-dismissible">
                        <p>
                        <strong><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">This theme requires the following plugins:
						<?php
						$first_occurance = false;
					}
					if ( $counter != 1 ) {
						echo ' and ';
					}
					?>
					<em><a href="<?php if ( empty( $theme_req['source'] ) ) {
						echo admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $theme_req['slug'] );
					} else {
						echo 'https://cubewp.com/';
					} ?>" target="_blank"><?php echo esc_html( $theme_req['name'] ); ?></a>
                    </em>
					<?php
					$last_occurance = true;
				}
				$counter ++;
			}

			if ( isset( $theme_req['min_version'] ) && ! empty( $theme_req['min_version'] ) ) {
				$plugin = $theme_req['slug'] . '/' . $theme_req['base'] . '.php';
				$min_version = $theme_req['min_version'];
				$plugin_name = $theme_req['name'];
				$plugin_path = WP_PLUGIN_DIR . '/' . $plugin;
				if ( is_plugin_active( $plugin ) ) {
					$plugin_data = get_plugin_data( $plugin_path );
					$plugin_version = $plugin_data['Version'];
					if ( version_compare( $plugin_version, $min_version, '<' ) ) {
						?>
						<div class="notice notice-warning">
							<h3><?php esc_html_e( 'ClassifiedPro', 'classified-pro' ); ?></h3>
							<p><?php echo sprintf( esc_html__( 'Please update your %s%s%s plugin to %s%s%s version or you may face issues in functionality.', 'classified-pro' ), '<strong>', $plugin_name, '</strong>', '<strong>', $min_version, '</strong>' ) ?></p>
						</div>
						<?php
					}
				}
			}
		}
		if ( ( $counter == count( $theme_required ) + 1 ) && $last_occurance ) { ?>
            </span>
            <span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;"><a
                        href="<?php echo admin_url( 'themes.php?page=cwp_license_manager' ); ?>">Begin Setup</a></span>
            </strong>
            </p>
            </div>
			<?php
		}
	}

	/**
	 * Class method, action after demo content imported
	 *
	 */

	public function cwp_actions_after_demo_imported_callback() {
		/**
		 * Configuring Home Page
		 **/
		$args = array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'title' => 'Home'
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			$page_id = $query->posts[0]->ID;
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $page_id );
		}
		wp_reset_postdata();

		/**
		 * Configuring Blog Page
		 **/
		$args = array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'title' => 'Blogs'
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			$page_id = $query->posts[0]->ID;
			update_option( 'page_for_posts', $page_id );

		}
		wp_reset_postdata();

		/**
		 * Configuring Dashboard Page And Help Page
		 **/
		$cwpOptions = get_option( 'cwpOptions' );
		$args = array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'title' => 'Dashboard'
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			$page_id = $query->posts[0]->ID;
			$cwpOptions['dashboard_page'] = $page_id;
		}
		wp_reset_postdata();
		$args['title'] = 'Contact Us';
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			$page_id = $query->posts[0]->ID;
			$cwpOptions['classified_help_page'] = $page_id;
		}
		wp_reset_postdata();
		update_option( 'cwpOptions', $cwpOptions );
		/**
		 * Configuring Elementor Container Sizes
		**/
        $elementor_default_kit = false;
        $classified_settings_kit = false;
        $args = array(
	        'post_type' => 'elementor_library',
	        'post_status' => 'publish',
	        'posts_per_page' => 1,
	        'title' => 'Default Kit'
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) {
	        $page_id = $query->posts[0]->ID;
	        $elementor_default_kit = $page_id;
        }
        wp_reset_postdata();
		$args = array(
			'post_type' => 'elementor_library',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'title' => 'Classified Settings Kit'
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			$page_id = $query->posts[0]->ID;
			$classified_settings_kit = $page_id;

		}
		wp_reset_postdata();
        if ( $elementor_default_kit && $classified_settings_kit ) {
            $classified_kit_settings = get_post_meta( $classified_settings_kit, '_elementor_page_settings', true );
            update_post_meta( $elementor_default_kit, '_elementor_page_settings', $classified_kit_settings );
        }
	}
}

$CWP_License_verification = new CWP_License_verification();