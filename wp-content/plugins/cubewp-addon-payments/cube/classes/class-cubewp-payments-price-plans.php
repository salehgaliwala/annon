<?php

/**
 * CubeWP Pricing plan shortcode.
 *
 * @package cubewp-addon-payments/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Payments_Price_Plans
 */
class CubeWp_Payments_Price_Plans {
	
	/**
	 * Method __construct
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'cwpPricingPlans', array( $this, 'cubewp_pricing_plans' ) );
		add_filter( "cubewp/frontend/single/pricing_plan", array($this, 'cubewp_get_plan_template'), 9, 5 );
	}
	
	/**
	 * Method init
	 *
	 * @return void
	 */
	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
	
	/**
	 * Method cubewp_pricing_plans
	 *
	 * @param array $atts 
	 * @param null $content 
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function cubewp_pricing_plans( $atts, $content = null ) {
		extract( shortcode_atts( array(
		   'post_type' => '',
		), $atts ) );
		$post_type_get = isset($_GET['cwp_ptype']) && !empty($_GET['cwp_ptype']) ? $_GET['cwp_ptype'] : '';
		$post_type = !empty($post_type) ? $post_type : $post_type_get;
		wp_enqueue_style( 'cubewp-plans' );
		$output = null;
		if ( isset( $post_type ) && $post_type != '' ) {
		   $submit_page = $this->cubewp_get_post_type_submit_page( $post_type );
		   $output      .= $this->cubewp_pricing_plans_list( $submit_page, $post_type, $atts );
		} else {
		   $cwp_custom_types = CWP_all_post_types();
		   if ( isset( $cwp_custom_types ) && ! empty( $cwp_custom_types ) ) {
			  foreach ( $cwp_custom_types as $slug => $label ) {
				 $submit_page = $this->cubewp_get_post_type_submit_page( $slug );
				 $output      .= $this->cubewp_pricing_plans_list( $submit_page, $slug, $atts );
			  }
		   }
		}
	 
		return $output;
	 }
	
	/**
	 * Method cubewp_get_post_type_submit_page
	 *
	 * @param string $post_type
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cubewp_get_post_type_submit_page( $post_type = '' ) {
		global $wpdb, $cwpOptions;

		if ( $post_type != '' ) {
			$result = $wpdb->get_row( "SELECT " . $wpdb->prefix . "posts.ID FROM " . $wpdb->prefix . "posts
                INNER JOIN " . $wpdb->prefix . "postmeta ON " . $wpdb->prefix . "postmeta.post_id = " . $wpdb->prefix . "posts.ID
                WHERE (" . $wpdb->prefix . "posts.post_content LIKE '%[cwpForm type=\"{$post_type}\"]%' OR " . $wpdb->prefix . "postmeta.meta_value = '%[cwpForm type=\"{$post_type}\"]%') AND " . $wpdb->prefix . "posts.post_type='page' ORDER BY " . $wpdb->prefix . "posts.ID DESC", ARRAY_A );
			if ( isset( $result['ID'] ) && ! empty( $result['ID'] ) ) {
				return get_permalink( $result['ID'] );
			} else {
				return isset( $cwpOptions['submit_edit_page'][ $post_type ] ) ? $cwpOptions['submit_edit_page'][ $post_type ] : '';
			}
		} else {
			return isset( $cwpOptions['submit_edit_page'][ $post_type ] ) ? $cwpOptions['submit_edit_page'][ $post_type ] : '';
		}
	}
	
	/**
	 * Method cubewp_pricing_plans_list
	 *
	 * @param string $submit_page 
	 * @param string $post_type
	 * @param array $atts
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function cubewp_pricing_plans_list( $submit_page, $post_type = '', $atts = array() ) {
		global $cwpOptions;
		$output           = '';
		$query_args       = array(
			'post_type'      => 'price_plan',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'meta_key'       => 'plan_price',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => 'plan_post_type',
					'value'   => $post_type,
					'compare' => '=',
				)
			),
		);
		$plan_options = array();
		$price_plans      = get_posts( $query_args );
		$submit_edit_post = isset( $cwpOptions['submit_edit_page'][ $post_type ] ) ? $cwpOptions['submit_edit_page'][ $post_type ] : '';
		if ( $submit_page != '' && is_numeric($submit_edit_post) ) {
			$submit_page = get_permalink($submit_edit_post);
		 }
		if ( isset( $price_plans ) && ! empty( $price_plans ) ) {
			$plan_options = $this->cubewp_get_plan_options_list( $price_plans, $post_type );
			$post_type_obj = get_post_type_object( $post_type ); 
			$output       .= '<div class="cwp-container cwp-plans-container">
			<h5>' . __( "Plans for", "cubewp-payments" ) . ' '. $post_type_obj->label .'</h5>
			<div class="cwp-row">';
			foreach ( $price_plans as $price_plan_id ) {
				$output .= apply_filters( "cubewp/frontend/single/pricing_plan", '', $price_plan_id, $submit_page, $plan_options, $atts);
			}
			$output .= '</div>
							</div>';
		}

		return apply_filters( "cubewp/frontend/{$post_type}/pricing_plans", $output, $price_plans, $submit_page, $post_type, $plan_options, $atts );
	}

	/**
	 * Method cubewp_get_plan_options_list
	 *
	 * @param array $price_plans 
	 * @param string $post_type
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function cubewp_get_plan_options_list( $price_plans = array(), $post_type = '' ) {
		$cwpform_post_types = CWP()->get_form( 'post_type' );
		$plans_fields       = $all_fields = $plan_options = array();
		if ( isset( $price_plans ) && ! empty( $price_plans ) ) {
		foreach ( $price_plans as $plan_id ) {
			$groups = $cwpform_post_types[ $post_type ][ $plan_id ]['groups'] ?? array();
			if ( isset( $groups ) && ! empty( $groups ) ) {
				foreach ( $groups as $group ) {
					if ( isset( $group['fields'] ) && ! empty( $group['fields'] ) ) {
					foreach ( $group['fields'] as $field ) {
						if ( isset( $field['show_in_plan'] ) && $field['show_in_plan'] ) {
							$plans_fields[ $plan_id ][ $field['name'] ] = $field['label'];
						}else {
							$plans_fields[ $plan_id ][ $field['name'] ] = false;
						}
						$all_fields[ $field['name'] ]               = $field['label'];
					}
					}
				}
			}
		}
		if ( isset( $all_fields ) && ! empty( $all_fields ) ) {
			foreach ( $price_plans as $plan_id ) {
				foreach ( $all_fields as $key => $label ) {
					if ( isset( $plans_fields[ $plan_id ][ $key ] ) ) {
					if ( $plans_fields[ $plan_id ][ $key ] ) {
						$plan_options[ $plan_id ][ $label ] = 'yes';
					}
					} else {
					$plan_options[ $plan_id ][ $label ] = 'no';
					}
				}
			}
		}
		}

		return $plan_options;
	}
	
	/**
	 * Method cubewp_get_plan_template
	 *
	 * @param int $price_plan_id [
	 * @param string $post_type 
	 * @param string $submit_page 
	 * @param string $plan_options 
	 * @param array $atts
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function cubewp_get_plan_template( $empty, $price_plan_id, $submit_page, $plan_options, $atts ) {
		extract( shortcode_atts( array(
			'column_per_row' => 3,
		), $atts ) );
		$plan_metas = array();
		$plan_hot           = get_post_meta( $price_plan_id, 'plan_hot', true );
		$plan_image         = get_post_meta( $price_plan_id, 'plan_image', true );
		$plan_text          = get_post_meta( $price_plan_id, 'plan_text', true );
		$plan_price         = get_post_meta( $price_plan_id, 'plan_price', true );
		$plan_duration_type = get_post_meta( $price_plan_id, 'plan_duration_type', true );
		$plan_duration      = get_post_meta( $price_plan_id, 'plan_duration', true );
		$plan_post_type     = get_post_meta( $price_plan_id, 'plan_post_type', true );
		$plan_type          = get_post_meta( $price_plan_id, 'plan_type', true );
		$no_of_posts        = get_post_meta( $price_plan_id, 'no_of_posts', true );
		$plan_metas = array(
			'plan_hot' => $plan_hot,
			'plan_image' => $plan_image,
			'plan_text' => $plan_text,
			'plan_price' => $plan_price,
			'plan_duration_type' => $plan_duration_type,
			'plan_duration' => $plan_duration,
			'plan_post_type' => $plan_post_type,
			'plan_type' => $plan_type,
			'no_of_posts' => $no_of_posts,
		);
		if ( $plan_duration_type == 'per_year' ) {
			$cwp_plan_duration = __( "/year", "cubewp-payments" );
		} else if ( $plan_duration_type == 'per_month' ) {
			$cwp_plan_duration = __( "/month", "cubewp-payments" );
		} else if ( $plan_duration_type == 'per_days' ) {
			$cwp_plan_duration = sprintf( __( "%s Days", "cubewp-payments" ), $plan_duration );
		} else {
			$cwp_plan_duration = __( "Lifetime", "cubewp-payments" );
		}
		$plan_image_src = wp_get_attachment_image_src( $plan_image, 'full' );
		$plan_submit    = $this->cubewp_get_plan_proceed_btn( $price_plan_id, $submit_page, $plan_post_type );
		$output         = '';
		if ( $column_per_row == 1 ) {
			$col_class = 'cwp-col-12';
		} else if ( $column_per_row == 2 ) {
			$col_class = 'cwp-col-12 cwp-col-md-6';
		} else if ( $column_per_row == 3 ) {
			$col_class = 'cwp-col-12 cwp-col-md-4';
		} else if ( $column_per_row == 4 ) {
			$col_class = 'cwp-col-12 cwp-col-md-3';
		} else if ( $column_per_row == 6 ) {
			$col_class = 'cwp-col-12 cwp-col-md-2';
		} else {
			$col_class = 'cwp-col-12 cwp-col-md-4';
		}
		$plan_hot_class = '';
		if ( $plan_hot == 'yes' || $plan_hot == 'Yes' ) {
			$plan_hot_class = 'cwp-hot-plan';
		}
		$output .= '<div class="' . $col_class . '">
		<div class="cwp-plan ' . $plan_hot_class . '">';
		if ( $plan_hot == 'yes' || $plan_hot == 'Yes' ) {
			$output .= '<div class="cwp-featured-plan">' . __( "Best Seller", "cubewp-payments" ) . '</div>';
		}
		$output .= '<div class="cwp-plans-image">
                        <div class="plan-img">';
		if ( ! empty ( $plan_image ) ) {
			$output .= '<img src="' . $plan_image_src[0] . '" alt="' . esc_html__( "Plan Featured Image", "cubewp-payments" ) . '">';
		} else {
			$output .= '<img src="' . CUBEWP_PAYMENTS_PLUGIN_URL . 'cube/assets/frontend/images/cube_1.png" alt="' . esc_html__( "Plan Featured Image", "cubewp-payments" ) . '">';
		}
		$output .= '</div>
                    </div>';
		$output .= '<div class="cwp-plan-title-des">
                        <h3 class="cwp-plan-title">' . esc_html( get_the_title( $price_plan_id ) ) . '</h3>
                        <p class="cwp-plan-des">' . $plan_text . '</p>
                    </div>';
		$output .= '<div class="cwp-plan-type-container">';
		if ( $plan_type == 'package' ) {
			$output .= '<span class="cwp-plan-type">' . __( "Per Package", "cubewp-payments" ) . '</span>';
		} else {
			$output .= '<span class="cwp-plan-type">' . sprintf( __( "Per %s", "cubewp-payments" ), $plan_post_type ) . '</span>';
		}
		$output .= '</div>';
		$output .= '<div class="cwp-plan-price-days">
                        <h2 class="cwp-plan-price">' . cubewp_price( $plan_price ) . '</h2>
                        <h4 class="cwp-plan-days">' . $cwp_plan_duration . '</h4>';
		$output .= '</div>';
		$output .= '<div class="cwp-plan-features">
		<ul class="cwp-plan-options">';
		if ( $plan_type == 'package' ) {
			if ( $no_of_posts == '' ) {
				$no_of_posts = __( "Unlimited", "cubewp-payments" );
			}
			$output .= '<li class="cwp-plan-option">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" fill="#007bff" viewBox="0 0 16 16">
                <path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5z"/>
                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
            </svg>
				' . sprintf( __( "Max. Posts: %s", "cubewp-payments" ), $no_of_posts ) . '
			</li>';
		}
		if ( isset( $plan_options[ $price_plan_id ] ) && ! empty( $plan_options[ $price_plan_id ] ) ) {
			foreach ( $plan_options[ $price_plan_id ] as $label => $check ) {
				$option_class = '';
				if ( $check == 'no' ) {
					$option_class = ' disabled';
				}
				$output .= '<li class="cwp-plan-option' . $option_class . '">';
				if ( $check == 'yes' ) {
					$output .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#007bff" viewBox="0 0 16 16">
					<path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
				  	</svg>';
				} else {
					$output .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
					<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
				 	</svg>';
				}
				$output .= $label . '</li>';
			}
		}
		$output .= '</ul>
		</div>';
		$output .= $plan_submit;
		$output .= '</div>
		</div>';

		return apply_filters( "cubewp/frontend/{$plan_post_type}/pricing_plan/template", $output, $price_plan_id, $plan_metas, $plan_submit, $plan_options, $atts );
	}
	
	/**
	 * Method cubewp_get_plan_proceed_btn
	 *
	 * @param int $price_plan_id 
	 * @param string $submit_page 
	 * @param string $plan_post_type 
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function cubewp_get_plan_proceed_btn( $price_plan_id, $submit_page, $plan_post_type ) {
		$output     = null;
		$output     .= '<form method="post" name="cwp-pan-from-' . esc_attr( $price_plan_id ) . '" action="' . esc_url( $submit_page ) . '" class="cwp-plan-btn">
			<input type="hidden" name="plan_id" value="' . esc_attr( $price_plan_id ) . '">
			<input type="hidden" name="type" value="' . esc_attr( $plan_post_type ) . '">';
		$submit_btn = '<input class="cwp-plan-submit" type="submit" value="' . esc_html( 'Get Started', 'cubewp-payments' ) . '" name="submit">';
		$output     .= apply_filters( "cubewp/frontend/{$plan_post_type}/pricing_plan/submit", $submit_btn, $price_plan_id );
		$output     .= '</form>';

		return $output;
	}
}