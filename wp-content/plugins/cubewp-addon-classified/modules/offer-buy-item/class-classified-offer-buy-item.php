<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Icons Class.
 *
 * @class Classified_Icons
 */
class Classified_Offer_Buy_Item {
	public function __construct() {
		// Buy And Make An Offer Ajax Callback
		add_action( 'wp_ajax_classified_make_offer', array( $this, 'classified_make_offer' ) );
		add_action( 'wp_ajax_classified_buy_item', array( $this, 'classified_buy_item' ) );

		// Order Action Ajax Callback
		add_action( 'wp_ajax_classified_make_order_action', array( $this, 'classified_make_order_action' ) );

		// Saving product author into order meta
		add_action( 'woocommerce_thankyou', array( $this, 'save_product_author_id_to_order_meta' ) );

		// Change WooCommerce order status to processing if online payment success
		add_action( 'woocommerce_payment_complete', array( $this, 'classified_update_order_status_to_processing' ) );

		add_filter('woocommerce_order_status_on-hold_to_processing', array( $this, 'classified_order_received_notification' ));

		// Republish ad if WooCommerce order got cancelled
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'classified_woo_order_cancelled' ) );

		// Registering Buy And Make An Offer Styles And Scripts
		add_filter( 'frontend/style/register', array( $this, 'classified_register_frontend_styles' ), 13 );
		add_filter( 'frontend/script/register', array( $this, 'classified_register_frontend_scripts' ), 13 );

		// Localizing Data Into Buy And Make An Offer Scripts
		add_filter( 'get_frontend_script_data', array( $this, 'classified_frontend_scripts_data' ), 13, 2 );

		// Adding Settings Into CubeWP Settings For Buy And Make An Offer
		add_filter( 'cubewp/options/sections', array( $this, 'classified_buy_and_offer_settings' ), 13, 1 );

		// Adding Custom Cubes
		add_filter( 'cubewp/builder/single/custom/cubes', array( $this, 'classified_buy_and_offer_cubes' ), 11, 2 );

		// Adding Email Type
		add_filter( 'cubewp/email/types', array( $this, 'classified_email_types' ) );

		// Shipped Order Status
		add_filter( 'wc_order_statuses', array( $this, 'add_shipped_order_status_into_woo' ) );
		add_filter( 'woocommerce_register_shop_order_post_statuses', array( $this, 'register_shipped_order_status' ) );
		add_filter( 'woocommerce_analytics_excluded_order_statuses', array(
			$this,
			'append_shipped_order_post_status'
		) );
		add_filter( 'woocommerce_valid_order_statuses_for_payment', array(
			$this,
			'append_shipped_order_post_status'
		) );
		add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array(
			$this,
			'append_shipped_order_post_status'
		) );
	}

	public static function classified_buy_item_btn( $post_id ) {
		CubeWp_Enqueue::enqueue_style( 'classified-offer-buy-item-styles' );
		CubeWp_Enqueue::enqueue_script( 'classified-offer-buy-item-scripts' );
		ob_start();
		if ( is_user_logged_in() ) {
			?>
            <div class="classified-item-buy-now">
                <button class="classified-filled-btn classified-item-buy-now-btn"
                        data-item-id="<?php echo esc_attr( $post_id ); ?>">
					<?php esc_html_e( "Buy Now", "cubewp-classified" ); ?>
                </button>
            </div>
			<?php
		} else {
			?>
            <div class="classified-item-buy-now">
                <button class="classified-filled-btn w-100" type="button" data-bs-toggle="modal"
                        data-bs-target="#classified-login-register">
					<?php esc_html_e( "Buy Now", "cubewp-classified" ); ?>
                </button>
            </div>
			<?php
		}

		return ob_get_clean();
	}

	public static function classified_offer_item_btn( $post_id ) {
		if ( ! classified_is_inbox_active() ) {
			return esc_html__( 'Please Install And Activate CubeWP Inbox Addon.', 'cubewp-classified' );
		}

		CubeWp_Enqueue::enqueue_style( 'classified-offer-buy-item-styles' );
		CubeWp_Enqueue::enqueue_script( 'classified-offer-buy-item-scripts' );
		$price = classified_get_item_price( $post_id );
		if ( $price < 1 ) {
			return '';
		}
		$suggested_offers = classified_get_setting( 'offer_suggestion' );
		$percentage       = classified_get_setting( 'offer_suggestion_percent' );
		$price_percentage = ( $percentage / 100 ) * $price;
		$less_price       = ( $price - $price_percentage );
		if ( $less_price < 0 ) {
			$less_price = 0.1;
		}
		$_less_price = ( $less_price - $price_percentage );
		if ( $_less_price < 0 ) {
			$_less_price = 0.1;
		}
		$minmax_percentage = classified_get_setting( 'minmax_offer_percent' );
		$minmax_percentage = ( $minmax_percentage / 100 ) * $price;
		$min_price         = ( $price - $minmax_percentage );
		if ( $min_price < 0 ) {
			$min_price = 0.1;
		}
		$max_price = $price;
		ob_start();
		?>
        <div class="classified-item-bid">
			<?php
			if ( is_user_logged_in() ) {
				?>
                <button class="classified-not-filled-btn classified-make-an-offer-btn">
					<?php esc_html_e( "Make an offer", "cubewp-classified" ); ?>
                </button>
                <div class="classified-make-an-offer-container">
					<?php if ( $suggested_offers == '1' ) { ?>
                        <p class="p-md"><?php esc_html_e( "Suggested Offers", "cubewp-classified" ); ?></p>
                        <div class="classified-make-an-offer-offers btn-group d-flex">
                            <button class="classified-not-filled-btn"><?php echo classified_build_price( $_less_price, false, false ); ?></button>
                            <button class="classified-not-filled-btn"><?php echo classified_build_price( $less_price, false, false ); ?></button>
                            <button class="classified-not-filled-btn"
                                    disabled><?php echo classified_build_price( $price, false, false ); ?></button>
                        </div>
					<?php } ?>
                    <p class="p-md"><?php esc_html_e( "Your Offer", "cubewp-classified" ); ?></p>
                    <form class="classified-make-an-offer-field" data-item-id="<?php echo esc_attr( $post_id ); ?>">
                        <label class="visually-hidden"
                               for="classified-make-an-offer"><?php echo classified_build_price( $price, false, false ); ?></label>
                        <div class="input-group">
                            <div class="input-group-text"><?php echo classified_build_price( $price, true ); ?></div>
                            <input type="text" class="form-control" id="classified-make-an-offer"
                                   placeholder="<?php esc_html_e( "Your Offer", "cubewp-classified" ); ?>"
                                   title="<?php esc_html_e( "Please Enter Your Offer", "cubewp-classified" ); ?>"
                                   value="<?php echo classified_build_price( $price, false, false ); ?>"
                                   min="<?php esc_attr_e( classified_build_price( $min_price, false, false ) ); ?>"
                                   max="<?php esc_attr_e( classified_build_price( $max_price, false, false ) ); ?>">
                            <div class="invalid-feedback">
								<?php echo sprintf( esc_html__( "Price Must Be Between %s - %s", "cubewp-classified" ), classified_build_price( $min_price, false, false ), classified_build_price( $max_price, false, false ) ); ?>
                            </div>
                        </div>
                        <button class="classified-not-filled-btn" type="submit">
                            <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
				<?php
			} else {
				?>
                <button class="classified-not-filled-btn w-100" type="button" data-bs-toggle="modal"
                        data-bs-target="#classified-login-register">
					<?php esc_html_e( "Make an offer", "cubewp-classified" ); ?>
                </button>
				<?php
			}
			?>
        </div>
		<?php

		return apply_filters( 'classified_offer_and_buy_item', ob_get_clean(), $post_id );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_email_types( $email_types ) {
		$new_types = array(
			array(
				'name'      => 'offer-received',
				'label'     => esc_html__( 'Offer Received (To Author)', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'offer-submitted',
				'label'     => esc_html__( 'Offer Submitted (To Customer)', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),

			array(
				'name'      => 'order-received',
				'label'     => esc_html__( 'Order Received (To Author)', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'order-canceled',
				'label'     => esc_html__( 'Order Canceled (To Author)', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'order-completed-author',
				'label'     => esc_html__( 'Order Completed (To Author)', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'order-shipped',
				'label'     => esc_html__( 'Order Shipped (To Customer)', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'order-completed-customer',
				'label'     => esc_html__( 'Order Completed (To Customer)', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
		);

		return array_merge( $email_types, $new_types );
	}

    public function classified_order_received_notification( $order_id ) {
	    if ( ! $order_id ) {
		    return $order_id;
	    }
	    $order = wc_get_order( $order_id );
	    if ( $order ) {
		    foreach ( $order->get_items() as $item ) {
			    $product_data = $item->get_data();
			    $product_id   = $product_data['product_id'] ?? false;
			    if ( $product_id ) {
				    if ( get_post_meta( $product_id, '_is_classified_item', true ) ) {
					    $item_id        = get_post_meta( $product_id, '_classified_item', true );
					    $product_author = classified_get_post_author( $item_id );
					    // Emails
					    $notification = cubewp_get_email_template_by_post_id( $item_id, 'user', 'order-received' );
					    if ( $notification ) {
						    $email = classified_get_userdata( $product_author, 'email' );
						    CubeWp_Emails::cubewp_send_email( $email, $notification, $product_author, $item_id );
					    }
				    }
			    }
		    }
	    }
    }

	public function classified_update_order_status_to_processing( $order_id ) {
		if ( ! $order_id ) {
			return $order_id;
		}
		$order = wc_get_order( $order_id );
		if ( $order->is_paid() && $order->get_payment_method_title() !== 'Cash on Delivery' ) {
			$order->update_status( 'processing' );
		}
	}

	public function classified_woo_order_cancelled( $order_id, $order ) {
		foreach ( $order->get_items() as $item ) {
			$product_id = $item->get_product_id();
			if ( get_post_meta( $product_id, '_is_classified_item', true ) ) {
				$classified_item_id = get_post_meta( $product_id, '_classified_item', true );
				if ( get_post_status( $classified_item_id ) === 'sold' ) {
					wp_update_post( array(
						'ID'          => $classified_item_id,
						'post_status' => 'publish'
					) );

					$notification = cubewp_get_email_template_by_post_id( $classified_item_id, 'user', 'order-canceled' );
					if ( $notification ) {
						$product_author = classified_get_post_author( $classified_item_id );
						$email          = classified_get_userdata( $product_author, 'email' );
						CubeWp_Emails::cubewp_send_email( $email, $notification, $product_author, $classified_item_id );
					}
				}
			}
		}
	}

	public function add_shipped_order_status_into_woo( $statuses ) {
		$statuses['wc-shipped'] = _x( 'Shipped', 'Order status', 'cubewp-classified' );

		return $statuses;
	}

	public function register_shipped_order_status( $statuses ) {
		$statuses['wc-shipped'] = array(
			'label'                     => _x( 'Shipped', 'cubewp-classified' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>' )
		);

		return $statuses;
	}

	public function append_shipped_order_post_status( $statuses ) {
		$statuses[] = 'shipped';

		return $statuses;
	}

	public function classified_make_order_action() {
		if ( ! classified_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'classified_make_order_action_nonce' ) ) {
			wp_send_json_error( esc_html__( "Nonce Security Error", "cubewp-classified" ) );
		}
		$order_id = sanitize_text_field( $_POST['order_id'] );
		$type     = sanitize_text_field( $_POST['type'] );
		$order    = wc_get_order( $order_id );

		if ( $type == 'processing' ) {
			$order->update_status( 'wc-processing' );
		} else if ( $type == 'shipped' ) {
			$tracking_details = sanitize_textarea_field( $_POST['shipping_details'] ?? false );
			$rating_request   = sanitize_text_field( $_POST['rating_request'] ?? true );
			$order_customer   = $order->get_customer_id();
			if ( $rating_request ) {
				$order_author               = $order->get_meta( '_classified_author' );
				$user_meta                  = get_user_meta( $order_customer, 'classified_rate_profile_requests', true );
				$user_meta                  = is_array( $user_meta ) && ! empty( $user_meta ) ? $user_meta : array();
				$user_meta[ $order_author ] = strtotime( 'NOW' );
				update_user_meta( $order_customer, 'classified_rate_profile_requests', $user_meta );
			}
			$order->update_status( 'wc-shipped' );
			$order->update_meta_data( '_classified_tracking_details', $tracking_details );
			$order->save();
			if ( ! empty( $order->get_items() ) && is_array( $order->get_items() ) ) {
				foreach ( $order->get_items() as $item ) {
					$product_data = $item->get_data();
					$product_id   = $product_data['product_id'] ?? false;
					if ( $product_id ) {
						if ( get_post_meta( $product_id, '_is_classified_item', true ) ) {
							$price   = $product_data['subtotal'];
							$item_id = get_post_meta( $product_id, '_classified_item', true );

							$notification = cubewp_get_email_template_by_post_id( $item_id, 'user', 'order-shipped' );
							if ( $notification ) {
								$email = classified_get_userdata( $order_customer, 'email' );
								CubeWp_Emails::cubewp_send_email( $email, $notification, $order_customer, $item_id );
							}
							if ( classified_is_wallet_active() ) {
								$parameters = array(
									'amount'      => $price,
									'post_id'     => $item_id,
									'order_id'    => $order_id,
									'customer_id' => $order_customer,
								);
								CubeWp_Wallet_Processor::cubewp_add_funds_to_wallet( $parameters );
							}
						}
					}
				}
			}
		} else if ( $type == 'received' ) {
			$rating_request = sanitize_text_field( $_POST['rating_request'] ?? true );
			$order_customer = $order->get_customer_id();
			if ( $rating_request ) {
				$order_author                 = $order->get_meta( '_classified_author' );
				$user_meta                    = get_user_meta( $order_author, 'classified_rate_profile_requests', true );
				$user_meta                    = is_array( $user_meta ) && ! empty( $user_meta ) ? $user_meta : array();
				$user_meta[ $order_customer ] = strtotime( 'NOW' );
				update_user_meta( $order_author, 'classified_rate_profile_requests', $user_meta );
			}
			$order->update_status( 'wc-completed' );

			if ( ! empty( $order->get_items() ) && is_array( $order->get_items() ) ) {
				foreach ( $order->get_items() as $item ) {
					$product_data = $item->get_data();
					$product_id   = $product_data['product_id'] ?? false;
					if ( $product_id ) {
						if ( get_post_meta( $product_id, '_is_classified_item', true ) ) {
							$item_id = get_post_meta( $product_id, '_classified_item', true );

							$notification = cubewp_get_email_template_by_post_id( $item_id, 'user', 'order-completed-author' );
							if ( $notification ) {
								$product_author = classified_get_post_author( $item_id );
								$email          = classified_get_userdata( $product_author, 'email' );
								CubeWp_Emails::cubewp_send_email( $email, $notification, $product_author, $item_id );
							}
							$notification = cubewp_get_email_template_by_post_id( $item_id, 'user', 'order-completed-customer' );
							if ( $notification ) {
								$email = classified_get_userdata( $order_customer, 'email' );
								CubeWp_Emails::cubewp_send_email( $email, $notification, $order_customer, $item_id );
							}
						}
					}
				}
			}
		} else if ( $type == 'dispute' ) {
			$dispute_details = sanitize_textarea_field( $_POST['dispute_details'] ?? true );
			if ( ! empty( $dispute_details ) ) {
				if ( classified_is_wallet_active() ) {
					$order_customer  = $order->get_customer_id();
					$dispute_request = CubeWp_Wallet_Disputes_Processor::cubewp_wallet_create_dispute_request( $order_id, $order_customer, $dispute_details );
					if ( $dispute_request ) {
						wp_send_json_success( esc_html__( "Request Created! Please Wait.", "cubewp-classified" ) );
					} else {
						wp_send_json_error( esc_html__( "Please Try Again", "cubewp-classified" ) );
					}
				} else {
					wp_send_json_error( esc_html__( "CubeWP Wallet Addon Is Required.", "cubewp-classified" ) );
				}
			} else {
				wp_send_json_error( esc_html__( "Please Try Again", "cubewp-classified" ) );
			}
		} else {
			wp_send_json_error( esc_html__( "Invalid Action", "cubewp-classified" ) );
		}

		wp_send_json_success( esc_html__( "Order Status Updated.", "cubewp-classified" ) );
	}

	public function save_product_author_id_to_order_meta( $order_id ) {
		if ( ! $order_id ) {
			return;
		}
		if ( ! get_post_meta( $order_id, '_classified_thankyou_action_done', true ) ) {
			$order          = wc_get_order( $order_id );
			$product_author = false;
			foreach ( $order->get_items() as $item ) {
				$product_data = $item->get_data();
				$product_id   = $product_data['product_id'] ?? false;
				if ( $product_id ) {
					if ( get_post_meta( $product_id, '_is_classified_item', true ) ) {
						$item_id = get_post_meta( $product_id, '_classified_item', true );
						if ( ! $product_author ) {
							$product_author = classified_get_post_author( $item_id );
						}
						if ( ! $order->has_status( 'failed' ) ) {
							wp_update_post( array(
								'ID'          => $item_id,
								'post_status' => 'sold',
							) );
						}
					}
				}
			}
			if ( $product_author ) {
				$order->update_meta_data( '_classified_author', $product_author );
				$order->update_meta_data( '_classified_thankyou_action_done', true );
				$order->save();
			}
		}
	}

	public function classified_buy_and_offer_cubes( $cubes, $post_type ) {
		global $classified_post_types;
		if ( ! in_array( $post_type, $classified_post_types ) ) {
			return $cubes;
		}

		return array_merge( $cubes, array(
			'classified_ad_buy_btn'   => array(
				'label' => __( "Buy Ad Button", "cubewp-classified" ),
				'name'  => 'classified_ad_buy_btn',
				'type'  => 'classified_ad_buy_btn',
			),
			'classified_ad_offer_btn' => array(
				'label' => __( "Offer Ad Button", "cubewp-classified" ),
				'name'  => 'classified_ad_offer_btn',
				'type'  => 'classified_ad_offer_btn',
			),
		) );
	}

	public function classified_buy_and_offer_settings( $sections ) {
		$new_sections['classified_buy_offer'] = array(
			'title'  => __( 'Buy And Offer', 'cubewp-classified' ),
			'id'     => 'classified_buy_offer',
			'icon'   => 'dashicons dashicons-chart-line',
			'fields' => array(
				array(
					'id'      => 'offer_suggestion',
					'title'   => __( 'Make Offers Suggestion', 'cubewp-classified' ),
					'desc'    => __( 'Show offers suggestion in make an offer process.', 'cubewp-classified' ),
					'type'    => 'switch',
					'default' => '1',
				),
				array(
					'id'       => 'offer_suggestion_percent',
					'title'    => __( 'Suggestion Offers Percentage', 'cubewp-classified' ),
					'desc'     => __( 'Enter a percentage number for system to calculate the suggested offers.', 'cubewp-classified' ),
					'type'     => 'text',
					'default'  => '10',
					'required' => array(
						array( 'offer_suggestion', 'equals', '1' )
					)
				),
				array(
					'id'      => 'minmax_offer_percent',
					'title'   => __( 'Min-Max Offers Percentage', 'cubewp-classified' ),
					'desc'    => __( 'Enter a percentage number for minimum and maximum offers.', 'cubewp-classified' ),
					'type'    => 'text',
					'default' => '30',
				)
			),
		);
		$new_section_pos                      = array_search( 'map', array_keys( $sections ) ) + 0;

		return array_merge( array_slice( $sections, 0, $new_section_pos ), $new_sections, array_slice( $sections, $new_section_pos ) );
	}

	public function classified_register_frontend_scripts( $script ) {
		return array_merge( $script, array(
			'classified-offer-buy-item-scripts' => array(
				'src'     => CLASSIFIED_PLUGIN_URL . 'assets/js/classified-offer-buy-item.js',
				'deps'    => array( 'jquery' ),
				'version' => CLASSIFIED_PLUGIN_VERSION,
				'has_rtl' => false,
			)
		) );
	}

	public function classified_register_frontend_styles( $styles ) {
		return array_merge( $styles, array(
			'classified-offer-buy-item-styles' => array(
				'src'     => CLASSIFIED_PLUGIN_URL . 'assets/css/classified-offer-buy-item.css',
				'deps'    => array(),
				'version' => CLASSIFIED_PLUGIN_VERSION,
				'has_rtl' => false,
			)
		) );
	}

	public function classified_frontend_scripts_data( $data, $handle ) {
		if ( $handle == 'classified-offer-buy-item-scripts' ) {
			return array(
				'classified_ajax_url'         => classified_ajax_url(),
				'classified_make_offer_nonce' => classified_create_nonce( 'classified_make_offer_nonce' ),
				'classified_buy_item_nonce'   => classified_create_nonce( 'classified_buy_item_nonce' ),
			);
		}

		return $data;
	}

	public function classified_make_offer() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( esc_html__( "Sorry! Please login/register to send offers.", "cubewp-classified" ) );
		}
		if ( classified_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'classified_make_offer_nonce' ) ) {
			$post_id         = sanitize_text_field( $_POST['item_id'] );
			$offer           = sanitize_text_field( $_POST['offer'] );
			$current_user_id = get_current_user_id();
			$post_author_id  = classified_get_post_author( $post_id );
			$price           = str_replace( ',', '', $offer );
			if ( empty( $offer ) || ! is_numeric( $price ) || floatval( $price ) < 0 ) {
				wp_send_json_error( esc_html__( "Invalid offer please try again.", "cubewp-classified" ) );
			}
			if ( empty( $post_id ) ) {
				wp_send_json_error( esc_html__( "Something went wrong please try again later.", "cubewp-classified" ) );
			}
			if ( $current_user_id == $post_author_id ) {
				wp_send_json_error( esc_html__( "Sorry! You can't send offer on your own ad.", "cubewp-classified" ) );
			}
			global $wpdb;
			$current_time = current_time( 'Y-m-d H:i:s' );
			$msg_content  = serialize( array( 'classified_offer' => $price ) );
			$conversation = cwpi_conversation_exists( $current_user_id, $post_id );
			if ( isset( $conversation['id'] ) ) {
				$message_Content   = $conversation['msg_content'];
				$message_Content[] = array(
					'msg_content' => $msg_content,
					'msg_sender'  => $current_user_id,
					'msg_to'      => $post_author_id,
					'msg_created' => $current_time,
					'unread'      => 'true'
				);
				$where             = [ 'id' => $conversation['id'] ];
				$wpdb->update( $wpdb->prefix . "cwp_inbox_messages", array(
					'msg_content'   => serialize( $message_Content ),
					'msg_status'    => 'publish',
					'last_reply'    => $current_time,
					'unread'        => 'true',
					'last_reply_by' => $current_user_id
				), $where );
				$msg_id = $conversation['id'];
			} else {
				$message_Content[] = array(
					'msg_content' => $msg_content,
					'msg_sender'  => $current_user_id,
					'msg_to'      => $post_author_id,
					'msg_created' => $current_time,
					'unread'      => 'true'
				);
				$wpdb->insert( $wpdb->prefix . "cwp_inbox_messages", array(
					'msg_post_id'        => $post_id,
					'msg_post_author_id' => $post_author_id,
					'msg_sender'         => $current_user_id,
					'msg_content'        => serialize( $message_Content ),
					'msg_status'         => 'new',
					'msg_created'        => $current_time,
					'last_reply'         => $current_time,
					'unread'             => 'true'
				), array( '%s', '%s', '%s', '%s' ) );
				$msg_id = $wpdb->insert_id;
			}
			if ( isset( $msg_id ) || isset( $conversation['id'] ) ) {
				cubewp_inbox_remove_conversation_delete_request( array( $current_user_id, $post_author_id ), $msg_id );
				// Emails
				$sender_notification = cubewp_get_email_template_by_post_id( $post_id, 'user', 'offer-submitted' );
				if ( $sender_notification ) {
					$sender_email = classified_get_userdata( $current_user_id, 'email' );
					CubeWp_Emails::cubewp_send_email( $sender_email, $sender_notification, $post_author_id, $post_id );
				}
				$receiver_notification = cubewp_get_email_template_by_post_id( $post_id, 'user', 'offer-received' );
				if ( $receiver_notification ) {
					$receiver_email = classified_get_userdata( $post_author_id, 'email' );
					CubeWp_Emails::cubewp_send_email( $receiver_email, $receiver_notification, $current_user_id, $post_id );
				}

				wp_send_json_success( esc_html__( "Your offer has been sent successfully.", "cubewp-classified" ) );
			} else {
				wp_send_json_error( esc_html__( "Something went wrong please try again later.", "cubewp-classified" ) );
			}
		} else {
			wp_send_json_error( esc_html__( "Nonce Security Error", "cubewp-classified" ) );
		}
	}

	public function classified_buy_item() {
		global $woocommerce;
		if ( ! is_user_logged_in() ) {
			wp_send_json( array(
				'type' => 'error',
				'msg'  => esc_html__( "Sorry! Please login/register to buy items.", "cubewp-classified" ),
			) );
		}
		if ( empty( $woocommerce ) ) {
			wp_send_json( array(
				'type' => 'error',
				'msg'  => esc_html__( "Something went wrong please try again later.", "cubewp-classified" ),
			) );
		}
		if ( classified_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'classified_buy_item_nonce' ) ) {
			$post_id         = sanitize_text_field( $_POST['item_id'] );
			$current_user_id = get_current_user_id();
			$post_author_id  = classified_get_post_author( $post_id );
			if ( empty( $post_id ) ) {
				wp_send_json( array(
					'type' => 'error',
					'msg'  => esc_html__( "Something went wrong please try again later.", "cubewp-classified" ),
				) );
			}
			if ( $current_user_id == $post_author_id ) {
				wp_send_json( array(
					'type' => 'error',
					'msg'  => esc_html__( "Sorry! You can't buy your own item.", "cubewp-classified" ),
				) );
			}
			$product_title = sprintf( esc_html__( 'Classified Item Product (%s)', 'cubewp-classified' ), get_the_title( $post_id ) );
			$product_id    = post_exists( $product_title, '', '', 'product', 'wc-hidden' );
			$price         = classified_get_item_price( $post_id );
			if ( empty( $product_id ) ) {
				$post       = array(
					'post_author'  => $post_author_id,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_title'   => $product_title,
					'post_parent'  => '',
					'post_type'    => 'product',
				);
				$product_id = wp_insert_post( $post );
			}
			if ( ! empty( $product_id ) && $product_id > 0 ) {
				update_post_meta( $product_id, '_stock_status', 'instock' );
				update_post_meta( $product_id, '_regular_price', $price );
				update_post_meta( $product_id, '_price', $price );
				update_post_meta( $product_id, '_is_classified_item', true );
				update_post_meta( $product_id, '_classified_item', $post_id );
				update_post_meta( $product_id, '_visibility', 'hidden' );
				wp_set_object_terms( $product_id, array(
					'exclude-from-catalog',
					'exclude-from-search'
				), 'product_visibility' );
				$woocommerce->cart->empty_cart();
				$woocommerce->cart->add_to_cart( $product_id, 1 );
				$checkout_url = wc_get_checkout_url();
				wp_send_json( array(
					'type'        => 'success',
					'msg'         => esc_html__( 'Success! Item successfully added into cart. Redirecting...', 'cubewp-classified' ),
					'redirectURL' => $checkout_url
				) );
			} else {
				wp_send_json( array(
					'type' => 'error',
					'msg'  => esc_html__( "Please try again later.", "cubewp-classified" ),
				) );
			}
		} else {
			wp_send_json( array(
				'type' => 'error',
				'msg'  => esc_html__( "Nonce Security Error", "cubewp-classified" ),
			) );
		}
	}
}

if ( ! function_exists( 'cube_classified_ad_buy_btn' ) ) {
	function cube_classified_ad_buy_btn( $args ) {
		global $post;
		$post_id = $post->ID ?? get_the_ID();

		if ( ! classified_is_item_buyable( $post_id ) ) {
			return false;
		}
		$container_class = $args['container_class'] ?? '';
		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ); ?>">
			<?php echo Classified_Offer_Buy_Item::classified_buy_item_btn( $post_id ); ?>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_classified_ad_offer_btn' ) ) {
	function cube_classified_ad_offer_btn( $args ) {
		global $post;
		$post_id = $post->ID ?? get_the_ID();
		if ( classified_is_item_buyable( $post_id ) || get_post_status( $post_id ) == 'sold' ) {
			return false;
		}
		$container_class = $args['container_class'] ?? '';
		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ); ?>">
			<?php echo Classified_Offer_Buy_Item::classified_offer_item_btn( $post_id ); ?>
        </div>
		<?php

		return ob_get_clean();
	}
}