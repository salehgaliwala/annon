<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Theme Single Widgets.
 *
 * @class Classified_Theme_Single_Widgets
 */
class Classified_Widget_Socials extends WP_Widget {
	function __construct() {
		parent::__construct( 'classified_socials_widgets', esc_html__( "Classified Socials", "cubewp-classified" ) );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}


	public function widget( $args, $instance ) {
		$facebook  = isset( $instance['facebook'] ) && ! empty( $instance['facebook'] ) ? $instance['facebook'] : '';
		$instagram = isset( $instance['instagram'] ) && ! empty( $instance['instagram'] ) ? $instance['instagram'] : '';
		$youtube   = isset( $instance['youtube'] ) && ! empty( $instance['youtube'] ) ? $instance['youtube'] : '';
		$linkedin  = isset( $instance['linkedin'] ) && ! empty( $instance['linkedin'] ) ? $instance['linkedin'] : '';
		$twitter   = isset( $instance['twitter'] ) && ! empty( $instance['twitter'] ) ? $instance['twitter'] : '';
		if ( empty( $facebook ) && empty( $instagram ) && empty( $youtube ) && empty( $linkedin ) && empty( $twitter ) ) {
			return false;
		}
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		?>
        <div class="classified-social-widget">
			<?php
			if ( ! empty( $facebook ) ) {
				?>
                <a href="<?php echo esc_url( $facebook ); ?>"><i class="fa-brands fa-square-facebook" aria-hidden="true"></i></a>
				<?php
			}
			if ( ! empty( $instagram ) ) {
				?>
                <a href="<?php echo esc_url( $instagram ); ?>"><i class="fa-brands fa-square-instagram" aria-hidden="true"></i></a>
				<?php
			}
			if ( ! empty( $youtube ) ) {
				?>
                <a href="<?php echo esc_url( $youtube ); ?>"><i class="fa-brands fa-square-youtube" aria-hidden="true"></i></a>
				<?php
			}
			if ( ! empty( $linkedin ) ) {
				?>
                <a href="<?php echo esc_url( $linkedin ); ?>"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
				<?php
			}
			if ( ! empty( $twitter ) ) {
				?>
                <a href="<?php echo esc_url( $twitter ); ?>"><i class="fa-brands fa-square-twitter" aria-hidden="true"></i></a>
				<?php
			}
			?>
        </div>
		<?php
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title     = $instance['title'] ?? '';
		$facebook  = $instance['facebook'] ?? '';
		$instagram = $instance['instagram'] ?? '';
		$youtube   = $instance['youtube'] ?? '';
		$linkedin  = $instance['linkedin'] ?? '';
		$twitter   = $instance['twitter'] ?? '';
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'cubewp-classified' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'Facebook:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>"
                   name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text"
                   value="<?php echo esc_attr( $facebook ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'instagram' ); ?>"><?php _e( 'Instagram:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'instagram' ); ?>"
                   name="<?php echo $this->get_field_name( 'instagram' ); ?>" type="text"
                   value="<?php echo esc_attr( $instagram ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'youtube' ); ?>"><?php _e( 'Youtube:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'youtube' ); ?>"
                   name="<?php echo $this->get_field_name( 'youtube' ); ?>" type="text"
                   value="<?php echo esc_attr( $youtube ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'linkedin' ); ?>"><?php _e( 'Linkedin:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'linkedin' ); ?>"
                   name="<?php echo $this->get_field_name( 'linkedin' ); ?>" type="text"
                   value="<?php echo esc_attr( $linkedin ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'Twitter:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>"
                   name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text"
                   value="<?php echo esc_attr( $twitter ); ?>">
        </p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance              = array();
		$instance['title']     = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['facebook']  = ( ! empty( $new_instance['facebook'] ) ) ? strip_tags( $new_instance['facebook'] ) : '';
		$instance['instagram'] = ( ! empty( $new_instance['instagram'] ) ) ? strip_tags( $new_instance['instagram'] ) : '';
		$instance['youtube']   = ( ! empty( $new_instance['youtube'] ) ) ? strip_tags( $new_instance['youtube'] ) : '';
		$instance['linkedin']  = ( ! empty( $new_instance['linkedin'] ) ) ? strip_tags( $new_instance['linkedin'] ) : '';
		$instance['twitter']   = ( ! empty( $new_instance['twitter'] ) ) ? strip_tags( $new_instance['twitter'] ) : '';

		return $instance;
	}

}