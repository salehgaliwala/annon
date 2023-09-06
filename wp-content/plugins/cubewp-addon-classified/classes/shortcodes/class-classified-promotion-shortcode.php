<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Items.
 *
 * @class Classified_Items_Shortcode
 */
class Classified_Promotion_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_promotion_shortcode', array( $this, 'classified_promotion_callback' ) );
		add_filter( 'classified_promotion_shortcode_output', array( $this, 'classified_promotion' ), 10, 2 );
	}

	public static function classified_promotion( $output, $parameters ) {
		$promotion_heading          = $parameters['promotion_heading'];
		$promotion_desc             = $parameters['promotion_desc'];
		$promotion_btn_text         = $parameters['promotion_btn_text'];
		$promotion_btn_url          = $parameters['promotion_btn_url'];
		$promotion_background_color = $parameters['promotion_background_color'];
		$promotion_background_image = $parameters['promotion_background_image'];
		$promotion_text_color       = $parameters['promotion_text_color'];
		$promotion_btn_bg           = $parameters['promotion_btn_bg'];
		$promotion_btn_text_color   = $parameters['promotion_btn_text_color'];
		$promotion_btn_url          = $promotion_btn_url['url'] ?? '';
		$promotion_background_image = $promotion_background_image['url'] ?? '';
		ob_start();
		?>
        <section class="classified-website-promotion-container"
                 style="background-color: <?php echo esc_attr( $promotion_background_color ); ?>;color: <?php echo esc_attr( $promotion_background_color ); ?>">
			<?php if ( ! empty( $promotion_background_image ) ) { ?>
                <img loading="lazy" width="100%" height="100%"
                     src="<?php echo esc_url( $promotion_background_image ); ?>"
                     alt="<?php esc_html_e( "Promo Background Image", "cubewp-classified" ); ?>"
                     class="classified-website-promotion-bg">
			<?php } ?>
            <div class="classified-website-promotion-content"
                 style="color: <?php echo esc_html( $promotion_text_color ); ?>;">
                <h4><?php echo esc_html( $promotion_heading ); ?></h4>
                <p class="p-lg"><?php echo esc_html( $promotion_desc ); ?></p>
                <style>
                    <?php
					$class = 'classified-temp-' . classified_rand();
					echo '.' . $class . ' {
						color: ' . $promotion_btn_text_color . ';
						background-color: ' . $promotion_btn_bg . ';
						border-color: ' . $promotion_btn_bg . ';
					}
					.' . $class . ':hover {
						color: ' . $promotion_btn_bg . ';
						background-color: ' . $promotion_btn_text_color . ';
						border-color: ' . $promotion_btn_bg . ';
					}';
					?>
                </style>
                <button class="classified-filled-btn position-relative <?php echo esc_attr( $class ); ?>">
                    <a href="<?php echo esc_url( $promotion_btn_url ); ?>" class="stretched-link"></a>
					<?php echo esc_html( $promotion_btn_text ); ?>
                </button>
            </div>
        </section>
		<?php

		return ob_get_clean();
	}

	public function classified_promotion_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_promotion_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}