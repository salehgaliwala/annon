<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Pricing Plans.
 *
 * @class Classified_Pricing_Plans_Shortcode
 */
class Classified_Pricing_Plans_Shortcode {
	public function __construct() {
		global $classified_post_types;
		if ( empty( $classified_post_types ) ) {
			return false;
		}
		foreach ( $classified_post_types as $post_type ) {
			$post_type_filter = str_replace( '-', '_', trim( $post_type ) );
			if ( method_exists( $this, "classified_{$post_type_filter}_pricing_plans" ) ) {
				add_filter( "cubewp/frontend/{$post_type}/pricing_plan/template", array(
					$this,
					"classified_{$post_type_filter}_pricing_plans"
				), 11, 6 );
			}
			add_filter( "cubewp/frontend/{$post_type}/pricing_plan/submit", array(
				$this,
				'classified_pricing_plan_submit'
			), 11, 2 );
		}
	}

	public function classified_pricing_plan_submit( $output, $price_plan ) {
		//var_dump($price_plan);
		return '<button id="add-to-cart-button" class="d-block w-100 classified-filled-btn" data-product-id="'.get_post_meta($price_plan , 'plan_duration_type', true).'">' . esc_html__( "Get Started", "cubewp-classified" ) . '</button>';
	}

	public function classified_classified_ad_pricing_plans( $output, $price_plan, $plan_metas, $plan_submit, $plan_options, $atts ) {
		return self::classified_pricing_plans( $price_plan, $plan_submit, $plan_options, $atts );
	}

	public static function classified_pricing_plans( $price_plan, $plan_submit, $plan_options, $atts ) {
		extract( shortcode_atts( array(
			'column_per_row' => 4,
		), $atts ) );
		wp_enqueue_style( 'classified-shortcode-pricing-plans' );
		$plan_price = get_post_meta( $price_plan, 'plan_price', true );
		$plan_duration = get_post_meta( $price_plan, 'plan_duration', true );
		$plan_type = get_post_meta( $price_plan, 'plan_type', true );
		$no_of_posts = get_post_meta( $price_plan, 'no_of_posts', true );
		if ( $plan_duration > 0 ) {
			$plan_duration = sprintf( esc_html__( "%s Days", "cubewp-classified" ), $plan_duration );
		} else {
			$plan_duration = esc_html__( "Unlimited", "cubewp-classified" );
		}
		if ( ! is_numeric( $no_of_posts ) || empty( $no_of_posts ) ) {
			$no_of_posts = esc_html__( "Unlimited", "cubewp-classified" );
		}
		if ( $column_per_row == 1 ) {
			$col_class = 'col-12';
		} else if ( $column_per_row == 2 ) {
			$col_class = 'col-12 col-md-6';
		} else if ( $column_per_row == 3 ) {
			$col_class = 'col-12 col-md-4';
		} else if ( $column_per_row == 4 ) {
			$col_class = 'col-12 col-md-3';
		} else if ( $column_per_row == 6 ) {
			$col_class = 'col-12 col-md-2';
		} else {
			$col_class = 'col-12 col-md-4';
		}
		ob_start();
		?>
        <div class="<?php echo esc_attr( $col_class ); ?>">
            <div class="classified-plan">
                <h6 class="classified-plan-title"><?php echo get_the_title( $price_plan ); ?></h6>
				<?php
				if ( $plan_type == 'package' ) {
					?>
                    <h1 class="classified-plan-pricing">
                        <sup><?php echo classified_build_price( $plan_price, true ) ?></sup><?php echo classified_build_price( $plan_price, false, false ) ?>
                        <sub><?php echo sprintf( esc_html__( "/%s Listings", "cubewp-classified" ), $no_of_posts ); ?></sub>
                    </h1>
					<?php
				} else {
					?>
                    <h1 class="classified-plan-pricing">
                        <sup><?php echo classified_build_price( $plan_price, true ) ?></sup><?php echo classified_build_price( $plan_price, false, false ) ?>
                        <sub><?php esc_html_e( "/Per Listing", "cubewp-classified" ); ?></sub></h1>
					<?php
				}
				?>
				<?php echo cubewp_core_data( $plan_submit ); ?>
                <ul class="classified-plan-features">
                    <li class="classified-plan-feature">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
						<?php echo sprintf( esc_html__( "Duration: %s", "cubewp-classified" ), $plan_duration ) ?>
                    </li>
					<?php
					if ( isset( $plan_options[ $price_plan ] ) && ! empty( $plan_options[ $price_plan ] ) && is_array( $plan_options[ $price_plan ] ) ) {
						foreach ( $plan_options[ $price_plan ] as $option => $available ) {
							$feature_class = 'classified-plan-feature';
							if ( $available == 'no' ) {
								$feature_class .= ' classified-plan-feature-not-available';
								$icon_class    = 'fa-regular fa-circle-xmark';
							} else {
								$feature_class .= ' classified-plan-feature-available';
								$icon_class    = 'fa-regular fa-circle-check';
							}
							?>
                            <li class="<?php echo esc_attr( $feature_class ); ?>">
                                <i class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
								<?php echo esc_html( $option ); ?>
                            </li>
							<?php
						}
					}
					?>
                </ul>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}

	public function classified_real_estate_pricing_plans( $output, $price_plan, $plan_metas, $plan_submit, $plan_options, $atts ) {
		return self::classified_pricing_plans( $price_plan, $plan_submit, $plan_options, $atts );
	}

	public function classified_automotive_pricing_plans( $output, $price_plan, $plan_metas, $plan_submit, $plan_options, $atts ) {
		return self::classified_pricing_plans( $price_plan, $plan_submit, $plan_options, $atts );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}