<?php
// Adding New Section
if ( ! function_exists( 'cubewp_add_payment_settings_sections' ) ) {
	function cubewp_add_payment_settings_sections( $sections ) {
		$settings['payment'] = array(
			'title'  => __( 'Payment', 'cubewp' ),
			'id'     => 'payment',
			'icon'   => 'dashicons-money-alt',
			'fields' => array(
				array(
					'id'       => 'paid_submission',
					'type'     => 'select',
					'title'    => __( 'Paid Submission', 'cubewp' ),
					'subtitle' => '',
					'desc'     => __( 'Enable to sell Posts.', 'cubewp' ),
					'options'  => array(
						'yes' => 'Yes',
						'no'  => 'No',
					),
					'default'  => 'yes',
				)
			)
		);

		$single_position = array_search( 'map', array_keys( $sections ) ) + 1;

		return array_merge( array_slice( $sections, 0, $single_position ), $settings, array_slice( $sections, $single_position ) );
	}

	add_filter( 'cubewp/options/sections', 'cubewp_add_payment_settings_sections', 8, 1 );
}

if ( ! function_exists('cubewp_payment_status_label')) {
    function cubewp_payment_status_label( $payment_status = '' ){
        $arr = array(
            'pending'  =>  __('Pending', 'cubewp-frontend'),
            'paid'     =>  __('Paid', 'cubewp-frontend'),
            'free'     =>  __('Free', 'cubewp-frontend')
        );
        
        return isset($arr[$payment_status]) ? $arr[$payment_status] : $payment_status;
    }
}

if ( ! function_exists('cwp_plan_duration')) {
	function cwp_plan_duration($plan_id) {
		$plan_duration_type  =  get_post_meta($plan_id, 'plan_duration_type', true);
		$plan_duration =  '';
		if($plan_duration_type == 'per_year'){
			$plan_duration =  365;
		}
		else if($plan_duration_type == 'per_month'){
			$plan_duration = 30;
		}
		else if($plan_duration_type == 'per_days'){
			$plan_duration  = (int) get_post_meta($plan_id, 'plan_duration', true);
		}

		return $plan_duration;
	}
}

/**
 * Method cubewp_payments_add_plans_into_post_type_builder
 *
 * @param string $post_type any post type slug
 *
 * @return array All Plans IDS
 * @since  1.0.0
 */
if (!function_exists('cubewp_payments_add_plans_into_post_type_builder')) {
	function cubewp_payments_add_plans_into_post_type_builder( $return = array() , $post_type = '' ) {
		global $cwpOptions;
		$paid_submission = $cwpOptions['paid_submission'] ?? '';
		if ($paid_submission == 'yes') {
			$query_args = array(
				'post_type'      => 'price_plan',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_query'     => array(
					array(
						'key'     => 'plan_post_type',
						'value'   => $post_type,
						'compare' => '=',
					)
				)
			);
			$plans = get_posts($query_args);
			if ( ! empty( $plans ) && is_array( $plans ) ) {
				$options = array();
				foreach ( $plans as $plan ) {
					$options[ $plan ] = get_the_title( $plan );
				}
				$return['title'] = esc_html__("Select Plan", "cubewp-payments");
				$return['options'] = $options;
			}
		}

		return $return;
	}
	add_filter( "cubewp/builder/post_type/switcher", 'cubewp_payments_add_plans_into_post_type_builder', 10, 2 );
}
if ( ! function_exists( 'cubewp_update_order_status_to_processing' ) ) {
	function cubewp_update_order_status_to_processing( $order_id ) {
		if ( ! $order_id ) {
			return;
		}
		$order = wc_get_order( $order_id );
		if ( $order->is_paid() && $order->get_payment_method_title() !== 'Cash on Delivery' ) {
			$order->update_status( 'processing' );
		}
	}

	add_action( 'woocommerce_payment_complete', 'cubewp_update_order_status_to_processing' );
}

if ( ! function_exists( "cubewp_current_url" ) ) {
	function cubewp_current_url( $args = array(), $server = array() ) {
		if ( empty( $server ) || ! is_array( $server ) ) {
			$server = $_SERVER;
		}
		$url = ( isset( $server['HTTPS'] ) && $server['HTTPS'] === 'on' ? "https" : "http" ) . "://$server[HTTP_HOST]$server[REQUEST_URI]";

		return add_query_arg( $args, $url );
	}
}

if ( ! function_exists( "cubewp_add_payment_into_dashboard" ) ) {
	function cubewp_add_payment_into_dashboard( $tabs ) {
		return array_merge( $tabs, array( 'order' => esc_html__( 'Orders', 'cubewp-payments' ), ) );
	}

	add_filter( 'user/dashboard/content/types', 'cubewp_add_payment_into_dashboard' );
}

if ( ! function_exists( "cubewp_payments_make_dispute_request" ) ) {
	function cubewp_payments_make_dispute_request() {
		$type = 'error';
		$msg = '';
		if ( ! wp_verify_nonce( $_POST['message'], 'cubewp-payments-make-dispute' ) ) {
			$msg = esc_html__( 'Security verification failed', "cubewp-payments" );
		}
		$dispute_details = sanitize_textarea_field( $_POST['message'] );
		if ( ! empty( $dispute_details ) ) {
			if ( class_exists( 'CubeWp_Wallet_Load' ) ) {
				$order_id = sanitize_text_field( $_POST['order_id'] );
				$order    = wc_get_order( $order_id );
				if ( ! empty( $order ) ) {
					$order_customer  = $order->get_customer_id();
					$dispute_request = CubeWp_Wallet_Disputes_Processor::cubewp_wallet_create_dispute_request( $order_id, $order_customer, $dispute_details );
					if ( $dispute_request ) {
						$type = 'success';
						$msg = esc_html__( 'Request Created! Please Wait.', "cubewp-payments" );
					} else {
						$msg = esc_html__( 'Please Try Again', "cubewp-payments" );
					}
				}else {
					$msg = esc_html__( 'Please Try Again', "cubewp-payments" );
				}
			} else {
				$msg = esc_html__( 'CubeWP Wallet Addon Is Required.', "cubewp-payments" );
			}
		} else {
			$msg = esc_html__( 'Please enter details about dispute.', "cubewp-payments" );
		}

		wp_send_json( array( 'type' => $type, 'msg' => $msg, 'redirectURL' => 'self' ) );
	}

	add_action( 'wp_ajax_cubewp_payments_make_dispute', 'cubewp_payments_make_dispute_request' );
}
if ( ! function_exists( 'cubewp_payments_form_builder_settings' ) ) {
    function cubewp_payments_form_builder_settings( $fields, $args ) {
       if ( $args['form_type'] == 'post_type' ) {
          global $cwpOptions;
          if ( isset( $cwpOptions['paid_submission'] ) && $cwpOptions['paid_submission'] == 'yes' ) {
             $fields['show_in_plan'] = [
                'class'       => 'group-field field-show_in_plan',
                'label'       => esc_html__( "Show In Plan", "cubewp-payments" ),
                'name'        => 'show_in_plan',
                'type'        => 'dropdown',
                'options'     => [ '0' => __( "No" ), '1' => __( "Yes" ) ],
                'value'       => isset( $args['show_in_plan'] ) && ! empty( $args['show_in_plan'] ) ? $args['show_in_plan'] : '1',
                'extra_attrs' => 'data-name="show_in_plan"',
             ];
          }
       }

       return $fields;
    }

    add_filter( 'cubewp/builder/cubes/fields', 'cubewp_payments_form_builder_settings', 10, 2 );
}