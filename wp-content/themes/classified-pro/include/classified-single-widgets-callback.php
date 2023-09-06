<?php
defined( 'ABSPATH' ) || exit;

//Section Filter
if ( ! function_exists( 'classified_single_cpt_section' ) ) {
	function classified_single_cpt_section( $output, $args ) {
		global $cubewp_frontend;
		$post_metas   = $cubewp_frontend->post_metas( get_the_ID() );
		$show_section = false;
		if ( is_array( $args["fields"] ) && ! empty( $args["fields"] ) ) {
			foreach ( $args["fields"] as $field ) {
				if ( ! is_array( $field ) ) {
					$field = get_field_options( $field );
				}
				$field_type = $field["type"] ?? "";
				$meta_key   = $field["name"] ?? "";
				if ( $field_type == 'google_address' ) {
					return $output;
				}
				$value = $post_metas[ $meta_key ]['meta_value'] ?? '';
				if ( ! empty( $value ) ) {
					$show_section = true;
				}
				if ( in_array( $field_type, classified_custom_cube_types() ) ) {
					$show_section = true;
				}
			}
		}
		if ( ! $show_section ) {
			return '';
		}
		$layout                = $args['section_layout'] ?? 'classified-without-styling-section';
		$section_title         = $args['section_show_title'] ?? 'yes';
		$args['section_class'] = $args['section_class'] ?? '';
		$args['section_id']    = $args['section_id'] ?? '';
		$args['section_title'] = $args['section_title'] ?? '';
		$args['section_class'] .= ' classified-single-section ' . esc_attr( $layout ) . ' classified-section-details ';
		$html                  = '<div class="' . $args['section_class'] . '" id="classified-item-section-' . $args['section_id'] . '">';
		if ( $section_title == 'yes' ) {
			$html .= '<h2 class="classified-section-title">';
			if ( $layout == 'classified-highlighted-section' ) {
				$html .= '<i class="fa-solid fa-lightbulb" aria-hidden="true"></i>';
			}
			$html .= $args["section_title"];
			$html .= '</h2>';
		}
		$html .= '<ul class="classified-section-fields">
		        ' . apply_filters( 'cubewp/frontend/single/section/fields', $args['fields'] ) . '
		    </ul>
		</div>';

		return $html;
	}

	add_filter( 'cubewp/frontend/single/section', 'classified_single_cpt_section', 10, 2 );
}

//Fields Filters

if ( ! function_exists( 'classified_field_gallery' ) ) {
	function classified_field_gallery( $output, $args ) {
		$html                    = '';
		$value                   = $args['value'] ?? array();
		$container_class         = $args['container_class'] ?? '';
		$gallery_slides_html     = '';
		$gallery_nav_slides_html = '';
		if ( ! empty( $value ) && is_array( $value ) ) {
			foreach ( $value as $galleryItemID ) {
				$galleryItemURL      = wp_get_attachment_url( $galleryItemID );
				$galleryItemURL_mini = wp_get_attachment_thumb_url( $galleryItemID );
				$galleryItemCaption  = wp_get_attachment_caption( $galleryItemID );
				if ( empty( $galleryItemCaption ) ) {
					$galleryItemCaption = sprintf( esc_html__( '%s Gallery Image', 'classified-pro' ), get_the_title( get_the_ID() ) );
				}
				$gallery_slides_html     .= '<div class="classified-gallery-slider-slide">
	        	    <a href="' . esc_url( $galleryItemURL ) . '" rel="classified[item_gallery]"><img loading="lazy" width="100%" height="100%" src="' . esc_url( $galleryItemURL ) . '" alt="' . esc_attr( $galleryItemCaption ) . '"></a>
	            </div>';
				$gallery_nav_slides_html .= '<div class="classified-gallery-nav-slide">
	        		<img loading="lazy" width="100%" height="100%" src="' . esc_url( $galleryItemURL_mini ) . '" alt="' . esc_attr( $galleryItemCaption ) . '">
	            </div>';
			}
			$html .= '<div class="classified-gallery-slider-container d-block d-lg-flex ' . esc_attr( $container_class ) . '">
	            <div class="classified-gallery-slider">';
			$html .= cubewp_core_data( $gallery_slides_html );
			$html .= '</div>';
			$html .= '<div class="classified-gallery-slider-nav-container">';
			$html .= '<div class="classified-gallery-slider-nav">';
			$html .= cubewp_core_data( $gallery_nav_slides_html );
			$html .= '</div>';
			$html .= '<div class="classified-gallery-actions classified-quick d-inline-flex d-lg-flex mb-3 classified-quick-views"></div>';
			$html .= '</div>';
			$html .= '</div>';
		}

		return $html;
	}

	add_filter( 'cubewp/singlecpt/field/gallery', 'classified_field_gallery', 11, 2 );
}

if ( ! function_exists( 'classified_single_page_fields_layout' ) ) {
	function classified_single_page_fields_layout( $output, $args ) {
		$flag = false;
		global $classified_post_types;
		$post_type = get_post_type( get_the_ID() );
		if ( isset( $post_type ) && ! empty( $post_type ) ) {
			if ( isset( $classified_post_types ) && is_array( $classified_post_types ) && in_array( $post_type, $classified_post_types ) ) {
				$flag = true;
			}
		}
		if ( $flag ) {
			$label           = $args['label'];
			$value           = $args['value'];
			$icon            = $args['classified_field_icon'] ?? '';
			$container_class = $args['container_class'] ?? '';
			if ( empty( $value ) ) {
				return '';
			}
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
            $value = esc_html__( $value, 'classified-pro' );
			$html = '<li class="classified-section-field ' . esc_attr( $container_class ) . '">';
			if ( ! empty( $icon ) ) {
				$html .= '<i class="classified-section-field-icon ' . esc_attr( $icon ) . '" aria-hidden="true"></i>';
			}
			$html .= '<div class="classified-section-field-content">';
			$html .= '<h6>' . $label . '</h6>';
			$html .= '<p class="p-lg">' . $value . '</p>';
			$html .= '</div>';
			$html .= '</li>';

			return $html;
		} else {
			return $output;
		}
	}

	add_filter( 'cubewp/singlecpt/field/text', 'classified_single_page_fields_layout', 10, 2 );
	add_filter( 'cubewp/singlecpt/field/number', 'classified_single_page_fields_layout', 10, 2 );
	add_filter( 'cubewp/singlecpt/field/dropdown', 'classified_single_page_fields_layout', 10, 2 );
	add_filter( 'cubewp/singlecpt/field/radio', 'classified_single_page_fields_layout', 10, 2 );
	add_filter( 'cubewp/singlecpt/field/switch', 'classified_single_page_fields_layout', 10, 2 );
}

if ( ! function_exists( 'classified_field_google_address' ) ) {
	function classified_field_google_address( $output, $args ) {
		$label = $args['label'];
		$value = $args['value'];
		$html = '';
		if ( is_array( $value ) && ( isset( $value['address'] ) && isset( $value['lat'] ) && isset( $value['lng'] ) ) ) {
			$address = $value['address'] ?? '';
			$lat     = $value['lat'] ?? '';
			$lng     = $value['lng'] ?? '';
			if ( ! empty( $address ) && ! empty( $lat ) && ! empty( $lng ) ) {
				$html = '<div class="classified-single-widget">
                    <div class="classified-seller-location">
                        <h4>' . $label . '</h4>
                        <p class="p-lg"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span class="classified-single-address">' . $address . '</span></p>
                        <div class="classified-sidebar-map"><div class="cpt-single-map" data-latitude="' . $lat . '" data-longitude="' . $lng . '" style="height: 200px;width: 100%;"></div></div>
                        <i class="d-flex">' . esc_html__( 'Map is approximate to keep seller’s location private.', 'classified-pro' ) . '</i>
                    </div>
                </div>';
			}
		}

		return $html;
	}

	add_filter( 'cubewp/singlecpt/field/google_address', 'classified_field_google_address', 10, 2 );
}


// Custom Cubes
if ( ! function_exists( 'cube_classified_ad_id_and_price' ) ) {
	function cube_classified_ad_id_and_price( $args ) {
		global $post, $cubewp_frontend;
		$post_id         = $post->ID ?? get_the_ID();
		$post_metas      = $cubewp_frontend->post_metas( get_the_ID() );
		$container_class = $args['container_class'] ?? '';
		$price           = $post_metas['classified_price'] ?? array();
		$price           = $price['meta_value'] ?? '';
		$show_ad_id      = $args['classified_show_ad_id'] ?? 'yes';
		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ); ?>">
            <div class="classified-item-id-price">
                <h2 class="classified-item-price">
                    <i class="fa-solid fa-tag" aria-hidden="true"></i>
					<?php echo classified_build_price( $price ); ?>
                </h2>
				<?php if ( $show_ad_id == 'yes' ) { ?>
                    <p class="classified-item-id p-lg">
						<?php echo sprintf( esc_html__( "Ad id #%s", "classified-pro" ), $post_id ); ?>
                    </p>
				<?php } ?>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_classified_ad_title_and_desc' ) ) {
	function cube_classified_ad_title_and_desc( $args ) {
		global $post;
		$post_id          = $post->ID ?? get_the_ID();
		$container_class  = $args['container_class'] ?? '';
		$description_type = $args['classified_description_type'] ?? '';
		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ); ?>">
            <h1 class="classified-item-title"><?php echo get_the_title( $post_id ); ?></h1>
			<?php
			if ( $description_type == 'excerpt' ) {
				$post_desc = get_the_excerpt( $post_id );
			} else {
				$post_desc = classified_limit_str_by_words( get_the_content( $post_id ), 20 );
			}
			if ( $description_type != 'hide' ) {
				?>
                <p class="classified-item-desc p-md"><?php echo esc_html( strip_tags( $post_desc ) ); ?></p>
			<?php } ?>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_classified_ad_single_actions' ) ) {
	function cube_classified_ad_single_actions( $args ) {
		global $post;
		$post_id         = $post->ID ?? get_the_ID();
		$container_class = $args['container_class'] ?? '';
		$ad_report       = $args['classified_show_ad_report'] ?? 'yes';
		$ad_share        = $args['classified_show_ad_share'] ?? 'yes';
		$ad_save         = $args['classified_show_ad_save'] ?? 'yes';
		$SavedText       = esc_html__( "Save", "classified-pro" );
		$SavedClass      = 'cwp-save-post';
		if ( class_exists( 'CubeWp_Saved' ) ) {
			$SavedText  = CubeWp_Saved::is_cubewp_post_saved( $post_id, false, false );
			$SavedClass = CubeWp_Saved::is_cubewp_post_saved( $post_id, false );
		}
		ob_start();
		if ( $ad_share != 'yes' && $ad_report != 'yes' && $ad_save != 'yes' ) {
			return '';
		}
		?>
        <div class="classified-single-widget classified-quick-container d-flex <?php echo esc_attr( $container_class ); ?>">
			<?php
			if ( $ad_share == 'yes' ) {
				?>
                <div class="classified-quick d-inline-flex d-lg-flex classified-dropdown">
                    <i class="fa-solid fa-share" aria-hidden="true"></i>
                    <p><?php esc_html_e( "Share", "classified-pro" ); ?></p>
                    <div class="classified-dropdown-items classified-social-share">
						<?php echo classified_get_socials_share( $post_id ); ?>
                    </div>
                </div>
				<?php
			}
			if ( $ad_report == 'yes' ) {
				if ( is_user_logged_in() ) {
					$modal_target = '#classified-report-modal-' . $post_id;
				} else {
					$modal_target = '#classified-login-register';
				}
				?>
                <div class="classified-quick d-inline-flex d-lg-flex" type="button" data-bs-toggle="modal"
                     data-bs-target="<?php echo esc_attr( $modal_target ); ?>">
                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                    <p><?php esc_html_e( "Report", "classified-pro" ); ?></p>
                </div>
				<?php
			}
			if ( $ad_save == 'yes' ) {
				?>
                <div class="classified-quick d-inline-flex d-lg-flex classified-item-like-inner <?php echo esc_attr( $SavedClass ); ?>"
                     data-pid="<?php echo esc_attr( $post_id ); ?>">
                    <i class="fa-regular fa-heart" aria-hidden="true"></i>
                    <p class="cwp-saved-text"><?php echo esc_attr( $SavedText ); ?></p>
                </div>
			<?php } ?>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_author' ) ) {
	function cube_author( $args ) {
		global $post;
		$post_id                  = $post->ID ?? get_the_ID();
		$post_author              = classified_get_post_author( $post_id, false );
		$post_author_avatar       = classified_get_userdata( $post_author->ID, 'avatar' );
		$post_author_name         = classified_get_userdata( $post_author->ID, 'name' );
		$post_author_rating       = classified_get_userdata( $post_author->ID, 'rating' );
		$post_author_profile_link = classified_get_userdata( $post_author->ID, 'profile_link' );
		$post_author_website      = classified_get_userdata( $post_author->ID, 'website' );
		$post_author_phone        = classified_get_userdata( $post_author->ID, 'phone' );
		$join_period              = classified_get_userdata( $post_author->ID, 'join_period' );
		$inbox_resp               = classified_get_userdata( $post_author->ID, 'inbox_response' );
		$resp_time                = $inbox_resp['response_time'];
		$country                  = classified_get_userdata( $post_author->ID, 'country' );
		$country                  = ! empty( $country ) ? $country : esc_html__( "--", "classified-pro" );
		$container_class          = $args['container_class'] ?? '';
		$author_website           = $args['classified_show_author_website'] ?? 'yes';
		$author_phone             = $args['classified_show_author_phone'] ?? 'yes';
		$author_stats             = $args['classified_show_author_stats'] ?? 'yes';
		$style                    = 'style2';
		ob_start();
		?>
        <div class="classified-single-widget pb-0 <?php echo esc_attr( $container_class ); ?>">
            <div class="classified-seller-details">
                <a href="<?php echo esc_url( $post_author_profile_link ) ?>">
                    <img loading="lazy" width="100%" height="100%" src="<?php echo esc_url( $post_author_avatar ) ?>"
                         alt="<?php echo sprintf( esc_html__( " Avatar of %s", "classified-pro" ), $post_author_name ); ?>">
                </a>
                <div>
                    <a href="<?php echo esc_url( $post_author_profile_link ) ?>">
                        <h5 class="mb-1">
                            <?php
                            echo esc_html( $post_author_name );
                            if ( classified_is_user_email_verified( $post_author->ID ) ) {
                                ?>
                                <i class="fa-regular fa-circle-check" aria-hidden="true"
                                   data-classified-tooltip="true"
                                   data-bs-placement="right"
                                   title="<?php esc_html_e( 'Verified', 'classified-pro' ); ?>"></i>
                                <?php
                            }
                            ?>
                        </h5>
                    </a>
					<?php
					if ( $post_author_rating ) {
						?>
                        <p><?php echo esc_html( $post_author_rating ) ?> <i class="fa-solid fa-star" aria-hidden="true"></i></p>
						<?php
					}
					?>
                </div>
				<?php if ( classified_is_inbox_active() ) { ?>
                    <button class="classified-not-filled-btn classified-seller-chat" type="button">
                        <i class="fa-regular fa-message" aria-hidden="true"></i>
						<?php esc_html_e( "Let’s Chat", "classified-pro" ) ?>
                    </button>
                    <div class="classified-seller-chat-form">
						<?php
						echo do_shortcode( '[cwpInboxForm]' );
						?>
                    </div>
				<?php } ?>
            </div>
			<?php
			if ( $style == 'style1' ) {
				if ( ! empty( $post_author_website ) && $author_website == 'yes' ) {
					?>
                    <div class="classified-seller-additional-details">
                        <h6>
                            <i class="fa-solid fa-link" aria-hidden="true"></i>
							<?php esc_html_e( "Visit Website", "classified-pro" ) ?>
                        </h6>
                        <p class="p-md position-relative">
                            <a target="_blank" class="stretched-link"
                               href="<?php echo esc_url( $post_author_website ) ?>"></a>
							<?php echo str_replace( 'http://', '', str_replace( 'https://', '', $post_author_website ) ) ?>
                            <span class="classified-single-copy-content"
                                  data-text="<?php esc_html_e( " link Copied", "classified-pro" ) ?>">
                            <?php esc_html_e( "Copy link", "classified-pro" ) ?>
                        </span>
                        </p>
                    </div>
					<?php
				}
				if ( ! empty( $post_author_phone ) && $author_phone == 'yes' ) {
					?>
                    <div class="classified-seller-additional-details">
                        <h6>
                            <i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i>
							<?php esc_html_e( "Phone Number", "classified-pro" ) ?>
                        </h6>
                        <p class="p-md position-relative">
                            <a target="_blank" class="stretched-link"
                               href="tel:<?php echo esc_html( $post_author_phone ) ?>"></a>
							<?php echo esc_html( $post_author_phone ) ?>
                            <span class="classified-single-copy-content"
                                  data-text="<?php esc_html_e( " Number Copied", "classified-pro" ) ?>">
                            <?php esc_html_e( "Copy Number", "classified-pro" ) ?>
                        </span>
                        </p>
                    </div>
					<?php
				}
			} else {
				if ( ! empty( $post_author_website ) && $author_website == 'yes' ) {
					?>
                    <div class="classified-seller-detail-style2">
                        <a class="d-flex align-items-center" href="<?php echo esc_url( $post_author_website ); ?>"
                           target="_blank">
                            <i class="fa-solid fa-globe me-1" aria-hidden="true"></i>
                            <p><?php esc_html_e( 'Website', 'classified-pro' ); ?></p>
                        </a>
                        <button class="position-relative classified-not-filled-btn classified-show-value"
                                data-show-value="<?php echo str_replace( 'http://', '', str_replace( 'https://', '', $post_author_website ) ) ?>"
                                data-show-text="<?php esc_html_e( 'Visit', 'classified-pro' ); ?>">
							<?php esc_html_e( 'Show', 'classified-pro' ); ?>
                            <a href="<?php echo esc_html( $post_author_website ); ?>" class="stretched-link"
                               target="_blank"></a>
                        </button>
                    </div>
					<?php
				}
				if ( ! empty( $post_author_phone ) && $author_phone == 'yes' ) {
					?>
                    <div class="classified-seller-detail-style2">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-mobile-screen-button me-1" aria-hidden="true"></i>
                            <p>
								<?php
								echo esc_html( substr( $post_author_phone, 0, 3 ) . '********' );
								?>
                            </p>
                        </div>
                        <button class="position-relative classified-not-filled-btn classified-show-value"
                                data-show-value="<?php echo esc_html( $post_author_phone ); ?>"
                                data-show-text="<?php esc_html_e( 'Call', 'classified-pro' ); ?>">
							<?php esc_html_e( 'Show', 'classified-pro' ); ?>
                            <a href="tel:<?php echo esc_html( $post_author_phone ); ?>" class="stretched-link"></a>
                        </button>
                    </div>
					<?php
				}
			}
			if ( $author_stats == 'yes' ) {
				?>
                <div class="classified-seller-stats">
                    <div class="classified-seller-stat">
                        <p><?php esc_html_e( "Member Since", "classified-pro" ) ?></p>
                        <h3><?php echo esc_html( $join_period ) ?></h3>
                    </div>
                    <div class="classified-seller-stat">
                        <p><?php esc_html_e( "Typically Replies In", "classified-pro" ) ?></p>
                        <h3><?php echo esc_html( $resp_time ) ?></h3>
                    </div>
                    <div class="classified-seller-stat">
                        <p><?php esc_html_e( "From", "classified-pro" ) ?></p>
                        <h3><?php echo esc_html( $country ) ?></h3>
                    </div>
                </div>
			<?php } ?>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_classified_ad_single_stats' ) ) {
	function cube_classified_ad_single_stats( $args ) {
		$container_class  = $args['container_class'] ?? '';
		$posted_duration  = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) );
		$updated_duration = human_time_diff( get_the_modified_time( 'U' ), current_time( 'timestamp' ) );
		$post_id          = get_the_ID();
		$ad_views         = $args['classified_show_ad_views'] ?? 'yes';
		$ad_posted        = $args['classified_show_ad_posted_date'] ?? 'yes';
		$ad_updated       = $args['classified_show_ad_updated_date'] ?? 'yes';
		$post_views       = classified_get_post_views( $post_id );
		if ( $ad_views != 'yes' && $ad_posted != 'yes' && $ad_updated != 'yes' ) {
			return '';
		}
		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ); ?> classified-section-details">
            <ul class="classified-section-fields classified-item-stats justify-content-between">
				<?php
				if ( $ad_posted == 'yes' ) {
					?>
                    <li class="classified-section-field classified-field-no-style">
                        <h6><?php esc_html_e( 'Posted', 'classified-pro' ); ?></h6>
                        <p class="p-lg"><?php echo sprintf( esc_html__( '%s ago', 'classified-pro' ), $posted_duration ); ?></p>
                    </li>
					<?php
				}
				if ( $ad_updated == 'yes' ) {
					if ( $ad_views == 'yes' && $ad_posted == 'yes' ) {
						echo '<hr class="d-none d-xl-block">';
					}
					?>
                    <li class="classified-section-field classified-field-no-style justify-content-end">
                        <h6><?php esc_html_e( 'Updated', 'classified-pro' ); ?></h6>
                        <p class="p-lg"><?php echo sprintf( esc_html__( '%s ago', 'classified-pro' ), $updated_duration ); ?></p>
                    </li>
					<?php
					if ( $ad_views == 'yes' && $ad_posted == 'yes' ) {
						echo '<hr class="d-none d-xl-block">';
					}
				}
				if ( $ad_views == 'yes' ) {
					?>
                    <li class="classified-section-field classified-field-no-style justify-content-end">
                        <h6><?php esc_html_e( 'Views', 'classified-pro' ); ?></h6>
                        <p class="p-lg"><?php echo esc_html( $post_views ); ?></p>
                    </li>
					<?php
				}
				?>
            </ul>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_classified_ad_single_property_type' ) ) {
	function cube_classified_ad_single_property_type( $args ) {
		$container_class = $args['container_class'] ?? '';
		$post_id         = get_the_ID();
		$purpose         = get_post_meta( $post_id, 'classified_property_ad_purpose', true );
		$tag_status      = 'classified-status-tag-unavailable';
		if ( $purpose == 'sale' ) {
			$tag_status = 'classified-status-tag-success';
		} else if ( $purpose == 'rent' ) {
			$tag_status = 'classified-status-tag-danger';
		} else if ( $purpose == 'sold' ) {
			$tag_status = 'classified-status-tag-gold';
		}

		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ); ?>">
            <div class="classified-property-item-type-status">
                <i class="classified-status-tag <?php echo esc_html( $tag_status ); ?>" aria-hidden="true"></i>
                <h3><?php echo esc_html( $purpose ); ?></h3>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_classified_ad_single_wordpress_sidebar' ) ) {
	function cube_classified_ad_single_wordpress_sidebar( $args ) {
		$container_class = $args['container_class'] ?? '';
		$post_type       = get_post_type();
		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ); ?>">
			<?php
			dynamic_sidebar( 'classified_single_sidebar_' . str_replace( '-', '_', $post_type ) );
			?>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_classified_ad_single_quick_tip' ) ) {
	function cube_classified_ad_single_quick_tip( $args ) {
		$container_class = $args['container_class'] ?? '';
		$tip_title       = $args['classified_quick_tip_title'] ?? esc_html__( 'Safety Tips', 'classified-pro' );
		$tip_desc        = $args['classified_quick_tip_desc'] ?? '';
		$tip_link_text   = $args['classified_quick_tip_link_text'] ?? '';
		$tip_link        = $args['classified_quick_tip_link'] ?? '';
		$tip_icon        = $args['classified_quick_tip_icon'] ?? '#';

		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ) ?>">
            <div class="classified-announcement">
                <div class="classified-announcement-icon">
                    <i class="<?php echo esc_attr( $tip_icon ) ?>" aria-hidden="true"></i>
                </div>
                <div class="classified-announcement-details">
                    <h5><?php echo esc_html( $tip_title ) ?></h5>
                    <p><?php echo esc_html( $tip_desc ) ?></p>
					<?php if ( ! empty( $tip_link_text ) ) { ?>
                        <a href="<?php echo esc_url( $tip_link ) ?>"><?php echo esc_html( $tip_link_text ) ?></a>
					<?php } ?>
                </div>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'cube_classified_ad_single_sections_tabs' ) ) {
	function cube_classified_ad_single_sections_tabs( $args ) {
		global $cubewp_frontend;
		$single          = $cubewp_frontend->single();
		$single_sections = $single::get_single_content_options();
		$container_class = $args['container_class'] ?? '';
		if ( empty( $single_sections ) || ! is_array( $single_sections ) || count( $single_sections ) < 2 ) {
			return '';
		}
		$html       = '<div class="classified-tabs-container ' . $container_class . '">
        <div class="row">';
		$post_metas = $cubewp_frontend->post_metas( get_the_ID() );
		foreach ( $single_sections as $single_section ) {
			$continue = false;
			if ( empty( $single_section['fields'] ) ) {
				continue;
			}
			foreach ( $single_section['fields'] as $key => $field ) {
				if ( ! is_array( $field ) ) {
					$field = get_field_options( $field );
				}
				$field_type = $field["type"] ?? "";
				$meta_key   = $field["name"] ?? "";
				if ( $field_type != 'google_address' && $field_type != 'classified_ad_single_sections_tabs' ) {
					$value = $post_metas[ $meta_key ]['meta_value'] ?? '';
					if ( empty( $value ) ) {
						$continue = true;
						break;
					}
				}
				if ( $key == $args['type'] ) {
					$continue = true;
					break;
				}
			}
			if ( $continue ) {
				continue;
			}
			$single_section_id    = $single_section['section_id'];
			$single_section_title = $single_section['section_title'];
			$html                 .= '<div class="classified-tab col-auto text-center">
                <a href="#classified-item-section-' . $single_section_id . '" class="stretched-link">' . $single_section_title . '</a>
            </div>';
		}
		$html .= '</div>
		</div>';

		return $html;
	}
}

if ( ! function_exists( 'cube_classified_ad_the_content' ) ) {
	function cube_classified_ad_the_content( $args ) {
		$container_class = $args['container_class'] ?? '';
		ob_start();
		?>
        <div class="classified-single-widget <?php echo esc_attr( $container_class ); ?> classified-section-details">
            <div class="classified-item-details">
				<?php the_content(); ?>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'classified_single_author_items' ) ) {
	function classified_single_author_items( $post_id = 0 ) {
		$classified_author_items = classified_get_setting( 'classified_author_items' );
		if ( ! $classified_author_items ) {
			return '';
		}
		global $post;
		$posts_per_page         = classified_get_setting( 'classified_author_items_to_show' );
		$posts_per_page         = ! empty( $posts_per_page ) ? $posts_per_page : '-1';
		$post_id                = $post_id != 0 ? $post_id : $post->ID;
		$post_type              = get_post_type( $post_id );
		$post_author            = classified_get_post_author( $post_id );
		$args                   = array();
		$args['post_type']      = $post_type;
		$args['posts_per_page'] = $posts_per_page;
		$args['author']         = $post_author;
		$args['post__not_in']   = array( $post_id );
		$query                  = ( new Classified_Query() )->Query( $args );
		ob_start();
		if ( $query->have_posts() ) {
			?>
            <div class="classified-single-section classified-without-styling-section classified-section-details classified-static-section"
                 id="classified-item-section-author-items">
                <h2 class="classified-section-title"><?php esc_html_e( 'Other items by seller', 'classified-pro' ); ?></h2>
                <div class="classified-single-widget">
                    <div class="classified-items-slider" data-slides-to-show="4">
						<?php
						while ( $query->have_posts() ) {
							$query->the_post();
							?>
                            <div class="classified-items-slider-slide">
								<?php
								set_query_var( 'col_class', 'col-12' );
								set_query_var( 'recommended', false );
								set_query_var( 'boosted', false );
								get_template_part( 'templates/loop/loop-views' );
								?>
                            </div>
							<?php
						}
						?>
                    </div>
                </div>
            </div>
			<?php
		}
		wp_reset_postdata();
		wp_reset_query();

		return ob_get_clean();
	}
}

if ( ! function_exists( 'classified_single_related_items' ) ) {
	function classified_single_related_items( $post_id = 0 ) {
		$classified_related_items = classified_get_setting( 'classified_related_items' );
		if ( ! $classified_related_items ) {
			return '';
		}
		global $post;
		$posts_per_page    = classified_get_setting( 'classified_related_items_to_show' );
		$posts_per_page    = ! empty( $posts_per_page ) ? $posts_per_page : '-1';
		$post_id           = $post_id != 0 ? $post_id : $post->ID;
		$post_type         = get_post_type( $post_id );
		$category_taxonomy = $post_type . '_category';
		$locations         = get_the_terms( $post_id, 'locations' );
		$categories        = get_the_terms( $post_id, $category_taxonomy );
		$args_categories   = array();
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$args_categories[] = $category->term_id;
			}
		}
		$args_locations = array();
		if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) {
			foreach ( $locations as $location ) {
				$args_locations[] = $location->term_id;
			}
		}
		$args                     = array();
		$args['post_type']        = $post_type;
		$args['posts_per_page']   = $posts_per_page;
		$args['post__not_in']     = array( $post_id );
		$args['categories_terms'] = $args_categories;
		$args['locations_terms']  = $args_locations;
		$query                    = ( new Classified_Query() )->Query( $args );
		ob_start();
		if ( $query->have_posts() ) {
			?>
            <div class="classified-single-section classified-without-styling-section classified-section-details classified-static-section"
                 id="classified-item-section-related-items">
                <h2 class="classified-section-title"><?php esc_html_e( 'Related Items', 'classified-pro' ); ?></h2>
                <div class="classified-single-widget">
                    <div class="classified-items-slider" data-slides-to-show="4">
						<?php
						while ( $query->have_posts() ) {
							$query->the_post();
							?>
                            <div class="classified-items-slider-slide">
								<?php
								set_query_var( 'col_class', 'col-12' );
								set_query_var( 'recommended', false );
								set_query_var( 'boosted', false );
								get_template_part( 'templates/loop/loop-views' );
								?>
                            </div>
							<?php
						}
						?>
                    </div>
                </div>
            </div>
			<?php
		}
		wp_reset_postdata();
		wp_reset_query();

		return ob_get_clean();
	}
}