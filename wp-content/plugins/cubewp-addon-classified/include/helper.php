<?php

defined( 'ABSPATH' ) || exit;



if ( ! function_exists( 'classified_is_review_active' ) ) {

	function classified_is_review_active() {

		return class_exists( 'CubeWp_Reviews_Stats' );

	}

}



if ( ! function_exists( 'classified_is_wallet_active' ) ) {

	function classified_is_wallet_active() {

		return class_exists( 'CubeWp_Wallet_Load' );

	}

}



if ( ! function_exists( 'classified_is_booster_active' ) ) {

	function classified_is_booster_active() {

		return class_exists( 'CubeWp_Booster_Load' );

	}

}



if ( ! function_exists( 'classified_is_inbox_active' ) ) {

	function classified_is_inbox_active() {

		return class_exists( 'CubeWp_Inbox_Load' );

	}

}



if ( ! function_exists( 'classified_is_payment_active' ) ) {

	function classified_is_payment_active() {

		return class_exists( 'CubeWp_Payments_Load' );

	}

}



if ( ! function_exists( 'classified_handle_custom_query_var' ) ) {

	function classified_handle_custom_query_var( $query, $query_vars ) {

		if ( ! empty( $query_vars['_classified_author'] ) ) {

			$query['meta_query'][] = array(

				'key'   => '_classified_author',

				'value' => (string) esc_attr( $query_vars['_classified_author'] ),

			);

		}



		return $query;

	}



	add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'classified_handle_custom_query_var', 10, 2 );

}



if ( ! function_exists( 'classified_loop_builder_dummy' ) ) {

	function classified_loop_builder_dummy( $post_type, $style ) {

		global $classified_post_types;

		$classified_post_types = is_array( $classified_post_types ) ? $classified_post_types : array();

		if ( in_array( $post_type, $classified_post_types ) ) {

			$loop_layout_html = '<div class="classified-item">

	<div class="classified-item-media-and-tags">

		<a href="[loop_post_link]" class="stretched-link"></a>

		<div class="classified-item-media">

			<img decoding="async" loading="lazy" width="100%" height="100%" src="[loop_featured_image]" alt="[loop_the_title]">

		</div>

		<div class="classified-item-tags">

			<a href="[loop_' . $post_type . '_category_tax_link]" class="classified-item-tag">[loop_' . $post_type . '_category]</a>

		</div>

	</div>

	<div class="classified-item-content">

		<div class="classified-item-content-top">

			<p class="classified-item-price">$[loop_classified_price]</p>

			<div class="d-flex">

				<p class="classified-item-condition">[loop_classified_ad_condition]</p>

				[loop_post_save]

			</div>

		</div>

		<div class="classified-item-content-details">

			<a href="[loop_post_link]">

				<h5 class="classified-item-title">[loop_the_title]</h5>

			</a>

		</div>

		<div class="classified-item-content-bottom">

			<p class="classified-item-content-term">

				<a href="[loop_locations_tax_link]">

					<i class="fa-solid fa-location-dot" aria-hidden="true"></i>

					<text>[loop_locations]</text>

				</a>

			</p>

			<p class="classified-item-timer">

				<i class="fa-regular fa-clock" aria-hidden="true"></i>

                [loop_the_date]

			</p>

		</div>

		<a href="[loop_post_link]" class="stretched-link"></a>

	</div>

</div>';

			?>

			<div class="cubewp-builder-section cubewp-expand-container">

				<div class="cubewp-builder-section-header">

					<h3><?php esc_html_e( 'Dummy Loop Layout HTML', 'cubewp-frontend' ); ?></h3>

					<div class="cubewp-builder-section-actions">

						<span

							class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>

					</div>

				</div>

				<div class="cubewp-builder-section-fields cubewp-expand-target">

					<div class="cubewp-builder-group-widget">

						<div class="cubewp-loop-builder-editor-container">

							<input type="hidden" class="cubewp-loop-builder-editor-value"

							       value='<?php echo cubewp_core_data( stripslashes( $loop_layout_html ) ); ?>'>

							<div class="cubewp-loop-builder-editor"

							     id="<?php echo esc_attr( 'cubewp-loop-builder-' . $post_type . '-' . $style . '-editor-dummy' ); ?>"></div>

						</div>

					</div>

				</div>

			</div>

			<?php

		}

	}



	add_action( 'cubewp/loop/builder/area', 'classified_loop_builder_dummy', 10, 2 );

}



if ( ! function_exists( 'classified_rand' ) ) {

	function classified_rand( $length = 10, $lower_only = true, $include_num = false ) {

		$characters = 'abcdefghijklmnopqrstuvwxyz';

		if ( ! $lower_only ) {

			$characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		}

		if ( $include_num ) {

			$characters .= '0123456789';

		}

		$characters_count = strlen( $characters );

		$rand_str         = '';

		for ( $i = 0; $i < $length; $i ++ ) {

			$rand_str .= $characters[ rand( 0, $characters_count - 1 ) ];

		}



		return $rand_str;

	}

}



if ( ! function_exists( 'classified_create_nonce' ) ) {

	function classified_create_nonce( $action ) {

		return wp_create_nonce( $action );

	}

}



if ( ! function_exists( 'classified_verify_nonce' ) ) {

	function classified_verify_nonce( $nonce, $action ) {

		return wp_verify_nonce( $nonce, $action );

	}

}



if ( ! function_exists( 'classified_ajax_url' ) ) {

	function classified_ajax_url() {

		return admin_url( 'admin-ajax.php', 'relative' );

	}

}



if ( ! function_exists( 'classified_is_offer' ) ) {

	function classified_is_offer( $message ) {

		return is_serialized( $message );

	}

}



if ( ! function_exists( 'classified_get_offer_from_message' ) ) {

	function classified_get_offer_from_message( $message ) {

		$message = maybe_unserialize( $message );



		return $message['classified_offer'] ?? false;

	}

}



if ( ! function_exists( 'classified_personalized_terms_search' ) ) {

	function classified_personalized_terms_search( $search, $query ) {

		global $wpdb;

		if ( ! empty( $query->query_vars['s'] ) ) {

			$search_strings = explode( '|', $query->query_vars['s'] );

			// Build the search query

			$search_query = '';

			foreach ( $search_strings as $search_string ) {

				$search_query .= " OR ({$wpdb->posts}.post_title LIKE '%{$search_string}%' OR {$wpdb->posts}.post_content LIKE '%{$search_string}%')";

			}

			$search_query = trim( $search_query, ' OR' );

			$tax_relation = classified_get_setting( 'classified_personalize_relation' );

			$search       = " {$tax_relation} ({$search_query}) ";

		}



		return $search;

	}

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



if ( ! function_exists( 'classified_get_icon_output' ) ) {

	function classified_get_icon_output( $output, $attr = '' ) {

		if ( ! empty( $output ) ) {

            if ( is_numeric( $output ) ) {

                $attachment = wp_get_attachment_url( $output );

                if ( ! empty( $attachment ) ) {

                    $output = $attachment;

                }

            }

			if ( filter_var( $output, FILTER_VALIDATE_URL ) !== false ) {

				return '<img src="' . esc_url( $output ) . '" ' . $attr . ' alt="' . esc_html__( 'Classified Icon', 'cubewp-classified' ) . '" loading="lazy" width="100%" height="100%">';

			} else if ( $output != strip_tags( $output ) ) {

				return cubewp_core_data( $output );

			} else {

				return '<i class="' . esc_attr( $output ) . '" ' . $attr . ' aria-hidden="true"></i>';

			}

		}



		return '';

	}

}



if ( ! function_exists( 'classified_inbox_chat' ) ) {

	function classified_inbox_chat( $output, $msg ) {

		$msg_content = $msg['msg_content'] ?? '';

		if ( classified_is_offer( $msg_content ) ) {

			$current_user_id = get_current_user_id();

			$msg_created     = isset( $msg['msg_created'] ) ? $msg['msg_created'] : 0;

			$msg_sender      = isset( $msg['msg_sender'] ) ? $msg['msg_sender'] : 0;

			$msg_to          = isset( $msg['msg_to'] ) ? $msg['msg_to'] : 0;

			$msg_sender_info = get_userdata( $msg_sender );

			$msg_to_info     = get_userdata( $msg_to );

			if ( $current_user_id == $msg_sender ) {

				$username   = esc_html__( "You", "cubewp-inbox" );

				$class      = 'classified-inbox-offer cwp-inbox-conversation-chat cwp-inbox-conversation-sent-chat';

				$offer_text = esc_html__( "Your Offer", "cubewp-classified" );

			} else {

				$username   = get_userdata( $msg_sender_info->ID )->display_name;

				$class      = 'classified-inbox-offer cwp-inbox-conversation-chat';

				$offer_text = esc_html__( "Offer", "cubewp-classified" );

			}

			$profile_image = cwpi_get_user_profile_image( $msg_sender_info->ID );

			$msg_content   = classified_get_offer_from_message( $msg_content );

			ob_start();

			?>

			<div class="<?php echo esc_attr( $class ); ?>">

				<img loading="lazy" width="100%" height="100%" src="<?php echo esc_url( $profile_image ); ?>"

				     alt="<?php echo esc_html( $username ); ?>">

				<p class="h6">

					<b><?php echo esc_html( $offer_text ); ?></b>

					<?php echo esc_html( classified_build_price( $msg_content ) ); ?>

				</p>

				<span><?php echo date_i18n( get_option( 'time_format' ), strtotime( $msg_created ) ); ?></span>

			</div>

			<?php

			return ob_get_clean();

		} else {

			return $output;

		}

	}



	add_filter( "cubewp/inbox/conversation/message-ui", "classified_inbox_chat", 11, 3 );

}



if ( ! function_exists( 'classified_inbox_last_reply' ) ) {

	function classified_inbox_last_reply( $last_reply ) {

		if ( isset( $last_reply['msg_content'] ) && ! empty( $last_reply['msg_content'] ) && classified_get_offer_from_message( $last_reply['msg_content'] ) ) {

			$current_user_id = get_current_user_id();

			$msg_sender      = $last_reply['msg_sender'] ?? 0;

			$msg_content     = classified_get_offer_from_message( $last_reply['msg_content'] );

			if ( $current_user_id == $msg_sender ) {

				$offer_text = esc_html__( "Your Offer", "cubewp-classified" );

			} else {

				$offer_text = esc_html__( "Offer", "cubewp-classified" );

			}

			$last_reply['msg_content'] = $offer_text . '&nbsp;' . classified_build_price( $msg_content );

		}



		return $last_reply;

	}



	add_filter( 'cubewp/inbox/conversation/last-reply', 'classified_inbox_last_reply', 11 );

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



if ( ! function_exists( 'classified_get_item_expiry' ) ) {

	function classified_get_item_expiry( $post_id ) {

		$paid_submission = classified_get_setting( 'paid_submission' );

		$plan_id         = get_post_meta( $post_id, 'plan_id', true );

		$plan_price      = get_post_meta( $plan_id, 'plan_price', true );

		$payment_status  = get_post_meta( $post_id, 'payment_status', true );

		if ( $payment_status == 'pending' && $paid_submission == 'yes' && $plan_price > 0 ) {

			$payment_status = apply_filters( 'cubewp_check_post_payment_status', '', $plan_id, $post_id );

		}

		$return = 'unlimited';

		if ( $payment_status == 'pending' ) {

			$return = 'pending';

		} else if ( $payment_status == 'paid' ) {

			$item_expiry = get_post_meta( $post_id, 'post_expired', true );

			$time_now    = strtotime( "now" );

			if ( $item_expiry > $time_now ) {

				$return = $item_expiry;

			} else {

				$return = 'expired';

			}

		}



		return $return;

	}

}



if ( ! function_exists( 'classified_dashboard_items_actions' ) ) {

	function classified_dashboard_items_actions( $post_id ) {

		$html            = null;

		$post_type       = get_post_type( $post_id );

		$post_status     = get_post_status( $post_id );

		$ad_submission   = classified_get_setting( 'submit_edit_page', 'page_url', $post_type );

		$edit_url        = add_query_arg( 'pid', $post_id, $ad_submission );

		$paid_submission = classified_get_setting( 'paid_submission' );

		$plan_id         = get_post_meta( $post_id, 'plan_id', true );

		$plan_price      = get_post_meta( $plan_id, 'plan_price', true );

		$payment_status  = get_post_meta( $post_id, 'payment_status', true );

		$button          = '';

		if ( $payment_status == 'pending' && $paid_submission == 'yes' && $plan_price > 0 ) {

			$payment_status = apply_filters( 'cubewp_check_post_payment_status', '', $plan_id, $post_id );

		}

		if ( $post_status == 'sold' ) {

			$button = /** @lang HTML */

				'<button class="classified-filled-btn" disabled title="' . esc_html__( "You Marked This AD As Sold", "cubewp-classified" ) . '">

			    ' . esc_html__( "Sold", "cubewp-classified" ) . '

            </button>';

		} else if ( $post_status == 'pending' || $post_status == 'draft' ) {

			if ( $payment_status == 'pending' ) {

				$button = /** @lang HTML */

					'<button class="classified-not-filled-btn cwp-pay-publish-btn" data-pid="' . absint( $post_id ) . '">

                    ' . esc_html__( "Pay Now", "cubewp-classified" ) . '

                </button>';

			} else {

				if ( classified_is_booster_active() ) {

					$button = /** @lang HTML */

						'<button class="classified-not-filled-btn" disabled title="' . esc_html__( "Pending Admin Approval", "cubewp-classified" ) . '">

                        ' . esc_html__( "Boost", "cubewp-classified" ) . '

                    </button>';

				} else {

					$button = /** @lang HTML */

						'<button class="classified-not-filled-btn position-relative">

                        <a href="' . esc_url( get_permalink( $post_id ) ) . '" class="stretched-link"></a>

                        ' . esc_html__( "Preview", "cubewp-classified" ) . '

                    </button>';

				}

			}

		} else if ( $post_status == 'publish' ) {

			if ( classified_is_booster_active() ) {

				if ( is_boosted( $post_id ) ) {

					$button = /** @lang HTML */

						'<button class="classified-filled-btn position-relative">

	                    <a href="' . esc_url( get_permalink( $post_id ) ) . '" class="stretched-link"></a>

	                    ' . esc_html__( "View", "cubewp-classified" ) . '

	                </button>';

				} else {

					CubeWp_Enqueue::enqueue_style( 'frontend-fields' );

					CubeWp_Enqueue::enqueue_script( 'cwp-frontend-fields' );

					CubeWp_Enqueue::enqueue_script( 'cwp-form-validation' );

					CubeWp_Enqueue::enqueue_script( 'cwp-submit-post' );

					$ppc_price = CubeWp_Booster_Fields::booster_type_price_per_click( get_post_type( $post_id ) );

					$ppd_price = CubeWp_Booster_Fields::booster_type_price_per_day( get_post_type( $post_id ) );

					$ppc_price = sprintf( esc_html__( 'Pay Per Click Price is $%.2f/per click', 'cubewp-classified' ), $ppc_price );

					$ppd_price = sprintf( esc_html__( 'Pay Per Day Price is $%.2f/per day', 'cubewp-classified' ), $ppd_price );

					$button    = /** @lang HTML */

						'<button class="classified-filled-btn classified-boost-item" data-ppc-desc="' . esc_html( $ppc_price ) . '" data-ppd-desc="' . esc_html( $ppd_price ) . '" data-item-id="' . absint( $post_id ) . '">

	                    ' . esc_html__( "Boost", "cubewp-classified" ) . '

	                </button>';

				}

			} else {

				$button = /** @lang HTML */

					'<button class="classified-filled-btn position-relative">

                    <a href="' . esc_url( get_permalink( $post_id ) ) . '" class="stretched-link"></a>

                    ' . esc_html__( "View", "cubewp-classified" ) . '

                </button>';

			}

		}



		$html .= /** @lang HTML */

			'<div class="btn-group">

            ' . $button . '

            <div class="d-flex justify-content-center align-items-center classified-dropdown classified-dashboard-item-options" type="button">

                <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>

                <div class="classified-dropdown-items drop-left have-indicator">

                    <a href="' . get_permalink( $post_id ) . '" target="_blank">

                        <p class="classified-dropdown-item">

                            <i class="fa-solid fa-eye" aria-hidden="true"></i>

                            ' . esc_html__( "View", "cubewp-classified" ) . '

                        </p>

                    </a>';

		if ( $post_status != 'sold' ) {

			$html .= /** @lang HTML */

				'<a href="' . esc_url( $edit_url ) . '">

                            <p class="classified-dropdown-item">

                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>

                                ' . esc_html__( "Edit", "cubewp-classified" ) . '

                            </p>

                        </a>';

		}

		if ( $post_status != 'sold' && $post_status != 'draft' && $post_status != 'pending' ) {

			$html .= /** @lang HTML */

				'<a href="">

                            <p class="classified-dropdown-item classified-mark-item-sold" data-item-id="' . esc_attr__( $post_id ) . '">

                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>

                                ' . esc_html__( "Sold", "cubewp-classified" ) . '

                            </p>

                        </a>';

		}

		$html .= /** @lang HTML */

			'<a href="" class="cwp-post-action-delete" data-pid="' . esc_attr( $post_id ) . '">

                        <p class="classified-dropdown-item">

                            <i class="fa-solid fa-trash" aria-hidden="true"></i>

                            ' . esc_html__( "Delete", "cubewp-classified" ) . '

                        </p>

                    </a>

                    ' . apply_filters( 'classified_dashboard_item_list_actions', '', $post_id ) . '

                </div>

            </div>

        </div>';



		return $html;

	}

}



if ( ! function_exists( 'classified_get_template_part' ) ) {

	function classified_get_template_part( $slug, $name = null ) {



		$templates = array();

		if ( isset( $name ) ) {

			$templates[] = "{$slug}-{$name}.php";

		}



		$templates[] = "{$slug}.php";



		classified_get_template_path( $templates, true, false );

	}

}



if ( ! function_exists( 'classified_get_template_path' ) ) {

	function classified_get_template_path( $template_names, $load = false, $require_once = true ) {

		$located = '';

		foreach ( (array) $template_names as $template_name ) {

			if ( ! $template_name ) {

				continue;

			}



			if ( file_exists( CLASSIFIED_PLUGIN_PATH . $template_name ) ) {

				$located = CLASSIFIED_PLUGIN_PATH . $template_name;

				break;

			}

		}



		if ( $load && '' != $located ) {

			load_template( $located, $require_once );

		}



		return $located;

	}

}



if ( ! function_exists( 'classified_get_rating_stars' ) ) {

	function classified_get_rating_stars( $value = 0, $style = 1 ) {

		$html = '';

		if ( $style == 1 ) {

			$html .= /** @lang HTML */

				'<div class="classified-rating-stars">

                <input type="radio" name="classified-dashboard-rating" id="classified-dashboard-rating-5" value="5" ' . checked( $value, "5", false ) . '>

                <label for="classified-dashboard-rating-5"><i class="fa-regular fa-star" aria-hidden="true"></i></label>

                <input type="radio" name="classified-dashboard-rating" id="classified-dashboard-rating-4" value="4" ' . checked( $value, "4", false ) . '>

                <label for="classified-dashboard-rating-4"><i class="fa-regular fa-star" aria-hidden="true"></i></label>

                <input type="radio" name="classified-dashboard-rating" id="classified-dashboard-rating-3" value="3" ' . checked( $value, "3", false ) . '>

                <label for="classified-dashboard-rating-3"><i class="fa-regular fa-star" aria-hidden="true"></i></label>

                <input type="radio" name="classified-dashboard-rating" id="classified-dashboard-rating-2" value="2" ' . checked( $value, "2", false ) . '>

                <label for="classified-dashboard-rating-2"><i class="fa-regular fa-star" aria-hidden="true"></i></label>

                <input type="radio" name="classified-dashboard-rating" id="classified-dashboard-rating-1" value="1" ' . checked( $value, "1", false ) . '>

                <label for="classified-dashboard-rating-1"><i class="fa-regular fa-star" aria-hidden="true"></i></label>

            </div>';

		}



		return $html;

	}

}



if ( ! function_exists( 'classified_active_helper' ) ) {

	function classified_active_helper( $helper, $current, $attr, $value, $echo ) {

		$return = '';

		if ( (string) $helper === (string) $current ) {

			if ( ! empty( $attr ) ) {

				$return = ' ' . $attr . '="' . $value . '" ';

			} else {

				$return = ' ' . $value . ' ';

			}

		}

		if ( $echo ) {

			echo $return;

		}



		return $return;

	}

}



if ( ! function_exists( 'classified_add_user_role' ) ) {

	function classified_add_user_role() {

		add_role( 'classified_author', 'Classified Author', array(

			'edit_published_posts'   => true,

			'upload_files'           => true,

			'delete_published_posts' => true,

			'edit_posts'             => true,

			'delete_posts'           => true,

			'read_private_posts'     => true,

			'read'                   => true,

		) );

	}



	add_action( 'init', 'classified_add_user_role', 8 );

}



if ( ! function_exists( 'classified_settings_on_item_submission' ) ) {

	function classified_settings_on_item_submission( $post_id ) {

        global $classified_post_types;

        if ( ! in_array( get_post_type( $post_id ), $classified_post_types ) ) {

            return $post_id;

        }

		// Check the logged-in user has permission to edit this post

		$post_author = classified_get_post_author( $post_id, false );

		if ( get_current_user_id() != $post_author->ID ) {

			return $post_id;

		}



		if ( ! in_array( 'classified_author', $post_author->roles ) ) {

			$user_role = new WP_User( $post_author->ID );

			$user_role->add_role( 'classified_author' );

		}



		// Recalculating Post Completion Status

        global $cubewp_frontend;

        if ( ! empty( $cubewp_frontend ) ) {

	        $post_metas  = $cubewp_frontend->post_metas( $post_id );

	        $metas_count = count( $post_metas );

	        $percentage  = 100 / $metas_count;

	        $completed   = 100;

	        if ( ! empty( $post_metas ) && is_array( $post_metas ) ) {

		        foreach ( $post_metas as $post_meta ) {

			        $meta_value = get_post_meta( $post_id, $post_meta['meta_key'], true );

			        if ( empty( $meta_value ) ) {

				        $completed = $completed - $percentage;

			        }

		        }

	        }

	        update_post_meta( $post_id, 'classified_item_completion', $completed );

        }



		return $post_id;

	}



	add_action( 'save_post', 'classified_settings_on_item_submission', 11 );

}



if ( ! function_exists( 'classified_custom_dashboard_content_types' ) ) {

	function classified_custom_dashboard_content_types( $types ) {

		$types['classified_main_dashboard'] = esc_html__( "Classified -- Dashboard Main", "cubewp-classified" );

		$types['classified_user_profile']   = esc_html__( "Classified -- User Profile", "cubewp-classified" );

		if ( classified_is_payment_active() ) {

			$types['classified_plans']             = esc_html__( "Classified -- Pricing And Packages", "cubewp-classified" );

			$types['classified_sales']             = esc_html__( "Classified -- Sales", "cubewp-classified" );

			$types['classified_purchased_history'] = esc_html__( "Classified -- Purchased History", "cubewp-classified" );

		}

		if ( classified_is_inbox_active() ) {

			$types['classified_inbox'] = esc_html__( "Classified -- Inbox", "cubewp-classified" );

		}



		return $types;

	}



	add_filter( 'user/dashboard/content/types', 'classified_custom_dashboard_content_types', 11 );

}



if ( ! function_exists( 'classified_register_post_status' ) ) {

	function classified_register_post_status() {

		$args = array(

			'label'                     => _x( 'Sold', 'cubewp-classified' ),

			'label_count'               => _n_noop( 'Sold <span class="count">(%s)</span>', 'Sold <span class="count">(%s)</span>' ),

			'public'                    => false,

			'exclude_from_search'       => true,

			'show_in_admin_all_list'    => true,

			'show_in_admin_status_list' => true

		);



		register_post_status( 'sold', $args );

	}



	add_action( 'init', 'classified_register_post_status' );

}



if ( ! function_exists( 'classified_add_post_status_into_dropdown' ) ) {

	function classified_add_post_status_into_dropdown() {

		global $post, $classified_post_types;

		if ( ! in_array( $post->post_type, $classified_post_types ) ) {

			return false;

		}

		$status = ( $post->post_status == 'sold' ) ? "jQuery('#post-status-display').text('Sold');jQuery('select[name=\"post_status\"]').val('sold');" : '';

		?>

		<script>

            jQuery(document).ready(function () {

                jQuery('select[name=\"post_status\"]').append('<option value=\"sold\">Sold</option>');

				<?php echo wp_kses_post( $status ); ?>

            });

		</script>

		<?php



		return true;

	}



	add_action( 'post_submitbox_misc_actions', 'classified_add_post_status_into_dropdown' );

}



if ( ! function_exists( 'classified_add_post_status_into_quick_edit' ) ) {

	function classified_add_post_status_into_quick_edit() {

		global $post, $classified_post_types;

		if ( isset( $post->post_type ) && ! in_array( $post->post_type, $classified_post_types ) ) {

			return false;

		}

		?>

		<script>

            jQuery(document).ready(function () {

                jQuery('select[name=\"_status\"]').append('<option value=\"sold\">Sold</option>');

            });

		</script>

		<?php



		return true;

	}



	add_action( 'admin_footer-edit.php', 'classified_add_post_status_into_quick_edit' );

}



if ( ! function_exists( 'classified_show_post_status_with_title' ) ) {

	function classified_show_post_status_with_title( $states ) {

		global $post;

		$arg = get_query_var( 'post_status' );

		if ( $arg != 'sold' ) {

			if ( ! empty( $post->post_status ) && $post->post_status == 'sold' ) {

				?>

				<script>

                    jQuery(document).ready(function () {

                        jQuery('#post-status-display').text('Sold');

                    });

				</script>

				<?php



				return array( 'Sold' );

			}

		}



		return $states;

	}



	add_filter( 'display_post_states', 'classified_show_post_status_with_title' );

}



if ( ! function_exists( 'classified_build_price' ) ) {

	function classified_build_price( $price = '', $only_currency = false, $show_currency = true, $currency_position = '' ) {

		if ( class_exists( 'woocommerce' ) ) {

			if ( $only_currency ) {

				return get_woocommerce_currency_symbol();

			}

			if ( ! $show_currency ) {

				$decimals           = wc_get_price_decimals();

				$decimal_separator  = wc_get_price_decimal_separator();

				$thousand_separator = wc_get_price_thousand_separator();



				return number_format( $price, $decimals, $decimal_separator, $thousand_separator );

			}



			return stripslashes( strip_tags( wc_price( $price ) ) );

		}

		$currency          = esc_html__( '$', 'cubewp-classified' );

		$price             = round( (float) $price, 2 );

		$currency_position = ! empty( $currency_position ) ? $currency_position : 'left';

		if ( $only_currency ) {

			$return = $currency;

		} else {

			if ( ! $show_currency ) {

				$return = $price;

			} else {

				if ( ! empty( $price ) ) {

					if ( $currency_position == 'left' ) {

						$return = $currency . $price;

					} else {

						$return = $price . $currency;

					}

				} else {

					$return = $currency;

				}

			}

		}



		return apply_filters( 'classified_build_price', $return, $only_currency, $show_currency, $currency_position );

	}

}



if ( ! function_exists( 'classified_get_custom_post_types' ) ) {

	function classified_get_custom_post_types() {

		return Classified_Plugin_Setup::$classified_custom_post_types;

	}

}



if ( ! function_exists( 'classified_get_cubewp_groups' ) ) {

	function classified_get_cubewp_groups( $key = 'ID', $value = 'post_title' ) {

		$args           = array(

			'numberposts' => - 1,

			'post_type'   => 'cwp_form_fields',

			'post_status' => array( 'private', 'publish' ),

			'meta_query'  => array(

				array(

					'key'     => '_cwp_group_visibility',

					'compare' => 'EXISTS',

				)

			)

		);

		$field_groups   = get_posts( $args );

		$groups_options = array();

		if ( ! empty( $field_groups ) && ! is_wp_error( $field_groups ) ) {

			foreach ( $field_groups as $group ) {

				$groups_options[ $group->$key ] = $group->$value;

			}

		}



		return $groups_options;

	}

}



if ( ! function_exists( 'classified_get_choices_custom_fields' ) ) {

	function classified_get_choices_custom_fields() {

		$return            = array();

		$choices_type      = array(

			'dropdown',

			'checkbox',

			'radio',

		);

		$all_custom_fields = CWP()->get_custom_fields( 'post_types' );

		if ( ! empty( $all_custom_fields ) && is_array( $all_custom_fields ) ) {

			foreach ( $all_custom_fields as $custom_field ) {

				$type = $custom_field['type'] ?? '';

				if ( in_array( $type, $choices_type ) ) {

					$name            = $custom_field['name'] ?? '';

					$label           = $custom_field['label'] ?? '';

					$return[ $name ] = $label;

				}

			}

		}



		return $return;

	}

}



if ( ! function_exists( 'classified_get_custom_fields_by_post_type' ) ) {

	function classified_get_custom_fields_by_post_type( $post_type ) {

		$return = array();

		$groups = cwp_get_groups_by_post_type( $post_type );

		if ( ! empty( $groups ) && count( $groups ) > 0 ) {

			foreach ( $groups as $group ) {

				$fields = cwp_get_fields_by_group_id( $group );

				if ( ! empty( $fields ) && count( $fields ) > 0 ) {

					foreach ( $fields as $field ) {

						$field           = get_field_options( $field );

						$name            = $field['name'] ?? '';

						$label           = $field['label'] ?? '';

						$return[ $name ] = $label;

					}

				}

			}

		}



		return $return;

	}

}



if ( ! function_exists( 'classified_is_user_email_verified' ) ) {

	function classified_is_user_email_verified( $user_id = 0 ) {

		if ( ! $user_id ) {

			$user_id = get_current_user_id();

		}

		$is_verified = get_user_meta( $user_id, 'is_email_verified', true );



		return $is_verified == 'yes';

	}

}

if ( ! function_exists( 'classified_get_userdata' ) ) {
	function classified_get_userdata( $user_id = 0, $field = '' ) {
		$return    = false;
		$user_data = get_userdata( $user_id );
		if ( ! isset( $user_data->ID ) ) {
			return $return;
		}
		$user_id = $user_data->ID;
		if ( $field == 'avatar' ) {
			$return = get_user_meta( $user_id, 'classified_avatar', true );
			if ( ! empty( $return ) ) {
				if ( is_numeric( $return ) ) {
					$return = wp_get_attachment_url( $return );
				}
			} else {
				$return = get_avatar_url( $user_id );
			}
		} elseif ( $field == 'name' ) {
			$first_name = get_user_meta( $user_id, 'first_name', true );
			$last_name  = get_user_meta( $user_id, 'last_name', true );
			if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
				$return = $first_name . ' ' . $last_name;
			} else {
				$return = $user_data->user_nicename;
			}
		} elseif ( $field == 'short_name' ) {
			$first_name = get_user_meta( $user_id, 'first_name', true );
			$last_name  = get_user_meta( $user_id, 'last_name', true );
			if ( ! empty( $first_name ) ) {
				$return = $first_name;
			} elseif ( ! empty( $last_name ) ) {
				$return = $last_name;
			} else {
				$return = $user_data->user_login;
			}
		} elseif ( $field == 'rating' ) {
			if ( classified_is_review_active() ) {
				$return = CubeWp_Reviews_Stats::get_current_post_rating( 'user', $user_id );
			}
		} elseif ( $field == 'profile_link' ) {
			$return = get_author_posts_url( $user_id );
		} elseif ( $field == 'website' ) {
			$return = $user_data->user_url;
		} elseif ( $field == 'phone' ) {
			$phone  = get_user_meta( $user_id, 'phone', true );
			$return = $phone;
		} elseif ( $field == 'joined' ) {
			$user_registered = strtotime( $user_data->user_registered );
			$return          = date_i18n( get_option( "date_format" ), $user_registered );
		} elseif ( $field == 'join_period' ) {
			$user_registered      = strtotime( $user_data->user_registered );
			$today                = strtotime( 'now' );
			$user_registered_diff = classified_get_difference_between_timestamps( $user_registered, $today, '%Y' );
			$user_registered_unit_p = esc_html__( "Years", "classified-pro" );
			$user_registered_unit_s = esc_html__( "Year", "classified-pro" );

			if ( is_object( $user_registered_diff ) || is_array( $user_registered_diff ) || $user_registered_diff == 0 ) {
				$user_registered_diff = classified_get_difference_between_timestamps( $user_registered, $today, '%m' );
				$user_registered_unit_p = esc_html__( "Months", "classified-pro" );
				$user_registered_unit_s = esc_html__( "Month", "classified-pro" );
			}
			if ( is_object( $user_registered_diff ) || is_array( $user_registered_diff ) || $user_registered_diff == 0 ) {
				$user_registered_diff = classified_get_difference_between_timestamps( $user_registered, $today, '%d' );
				$user_registered_unit_p = esc_html__( "Days", "classified-pro" );
				$user_registered_unit_s = esc_html__( "Day", "classified-pro" );
			}
			$user_registered_unit = $user_registered_unit_p;
			if ( $user_registered_diff == 1 ) {
				$user_registered_unit = $user_registered_unit_s;
			}
			if ( $user_registered_diff > 0 ) {
				$return = $user_registered_diff . ' ' . $user_registered_unit;
			} else {
				$return = esc_html__( 'Today', 'cubewp-classified' );
			}
		} elseif ( $field == 'inbox_response' || $field == 'inbox_short_response' ) {
			if ( classified_is_inbox_active() ) {
				global $wpdb;
				$response_rate = 100;
				$have_res_rate = false;
				$response_time = [];
				$conversations = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cwp_inbox_messages WHERE msg_sender!={$user_id} OR msg_post_author_id={$user_id} LIMIT 5", ARRAY_A );
				if ( ! empty( $conversations ) && is_array( $conversations ) ) {
					$conversations_count = count( $conversations );
					$percent_age         = $response_rate / $conversations_count;
					foreach ( $conversations as $conversation ) {
						$chats = isset( $conversation['msg_content'] ) && ! empty( $conversation['msg_content'] ) ? maybe_unserialize( $conversation['msg_content'] ) : [];
						if ( ! empty( $chats ) && is_array( $chats ) ) {
							$msg_created = '';
							$msg_replied = '';
							foreach ( $chats as $chat ) {
								$msg_to = $chat['msg_to'] ?? 0;
								if ( $msg_to != $user_id ) {
									$msg_replied = $chat['msg_created'] ?? 0;
									break;
								} else {
									$msg_created = ! empty( $msg_created ) ? $msg_created : $chat['msg_created'] ?? 0;
								}
							}
							if ( ! empty( $msg_created ) && ! empty( $msg_replied ) ) {
								$start           = strtotime( $msg_created );
								$end             = strtotime( $msg_replied );
								$diff            = $end - $start;
								$response_time[] = $diff;
							} else {
								$response_rate = $response_rate - $percent_age;
							}
							$have_res_rate = true;
						}
					}
				}
				$response_time = array_filter( $response_time );
				if ( count( $response_time ) ) {
					$response_time = array_sum( $response_time ) / count( $response_time );
					$response_time = $response_time / 3600;
					if ( $field == 'inbox_short_response' ) {
						$response_time = round( max( $response_time, 1 ), 0 );
						if ( $response_time < 24 ) {
							$response_time = $response_time . esc_html__( 'hr', 'cubewp-classified' );
						} elseif ( $response_time >= 24 && $response_time < 48 ) {
							$response_time = esc_html__( '1day', 'cubewp-classified' );
						} elseif ( $response_time >= 48 && $response_time < 168 ) {
							$response_time = esc_html__( '1Week', 'cubewp-classified' );
						} else {
							$response_time = sprintf( esc_html__( '1Mth', 'cubewp-classified' ), $response_time );
						}
					} else {
						if ( $response_time < 1 ) {
							$response_time = esc_html__( 'Few Minutes', 'cubewp-classified' );
						} elseif ( $response_time > 24 && $response_time < 48 ) {
							$response_time = esc_html__( 'Few Hours', 'cubewp-classified' );
						} elseif ( $response_time >= 48 && $response_time < 168 ) {
							$response_time = esc_html__( 'Few Days', 'cubewp-classified' );
						} elseif ( $response_time >= 168 ) {
							$response_time = esc_html__( 'Few Week', 'cubewp-classified' );
						} else {
							$response_time = sprintf( esc_html__( 'Few Months', 'cubewp-classified' ), $response_time );
						}
					}
				} else {
					$response_time = esc_html__( 'N/A', 'cubewp-classified' );
				}
				if ( ! $have_res_rate ) {
					$response_rate = esc_html__( 'N/A', 'cubewp-classified' );
				} else {
					$response_rate = round( $response_rate );
					$response_rate .= '%';
				}
				$return = [
					'response_rate' => $response_rate,
					'response_time' => $response_time,
				];
			} else {
				$return = [
					'response_rate' => esc_html__( 'N/A', 'cubewp-classified' ),
					'response_time' => esc_html__( 'N/A', 'cubewp-classified' ),
				];
			}
		} elseif ( $field == 'country' ) {
			$country       = get_user_meta( $user_id, 'country', true );
			$field_options = get_user_field_options( 'country' );
			if ( isset( $field_options['type'] ) && ( $field_options['type'] == 'dropdown' || $field_options['type'] == 'checkbox' || $field_options['type'] == 'radio' ) ) {
				$field_options['value'] = $country;
				$country                = cwp_handle_data_format( $field_options );
				$country                = render_multi_value( 'country', $country, 'user' );
			}
			$return = $country;
		} elseif ( $field == 'ads_count' ) {
			global $classified_post_types;
			$return = count_user_posts( $user_id, $classified_post_types );
		} elseif ( $field == 'facebook' ) {
			$facebook = get_user_meta( $user_id, 'facebook', true );
			$return   = $facebook;
		} elseif ( $field == 'instagram' ) {
			$instagram = get_user_meta( $user_id, 'instagram', true );
			$return    = $instagram;
		} elseif ( $field == 'twitter' ) {
			$twitter = get_user_meta( $user_id, 'twitter', true );
			$return  = $twitter;
		} elseif ( $field == 'linkedin' ) {
			$linkedin = get_user_meta( $user_id, 'linkedin', true );
			$return   = $linkedin;
		} elseif ( $field == 'youtube' ) {
			$youtube = get_user_meta( $user_id, 'youtube', true );
			$return  = $youtube;
		} elseif ( $field == 'role' ) {
			$roles  = $user_data->roles;
			$role   = is_array( $roles ) && count( $roles ) > 0 ? $roles[0] : '';
			$return = $role;
		} elseif ( $field == 'roles' ) {
			$roles  = $user_data->roles;
			$return = $roles;
		} else {
			if ( isset( $user_data->$field ) ) {
				$return = $user_data->$field;
			}
		}

		return apply_filters( 'classified_user_data', $return, $user_data, $field );
	}
}


if ( ! function_exists( 'classified_get_difference_between_timestamps' ) ) {

	function classified_get_difference_between_timestamps( $timestamp_one, $timestamp_two, $difference_format = '' ) {

		$timestamp_one = new DateTime( date( 'Y-m-d g:i:s', $timestamp_one ) );

		$timestamp_two = new DateTime( date( 'Y-m-d g:i:s', $timestamp_two ) );

		$interval      = $timestamp_one->diff( $timestamp_two );

		if ( empty( $difference_format ) ) {

			return $interval;

		}



		/*

		 *  %Y - use for difference in year

		 *	%m - use for difference in months

		 *	%d - use for difference in days

		 *	%H - use for difference in hours

		 *	%i - use for difference in minutes

		 *	%s - use for difference in seconds

		 * */



		return $interval->format( $difference_format );

	}

}



if ( ! function_exists( 'classified_get_post_author' ) ) {

	function classified_get_post_author( $post_id = 0, $id_only = true ) {

		$post_author_id = get_post_field( 'post_author', $post_id );

		if ( $id_only ) {

			return $post_author_id;

		} else {

			return get_userdata( $post_author_id );

		}

	}

}



if ( ! function_exists( 'classified_round_int' ) ) {

	function classified_round_int( $int, $max_length = 2 ) {

		return round( $int, $max_length );

	}

}



if ( ! function_exists( 'classified_get_post_views' ) ) {

	function classified_get_post_views( $post_id ) {

		$item_views = get_post_meta( $post_id, 'cubewp_post_views', true );



		return is_numeric( $item_views ) && ! empty( $item_views ) ? $item_views : 0;

	}

}



if ( ! function_exists( 'classified_get_post_leads' ) ) {

	function classified_get_post_leads( $post_id, $user_id = false ) {

		global $wpdb;

		if ( classified_is_inbox_active() ) {

			if ( ! $user_id ) {

				$user_id = get_current_user_id();

			}

			$ad_leads = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cwp_inbox_messages WHERE msg_post_id = {$post_id} AND (msg_sender = {$user_id} OR msg_post_author_id = {$user_id}) ", ARRAY_A );

			if ( ! empty( $ad_leads ) && is_array( $ad_leads ) ) {

				return count( $ad_leads );

			}

		}



		return 0;

	}

}



if ( ! function_exists( 'classified_calc_user_stats_count' ) ) {

	function classified_calc_user_stats_count( $user ) {

		global $classified_post_types, $classified_taxonomies;

		if ( is_string( $user ) ) {

			$user = get_userdata( $user );

		}

		$user_id                  = $user->ID;

		$ads_args                 = array(

			'post_type'   => $classified_post_types,  // Classified Post Types

			'author'      => $user_id,   // Only get books written by the user

			'numberposts' => - 1, // Get All Posts

			'fields'      => 'ids', // Get All Posts

			'post_status' => array( 'publish', 'sold', 'trash', 'pending' )

		);

		$post_completion_statuses = array(

			'publish',

			'pending'

		);

		$ads                      = get_posts( $ads_args );

		if ( ! empty( $ads ) ) {

			$user_views     = 0;

			$user_leads     = 0;

			$unfinished_ads = array();

			foreach ( $ads as $ad ) {

				$ad_id       = $ad;

				$post_status = get_post_status( $ad );

				$post_type   = get_post_type( $ad );

				$ad_views    = classified_get_post_views( $ad_id );

				$user_views  += $ad_views;

				$user_leads  += classified_get_post_leads( $ad_id, $user_id );

				if ( in_array( $post_status, $post_completion_statuses ) ) {

					$post_type_form_fields = CWP()->get_form( 'post_type' );

					$post_type_form        = $post_type_form_fields[ $post_type ] ?? array();

					$post_type_fields      = array();

					if ( ! empty( $post_type_form ) ) {

						$form_groups = $post_type_form['groups'] ?? array();

						if ( ! empty( $form_groups ) ) {

							foreach ( $form_groups as $form_group ) {

								$form_fields = $form_group['fields'] ?? array();

								if ( ! empty( $form_fields ) ) {

									foreach ( $form_fields as $field => $field_data ) {

										$field_name           = $field_data['name'] ?? $field;

										$field_data           = get_field_options( $field_name );

										$conditional_field    = $field_data['conditional_field'] ?? false;

										$conditional_operator = $field_data['conditional_operator'] ?? false;

										if ( ! empty( $conditional_field ) && ! empty( $conditional_operator ) ) {

											continue;

										}

										$post_type_fields[] = $field_name;

									}

								}

							}

						}

					}

					if ( ! empty( $post_type_fields ) && is_array( $post_type_fields ) ) {

						$post_type_field_count = count( $post_type_fields );

						$percentage            = 100;

						$minus                 = $percentage / $post_type_field_count;

						foreach ( $post_type_fields as $post_type_field ) {

							if ( $post_type_field == 'the_title' ) {

								$value = get_the_title( $ad_id );

							} else if ( $post_type_field == 'the_content' ) {

								$value = get_the_content( null, null, $ad_id );

							} else if ( $post_type_field == 'featured_image' ) {

								$value = get_the_post_thumbnail_url( $ad_id );

							} else if ( in_array( $post_type_field, $classified_taxonomies ) ) {

								$value = get_the_terms( $ad_id, $post_type_field );

							} else {

								$value = get_post_meta( $ad_id, $post_type_field, true );

							}

							if ( empty( $value ) ) {

								$percentage -= $minus;

							}

						}

						if ( $percentage < 0 ) {

							$percentage = 0;

						}

						if ( $percentage > 100 ) {

							$percentage = 100;

						}

						$percentage = classified_round_int( $percentage );

						if ( $percentage < 100 ) {

							$unfinished_ads[ $ad_id ] = $percentage;

						}

					}

				}

			}

			update_user_meta( $user_id, 'classified_overall_ads_views', $user_views );

			update_user_meta( $user_id, 'classified_overall_ads_leads', $user_leads );

			update_user_meta( $user_id, 'classified_unfinished_ads', $unfinished_ads );

		}

	}

}



if ( ! function_exists( 'classified_calc_user_profile_completion_status' ) ) {

	function classified_calc_user_profile_completion_status( $user ) {

		if ( is_string( $user ) ) {

			$user = get_userdata( $user );

		}

		$user_id                    = $user->ID;

		$user_roles                 = $user->roles;

		$update_profile_form_fields = CWP()->get_form( 'user_profile' );

		if ( ! empty( $user_roles ) && is_array( $user_roles ) ) {

			$user_fields = array();

			foreach ( $user_roles as $user_role ) {

				$role_form = $update_profile_form_fields[ $user_role ] ?? array();

				if ( ! empty( $role_form ) ) {

					$form_groups = $role_form['groups'] ?? array();

					if ( ! empty( $form_groups ) ) {

						foreach ( $form_groups as $form_group ) {

							$form_fields = $form_group['fields'] ?? array();

							if ( ! empty( $form_fields ) ) {

								$void_fields = array(

									'user_login',

									'user_email',

									'user_pass',

									'confirm_pass',

								);

								foreach ( $void_fields as $void_field ) {

									if ( isset( $form_fields[ $void_field ] ) ) {

										unset( $form_fields[ $void_field ] );

									}

								}

								foreach ( $form_fields as $field => $field_data ) {

									$field_name           = $field_data['name'] ?? $field;

									$field_data           = get_field_options( $field_name );

									$conditional_field    = $field_data['conditional_field'] ?? false;

									$conditional_operator = $field_data['conditional_operator'] ?? false;

									if ( ! empty( $conditional_field ) && ! empty( $conditional_operator ) ) {

										continue;

									}

									$user_fields[] = $field_name;

								}

							}

						}

					}

				}

			}

			if ( ! empty( $user_fields ) && is_array( $user_fields ) ) {

				$user_field_count = count( $user_fields );

				$percentage       = 100;

				$minus            = $percentage / $user_field_count;

				foreach ( $user_fields as $user_field ) {

					if ( isset( $user->$user_field ) ) {

						$value = $user->$user_field;

					} else {

						$value = get_user_meta( $user_id, $user_field, true );

					}

					if ( empty( $value ) ) {

						$percentage -= $minus;

					}

				}

				if ( $percentage < 0 ) {

					$percentage = 0;

				}

				if ( $percentage > 100 ) {

					$percentage = 100;

				}

				$percentage = classified_round_int( $percentage );

				update_user_meta( $user_id, 'classified_profile_completion_status', $percentage );

			}

		}

	}

}



if ( ! function_exists( 'classified_get_item_price' ) ) {

	function classified_get_item_price( $post_id ) {

		$price = get_post_meta( $post_id, "classified_price", true );



		return ! empty( $price ) && is_numeric( $price ) ? $price : 0;

	}

}



if ( ! function_exists( 'classified_is_item_buyable' ) ) {

	function classified_is_item_buyable( $post_id ) {

		$is_buyable = get_post_meta( $post_id, "classified_buyable", true );

		$is_buyable = isset( $is_buyable ) && ! empty( $is_buyable ) ? $is_buyable : 'no';

		$is_buyable = 'yes' == $is_buyable;

		if ( $is_buyable && get_post_status( $post_id ) == 'sold' ) {

			$is_buyable = false;

		}



		return $is_buyable;

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



if ( ! function_exists( 'classified_get_rating_stars_html' ) ) {

	function classified_get_rating_stars_html( $rating, $star_wrap_start = '', $star_wrap_end = '', $maxRating = 5 ) {

		$fullStar  = $star_wrap_start . '<i class="fa-solid fa-star" aria-hidden="true"></i>' . $star_wrap_end;

		$halfStar  = $star_wrap_start . '<i class="fa-regular fa-star-half-stroke" aria-hidden="true"></i>' . $star_wrap_end;

		$emptyStar = $star_wrap_start . '<i class="fa-regular fa-star" aria-hidden="true"></i>' . $star_wrap_end;

		$rating    = min( $rating, $maxRating );



		$fullStarCount  = (int) $rating;

		$halfStarCount  = ceil( $rating ) - $fullStarCount;

		$emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;



		$html = str_repeat( $fullStar, $fullStarCount );

		$html .= str_repeat( $halfStar, $halfStarCount );

		$html .= str_repeat( $emptyStar, $emptyStarCount );



		return $html;

	}

}



if ( ! function_exists( 'classified_get_svg' ) ) {

	function classified_get_svg( $icon ) {

		$return = '';

		if ( class_exists( 'Classified_Icons' ) ) {

			$return = Classified_Icons::get_svg( $icon );

		}



		return $return;

	}

}



if ( ! function_exists( 'classified_get_user_purchased_plans' ) ) {

	function classified_get_user_purchased_plans( $user_id = 0 ) {

		global $wpdb;

		if ( ! $user_id ) {

			$user_id = get_current_user_id();

		}

		$table_name = $wpdb->prefix . 'cube_orders';



		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE userID=%d ORDER BY ID DESC", $user_id ) );

	}

}



if ( ! function_exists( 'classified_get_package_posts' ) ) {

	function classified_get_package_posts( $order_id ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'cube_order_meta';



		return $wpdb->get_results( $wpdb->prepare( "SELECT `meta_value` FROM $table_name WHERE orderID=%d AND `meta_key`='postID' ORDER BY ID DESC", $order_id ) );

	}

}



if ( ! function_exists( 'classified_time_elapsed_string' ) ) {

	function classified_time_elapsed_string( $datetime, $full = false ) {

		$now  = new DateTime;

		$ago  = new DateTime( $datetime );

		$diff = $now->diff( $ago );



		$diff->w = floor( $diff->d / 7 );

		$diff->d -= $diff->w * 7;



		$string = array(

            'y' => array(

                'singular' => esc_html__( 'year', 'cubewp-classified' ),

                'plural' => esc_html__( 'years', 'cubewp-classified' ),

            ),

            'm' => array(

                'singular' => esc_html__( 'month', 'cubewp-classified' ),

                'plural' => esc_html__( 'months', 'cubewp-classified' ),

            ),

            'w' => array(

                'singular' => esc_html__( 'week', 'cubewp-classified' ),

                'plural' => esc_html__( 'weeks', 'cubewp-classified' ),

            ),

            'd' => array(

                'singular' => esc_html__( 'day', 'cubewp-classified' ),

                'plural' => esc_html__( 'days', 'cubewp-classified' ),

            ),

            'h' => array(

                'singular' => esc_html__( 'hour', 'cubewp-classified' ),

                'plural' => esc_html__( 'hours', 'cubewp-classified' ),

            ),

            'i' => array(

                'singular' => esc_html__( 'minute', 'cubewp-classified' ),

                'plural' => esc_html__( 'minutes', 'cubewp-classified' ),

            ),

            's' => array(

                'singular' => esc_html__( 'second', 'cubewp-classified' ),

                'plural' => esc_html__( 'seconds', 'cubewp-classified' ),

            ),

		);

		foreach ( $string as $k => &$v ) {

			if ( $diff->$k ) {

				$v = $diff->$k . ' ' . ( $diff->$k > 1 ? $v['plural'] : $v['singular'] );

			} else {

				unset( $string[ $k ] );

			}

		}



		if ( ! $full ) {

			$string = array_slice( $string, 0, 1 );

		}



		return $string ? implode( ', ', $string ) . ' ' . esc_html__( 'ago', 'cubewp-classified' ) : esc_html__( 'just now', 'cubewp-classified' );

	}

}



if ( ! function_exists( 'classified_add_into_array_after_key' ) ) {

	function classified_add_into_array_after_key( $main_array, $to_add, $add_after ) {

		$index = array_search( $add_after, array_keys( $main_array ) ) + 1;



		return array_merge(

			array_slice( $main_array, 0, $index, true ),

			$to_add,

			array_slice( $main_array, $index, null, true )

		);

	}

}