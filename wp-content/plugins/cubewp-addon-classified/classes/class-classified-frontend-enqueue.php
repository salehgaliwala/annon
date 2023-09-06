<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Frontend Enqueue Class.
 *
 * @class Classified_Frontend_Enqueue
 */
class Classified_Frontend_Enqueue {
	public function __construct() {
		add_filter( 'frontend/style/register', array( $this, 'classified_register_frontend_styles' ), 12 );
		add_filter( 'frontend/script/register', array( $this, 'classified_register_frontend_scripts' ), 12 );

		add_filter( 'frontend/script/enqueue', array( $this, 'classified_load_frontend_scripts' ), 12 );

		add_filter( 'get_frontend_script_data', array( $this, 'classified_frontend_scripts_data' ), 12, 2 );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	private static function classified_get_order_dispute_body() {
		ob_start();
		?>
        <form class="m-0 p-0" id="classified-dispute-details">
            <div class="mb-0">
                <label for="classified-dispute" class="form-label"><?php esc_html_e( 'Dispute Details', 'cubewp-classified' ); ?></label>
                <textarea class="form-control" id="classified-dispute" placeholder="<?php esc_html_e( 'Enter The Issue You Are Facing.', 'cubewp-classified' ); ?>" rows="3" style="resize: none;"></textarea>
            </div>
        </form>
		<?php

		return ob_get_clean();
    }

	private static function classified_get_order_shipped_body() {
		ob_start();
		?>
		<form class="m-0 p-0" id="classified-shipping-confirmation">
            <div class="mb-4">
                <label for="classified-tracking" class="form-label"><?php esc_html_e( 'Tracking Info', 'cubewp-classified' ); ?></label>
                <textarea class="form-control" id="classified-tracking" placeholder="<?php esc_html_e( 'Enter Tracking ID And Courier Information Here.', 'cubewp-classified' ); ?>" rows="3" style="resize: none;"></textarea>
            </div>
            <div class="form-check m-0 p-0 d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="classified-rating-request" checked>
                <label class="form-check-label" for="classified-rating-request">
	                <?php esc_html_e( 'Request Customer To Rate Your Profile.', 'cubewp-classified' ); ?>
                </label>
            </div>
		</form>
		<?php

		return ob_get_clean();
	}

	private static function classified_get_order_received_body() {
		ob_start();
		?>
		<form class="m-0 p-0" id="classified-shipping-confirmation">
            <div class="form-check m-0 p-0 d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="classified-rating-request" checked>
                <label class="form-check-label" for="classified-rating-request">
	                <?php esc_html_e( 'Request Seller To Rate Your Profile.', 'cubewp-classified' ); ?>
                </label>
            </div>
		</form>
		<?php

		return ob_get_clean();
	}

	public function classified_frontend_scripts_data( $data, $handle ) {
		if ( $handle == 'classified-frontend-dashboard-scripts' ) {
			return array(
				'classified_ajax_url'                   => classified_ajax_url(),
				'classified_ignore_rate_request_nonce'  => classified_create_nonce( 'classified_ignore_rate_request_nonce' ),
				'classified_make_order_action_nonce'    => classified_create_nonce( 'classified_make_order_action_nonce' ),
				'classified_mark_item_sold_nonce'       => classified_create_nonce( 'classified_mark_item_sold_nonce' ),
				'classified_boost_item_modal_nonce'     => classified_create_nonce( 'classified_boost_item_modal_nonce' ),
				'classified_make_order_processing_texts'    => array(
					'heading' => esc_html__( "Are You Sure! You Are Processing This Order", "cubewp-classified" ),
					'confirm' => esc_html__( "Yes! Do It", "cubewp-classified" ),
					'cancel'  => esc_html__( "No", "cubewp-classified" ),
				),
				'classified_make_order_shipped_texts'    => array(
					'heading' => esc_html__( "Are You Sure! This Order Is Shipped?", "cubewp-classified" ),
					'confirm' => esc_html__( "Yes! It Is", "cubewp-classified" ),
					'cancel'  => esc_html__( "No", "cubewp-classified" ),
					'body'    => self::classified_get_order_shipped_body(),
				),
				'classified_make_order_dispute_texts'    => array(
					'heading' => esc_html__( "Are You Sure! You Want To Create Dispute Request?", "cubewp-classified" ),
					'confirm' => esc_html__( "Yes! I Am", "cubewp-classified" ),
					'cancel'  => esc_html__( "No", "cubewp-classified" ),
					'body'    => self::classified_get_order_dispute_body(),
				),
				'classified_mark_item_sold_modal_texts' => array(
					'heading' => esc_html__( "Are You Sure! You Want To Mark This Item As Sold", "cubewp-classified" ),
					'confirm' => esc_html__( "Yes! Do It", "cubewp-classified" ),
					'cancel'  => esc_html__( "No", "cubewp-classified" ),
				),
				'classified_ignore_rate_request' => array(
					'heading' => esc_html__( "Are You Sure! You Want To Ignore This Request", "cubewp-classified" ),
					'confirm' => esc_html__( "Yes", "cubewp-classified" ),
					'cancel'  => esc_html__( "No", "cubewp-classified" ),
				),
				'classified_make_order_complete_texts' => array(
					'heading' => esc_html__( "Are You Sure! You Have Received This Items.", "cubewp-classified" ),
					'confirm' => esc_html__( "Yes", "cubewp-classified" ),
					'cancel'  => esc_html__( "No", "cubewp-classified" ),
					'body'    => self::classified_get_order_received_body(),
				),
			);
		} else if ( $handle == 'classified-elements-scripts' ) {
			return array(
				'classified_ajax_url'              => classified_ajax_url(),
			);
		}

		return $data;
	}

	public function classified_register_frontend_styles( $styles ) {
		return array_merge( $styles, array(
			'classified-elements-styles'           => array(
				'src'     => CLASSIFIED_PLUGIN_URL . 'assets/css/classified-elements-styles.css',
				'deps'    => array( 'classified-styles', 'classified-slick-styles' ),
				'version' => CLASSIFIED_PLUGIN_VERSION,
				'has_rtl' => false,
			),
			'classified-frontend-dashboard-styles' => array(
				'src'     => CLASSIFIED_PLUGIN_URL . 'assets/css/classified-frontend-dashboard.css',
				'deps'    => array( 'classified-loop-style1-styles' ),
				'version' => CLASSIFIED_PLUGIN_VERSION,
				'has_rtl' => false,
			)
		) );
	}

	public function classified_register_frontend_scripts( $script ) {
		return array_merge( $script, array(
			'classified-elements-scripts'           => array(
				'src'     => CLASSIFIED_PLUGIN_URL . 'assets/js/classified-elements-scripts.js',
				'deps'    => array( 'jquery', 'classified-slick-scripts', 'classified-masonry-scripts' ),
				'version' => CLASSIFIED_PLUGIN_VERSION,
				'has_rtl' => false,
			),
			'classified-frontend-dashboard-scripts' => array(
				'src'     => CLASSIFIED_PLUGIN_URL . 'assets/js/classified-frontend-dashboard-scripts.js',
				'deps'    => array( 'jquery', 'classified-countdown-scripts' ),
				'version' => CLASSIFIED_PLUGIN_VERSION,
				'has_rtl' => false,
			)
		) );
	}

	public function classified_load_frontend_scripts( $data ) {
		if ( is_page() ) {
			if ( has_shortcode( get_the_content(), 'cwp_dashboard' ) ) {
				CubeWp_Enqueue::enqueue_style( 'classified-frontend-dashboard-styles' );
				CubeWp_Enqueue::enqueue_script( 'classified-frontend-dashboard-scripts' );
			}
		}

		if ( is_page() || is_home() || cubewp_is_elementor_editing() ) {
			CubeWp_Enqueue::enqueue_style( 'classified-elements-styles' );
			CubeWp_Enqueue::enqueue_script( 'classified-elements-scripts' );
		}

		return $data;
	}
}