<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Browse By.
 *
 * @class Classified_Browse_By_Shortcode
 */
class Classified_Browse_By_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_browse_by_shortcode', array( $this, 'classified_browse_by_callback' ) );
		add_filter( 'classified_browse_by_shortcode_output', array( $this, 'classified_browse_by' ), 10, 2 );
	}

	public static function classified_browse_by( $output, $parameters ) {
		$browse_by        = $parameters['browse_by'];
		$browse_by_layout = $parameters['browse_by_layout'];
		$browse_by_col    = $parameters['browse_by_col'] ?? 'col-6 col-lg-3 col-xl-2';

		if ( empty( $browse_by ) || ! is_array( $browse_by ) ) {
			return false;
		}
		ob_start();
		?>
        <section class="classified-browse-by-container">
			<?php
			if ( count( $browse_by ) > 1 ) {
				?>
                <ul class="nav nav-tabs classified-browse-by-tabs" role="tablist">
					<?php
					foreach ( $browse_by as $counter => $value ) {
						$unique_id = 'classified-browse-by-' . $value['_id'];
						$tab_text  = $value['classified_browse_by_tab_text'] ?? '';
						?>
                        <li class="nav-item classified-browse-by-tab" role="presentation">
                            <button class="nav-link classified-browse-by-tab-btn <?php echo $counter == 0 ? 'active' : ''; ?>"
                                    id="<?php echo esc_attr( $unique_id ); ?>" data-bs-toggle="tab"
                                    data-bs-target="#<?php echo esc_attr( $unique_id . '-target' ); ?>"
                                    type="button" role="tab" aria-controls="<?php echo esc_attr( $tab_text ); ?>"
                                    aria-selected="false">
								<?php
								echo esc_html( $tab_text );
								?>
                            </button>
                        </li>
						<?php
					}
					?>
                </ul>
				<?php
			}
			?>
            <div class="tab-content">
				<?php
				foreach ( $browse_by as $counter => $value ) {
					$unique_id         = 'classified-browse-by-' . $value['_id'];
					$tab_type          = $value['classified_browse_by_tab_type'] ?? '';
					$tab_taxonomy      = $value['classified_browse_by_taxonomy'] ?? '';
					$tab_custom_fields = $value['classified_browse_by_custom_fields'] ?? '';
					$max_options       = $value['classified_browse_by_max_options'] ?? 0;
					$tab_post_type     = $value['classified_browse_by_post_type'] ?? '';
					$options           = array();
					if ( $tab_type == 'terms' ) {
						if ( ! empty( $tab_taxonomy ) ) {
							$args  = array(
								'taxonomy'   => $tab_taxonomy,
								'hide_empty' => false,
								'number'     => $max_options,
							);
							$terms = get_terms( $args );
							if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
								foreach ( $terms as $term ) {
									$icon_name                      = 'classified_category_icon';
									$term_icon                      = get_term_meta( $term->term_id, $icon_name, true );
									$options[ $term->slug ]['name'] = $term->name;
									$options[ $term->slug ]['link'] = get_term_link( $term );
									$options[ $term->slug ]['icon'] = $term_icon;
								}
							}
						}
					} else if ( $tab_type == 'fields' ) {
						if ( ! empty( $tab_custom_fields ) ) {
							foreach ( $tab_custom_fields as $field ) {
								$field_args = get_field_options( $field );
								$_options   = $field_args['options'] ?? false;
								$_options   = ! empty( $_options ) ? json_decode( $_options, true ) : array();
								$labels     = $_options['label'] ?? array();
								$values     = $_options['value'] ?? array();
								$icons      = $_options['icon'] ?? array();
								$max_option = $max_options < 1 ? count( $max_options ) : $max_options - 1;
								foreach ( $labels as $key => $label ) {
									if ( ! empty( $label ) && ! empty( $values[ $key ] ) ) {
										$options[ $field . '-' . $key ]['name'] = $label;
										$link                                   = home_url();
										$link                                   = add_query_arg( array(
											$field      => $values[ $key ],
											'post_type' => $tab_post_type,
											'page_num'  => 1
										), $link );
										$options[ $field . '-' . $key ]['link'] = $link;
										$options[ $field . '-' . $key ]['icon'] = $icons[ $key ] ?? '';
									}
									if ( $max_option == $key ) {
										break;
									}
								}
							}
						}
					} else if ( $tab_type == 'field-terms' ) {
						if ( ! empty( $tab_taxonomy ) ) {
							$custom_field = $value['classified_browse_by_custom_field'] ?? '';
							$field_value  = $value['classified_browse_by_custom_field_value'] ?? '';
							$args         = array(
								'taxonomy'   => $tab_taxonomy,
								'hide_empty' => false,
								'number'     => $max_options,
							);
							$terms        = get_terms( $args );
							if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
								foreach ( $terms as $term ) {
									$term_link = get_term_link( $term );
									if ( ! empty( $custom_field ) ) {
										$term_link = add_query_arg( array(
											$custom_field => $field_value,
										), $term_link );
									}
									$icon_name                      = 'classified_category_icon';
									$term_icon                      = get_term_meta( $term->term_id, $icon_name, true );
									$options[ $term->slug ]['name'] = $term->name;
									$options[ $term->slug ]['link'] = $term_link;
									$options[ $term->slug ]['icon'] = $term_icon;
								}
							}
						}
					}
					if ( ! empty( $options ) ) {
						?>
                        <div class="tab-pane <?php echo $counter == 0 ? 'active' : ''; ?>"
                             id="<?php echo esc_attr( $unique_id . '-target' ); ?>"
                             role="tabpanel">
                            <div class="<?php echo $browse_by_layout == 'style-1' ? 'classified-browse-by-list' : 'classified-browse-by-card'; ?>">
                                <div class="row">
									<?php
									$icon_accent = $parameters['browse_by_icon_accent'] ?? 'no';
									$icon_colors = array();
                                    if ( $icon_accent == 'yes' ) {
	                                    $icon_colors  = array(
		                                    'var(--orange-700)',
		                                    'var(--purple-700)',
		                                    'var(--red-700)',
		                                    'var(--green-700)',
		                                    'var(--cyan-700)'
	                                    );
                                    }
									$icon_counter = 0;
									foreach ( $options as $option ) {
										if ( $icon_accent == 'yes' && $icon_counter >= count( $icon_colors ) ) {
											$icon_counter = 0;
										}
										$name = $option['name'];
										$link = $option['link'];
										$icon = $option['icon'];
										?>
                                        <div class="<?php echo esc_attr( $browse_by_col ); ?>">
                                            <div class="<?php echo $browse_by_layout == 'style-1' ? 'classified-browse-by-list-item' : 'classified-browse-by-card-item'; ?>">
                                                <a href="<?php echo esc_url( $link ); ?>"
                                                   title="<?php echo esc_attr( $name ); ?>">
													<?php
													if ( ! empty( $icon ) ) {
														if ( $icon_accent == 'yes' ) {
															$term_icon_color = $icon_colors[ $icon_counter ];
															$icon_counter ++;
															echo classified_get_icon_output( $icon, 'style="color: ' . esc_attr( $term_icon_color ) . ';"' );
														} else {
															echo classified_get_icon_output( $icon );
														}
													}
													echo esc_html( $name );
													?>
                                                </a>
                                            </div>
                                        </div>
										<?php
									}
									?>
                                </div>
                            </div>
                        </div>
						<?php
					}
				}
				?>
            </div>
        </section>
		<?php

		return ob_get_clean();
	}

	public function classified_browse_by_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_browse_by_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}