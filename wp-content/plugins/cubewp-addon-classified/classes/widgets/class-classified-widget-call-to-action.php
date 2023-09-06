<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Theme Single Widgets.
 *
 * @class Classified_Theme_Single_Widgets
 */
class Classified_Widget_Call_To_Action extends WP_Widget {
	function __construct() {
		parent::__construct( 'classified_call_to_action_widgets', esc_html__( "Classified Call To Action", "cubewp-classified" ) );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function widget( $args, $instance ) {
		$btn_text = isset( $instance['btn_text'] ) && ! empty( $instance['btn_text'] ) ? $instance['btn_text'] : '';
		$btn_icon = isset( $instance['btn_icon'] ) && ! empty( $instance['btn_icon'] ) ? $instance['btn_icon'] : '';
		$btn_link = isset( $instance['btn_link'] ) && ! empty( $instance['btn_link'] ) ? $instance['btn_link'] : '';
		echo $args['before_widget'] ?? '<div class="classified-widget">';
		?>
            <div class="d-flex w-100">
                <div class="classified-call-to-action-widget">
                    <h5><?php echo apply_filters( 'widget_title', $instance['title'] ); ?></h5>
                    <button class="classified-filled-btn position-relative">
                        <a href="<?php echo esc_url( $btn_link ); ?>" class="stretched-link"></a>
                        <i class="<?php echo esc_attr( $btn_icon ); ?>" aria-hidden="true"></i>
                        <?php echo esc_html( $btn_text ); ?>
                    </button>
                </div>
            </div>
		<?php
		echo $args['after_widget'] ?? '</div>';
	}

	public function form( $instance ) {
		$title    = $instance['title'] ?? '';
		$btn_text = $instance['btn_text'] ?? '';
		$btn_icon = $instance['btn_icon'] ?? '';
		$btn_link = $instance['btn_link'] ?? '';
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'cubewp-classified' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'btn_text' ) ); ?>"><?php echo esc_html__( 'Button Text:', 'cubewp-classified' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'btn_text' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'btn_text' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $btn_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'btn_icon' ) ); ?>"><?php echo esc_html__( 'Button Icon:', 'cubewp-classified' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'btn_icon' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'btn_icon' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $btn_icon ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'btn_link' ) ); ?>"><?php echo esc_html__( 'Button Link:', 'cubewp-classified' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'btn_link' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'btn_link' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $btn_link ); ?>">
        </p>
        <?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['btn_text'] = ( ! empty( $new_instance['btn_text'] ) ) ? strip_tags( $new_instance['btn_text'] ) : '';
		$instance['btn_icon'] = ( ! empty( $new_instance['btn_icon'] ) ) ? strip_tags( $new_instance['btn_icon'] ) : '';
		$instance['btn_link'] = ( ! empty( $new_instance['btn_link'] ) ) ? strip_tags( $new_instance['btn_link'] ) : '';

		return $instance;
	}

}