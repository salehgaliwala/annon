<?php

if ( ! function_exists( "cwp_dashboard_order_tab" ) ) {
	function cwp_dashboard_order_tab( $tab_id ) {
		$ui = '';
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $ui;
		}

        wp_enqueue_script( 'cubewp-payments-dashboard-scripts' );

		$show_invoice = false;
		if ( isset( $_GET['order_id'] ) && ! empty( $_GET['order_id'] ) ) {
			$order = wc_get_order( sanitize_text_field( $_GET['order_id'] ) );
			if ( ! empty( $order ) && is_object( $order ) ) {
				$show_invoice = true;
			}
		}

		if ( $show_invoice ) {
			$order_id = sanitize_text_field( $_GET['order_id'] );
			wp_enqueue_style( "woocommerce-blocktheme" );
			ob_start();
			$view_url = cubewp_current_url( array( 'tab_id' => $tab_id ) );
			$view_url = remove_query_arg( "order_id", $view_url );
			?>
            <div class="woocommerce woocommerce-page">
                <a class="button"
                   href="<?php echo esc_url( $view_url ); ?>"><?php esc_html_e( "View All Orders", "cubewp-payments" ); ?></a>
				<?php WC_Shortcode_My_Account::view_order( $order_id ); ?>
            </div>
			<?php
			$ui .= ob_get_clean();
		} else {
			$query  = new WC_Order_Query( array(
				'limit'       => - 1,
				'customer_id' => get_current_user_id(),
				'orderby'     => 'date',
				'order'       => 'DESC',
				'return'      => 'ids',
			) );
			$orders = $query->get_orders();
			if ( ! empty( $orders ) && is_array( $orders ) ) {
				$ui        .= '<div class="cwp-table-responsive">';
				$ui        .= '<table class="cwp-user-dashboard-tables">';
				$ui        .= '<tr class="cwp-dashboard-list-head">';
				$ui        .= '<th>' . esc_html__( "Transaction ID", "cubewp-payments" ) . '</th>';
				$ui        .= '<th>' . esc_html__( "Created At", "cubewp-payments" ) . '</th>';
				$ui        .= '<th>' . esc_html__( "Status", "cubewp-payments" ) . '</th>';
				$ui        .= '<th>' . esc_html__( "Total Paid", "cubewp-payments" ) . '</th>';
				$ui        .= '<th>' . esc_html__( "Actions", "cubewp-payments" ) . '</th>';
				$ui        .= '</tr>';
				$modals_ui = '';
				foreach ( $orders as $orderID ) {
					$show_dispute = false;
					$order        = wc_get_order( $orderID );
					$order_status = $order->get_status();
					$release_on   = strtotime( 'NOW' );
					if ( class_exists( 'CubeWp_Wallet_Load' ) ) {
						$wallet = CubeWp_Wallet_Processor::get_wallet_transactions_by( 'order_id', $orderID );
						if ( ! empty( $wallet ) && is_array( $wallet ) ) {
							$wallet = $wallet[0];
							$status = $wallet['status'];
							if ( $status == 'on-hold' ) {
								$wallet_data = maybe_unserialize( $wallet['data'] );
								$hold_period = $wallet_data['hold_period'];
								$release_on  = strtotime( $wallet['created_at'] . " +$hold_period days" );
								$now         = strtotime( 'NOW' );
								if ( $release_on >= $now ) {
									$show_dispute = true;
								}
							} else if ( $status == 'disputed' ) {
								$order_status = esc_html__( 'Disputed', 'cubewp-payments' );
							} else if ( $status == 'refunded' ) {
								$order_status = esc_html__( 'Refunded', 'cubewp-payments' );
							}
						}
					}
					$show_dispute = $show_dispute && $order->get_status() == 'processing';

					$view_url = cubewp_current_url( array( 'tab_id' => $tab_id, 'order_id' => $order->get_id() ) );
					$ui       .= '<tr>';
					$ui       .= '<td>#' . $order->get_id() . '</td>';
					$ui       .= '<td>' . $order->get_date_created()->date_i18n( get_option( "date_format" ) ) . '</td>';
					$ui       .= '<td>' . $order_status . '</td>';
					$ui       .= '<td>' . $order->get_currency() . $order->get_total() . '</td>';
					$ui       .= '<td>';
					$ui       .= '<div class="cwp-dasboard-list-action">';
					$ui       .= '<span class="cwp-dashboard-tooltip">' . esc_html__( "View Details", "cubewp-payments" ) . '</span>';
					$ui       .= '<a class="cwp-user-dashboard-tab-content-post-action cwp-post-action-view" href="' . esc_url( $view_url ) . '" type="button">';
					$ui       .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">';
					$ui       .= '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"></path>';
					$ui       .= '<path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"></path>';
					$ui       .= '</svg>';
					$ui       .= '</a>';
					$ui       .= '</div>';
					if ( $show_dispute ) {
						$ui .= '<div class="cwp-dasboard-list-action">';
						$ui .= '<span class="cwp-dashboard-tooltip">' . esc_html__( "Dispute", "cubewp-payments" ) . '</span>';
						$ui .= '<a class="cwp-user-dashboard-tab-content-post-action cubewp-modal-trigger" type="button" data-cubewp-modal="#cubewp-make-dispute-' . $order->get_id() . '">';
						$ui .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">';
						$ui .= '<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>';
						$ui .= '<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>';
						$ui .= '</svg>';
						$ui .= '</a>';
						$ui .= '</div>';
						$ui .= '<br>' . esc_html__( "You can create dispute until ", "cubewp-payments" ) . '<b>' . date_i18n( get_option( 'date_format' ), $release_on ) . '</b>';
					}
					$ui .= '</td>';
					$ui .= '</tr>';
					if ( $show_dispute ) {
						$modals_ui .= '<div class="cubewp-modal" id="cubewp-make-dispute-' . esc_attr( $order->get_id() ) . '">';
						$modals_ui .= '<div class="cubewp-modal-content">';
						$modals_ui .= '<span class="dashicons dashicons-no cubewp-modal-close"></span>';
						$modals_ui .= '<form method="post" class="cubewp-make-dispute" data-cubewp-dispute-id="' . esc_attr( $order->get_id() ) . '">';
						$modals_ui .= '<div class="cubewp-dispute-information">';
						$modals_ui .= '<label for="cubewp_dispute_message-' . esc_attr( $order->get_id() ) . '">' . esc_html__( 'Tell us why are you making this dispute request? *', 'cubewp-payments' ) . '</label>';
						$modals_ui .= '<textarea name="cubewp_dispute_message" id="cubewp_dispute_message-' . esc_attr( $order->get_id() ) . '"></textarea>';
						$modals_ui .= '</div>';
						$modals_ui .= '<input type="submit" value="' . esc_html__( 'Send Request', 'cubewp-payments' ) . '">';
						$modals_ui .= '</form>';
						$modals_ui .= '</div>';
						$modals_ui .= '</div>';
					}
				}
				$ui .= '</table>';
				$ui .= '</div>';
				$ui .= $modals_ui;
			} else {
				$ui .= '<div class="cwp-empty-posts"><img class="cwp-empty-img" src="' . esc_url( CWP_PLUGIN_URI . "cube/assets/frontend/images/no-result.png" ) . '" alt=""><h2>' . esc_html__( "No Order Found", "cubewp-frontend" ) . '</h2><p>' . esc_html__( "There are no order found against you posts.", "cubewp-frontend" ) . '</p></div>';
			}
		}

		return $ui;
	}
}