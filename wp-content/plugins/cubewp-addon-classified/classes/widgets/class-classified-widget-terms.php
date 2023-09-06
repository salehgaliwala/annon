<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Theme Single Widgets.
 *
 * @class Classified_Theme_Single_Widgets
 */
class Classified_Widget_Terms extends WP_Widget {
	function __construct() {
		parent::__construct( 'classified_terms_widgets', esc_html__( "Classified Terms", "cubewp-classified" ) );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function widget( $args, $instance ) {
		$taxonomy = isset( $instance['taxonomy'] ) && ! empty( $instance['taxonomy'] ) ? $instance['taxonomy'] : '';
		$terms_per_page = isset( $instance['terms_per_page'] ) && ! empty( $instance['terms_per_page'] ) ? $instance['terms_per_page'] : 10;
		if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
			return false;
		}
		echo $args['before_widget'] ?? '<div class="classified-widget">';
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		?>
        <div class="classified-terms-widget">
			<?php
			$args       = array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => $terms_per_page
			);
			$terms = get_terms( $args );
            if ( ! empty( $terms ) && is_array( $terms ) ) {
                foreach ( $terms as $term ) {
                    ?>
                    <a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>">
                        <?php echo esc_html( $term->name ); ?>
                    </a>
                    <?php
                }
            }
			?>
        </div>
		<?php
		echo $args['after_widget'] ?? '</div>';
	}

	public function form( $instance ) {
		global $classified_taxonomies;
		$title    = $instance['title'] ?? '';
		$taxonomy = $instance['taxonomy'] ?? '';
		$terms_per_page = $instance['terms_per_page'] ?? 10;
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'cubewp-classified' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
		<?php
		if ( ! empty( $classified_taxonomies ) && is_array( $classified_taxonomies ) ) {
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"><?php esc_html_e( 'Select Taxonomy', 'cubewp-classified' ); ?></label>
                <select id="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) ); ?>"
                        class="widefat cwp-widget-select-term">
					<?php
					foreach ( $classified_taxonomies as $classified_taxonomy ) {
						?>
                        <option <?php selected( $taxonomy, $classified_taxonomy ) ?>
                                value="<?php echo esc_attr( $classified_taxonomy ); ?>"><?php echo esc_html( $classified_taxonomy ); ?></option>
						<?php
					}
					?>
                </select>
            </p>
			<?php
		}
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'terms_per_page' ) ); ?>"><?php echo esc_html__( 'Terms Per Page:', 'cubewp-classified' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'terms_per_page' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'terms_per_page' ) ); ?>" type="number"
                   value="<?php echo esc_attr( $terms_per_page ); ?>">
        </p>
        <?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['taxonomy'] = ( ! empty( $new_instance['taxonomy'] ) ) ? strip_tags( $new_instance['taxonomy'] ) : '';
		$instance['terms_per_page'] = ( ! empty( $new_instance['terms_per_page'] ) ) ? strip_tags( $new_instance['terms_per_page'] ) : 10;

		return $instance;
	}

}