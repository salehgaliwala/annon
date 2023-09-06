<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Submit Edit Class.
 *
 * @class Classified_Submit_Edit_Shortcode
 */
class Classified_Submit_Edit_Shortcode {
	private static $total_sections = 0;

	public function __construct() {
		global $classified_post_types;
		if ( empty( $classified_post_types ) ) {
			return false;
		}
		foreach ( $classified_post_types as $post_type ) {
			add_filter( "cubewp/frontend/form/{$post_type}/section/", function ( $output, $args ) use ( $post_type ) {
				return self::classified_submission_post_type_form_sections( $args, $post_type );
			}, 11, 2 );
			add_filter( "cubewp/frontend/form/{$post_type}/button", array(
				$this,
				"classified_submission_form_actions"
			), 11, 3 );
		}

		add_shortcode( 'classified_submission_shortcode', array( $this, 'classified_submission_shortcode_callback' ) );
		add_filter( 'classified_ads_submission_shortcode_output', array( $this, 'classified_submission' ), 10, 2 );
	}

	public function classified_submission_post_type_form_sections( $args, $post_type ) {
		$section_data = $args;
		if ( ! isset( $section_data['section_id'] ) ) {
			return '';
		}
		$section_id           = $section_data['section_id'];
		$section_title        = $section_data['section_title'] ?? '';
		$section_class        = $section_data['section_class'] ?? '';
		$section_counter      = $section_data['section_number'] ?? 1;
		$section_count        = $section_data['total_sections'] ?? 1;
		self::$total_sections = $section_count;
		$section_progress     = round( 100 / $section_count );
		$section_progress     = ( $section_progress * $section_counter ) - $section_progress;
		if ( $section_progress >= 98 ) {
			$section_progress = 100;
		}
		$section_class = 'classified-submission-form-section ' . $section_class;
		if ( $section_counter == '1' ) {
			$section_class .= ' classified-submission-form-section-active';
		}
		$html = '<div class="' . $section_class . '" id="' . $section_id . '" data-section-counter="' . $section_counter . '">';
		$html .= '<div class="classified-submission-form-section-info">';
		$html .= '<h6 class="classified-submission-form-section-title"><span class="classified-submission-form-section-count">' . $section_counter . '</span>' . $section_title . '</h6>';
		$html .= '<p class="classified-submission-form-section-progress p-md"> ' . sprintf( esc_html__( "%u%s Complete", "cubewp-classified" ), $section_progress, "%" ) . ' </p>';
		$html .= '<div class="classified-submission-form-section-steps d-none d-md-flex">';
		for ( $i = 1; $i <= $section_count; $i ++ ) {
			$step_class = 'col classified-submission-form-section-step';
			if ( $i <= $section_counter ) {
				$step_class .= ' classified-submission-form-section-step-active';
			}
			$html .= '<span class="' . $step_class . '"></span>';
		}
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div class="classified-submission-form-fields">';
		if ( isset( $section_data['fields'] ) && is_array( $section_data['fields'] ) && ! empty( $section_data['fields'] ) ) {
			$html .= apply_filters( "cubewp/frontend/form/{$post_type}/section/fields", '', $section_data['fields'], $section_data['post_content'] );
		} else {
			$html .= cwp_alert_ui( esc_html__( "Sorry! There is no fields within this section.", "cubewp-classified" ) );
		}
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public function classified_submission( $output, $parameters ) {
		$classified_post_type                   = $parameters['classified_post_type'];
		$classified_submission_sidebar          = $parameters['classified_submission_sidebar'];
		$classified_submission_live_preview     = $parameters['classified_submission_live_preview'];
		$classified_submission_quicktip         = $parameters['classified_submission_quicktip'];
		$classified_submission_quicktip_heading = $parameters['classified_submission_quicktip_heading'];
		$classified_submission_quicktip_desc    = $parameters['classified_submission_quicktip_desc'];
		ob_start();
		wp_enqueue_style( 'classified-submission-styles' );
		wp_enqueue_script( 'classified-submission-scripts' );

		$submission_class = '';
		if ( $classified_submission_sidebar == 'yes' ) {
			$submission_class = 'col-lg-8';
		}
		?>
        <section class="classified-submission-container">
            <div class="row">
                <div class="col-12 <?php echo esc_attr( $submission_class ); ?>"><?php echo do_shortcode( '[cwpForm type="' . $classified_post_type . '"]' ); ?></div>
				<?php if ( $classified_submission_sidebar == 'yes' ) { ?>
                    <div class="col-12 col-lg-4"><?php echo self::classified_submission_form_sidebar( $classified_post_type, $classified_submission_live_preview, $classified_submission_quicktip, $classified_submission_quicktip_heading, $classified_submission_quicktip_desc ); ?></div>
				<?php } ?>
            </div>
        </section>
		<?php

		return ob_get_clean();
	}

	public static function classified_submission_form_sidebar( $post_type, $live_preview = 'yes', $quick_tip = 'yes', $quick_tip_heading = '', $quick_tip_desc = '' ) {
		wp_enqueue_style( 'classified-loop-style1-styles' );
		$html = '<div class="classified-submission-form-sidebar">';
		if ( $quick_tip == 'yes' ) {
			$html .= '<div class="classified-submission-form-sidebar-tips">';
			$html .= '<h5>' . esc_html( $quick_tip_heading ) . '</h5>';
			$html .= '<p class="p-md">' . esc_html( $quick_tip_desc ) . '</p>';
			$html .= '</div>';
		}
		if ( $live_preview == 'yes' ) {
			$html .= self::classified_submission_form_sidebar_grid_template( $post_type );
			$html .= '<div class="classified-submission-form-sidebar-live-text">';
			$html .= '<i></i>';
			$html .= '<p>' . esc_html__( "Live Preview", "cubewp-classified" ) . '</p>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}

	public static function classified_submission_form_sidebar_grid_template( $post_type ) {
		ob_start();
		set_query_var( 'is_preview', true );
		set_query_var( 'current_post_type', $post_type );
		get_template_part( 'templates/loop/loop-views' );

		return ob_get_clean();
	}

	public function classified_submission_shortcode_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_ads_submission_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}

	public function classified_submission_form_actions( $output, $btn_text, $btn_class ) {
		if ( ! isset( $btn_class ) ) {
			return $output;
		}
		$submit_disabled = 'disabled';
		$submit_text     = esc_html__( "Post anyway", "cubewp-classified" );
		if ( isset( $_GET['pid'] ) ) {
			$submit_disabled = '';
			$submit_text     = esc_html__( "Save changes", "cubewp-classified" );
		}
		$html = '<div class="classified-submission-form-actions">';
		if ( self::$total_sections > 1 ) {
			$html .= '<button class="classified-filled-btn classified-submission-form-action-back" disabled><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> ' . esc_html__( "Back", "cubewp-classified" ) . '</button>';
			$html .= '<button class="classified-filled-btn classified-submission-form-action-next">' . esc_html__( "Next", "cubewp-classified" ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>';
		}
		$html .= '<button type="submit" class="classified-filled-btn classified-submission-form-action-submit ' . $btn_class . '" ' . $submit_disabled . '>' . $submit_text . '</button>';
		$html .= '</div>';

		return $html;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}