<?php
defined( 'ABSPATH' ) || exit;

/**
 * CLASSIFIED_VERSION is defined for current Classified version
 */
if ( ! defined( 'CLASSIFIED_VERSION' ) ) {
	define( 'CLASSIFIED_VERSION', '1.0.10' );
}

/**
 * CLASSIFIED_PATH Defines for load PHP files
 */
if ( ! defined( 'CLASSIFIED_PATH' ) ) {
	define( 'CLASSIFIED_PATH', get_template_directory() . '/' );
}

/**
 * CLASSIFIED_URL Defines for load JS and CSS files
 */
if ( ! defined( 'CLASSIFIED_URL' ) ) {
	define( 'CLASSIFIED_URL', get_template_directory_uri() . '/' );
}

/**
 * @function cwp_theme_required_plugins
 *
 * Classified Theme required plugins.
 *
 * @returns bool
 */
if ( ! function_exists( 'cwp_theme_required_plugins' ) ) {
	function cwp_theme_required_plugins() {
		return array(
			array(
				'name'         => esc_html__( 'Elementor', 'classified-pro' ),
				'slug'         => 'elementor',
				'base'         => 'elementor',
				'required'     => 'yes',
				'class_exists' => 'Elementor\Plugin',
				'min_version'  => false
			),
			array(
				'name'         => esc_html__( 'CubeWP Framework', 'classified-pro' ),
				'slug'         => 'cubewp-framework',
				'base'         => 'cube',
				'required'     => 'yes',
				'class_exists' => 'CubeWp_Load',
				'min_version'  => '1.1.5'
			),
			array(
				'name'         => esc_html__( 'CubeWP Frontend Pro', 'classified-pro' ),
				'slug'         => 'cubewp-addon-frontend-pro',
				'base'         => 'cubewp-frontend',
				'required'     => 'yes',
				'cwp-source'   => 'yes',
				'class_exists' => 'CubeWp_Frontend_Load',
				'min_version'  => false
			),
			array(
				'name'         => esc_html__( 'CubeWP Classified', 'classified-pro' ),
				'slug'         => 'cubewp-addon-classified',
				'base'         => 'cubewp-classified',
				'required'     => 'yes',
				'cwp-source'   => 'yes',
				'class_exists' => 'CubeWp_Classified_Load',
				'min_version'  => '1.0.7'
			),
			array(
				'name'         => esc_html__( 'CubeWP Inbox', 'classified-pro' ),
				'slug'         => 'cubewp-addon-inbox',
				'base'         => 'cubewp-inbox',
				'required'     => 'yes',
				'cwp-source'   => 'yes',
				'class_exists' => 'CubeWp_Inbox_Load',
				'min_version'  => '1.0.3'
			),
			array(
				'name'         => esc_html__( 'CubeWP Booster', 'classified-pro' ),
				'slug'         => 'cubewp-addon-booster',
				'base'         => 'cubewp-booster',
				'required'     => 'yes',
				'cwp-source'   => 'yes',
				'class_exists' => 'CubeWp_Booster_Load',
				'min_version'  => false
			),
			array(
				'name'         => esc_html__( 'CubeWP Reviews', 'classified-pro' ),
				'slug'         => 'cubewp-addon-reviews',
				'base'         => 'cubewp-reviews',
				'required'     => 'yes',
				'cwp-source'   => 'yes',
				'class_exists' => 'CubeWp_Reviews_Load',
				'min_version'  => false
			),
			array(
				'name'         => esc_html__( 'CubeWP Wallet', 'classified-pro' ),
				'slug'         => 'cubewp-addon-wallet',
				'base'         => 'cubewp-wallet',
				'required'     => 'no',
				'class_exists' => 'CubeWp_Wallet_Load',
				'min_version'  => false
			),
			array(
				'name'         => esc_html__( 'CubeWP Payments', 'classified-pro' ),
				'slug'         => 'cubewp-addon-payments',
				'base'         => 'cubewp-payments',
				'required'     => 'no',
				'cwp-source'   => 'yes',
				'class_exists' => 'CubeWp_Payments_Load',
				'min_version'  => false
			),
			array(
				'name'         => esc_html__( 'CubeWP Social Logins', 'classified-pro' ),
				'slug'         => 'cubewp-addon-social-logins',
				'base'         => 'cubewp-social-logins',
				'required'     => 'no',
				'cwp-source'   => 'yes',
				'class_exists' => 'CubeWp_Social_Logins_Load',
				'min_version'  => false
			),
			array(
				'name'         => esc_html__( 'CubeWP Forms', 'classified-pro' ),
				'slug'         => 'cubewp-forms',
				'base'         => 'cubewp-forms',
				'required'     => 'no',
				'class_exists' => 'CubeWp_Forms_Custom',
				'min_version'  => false
			),
		);
	}
}

/**
 * Including theme setup file.
 *
 * @since  1.0.0
 */
require_once CLASSIFIED_PATH . 'include/sdk/controller.php';

/**
 * @function if_theme_can_load
 *
 * Check all required plugins for Classified Theme.
 *
 * @param bool $check_frontend Also Check CubeWP Frontend Pro.
 *
 * @returns bool
 */
if ( ! function_exists( 'if_theme_can_load' ) ) {
	function if_theme_can_load( $check_frontend = false ) {
		if ( ! $check_frontend ) {
			if ( class_exists( 'CubeWp_Classified_Load' ) && function_exists( 'CWP' ) ) {
				return true;
			}
		} else {
			if ( class_exists( 'CubeWp_Classified_Load' ) && function_exists( 'CWP' ) && class_exists( 'CubeWp_Frontend_Load' ) ) {
				return true;
			}
		}

		return false;
	}
}

/**
 * @function classified_frontend_styles
 *
 * General Classified Theme Styles And Scripts.
 */
if ( ! function_exists( 'classified_frontend_styles' ) ) {
	function classified_frontend_styles() {
		$styles = array(
			'classified-fa' => array(
				'src'   => CLASSIFIED_URL . 'assets/lib/fontawesome/css/fontawesome.min.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-fa-solid' => array(
				'src'   => CLASSIFIED_URL . 'assets/lib/fontawesome/css/solid.min.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-fa-regular' => array(
				'src'   => CLASSIFIED_URL . 'assets/lib/fontawesome/css/regular.min.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-fa-brands' => array(
				'src'   => CLASSIFIED_URL . 'assets/lib/fontawesome/css/brands.min.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-bootstrap-styles' => array(
				'src'   => CLASSIFIED_URL . 'assets/lib/bootstrap/css/bootstrap.min.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-core-styles'      => array(
				'src'   => get_stylesheet_uri(),
				'deps'  => array( 'classified-bootstrap-styles' ),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-styles'           => array(
				'src'   => CLASSIFIED_URL . 'assets/css/classified-styles.css',
				'deps'  => array( 'classified-core-styles' ),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-dynamic-styles'   => array(
				'src'   => CLASSIFIED_URL . 'assets/css/dynamic-css.css',
				'deps'  => array( 'classified-fields-styles', 'classified-styles' ),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-fields-styles'    => array(
				'src'   => CLASSIFIED_URL . 'assets/css/classified-frontend-fields.css',
				'deps'  => array( 'classified-styles' ),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-blog-styles'      => array(
				'src'   => CLASSIFIED_URL . 'assets/css/classified-blog-styles.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-blogs-styles'     => array(
				'src'   => CLASSIFIED_URL . 'assets/css/classified-blogs-styles.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-author-1-styles'  => array(
				'src'   => CLASSIFIED_URL . 'assets/css/classified-author-style-1.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-author-2-styles'  => array(
				'src'   => CLASSIFIED_URL . 'assets/css/classified-author-style-2.css',
				'deps'  => array(),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
			'classified-header-styles'    => array(
				'src'   => CLASSIFIED_URL . 'assets/css/classified-header-styles.css',
				'deps'  => array( 'classified-styles' ),
				'ver'   => CLASSIFIED_VERSION,
				'media' => '',
			),
		);

		foreach ( $styles as $handle => $data ) {
			wp_register_style( $handle, $data['src'], $data['deps'], $data['ver'], $data['media'] );
		}

		$scripts = array(
			'classified-bootstrap-scripts' => array(
				'src'       => CLASSIFIED_URL . 'assets/lib/bootstrap/js/bootstrap.bundle.min.js',
				'deps'      => array(),
				'ver'       => CLASSIFIED_VERSION,
				'in_footer' => true
			),
			'classified-author-1-scripts'  => array(
				'src'       => CLASSIFIED_URL . 'assets/js/classified-author-scripts-1.js',
				'deps'      => array( 'jquery' ),
				'ver'       => CLASSIFIED_VERSION,
				'in_footer' => true
			),
			'classified-author-2-scripts'  => array(
				'src'       => CLASSIFIED_URL . 'assets/js/classified-author-scripts-2.js',
				'deps'      => array( 'jquery' ),
				'ver'       => CLASSIFIED_VERSION,
				'in_footer' => true
			),
			'classified-scripts'           => array(
				'src'       => CLASSIFIED_URL . 'assets/js/classified-scripts.js',
				'deps'      => array( 'jquery' ),
				'ver'       => CLASSIFIED_VERSION,
				'in_footer' => true,
			),
			'classified-headers-scripts'   => array(
				'src'       => CLASSIFIED_URL . 'assets/js/classified-header-scripts.js',
				'deps'      => array( 'jquery' ),
				'ver'       => CLASSIFIED_VERSION,
				'in_footer' => true,
			),
		);

		foreach ( $scripts as $handle => $data ) {
			wp_register_script( $handle, $data['src'], $data['deps'], $data['ver'], $data['in_footer'] );
		}

		wp_enqueue_script( 'classified-bootstrap-scripts' );
		wp_enqueue_script( 'classified-scripts' );
		wp_enqueue_script( 'classified-headers-scripts' );

		wp_localize_script( 'classified-scripts', 'classified_script_obj', array(
			'classified_ajax_url' => admin_url( 'admin-ajax.php' ),
		) );

		wp_enqueue_style( 'classified-fa' );
		wp_enqueue_style( 'classified-fa-solid' );
		wp_enqueue_style( 'classified-fa-regular' );
		wp_enqueue_style( 'classified-fa-brands' );

		wp_enqueue_style( 'classified-fields-styles' );
		wp_enqueue_style( 'classified-dynamic-styles' );
		wp_enqueue_style( 'classified-header-styles' );

		if ( ( is_search() || is_tag() || is_category() ) && ! classified_is_archive() ) {
			wp_enqueue_style( 'classified-blogs-styles' );
		}

		$post = get_post();
		if ( is_page() && strpos( $post->post_content, 'cwpForm' ) !== false ) {
			global $classified_post_types;
			preg_match( '/\[cwpForm\s+type="([^"]+)"\s*\]/', $post->post_content, $matches );
			$type = isset( $matches[1] ) ? $matches[1] : 'post';
			if ( isset( $classified_post_types ) && is_array( $classified_post_types ) && in_array( $type, $classified_post_types ) ) {
				wp_enqueue_style( 'classified-submission-styles' );
				wp_enqueue_script( 'classified-submission-scripts' );
			}
		}

		if ( ! if_theme_can_load() ) {
			$font_family     = 'Source Sans Pro';
			$url_font_family = "family=" . str_replace( " ", "+", $font_family ) . ":ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&";
			$google_font_api = 'https://fonts.googleapis.com/css2?' . $url_font_family . 'display=swap';
			wp_enqueue_style( 'google_fonts', $google_font_api );
		}
	}

	add_action( 'wp_enqueue_scripts', 'classified_frontend_styles' );
}

if ( ! function_exists( "classified_is_singular" ) ) {
	function classified_is_singular() {
		global $classified_post_types;
		$return = false;
		if ( is_singular( $classified_post_types ) ) {
			$return = true;
		}

		return apply_filters( 'classified_is_singular', $return );
	}
}

if ( ! function_exists( "classified_is_archive" ) ) {
	function classified_is_archive() {
		global $classified_taxonomies, $classified_post_types;
		$return = false;
		if ( is_post_type_archive( $classified_post_types ) || is_tax( $classified_taxonomies ) ) {
			$return = true;
		}

		return apply_filters( 'classified_is_archive', $return );
	}
}

/**
 * @function classified_get_setting
 *
 * Return settings from CubeWP Settings.
 */
if ( ! function_exists( 'classified_get_setting' ) ) {
	function classified_get_setting( $setting, $handle_as = 'default', $find_array = '' ) {
		global $cwpOptions;
		if ( empty( $cwpOptions ) || ! is_array( $cwpOptions ) ) {
			$cwpOptions = get_option( 'cwpOptions' );
		}
		$return = '';
		if ( $handle_as == 'default' ) {
			$return = $cwpOptions[ $setting ] ?? '';
		} else {
			if ( $handle_as == 'page_url' ) {
				$return = $cwpOptions[ $setting ] ?? false;
				if ( is_array( $return ) ) {
					$return = $return[ $find_array ] ?? false;
				}
				if ( is_numeric( $return ) ) {
					$return = get_permalink( $return );
				}
			} else if ( $handle_as == 'media_url' ) {
				$return = $cwpOptions[ $setting ] ?? '';
				$return = wp_get_attachment_url( $return );
			}
		}

		return apply_filters( 'classified_get_setting', $return, $setting, $handle_as, $find_array );
	}
}

/**
 * @function classified_theme_support
 *
 * Add Classified Theme features support.
 */
if ( ! function_exists( 'classified_theme_support' ) ) {
	function classified_theme_support() {
		global $content_width;
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'widgets' );
		add_theme_support( 'html5', array(
			'comment-list',
			'comment-form',
			'search-form',
			'gallery',
			'caption',
			'navigation-widgets'
		) );
		add_theme_support( 'post-thumbnails', array( 'classified-ad', 'real-estate', 'automotive' ) );
		add_image_size( 'classified-grid', 340, 195, true );

		if ( ! isset( $content_width ) ) {
			$content_width = 900;
		}
	}

	add_filter( "after_setup_theme", "classified_theme_support", 11 );
}

/**
 * @function classified_register_sidebar_widget_area
 *
 * Register Default Sidebar.
 */
if ( ! function_exists( 'classified_register_sidebar_widget_area' ) ) {
	function classified_register_sidebar_widget_area() {
		register_sidebar( array(
			'name'          => esc_html__( "Default Sidebar", "classified-pro" ),
			'id'            => 'classified_default_sidebar',
			'before_widget' => '<div class="classified-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5>',
			'after_title'   => '</h5>'
		) );
	}

	add_action( 'widgets_init', 'classified_register_sidebar_widget_area' );
}

/**
 * @function classified_register_menu_locations
 *
 * Register WordPress menu locations.
 */
if ( ! function_exists( 'classified_register_menu_locations' ) ) {
	function classified_register_menu_locations() {
		$menu_locations = array(
			'classified_home_header'     => esc_html__( "Home Bottom Bar Page Header", "classified-pro" ),
			'classified_inner_header'    => esc_html__( "Inner Bottom Bar Pages Header", "classified-pro" ),
			'classified_home_offcanvas'  => esc_html__( "Home Page Offcanvas", "classified-pro" ),
			'classified_inner_offcanvas' => esc_html__( "Inner Pages Offcanvas", "classified-pro" ),
			'classified_header_topbar'   => esc_html__( "Header Top Bar", "classified-pro" ),
		);

		if ( function_exists( 'classified_get_setting' ) ) {
			$sub_footer = classified_get_setting( 'sub_footer' );
			if ( $sub_footer ) {
				$menu_locations['classified_sub_footer'] = esc_html__( "Sub Footer", "classified-pro" );
			}
		}

		register_nav_menus( $menu_locations );
	}

	add_filter( "init", "classified_register_menu_locations", 11 );
}

/**
 * @function classified_get_site_logo_url
 *
 * Return site logo image url.
 */
if ( ! function_exists( 'classified_get_site_logo_url' ) ) {
	function classified_get_site_logo_url() {
		$logo_url = '';
		if ( if_theme_can_load() ) {
			if ( is_front_page() || is_home() ) {
				$logo_url = classified_get_setting( 'home_page_logo', 'media_url' );
			} else {
				$logo_url = classified_get_setting( 'inner_pages_logo', 'media_url' );
			}
		}
		if ( empty( $logo_url ) ) {
			$logo_url = CLASSIFIED_URL . 'assets/images/logo.png';
		}

		return $logo_url;
	}
}

/**
 * @function classified_get_page_banner_bg_url
 *
 * Return banner image url for pages.
 */
if ( ! function_exists( 'classified_get_page_banner_bg_url' ) ) {
	function classified_get_page_banner_bg_url( $page_id ) {
		$bg_url = get_the_post_thumbnail_url( $page_id );
		if ( empty( $bg_url ) ) {
			$bg_url = CLASSIFIED_URL . 'assets/images/banner-bg.jpg';
		}

		return $bg_url;
	}
}

/**
 * @function classified_breadcrumb
 *
 * Classified Breadcrumb
 */
if ( ! function_exists( 'classified_breadcrumb' ) ) {
	function classified_breadcrumb() {
		ob_start();
		$sep = '&nbsp;/&nbsp;';
		if ( ! is_front_page() ) {
			?>
			<div class="classified-breadcrumbs">
				<a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'classified-pro' ); ?></a>
				<?php
				echo esc_html( $sep );
				if ( is_category() ) {
					the_category( '/' );
				} else if ( is_page() ) {
					the_title();
				} else if ( is_single() || is_archive() || is_search() || is_tax() || is_author() ) {
					if ( is_day() ) {
						printf( __( '%s', 'classified-pro' ), get_the_date() );
					} else if ( is_month() ) {
						printf( __( '%s', 'classified-pro' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'classified-pro' ) ) );
					} else if ( is_year() ) {
						printf( __( '%s', 'classified-pro' ), get_the_date( _x( 'Y', 'yearly archives date format', 'classified-pro' ) ) );
					} else if ( is_author() ) {
						$queried_object = get_queried_object();
						if ( isset( $queried_object->display_name ) ) {
							echo esc_html( $queried_object->display_name );
						}
					} else {
						if ( is_single() ) {
							global $classified_category_taxonomies;
							$post_id       = get_the_ID();
							$post_category = classified_get_post_terms( $post_id, $classified_category_taxonomies );
							$post_location = classified_get_post_terms( $post_id, array( 'locations' ) );
							if ( ! empty( $post_category ) && is_array( $post_category ) ) {
								foreach ( $post_category as $counter => $term ) {
									if ( $counter != 0 ) {
										echo ',&nbsp;';
									}
									echo '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
								}
								echo esc_html( $sep );
							}
							if ( ! empty( $post_location ) && is_array( $post_location ) ) {
								foreach ( $post_location as $counter => $location ) {
									if ( $counter != 0 ) {
										echo ',&nbsp;';
									}
									echo '<a href="' . esc_url( get_term_link( $location ) ) . '">' . esc_html( $location->name ) . '</a>';
								}
								echo esc_html( $sep );
							}
							the_title();
						} else {
							$queried_object = get_queried_object();
							if ( ! empty( $queried_object ) && isset( $queried_object->name ) ) {
								if ( isset( $queried_object->term_id ) ) {
									echo '<a href="' . esc_url( get_term_link( $queried_object->term_id ) ) . '">';
								}
								if ( isset( $queried_object->label ) ) {
									echo esc_html( $queried_object->label );
								} else {
									echo esc_html( $queried_object->name );
								}
								if ( isset( $queried_object->term_id ) ) {
									echo '</a>';
								}
							}
							if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
								echo esc_html( $sep );
								printf( __( 'Search: %s', 'classified-pro' ), sanitize_text_field( $_GET['s'] ) );
							}
						}
					}
				} else if ( is_home() ) {
					global $post;
					$page_for_posts_id = get_option( 'page_for_posts' );
					if ( $page_for_posts_id ) {
						$post = get_post( $page_for_posts_id );
						setup_postdata( $post );
						the_title();
						rewind_posts();
					}
				}
				?>
			</div>
			<?php
		}
		$output = ob_get_clean();

		return apply_filters( 'classified_breadcrumb', $output );
	}
}

if ( ! function_exists( 'classified_get_post_featured_image' ) ) {
	function classified_get_post_featured_image( $post_id = 0, $id_only = false, $size = 'medium' ) {
		$return = '';
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		if ( has_post_thumbnail( $post_id ) ) {
			if ( $id_only ) {
				$return = get_post_thumbnail_id( $post_id );
			} else {
				$return = get_the_post_thumbnail_url( $post_id, $size );
			}
		} else {
			$gallery = get_post_meta( $post_id, 'classified_gallery', true );
			$gallery = $gallery['meta_value'] ?? '';
			if ( ! empty( $gallery ) && is_array( $gallery ) ) {
				foreach ( $gallery as $galleryItemID ) {
					if ( $id_only ) {
						$return = $galleryItemID;
					} else {
						$return = wp_get_attachment_url( $galleryItemID );
					}
					break;
				}
			}
		}

		if ( empty( $return ) ) {
			$return = classified_get_setting( 'default_featured_image', 'media_url' );
		}

		if ( empty( $return ) ) {
			$return = CLASSIFIED_URL . 'assets/images/placeholder.png';
		}

		return $return;
	}
}

/**
 * @function classified_estimate_reading_time
 *
 * Classified Estimate Post Read Time
 */
if ( ! function_exists( 'classified_estimate_reading_time' ) ) {
	function classified_estimate_reading_time( $content, $words_per_minute = 200 ) {
		if ( has_blocks( $content ) ) {
			$blocks      = parse_blocks( $content );
			$contentHtml = '';
			if ( ! empty( $blocks ) ) {
				foreach ( $blocks as $block ) {
					$contentHtml .= render_block( $block );
				}
			}
			$content = $contentHtml;
		}
		$content = wp_strip_all_tags( $content );
		if ( ! $content ) {
			return 0;
		}
		$words_count = str_word_count( $content );

		return ceil( $words_count / $words_per_minute );
	}
}

/**
 * @function classified_after_theme_setup
 *
 * Remove admin bar for roles which caps are lower or equal to subscriber
 */
if ( ! function_exists( 'classified_after_theme_setup' ) ) {
	function classified_after_theme_setup() {
		$current_user = wp_get_current_user();
		if ( $current_user->user_level < 1 ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}

		load_theme_textdomain( 'classified-pro', get_template_directory() . '/languages' );
	}

	add_action( 'after_setup_theme', 'classified_after_theme_setup' );
}

/**
 * All Classified classes files to be loaded automatically.
 *
 * @param string $className Class name.
 */
if ( ! function_exists( 'classified_autoload_classes' ) ) {
	function classified_autoload_classes( $className ) {
		// If class does not start with our prefix (Classified), nothing will return.
		if ( ! str_contains( $className, 'Classified' ) ) {
			return null;
		}
		// Replace _ with - to match the file name.
		$file_name = str_replace( '_', '-', strtolower( $className ) );
		// Calling class file.
		$files = array(
			CLASSIFIED_PATH . 'classes/class-' . $file_name . '.php',
			CLASSIFIED_PATH . 'classes/fields/class-' . $file_name . '.php'
		);
		// Checking if exists then include.
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				require $file;
			}
		}

		return $className;
	}

	spl_autoload_register( 'classified_autoload_classes' );
}

/**
 * Class Classified: Loads Classified theme configurations.
 *
 * @since  1.0.0
 */
if ( ! function_exists( 'classified' ) ) {
	function classified() {
		if ( if_theme_can_load() ) {
			return Classified::instance();
		}

		return array();
	}

	classified();
}

function add_product_to_cart_via_ajax() {
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        $quantity = 1;
        $custom_data = isset($_POST['custom_data']) ? $_POST['custom_data'] : array();
		
        // Add the product to the cart
        WC()->cart->add_to_cart($product_id, $quantity, 0, $custom_data);

        // Send a response back to the AJAX request
        echo json_encode(array(
            'success' => true,
            'message' => 'Product added to cart successfully'
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'Invalid request'
        ));
    }
    wp_die(); // This is required to terminate AJAX properly
}

add_action('wp_ajax_add_product_to_cart', 'add_product_to_cart_via_ajax');
add_action('wp_ajax_nopriv_add_product_to_cart', 'add_product_to_cart_via_ajax');

function localize_ajax_params() {
    wp_localize_script('jquery', 'wc_add_to_cart_params_custom', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('ajax-nonce'),
        'checkout_url' => wc_get_checkout_url() // Get the WooCommerce checkout URL
    ));
}
add_action('wp_enqueue_scripts', 'localize_ajax_params');
function add_custom_user_meta_field() {
    register_meta('user', 'post_available', array(
        'type' => 'integer',
        'description' => 'Number of posts available for the user',
        'single' => true,
        'show_in_rest' => true,
    ));
}
add_action('init', 'add_custom_user_meta_field');
function add_post_available_on_order_complete($order_id) {
    // Get the order object
    $order = wc_get_order($order_id);
	$items = $order->get_items(); 
	$user_id = $order->get_customer_id();

	foreach ( $order->get_items() as $item_id => $item ) {
		 // Get the user ID associated with the order
		 $plan_id = wc_get_order_item_meta( $item_id, 'plan_id', true );
	
		  if( !empty($plan_id) ){
			  // Get the current post_available value
			 $current_post_available = get_user_meta($user_id, 'post_available', true);
		
			 if(empty($current_post_available))
				$current_post_available = 0;
			 $set_availability =  get_post_meta($plan_id, 'no_of_posts', true);
				
			 $new_post_available = $current_post_available + $set_availability;	
			 				// Update the user's meta field
				
			 update_user_meta($user_id, 'post_available', $new_post_available);		
		  }
	}		

   
}
add_action('woocommerce_order_status_completed', 'add_post_available_on_order_complete');

function custom_post_validation($post_id) {
    // Check if it's an autosave or a revision, and exit if true
	$current_user = wp_get_current_user();
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
    if (wp_is_post_revision($post_id)) return $post_id;
	if (user_can( $current_user, 'administrator' )) {
  			// no restriction for admin
			return $post_id;
		}
	if(!is_user_logged_in())
		wp_die('Please log in before posting');	
    // Define your custom validation logic here
    $content = $post->post_content;

    // For custom post types, adjust the post type condition as needed
   
        // Example: Ensure the content has at least 100 characters
        if (get_current_user_id()) {

			 $current_post_available = get_user_meta(get_current_user_id(), 'post_available', true);
			 if($current_post_available > 0)
			 {
			 	update_user_meta(get_current_user_id(), 'post_available', $current_post_available - 1);	 
				return $post_id;
			 }	 
			 else
			 {
				wp_die('You don\'t have sufficient post credit');	
			 }
			 		

		}
		else {
				wp_die('You don\'t have sufficient post credit');	
		}
        // You can add more custom validation rules as needed
        // Example: Check for specific keywords, etc.

        // If the post passes your validation, you can proceed to save it
       
    
	wp_die('You don\'t have sufficient post credit');		
    //return $post_id;
}

//add_action('save_post', 'custom_post_validation', 10, 2);

