<?php
defined( 'ABSPATH' ) || exit;

$GLOBALS['classified_modals'] = array();

if ( ! function_exists( 'classified_require_modal' ) ) {
	function classified_require_modal( $modal ) {
		global $classified_modals;
		if ( isset( $classified_modals ) && is_array( $classified_modals ) ) {
			$GLOBALS['classified_modals'][] = $modal;
		} else {
			$GLOBALS['classified_modals'] = array( $modal );
		}
	}
}

if ( ! function_exists( 'classified_overwrite_default_loop' ) ) {
	function classified_overwrite_default_loop( $output = null, $col_class = 'col-12 col-md-6 col-lg-4' ) {
		global $classified_post_types;
		if ( ! in_array( get_post_type(), $classified_post_types ) ) {
			return $output;
		}
		if ( function_exists( 'cubewp_get_loop_builder_by_post_type' ) ) {
			$dynamic_layout = cubewp_get_loop_builder_by_post_type( get_post_type() );
			if ( ! empty( $dynamic_layout ) ) {
				ob_start();
				?>
                <div <?php post_class( $col_class ); ?>>
					<?php
					echo cubewp_core_data( $dynamic_layout );
					?>
                </div>
				<?php

				return ob_get_clean();
			}
		}
		ob_start();
		set_query_var( 'col_class', $col_class );
		set_query_var( 'post_id', get_the_ID() );
		get_template_part( 'templates/loop/loop-views' );

		return ob_get_clean();
	}

	add_filter( "cubewp/frontend/loop/grid/html", "classified_overwrite_default_loop", 11 );
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

if ( ! function_exists( 'classified_is_review_active' ) ) {
	function classified_is_review_active() {
		return class_exists( 'CubeWp_Reviews_Stats' );
	}
}

if ( ! function_exists( 'classified_overwrite_archive_found_text' ) ) {
	function classified_overwrite_archive_found_text( $output, $args ) {
		global $classified_category_taxonomies;
		$output      = '';
		$total_posts = $args['total_posts'] ?? '';
		$posted_data = $args['data'] ?? array();
		$result_text = esc_html__( 'result', 'classified-pro' );
		if ( $total_posts > 1 ) {
			$result_text = esc_html__( 'results', 'classified-pro' );
		}

		if ( isset( $posted_data['s'] ) && ! empty( $posted_data['s'] ) ) {
			$result_text = sanitize_text_field( $posted_data['s'] );
		}

		$output .= $total_posts;
		$output .= ' ';
		$output .= $result_text;
		$output .= ' ';
		$output .= esc_html__( 'found', 'classified-pro' );
		if ( isset( $posted_data['locations'] ) && ! empty( $posted_data['locations'] ) ) {
			$locations = explode( ',', $posted_data['locations'] );
			if ( ! empty( $locations ) && is_array( $locations ) ) {
				$output .= ' ';
				$output .= esc_html__( 'in', 'classified-pro' );
				$output .= ' ';
				foreach ( $locations as $counter => $location ) {
					if ( $counter != 0 ) {
						$output .= ',&nbsp;';
					}
					$term = get_term_by( 'slug', $location, 'locations' );
					if ( ! is_wp_error( $term ) ) {
						$output .= sanitize_text_field( $term->name );
					} else {
						$output .= sanitize_text_field( $location );
					}
				}
			}
		} else if ( ! empty( $classified_category_taxonomies ) && is_array( $classified_category_taxonomies ) ) {
			foreach ( $classified_category_taxonomies as $category ) {
				if ( isset( $posted_data[ $category ] ) && ! empty( $posted_data[ $category ] ) ) {
					$categories = explode( ',', $posted_data[ $category ] );
					if ( ! empty( $categories ) && is_array( $categories ) ) {
						$output .= ' ';
						$output .= esc_html__( 'in', 'classified-pro' );
						$output .= ' ';
						foreach ( $categories as $counter => $_category ) {
							if ( $counter != 0 ) {
								$output .= ',&nbsp;';
							}
							$term = get_term_by( 'slug', $_category, $category );
							if ( ! is_wp_error( $term ) ) {
								$output .= sanitize_text_field( $term->name );
							} else {
								$output .= sanitize_text_field( $_category );
							}
						}
					}
					break;
				}
			}
		}


		return $output;
	}

	add_filter( "cubewp_frontend_search_data", "classified_overwrite_archive_found_text", 11, 2 );
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

if ( ! function_exists( 'classified_adding_header_nav_item' ) ) {
	function classified_adding_header_nav_item( $items, $args ) {
		if ( is_front_page() || is_home() ) {
			$show        = classified_get_setting( 'header_cats_home' );
			$banner_type = classified_get_setting( 'home_header_cats_banner' );
			$image_url   = classified_get_setting( 'home_cats_banner_image' );
			$link        = classified_get_setting( 'home_cats_banner_img_link' );
			$adsense     = classified_get_setting( 'home_cats_banner_ads' );
			$location    = 'classified_home_header';
		} else {
			$location    = 'classified_inner_header';
			$banner_type = classified_get_setting( 'inner_header_cats_banner' );
			$show        = classified_get_setting( 'header_cats_inner' );
			$image_url   = classified_get_setting( 'inner_cats_banner_image' );
			$link        = classified_get_setting( 'inner_cats_banner_img_link' );
			$adsense     = classified_get_setting( 'inner_cats_banner_ads' );
		}
		$header_cats_number = classified_get_setting( 'header_cats_number' );
		if ( empty( $header_cats_number ) ) {
			$header_cats_number = 6;
		}
		$output = null;
		if ( $show && $args->theme_location == $location ) {
			global $classified_category_taxonomies;
			$args       = array(
				'taxonomy'   => $classified_category_taxonomies,
				'hide_empty' => false,
				'parent'     => 0,
				'number'     => 10
			);
			$categories = get_terms( $args );

			ob_start();
			?>
            <li class="classified-dropdown position-static">
                <a href="#" class="d-flex align-items-center text-decoration-none classified-nav-all-categories">
                    <i class="fa-solid fa-list me-2" aria-hidden="true"></i>
					<?php esc_html_e( "All Categories", "classified-pro" ); ?>
                    <i class="fa-solid fa-chevron-down ms-2" aria-hidden="true"></i>
                </a>
                <div class="classified-dropdown-items w-100">
                    <div class="classified-nav-all-categories-container">
                        <div class="row" data-masonry='{"percentPosition": true, "horizontalOrder": true}'>
							<?php
							if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
								if ( ! wp_is_mobile() ) {
									wp_enqueue_script( 'classified-masonry-scripts' );
								}
								$counter = 0;
								foreach ( $categories as $category ) {
									if ( $counter >= $header_cats_number ) {
										break;
									}
									$counter ++;
									$term_icon = get_term_meta( $category->term_id, 'classified_category_icon', true );
									?>
                                    <div class="col-12 col-md-6 col-lg-3 col-xxl-3" style="margin: 0 0 43px 0;">
                                        <a href="<?php echo esc_url( get_term_link( $category->term_id ) ) ?>">
                                            <h5>
												<?php
												echo classified_get_icon_output( $term_icon );
												echo esc_html( $category->name );
												?>
                                            </h5>
                                        </a>
										<?php
										$child_args       = array(
											'taxonomy'   => $classified_category_taxonomies,
											'hide_empty' => false,
											'parent'     => $category->term_id
										);
										$child_categories = get_terms( $child_args );
										if ( ! empty( $child_categories ) && ! is_wp_error( $child_categories ) ) {
											foreach ( $child_categories as $child_category ) {
												?>
                                                <a href="<?php echo esc_url( get_term_link( $child_category->term_id ) ) ?>">
                                                    <p class="p-md">
                                                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
														<?php echo esc_html( $child_category->name ); ?>
                                                    </p>
                                                </a>
												<?php
											}
										}
										?>
                                    </div>
									<?php
									if ( $counter == 3 && ! empty( $banner_type ) ) {
										?>
                                        <div class="col-12 col-md-6 col-lg-3 col-xxl-3" style="margin: 0 0 43px 0;">
											<?php

											if ( $banner_type == "static_banner" ) {
												if ( ! empty( $image_url ) ) {
													$attach_image_url = wp_get_attachment_url( $image_url );
													if ( ! empty( $attach_image_url ) ) {
														?>
                                                        <a href="<?php echo esc_url( $link ); ?>" target="_blank">
                                                            <img loading="lazy" width="100%" height="100%" class="m-0"
                                                                 src="<?php echo esc_url( $attach_image_url ); ?>"
                                                                 alt="<?php esc_html_e( 'Advertisement', 'classified-pro' ); ?>"></a>
														<?php
													}
												}
											} else if ( $banner_type == "google_adsense" ) {
												if ( ! empty( $adsense ) ) {
													echo cubewp_core_data( $adsense );
												}
											}

											?>
                                        </div>
										<?php
									}
								}
							}
							?>
                        </div>
                    </div>
                </div>
            </li>
			<?php
			$output = ob_get_clean();
		}

		return $output . $items;
	}

	add_filter( 'wp_nav_menu_items', 'classified_adding_header_nav_item', 10, 2 );
}

if ( ! function_exists( 'classified_get_post_terms' ) ) {
	function classified_get_post_terms( $post_id, $taxonomies = array(), $result_count = - 1 ) {
		global $classified_taxonomies;
		$post_terms = array();
		if ( empty( $taxonomies ) || ! is_array( $taxonomies ) ) {
			$taxonomies = $classified_taxonomies;
		}
		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$_post_terms = get_the_terms( $post_id, $taxonomy );
				if ( ! empty( $_post_terms ) && is_array( $_post_terms ) ) {
					$counter = 1;
					foreach ( $_post_terms as $_post_term ) {
						if ( ! is_wp_error( $_post_term ) ) {
							$post_terms[] = $_post_term;
							$counter ++;
							if ( $counter > $result_count && $result_count != '-1' ) {
								break;
							}
						}
					}
				}
			}
		}

		return apply_filters( 'classified_get_post_terms', $post_terms, $post_id, $taxonomies, $result_count );
	}
}

if ( ! function_exists( 'classified_file_force_contents' ) ) {
	function classified_file_force_contents( $file_path, $file_content, $flags = 0644 ) {
		global $wp_filesystem;
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		WP_Filesystem();

		$parts = explode( '/', $file_path );
		array_pop( $parts );
		$dir = implode( '/', $parts );

		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		return $wp_filesystem->put_contents( $file_path, $file_content, $flags );
	}
}

if ( ! function_exists( 'classified_get_cwp_form_type' ) ) {
	function classified_get_cwp_form_type( $shortcode, $type ) {
		if ( strpos( $shortcode, 'type="' . $type . '"' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'classified_get_item_default_thumbnail' ) ) {
	function classified_get_item_default_thumbnail() {
		return classified_get_setting( 'default_featured_image', 'media_url' );
	}
}

if ( ! function_exists( 'classified_limit_str_by_words' ) ) {
	function classified_limit_str_by_words( $str, $limit = 10 ) {
		if ( str_word_count( $str, 0 ) > $limit ) {
			$words = str_word_count( $str, 2 );
			$pos   = array_keys( $words );
			$str   = substr( $str, 0, $pos[ $limit ] ) . '...';
		}

		return $str;
	}
}

if ( ! function_exists( 'classified_get_navigation' ) ) {
	function classified_get_navigation( $nav_loc = 'classified_inner_header', $echo = false, $class = 'classified-navigation-nav w-100' ) {
		$args = array(
			'theme_location' => $nav_loc,
			'menu_class'     => $class,
			'container'      => '',
			'fallback_cb'    => false,
			'walker'         => new Classified_Walker_Nav_Menu()
		);
		ob_start();
		wp_nav_menu( $args );
		$return = ob_get_clean();

		if ( $echo ) {
			echo cubewp_core_data( $return );
			$return = 1;
		}

		return $return;
	}
}

if ( ! function_exists( 'classified_get_navigation_quicks' ) ) {
	function classified_get_navigation_quicks( $parent_class = '' ) {
		$dashboard_url  = classified_get_setting( 'dashboard_page', 'page_url' );
		$inbox_url      = $dashboard_url;
		$saved_url      = $dashboard_url;
		$dashboard_tabs = CWP()->cubewp_options( 'cwp_userdash' );
		if ( empty( $dashboard_tabs ) || ! is_array( $dashboard_tabs ) ) {
			$dashboard_tabs = array();
		}
		$show_inbox = false;
		$show_saved = false;
		foreach ( $dashboard_tabs as $tab_id => $tab ) {
			if ( isset( $tab['content_type'] ) && $tab['content_type'] === 'classified_inbox' ) {
				$inbox_url  = add_query_arg( array(
					'tab_id' => $tab_id
				), $dashboard_url );
				$show_inbox = true;
			}
			if ( isset( $tab['content_type'] ) && $tab['content_type'] === 'saved' ) {
				$saved_url  = add_query_arg( array(
					'tab_id' => $tab_id
				), $dashboard_url );
				$show_saved = true;
			}
			if ( $show_inbox && $show_saved ) {
				break;
			}
		}
		$header_quick_link_inbox = classified_get_setting( 'header_quick_link_inbox' );
		$header_quick_link_saved = classified_get_setting( 'header_quick_link_saved' );
		if ( $show_inbox ) {
			$show_inbox = $header_quick_link_inbox;
		}
		if ( $show_saved ) {
			$show_saved = $header_quick_link_saved;
		}
		ob_start();
		echo do_shortcode( '[cubewp_inbox_notifications]' );
		$new_chat_count = ob_get_clean();
		ob_start();
		?>
        <div class="classified-navigation-quick-container <?php echo esc_attr( $parent_class ); ?>">
			<?php
			if ( classified_is_inbox_active() && $show_inbox ) {
				?>
                <div class="classified-navigation-quick classified-unread-chat-count-container">
                    <button
                            class="classified-not-filled-btn position-relative classified-unread-chat-count" <?php if ( ! is_user_logged_in() ) {
						echo 'data-bs-toggle="modal" data-bs-target="#classified-login-register"';
					} ?>>
                        <i class="fa-regular fa-message" aria-hidden="true"></i>
						<?php
						esc_html_e( 'Messages', 'classified-pro' );
						if ( is_user_logged_in() ) { ?>
                            <a class="stretched-link" href="<?php echo esc_url( $inbox_url ); ?>"></a>
							<?php
							if ( $new_chat_count > 0 ) { ?>
                                <span class="classified-count-badge">
                                    <?php echo esc_html( $new_chat_count ); ?>
                                </span>
							<?php } ?>
						<?php } ?>
                    </button>
                </div>
				<?php
			}
			if ( $show_saved ) {
				?>
                <div class="classified-navigation-quick classified-saved-items-container">
                    <button
                            class="classified-not-filled-btn position-relative classified-saved-items" <?php if ( ! is_user_logged_in() ) {
						echo 'data-bs-toggle="modal" data-bs-target="#classified-login-register"';
					} ?>>
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
						<?php
						esc_html_e( 'Favorites', 'classified-pro' );
						if ( is_user_logged_in() ) {
							?>
                            <a class="stretched-link" href="<?php echo esc_url( $saved_url ); ?>"></a>
							<?php
						}
						?>
                    </button>
                </div>
				<?php
			}
			?>
            <div class="classified-navigation-quick classified-user-menu-container">
				<?php if ( is_user_logged_in() ) { ?>
                    <div class="classified-dropdown classified-user-menu classified-switch-regular-icon">
                        <i class="fa-regular fa-user me-1" aria-hidden="true"></i>
						<?php echo classified_get_userdata( get_current_user_id(), 'short_name' ) ?>
                        <i class="fa-solid fa-chevron-down ms-0" aria-hidden="true"></i>
                        <div class="classified-dropdown-items">
							<?php
							ob_start();
							if ( $dashboard_url ) {
								?>
                                <a href="<?php echo esc_url( $dashboard_url ); ?>">
                                    <p class="classified-dropdown-item"><?php esc_html_e( "Dashboard", "classified-pro" ); ?></p>
                                </a>
								<?php
							}
							?>
                            <a href="<?php echo wp_logout_url( home_url() ); ?>">
                                <p class="classified-dropdown-item"><?php esc_html_e( "Logout", "classified-pro" ); ?></p>
                            </a>
							<?php
							echo apply_filters( 'classified_header_user_menu', ob_get_clean() );
							?>
                        </div>
                    </div>
				<?php } else { ?>
                    <div class="classified-login-register-btn" type="button" data-bs-toggle="modal"
                         data-bs-target="#classified-login-register">
                        <i class="fa-regular fa-user me-2" aria-hidden="true"></i>
						<?php esc_html_e( "Login", "classified-pro" ); ?>
                    </div>
				<?php } ?>
            </div>
			<?php
			if ( is_user_logged_in() ) {
				$modal = '#classified-item-type';
			} else {
				$modal = '#classified-login-register';
			}
			global $classified_post_types;
			$show_sell           = false;
			$ad_submission_count = 0;
			$ad_submission_url   = '';
			if ( ! empty( $classified_post_types ) && is_array( $classified_post_types ) ) {
				foreach ( $classified_post_types as $classified_post_type ) {
					$ad_submission = classified_get_setting( 'submit_edit_page', 'page_url', $classified_post_type );
					if ( ! empty( $ad_submission ) ) {
						$ad_submission_url = $ad_submission;
						$show_sell         = true;
						$ad_submission_count ++;
					}
				}
			}
			if ( $show_sell ) {
				if ( $ad_submission_count > 1 ) {
					?>
                    <div class="classified-navigation-quick classified-submission-btn-container">
                        <button class="classified-filled-btn classified-submission-btn" type="button"
                                data-bs-toggle="modal"
                                data-bs-target="<?php echo esc_attr( $modal ); ?>">
                            <i class="fa-solid fa-camera me-2" aria-hidden="true"></i>
							<?php esc_html_e( "Sell Now", "classified-pro" ); ?>
                        </button>
                    </div>
					<?php
				} else {
					?>
                    <div class="classified-navigation-quick classified-submission-btn-container">
                        <button class="classified-filled-btn classified-submission-btn position-relative"
							<?php
							if ( ! is_user_logged_in() ) {
								?>
                                data-bs-toggle="modal"
                                data-bs-target="<?php echo esc_attr( $modal ); ?>"
								<?php
							}
							?>
                                type="button">
							<?php
							if ( is_user_logged_in() ) {
								?>
                                <a class="stretched-link" href="<?php echo esc_url( $ad_submission_url ); ?>"></a>
								<?php
							}
							?>
                            <i class="fa-solid fa-camera me-2" aria-hidden="true"></i>
							<?php esc_html_e( "Sell Now", "classified-pro" ); ?>
                        </button>
                    </div>
					<?php
				}
			}
			?>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'classified_get_socials_share' ) ) {
	function classified_get_socials_share( $ID = 0, $is_user = false ) {
		$url       = get_site_url();
		$title     = get_bloginfo();
		$thumbnail = get_site_icon_url();
		if ( $ID != 0 ) {
			if ( $is_user ) {
				$url       = classified_get_userdata( $ID, 'profile_link' );
				$title     = classified_get_userdata( $ID, 'name' );
				$thumbnail = classified_get_userdata( $ID, 'avatar' );
			} else {
				$url       = get_post_permalink( $ID );
				$title     = get_the_title( $ID );
				$thumbnail = classified_get_post_featured_image( $ID );
			}
		}
		$title       = str_replace( ' ', '%20', $title );
		$twitterURL  = 'https://twitter.com/intent/tweet?text=' . $title . '&amp;url=' . $url;
		$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
		$pinterest   = 'https://pinterest.com/pin/create/button/?url=' . $url . '&media=' . $thumbnail . '&description=' . $title;
		$linkedin    = 'http://www.linkedin.com/shareArticle?mini=true&url=' . $url;
		$reddit      = 'https://www.reddit.com/login?dest=https%3A%2F%2Fwww.reddit.com%2Fsubmit%3Ftitle%3D' . $title . '%26url%3D' . $url;
		ob_start();
		?>
        <div class="d-flex justify-content-center align-items-center p-3">
            <a href="<?php echo esc_url( $twitterURL ); ?>" class="d-block mx-2" target="_blank">
                <i class="fa-brands fa-twitter m-0" aria-hidden="true"></i>
            </a>
            <a href="<?php echo esc_url( $facebookURL ); ?>" class="d-block mx-2" target="_blank">
                <i class="fa-brands fa-facebook m-0" aria-hidden="true"></i>
            </a>
            <a href="<?php echo esc_url( $pinterest ); ?>" class="d-block mx-2" target="_blank">
                <i class="fa-brands fa-pinterest m-0" aria-hidden="true"></i>
            </a>
            <a href="<?php echo esc_url( $linkedin ); ?>" class="d-block mx-2" target="_blank">
                <i class="fa-brands fa-linkedin m-0" aria-hidden="true"></i>
            </a>
            <a href="<?php echo esc_url( $reddit ); ?>" class="d-block mx-2" target="_blank">
                <i class="fa-brands fa-reddit m-0" aria-hidden="true"></i>
            </a>
        </div>
		<?php
		$output = ob_get_clean();

		return apply_filters( 'classified_item_social_share', $output, $ID, $is_user );
	}
}

if ( ! function_exists( 'classified_custom_cube_types' ) ) {
	function classified_custom_cube_types() {
		return apply_filters( 'classified_custom_cube_types', array(
			'classified_ad_the_content',
			'classified_ad_id_and_price',
			'classified_ad_title_and_desc',
			'classified_ad_single_actions',
			'classified_ad_single_quick_tip',
			'classified_ad_single_sections_tabs',
			'classified_ad_single_stats'
		) );
	}
}

if ( ! function_exists( 'classified_body_class' ) ) {
	function classified_body_class( $classes ) {
		if ( ! in_array( 'home', $classes ) ) {
			global $classified_post_types;
			if ( ! empty( $classified_post_types ) ) {
				foreach ( $classified_post_types as $post_type ) {
					$page = classified_get_setting( 'header_top_bar_landing_pages_' . $post_type );
					if ( is_page( $page ) ) {
						$classes[] = 'home';
					}
				}
			}
		}

		return $classes;
	}

	add_filter( 'body_class', 'classified_body_class' );
}

if ( ! function_exists( 'classified_add_icon_to_cubewp_choices_fields' ) ) {
	function classified_add_icon_to_cubewp_choices_fields( $output = '', $args = array(), $data = array() ) {
		$choices_fields = array(
			'radio',
			'dropdown',
			'checkbox'
		);
		$field_type     = $data['type'] ?? '';
		if ( in_array( $field_type, $choices_fields ) ) {
			$field_options = $args['options'] ?? '';
			$options       = (array) json_decode( $field_options, true );
			if ( ! isset( $options['icon'] ) ) {
				$args['options'] = json_encode( array_merge( $options, array( 'icon' => '' ) ) );
			}
			$args['icon_placeholder'] = esc_html__( 'Enter icon image url or font awesome class or SVG code.', 'classified-pro' );
		}

		return apply_filters( "cubewp/admin/options/customfield", '', $args );
	}

	add_filter( 'cubewp/custom_fields/options/output', 'classified_add_icon_to_cubewp_choices_fields', 9, 3 );
}

if ( ! function_exists( 'classified_get_archive_layout' ) ) {
	function classified_get_archive_layout() {
		$card_view = 'classified-grid-view';
		if ( isset( $_COOKIE['cwp_archive_switcher'] ) && ! empty( $_COOKIE['cwp_archive_switcher'] ) && $_COOKIE['cwp_archive_switcher'] != 'grid-view' ) {
			$card_view = 'classified-list-view';
		}

		return $card_view;
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

if ( ! function_exists( 'classified_add_watermark_onto_gallery_images' ) ) {
	function classified_add_watermark_onto_gallery_images() {
		global $classified_post_types;
		$classified_post_types = is_array( $classified_post_types ) ? $classified_post_types : array();
		$watermark_position    = classified_get_setting( 'classified_watermark_position' );
		$watermark_image       = classified_get_setting( 'classified_watermark_image' );
		$margin                = 5;
		$watermark_image       = get_attached_file( $watermark_image );
		if ( empty( $watermark_position ) || empty( $watermark_image ) ) {
			return false;
		}
		foreach ( $classified_post_types as $post_type ) {
			add_filter( "cubewp/{$post_type}/after/submit/actions", function ( $return, $post ) use ( $watermark_position, $watermark_image, $margin ) {
				$post_id = isset( $post['post_id'] ) ? $post['post_id'] : 0;
				if ( ! empty( $post_id ) ) {
					$field_args          = get_field_options( 'classified_gallery' );
					$gallery_ids         = get_post_meta( $post_id, 'classified_gallery', true );
					$field_args['value'] = $gallery_ids;
					$gallery_ids         = cwp_handle_data_format( $field_args );
					if ( has_post_thumbnail( $post_id ) ) {
						$gallery_ids[] = get_post_thumbnail_id( $post_id );
					}
					if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) {
						foreach ( $gallery_ids as $gallery_id ) {
							$gallery_id   = cwp_get_attachment_id( $gallery_id );
							$is_processed = get_post_meta( $gallery_id, 'classified_watermark_processed', true );
							if ( $is_processed == 'yes' ) {
								continue;
							}
							update_post_meta( $gallery_id, 'classified_watermark_processed', 'yes' );
							$attachment_meta = wp_get_attachment_metadata( $gallery_id );
							$file_path       = get_attached_file( $gallery_id );
							classified_add_watermark_onto_image( $file_path, $watermark_image, $watermark_position, $margin );
							foreach ( $attachment_meta['sizes'] as $size_info ) {
								$file_path_size = pathinfo( $file_path );
								$file_path_size = $file_path_size['dirname'] . '/' . $size_info['file'];
								$image_width    = $size_info['width'];
								$image_height   = $size_info['height'];
								if ( $image_width > 450 && $image_height > 450 ) {
									classified_add_watermark_onto_image( $file_path_size, $watermark_image, $watermark_position, $margin );
								}
							}
						}
					}
				}

				return $return;
			}, 10, 2 );
		}
	}

	if ( classified_get_setting( 'classified_watermark' ) ) {
		add_action( 'init', 'classified_add_watermark_onto_gallery_images' );
	}
}

if ( ! function_exists( 'classified_add_watermark_onto_image' ) ) {
	function classified_add_watermark_onto_image( $image_path, $watermark_image, $watermark_position = 'center', $margin = 10 ) {
		// Create an image resource from the watermark image
		$watermark = imagecreatefrompng( $watermark_image );

		// Create an image resource from the original image
		$image = imagecreatefromstring( file_get_contents( $image_path ) );

		// Set the blending mode for the watermark
		imagealphablending( $watermark, true );

		// Get the dimensions of the original image
		$image_width  = imagesx( $image );
		$image_height = imagesy( $image );

		// Get the dimensions of the watermark
		$watermark_width  = imagesx( $watermark );
		$watermark_height = imagesy( $watermark );

		// Calculate the effective watermark dimensions with margin
		$effective_watermark_width  = $watermark_width + ( 2 * $margin );
		$effective_watermark_height = $watermark_height + ( 2 * $margin );

		// Check if the effective watermark dimensions exceed the main image dimensions
		if ( $effective_watermark_width > $image_width || $effective_watermark_height > $image_height ) {
			// Calculate the maximum scale ratio based on the main image dimensions and the effective watermark dimensions
			$scale_ratio = min( $image_width / $effective_watermark_width, $image_height / $effective_watermark_height );

			// Calculate the new dimensions of the watermark and adjust the margin accordingly
			$new_watermark_width  = $watermark_width * $scale_ratio;
			$new_watermark_height = $watermark_height * $scale_ratio;
			$new_margin           = $margin * $scale_ratio;

			// Resize the watermark
			$resized_watermark = imagescale( $watermark, $new_watermark_width, $new_watermark_height );
			imagedestroy( $watermark );
			$watermark = $resized_watermark;

			// Adjust the watermark dimensions and margin
			$watermark_width  = $new_watermark_width;
			$watermark_height = $new_watermark_height;
			$margin           = $new_margin;
		}

		// Calculate the position to place the watermark on the image
		switch ( $watermark_position ) {
			case 'top-left':
				$position_x = $margin;
				$position_y = $margin;
				break;
			case 'top-right':
				$position_x = $image_width - $watermark_width - $margin;
				$position_y = $margin;
				break;
			case 'bottom-left':
				$position_x = $margin;
				$position_y = $image_height - $watermark_height - $margin;
				break;
			case 'bottom-right':
				$position_x = $image_width - $watermark_width - $margin;
				$position_y = $image_height - $watermark_height - $margin;
				break;
			case 'center':
			default:
				$position_x = (int) ( ( $image_width - $watermark_width ) / 2 );
				$position_y = (int) ( ( $image_height - $watermark_height ) / 2 );
				break;
		}

		// Merge the watermark with the image
		imagecopy( $image, $watermark, $position_x, $position_y, 0, 0, $watermark_width, $watermark_height );

		// Save the modified image
		imagepng( $image, $image_path );

		// Free up memory
		imagedestroy( $image );
		imagedestroy( $watermark );
	}
}