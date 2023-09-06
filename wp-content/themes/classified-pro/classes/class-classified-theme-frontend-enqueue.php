<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Theme Frontend Enqueue Class.
 *
 * @class Classified_Theme_Frontend_Enqueue
 */
class Classified_Theme_Frontend_Enqueue {
	public function __construct() {
		add_filter( 'frontend/style/register', array( $this, 'classified_register_frontend_styles' ), 11 );
		add_filter( 'frontend/script/register', array( $this, 'classified_register_frontend_scripts' ), 11 );

		add_filter( 'frontend/script/enqueue', array( $this, 'classified_load_frontend_scripts' ), 11 );
	}

	public function classified_register_frontend_styles( $styles ) {
		unset( $styles['cwp-login-register'] );
		if ( classified_is_archive() ) {
			unset( $styles['archive-cpt-styles'] );
		}

		if ( ! isset( $styles['cubewp-datepicker'] ) ) {
			$styles['cubewp-datepicker'] = array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/datepicker/jquery-ui.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'media'   => 'all',
				'has_rtl' => false,
			);
		}

		$url_font_family = '';
		$typos           = array(
			'body'  => true,
			'h1'    => true,
			'h2'    => true,
			'h3'    => true,
			'h4'    => true,
			'h5'    => true,
			'h6'    => true,
			'p'     => true,
			'span'  => true,
			'label' => true,
			'a'     => true,
			'p-sm'  => false,
			'p-md'  => false,
			'p-lg'  => false
		);
		foreach ( $typos as $tag => $is_tag ) {
			$setting_id = 'typography-' . $tag;
			$settings   = classified_get_setting( $setting_id );
			if ( isset( $settings["font-family"] ) && ! empty( $settings["font-family"] ) ) {
				$font_family     = $settings["font-family"];
				$url_font_family .= "family=" . str_replace( " ", "+", $font_family ) . ":ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&";
			}
		}

		$google_font_api = 'https://fonts.googleapis.com/css2?' . $url_font_family . 'display=swap';

		return array_merge( $styles, array(
			'classified-font-family'             => array(
				'src'     => $google_font_api,
				'deps'    => array(),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-slick-styles'            => array(
				'src'     => CLASSIFIED_URL . 'assets/lib/slick/slick.css',
				'deps'    => array(),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-pretty-photo-styles'     => array(
				'src'     => CLASSIFIED_URL . 'assets/lib/pretty-photo/css/prettyPhoto.css',
				'deps'    => array(),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-modals-styles'           => array(
				'src'     => CLASSIFIED_URL . 'assets/css/classified-modals.css',
				'deps'    => array( 'classified-styles' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-login-register-styles'   => array(
				'src'     => CLASSIFIED_URL . 'assets/css/classified-login-register.css',
				'deps'    => array( 'classified-styles', 'classified-fields-styles', 'classified-modals-styles' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-shortcode-pricing-plans' => array(
				'src'     => CLASSIFIED_URL . 'assets/css/classified-shortcode-pricing-plans.css',
				'deps'    => array( 'classified-styles' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-loop-style1-styles'      => array(
				'src'     => CLASSIFIED_URL . 'assets/css/classified-loop-style1-styles.css',
				'deps'    => array( 'classified-styles' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-loop-style2-styles'      => array(
				'src'     => CLASSIFIED_URL . 'assets/css/classified-loop-style2-styles.css',
				'deps'    => array( 'classified-styles' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-submission-styles'       => array(
				'src'     => CLASSIFIED_URL . 'assets/css/classified-submission-styles.css',
				'deps'    => array(),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-single-styles'           => array(
				'src'     => CLASSIFIED_URL . 'assets/css/classified-single-styles.css',
				'deps'    => array(
					'classified-styles',
					'classified-dynamic-styles',
					'classified-slick-styles',
					'classified-pretty-photo-styles',
					'classified-loop-style1-styles'
				),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-archive-styles'          => array(
				'src'     => CLASSIFIED_URL . 'assets/css/classified-archive-styles.css',
				'deps'    => array( 'classified-styles', 'classified-loop-style1-styles', 'cubewp-datepicker' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			)
		) );
	}

	public function classified_register_frontend_scripts( $script ) {
		return array_merge( $script, array(
			'classified-masonry-scripts'        => array(
				'src'     => CLASSIFIED_URL . 'assets/lib/masonry/masonry.pkgd.min.js',
				'deps'    => array(),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-sticky-kit-scripts'     => array(
				'src'     => CLASSIFIED_URL . 'assets/lib/sticky-kit/jquery.sticky-kit.min.js',
				'deps'    => array( 'jquery' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-slick-scripts'          => array(
				'src'     => CLASSIFIED_URL . 'assets/lib/slick/slick.min.js',
				'deps'    => array( 'jquery' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-pretty-photo-scripts'   => array(
				'src'     => CLASSIFIED_URL . 'assets/lib/pretty-photo/js/jquery.prettyPhoto.js',
				'deps'    => array( 'jquery' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-countdown-scripts'      => array(
				'src'     => CLASSIFIED_URL . 'assets/lib/countdown/countdown.js',
				'deps'    => array(),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-confirm-scripts'        => array(
				'src'     => CLASSIFIED_URL . 'assets/lib/classified-confirm/classified-confirm.js',
				'deps'    => array( 'jquery' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-modals-scripts'         => array(
				'src'     => CLASSIFIED_URL . 'assets/js/classified-modals-scripts.js',
				'deps'    => array( 'jquery' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-loop-style1-scripts'    => array(
				'src'     => CLASSIFIED_URL . 'assets/js/classified-loop-style1.js',
				'deps'    => array(),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-login-register-scripts' => array(
				'src'     => CLASSIFIED_URL . 'assets/js/classified-login-register.js',
				'deps'    => array( 'jquery' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-submission-scripts'     => array(
				'src'     => CLASSIFIED_URL . 'assets/js/classified-submission-scripts.js',
				'deps'    => array( 'jquery', 'cwp-frontend-fields' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-archive-scripts'        => array(
				'src'     => CLASSIFIED_URL . 'assets/js/classified-archive-scripts.js',
				'deps'    => array( 'jquery', 'classified-loop-style1-scripts' ),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			),
			'classified-single-scripts'         => array(
				'src'     => CLASSIFIED_URL . 'assets/js/classified-single-scripts.js',
				'deps'    => array(
					'jquery',
					'classified-slick-scripts',
					'classified-sticky-kit-scripts',
					'classified-pretty-photo-scripts',
					'classified-modals-scripts'
				),
				'version' => CLASSIFIED_VERSION,
				'has_rtl' => false,
			)
		) );
	}

	public function classified_load_frontend_scripts( $data ) {
		if ( CWP()->is_request( 'frontend' ) || cubewp_is_elementor_editing() ) {
			CubeWp_Enqueue::enqueue_style( 'classified-font-family' );
			CubeWp_Enqueue::enqueue_style( 'classified-dynamic-styles' );
			CubeWp_Enqueue::enqueue_script( 'classified-confirm-scripts' );
			if ( classified_is_singular() ) {
				CubeWp_Enqueue::enqueue_style( 'classified-single-styles' );
				CubeWp_Enqueue::enqueue_script( 'classified-single-scripts' );
				CubeWp_Enqueue::enqueue_script( 'cwp-single' );
			}
			if ( classified_is_archive() ) {
				CubeWp_Enqueue::enqueue_style( 'classified-archive-styles' );
				CubeWp_Enqueue::enqueue_script( 'classified-archive-scripts' );
			}
			if ( ! is_user_logged_in() ) {
				CubeWp_Enqueue::enqueue_style( 'classified-login-register-styles' );
				CubeWp_Enqueue::enqueue_script( 'classified-login-register-scripts' );
			}
		}

		return $data;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}