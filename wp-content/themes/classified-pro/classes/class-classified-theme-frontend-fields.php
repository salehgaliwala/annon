<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Frontend Fields.
 *
 * @class Classified_Theme_Frontend_Fields
 */
class Classified_Theme_Frontend_Fields extends CubeWp_Frontend {
	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct() {
		add_filter( 'cubewp/frontend/radio/field', array( $this, 'classified_radio_field_with_icons' ), 11, 2 );

		add_filter( 'cubewp/search_filters/checkbox/taxonomy/field', array( $this, 'search_filter_checkbox' ), 11, 2 );
	}

	public function classified_radio_field_with_icons( $output = '', $args = array() ) {
		$args                    = apply_filters( 'cubewp/frontend/field/parametrs', $args );
		$args['container_class'] .= 'classified-radio-with-icons';
		if ( str_contains( $args['container_class'], 'classified-radio-with-icons' ) ) {
			$output   = '';
			$options  = cwp_convert_choices_to_array( $args['options'] );
			$required = self::cwp_frontend_field_required( $args['required'] );
			$required = ! empty( $required['class'] ) ? $required['class'] : '';
			$output   = self::cwp_frontend_post_field_container( $args );
			$output   .= '<div class="cwp-radio-container">';
			$output   .= self::cwp_frontend_field_label( $args );
			$output   .= '<div class="cwp-field-radio-container">';
			$icons    = json_decode( $args['options'], true );
			$icons    = $icons['icon'] ?? array();
			$key      = 0;
			foreach ( $options as $value => $label ) {
				$icon = '';
				if ( isset( $icons[ $key ] ) ) {
					$icon = $icons[ $key ];
				}
				$output      .= '<div class="cwp-field-radio">';
				$input_attrs = array(
					'type'  => 'radio',
					'id'    => esc_attr( $args['id'] . $label ),
					'name'  => ! empty( $args['custom_name'] ) ? $args['custom_name'] : $args['name'],
					'value' => $value,
					'class' => 'custom-control-input ' . $args['class'] . ' ' . $required,
				);
				if ( isset( $args['value'] ) && $args['value'] == $value ) {
					$input_attrs['extra_attrs'] = ' checked="checked"';
				}
				$output .= cwp_render_text_input( $input_attrs );
				if ( ! empty( $icon ) ) {
					$output .= '<i class="' . esc_attr( $icon ) . '" aria-hidden="true"></i>';
				}
				$output .= '<label for="' . esc_attr( $args['id'] . $label ) . '">' . esc_html( $label ) . '</label>';
				$output .= '</div>';
				$key ++;
			}
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';

			return apply_filters( "cubewp/frontend/{$args['name']}/field", $output, $args );
		} else {
			return $output;
		}
	}

	public function search_filter_checkbox( $output = '', $args = array() ) {
		$args    = apply_filters( 'cubewp/frontend/field/parametrs', $args );
		$options = $args['options'];
		if ( ! empty( $options ) && is_array( $options ) ) {
			$values = ! empty( $args['value'] ) && is_array( $args['value'] ) ? implode( ',', $args['value'] ) : $args['value'];
			$name   = '';
			$output = self::cwp_frontend_post_field_container( $args );
			$output .= '<div class="cwp-search-field cwp-search-field-checkbox ' . $args['container_class'] . '">';
			$output .= self::cwp_frontend_field_label( $args );
			if ( count( $options ) > 5 ) {
				$output .= '<div class="cwp-field-checkbox-container classified-category-card-have-collapse">';
			} else {
				$output .= '<div class="cwp-field-checkbox-container">';
			}
			$counter = 1;
			foreach ( $options as $value => $label ) {
				$output      .= '<ul class="classified-term-container">
				<li ' . $args['class'] . '>
				<div class="cwp-field-checkbox">';
				$input_attrs = array(
					'type'  => 'checkbox',
					'id'    => esc_attr( $args['id'] . ' ' . $label['term_name'] ),
					'name'  => $name,
					'value' => $value,
					'class' => 'custom-control-input ' . $args['class'],
				);
				if ( isset( $args['value'] ) && is_array( $args['value'] ) && in_array( $value, $args['value'] ) ) {
					$input_attrs['extra_attrs'] = ' checked="checked"';
				} else if ( isset( $args['value'] ) && $args['value'] == $value ) {
					$input_attrs['extra_attrs'] = ' checked="checked"';
				} else if ( is_tax() ) {
					$queried_object = get_queried_object();
					$CurrentSlug    = $queried_object->slug;
					if ( isset( $CurrentSlug ) && $CurrentSlug == $value ) {
						$input_attrs['extra_attrs'] = ' checked="checked"';
						$currentVal                 = $CurrentSlug;
					}
				}
				$output .= cwp_render_text_input( $input_attrs );
				$output .= '<label for="' . esc_attr( $args['id'] . ' ' . $label['term_name'] ) . '">' . esc_html( $label['term_name'] ) . '</label>
				</div>
				</li>';
				if ( ! empty( $label['childern'] ) && is_array( $label['childern'] ) ) {
					$icon_class = '';
					$ul_style   = '';
					if ( $counter == 1 ) {
						$icon_class = 'expanded';
						$ul_style   = 'style="display: block;"';
					}
					$counter ++;
					$output .= '
					<i class="fa-solid fa-chevron-down classified-expand-more-terms ' . $icon_class . '" aria-hidden="true"></i>
					<ul ' . $ul_style . '>';
					foreach ( $label['childern'] as $c_value => $c_label ) {
						$output      .= '<li ' . $args['class'] . '>';
						$output      .= '<div class="cwp-field-checkbox">';
						$input_attrs = array(
							'type'  => 'checkbox',
							'id'    => esc_attr( $args['id'] . ' ' . $c_label['term_name'] ),
							'name'  => $name,
							'value' => $c_value,
							'class' => 'custom-control-input ' . $args['class'],
						);
						if ( isset( $args['value'] ) && is_array( $args['value'] ) && in_array( $c_value, $args['value'] ) ) {
							$input_attrs['extra_attrs'] = ' checked="checked"';
						} else if ( isset( $args['value'] ) && $args['value'] == $c_value ) {
							$input_attrs['extra_attrs'] = ' checked="checked"';
						} else if ( is_tax() ) {
							$queried_object = get_queried_object();
							$CurrentSlug    = $queried_object->slug;
							if ( isset( $CurrentSlug ) && $CurrentSlug == $c_value ) {
								$input_attrs['extra_attrs'] = ' checked="checked"';
								$currentVal                 = $CurrentSlug;
							}
						}

						$output .= cwp_render_text_input( $input_attrs );
						$output .= '<label for="' . esc_attr( $args['id'] . ' ' . $c_label['term_name'] ) . '">' . esc_html( $c_label['term_name'] ) . '</label>';
						$output .= '</div>';
						$output .= '</li>';
						if ( ! empty( $c_label['childern'] ) && is_array( $c_label['childern'] ) ) {
							$output .= '<ul>';
							foreach ( $c_label['childern'] as $cc_value => $cc_label ) {
								$output      .= '<li ' . $args['class'] . '>';
								$output      .= '<div class="cwp-field-checkbox">';
								$input_attrs = array(
									'type'  => 'checkbox',
									'id'    => esc_attr( $args['id'] . ' ' . $cc_label['term_name'] ),
									'name'  => $name,
									'value' => $cc_value,
									'class' => 'custom-control-input ' . $args['class'],
								);
								if ( isset( $args['value'] ) && is_array( $args['value'] ) && in_array( $cc_value, $args['value'] ) ) {
									$input_attrs['extra_attrs'] = ' checked="checked"';
								} else if ( isset( $args['value'] ) && $args['value'] == $cc_value ) {
									$input_attrs['extra_attrs'] = ' checked="checked"';
								} else if ( is_tax() ) {
									$queried_object = get_queried_object();
									$CurrentSlug    = $queried_object->slug;
									if ( isset( $CurrentSlug ) && $CurrentSlug == $cc_value ) {
										$input_attrs['extra_attrs'] = ' checked="checked"';
										$currentVal                 = $CurrentSlug;
									}
								}

								$output .= cwp_render_text_input( $input_attrs );
								$output .= '<label for="' . esc_attr( $args['id'] . ' ' . $cc_label['term_name'] ) . '">' . esc_html( $cc_label['term_name'] ) . '</label>';
								$output .= '</div>';
								$output .= '</li>';
							}
							$output .= '</ul>';
						}
					}
					$output .= '</ul>';
				}
				$output .= '</ul>';
			}
			if ( ! is_page() ) {
				$input_attrs = array(
					'name'  => $args['name'],
					'value' => $currentVal ?? $values,
				);
				$output      .= cwp_render_hidden_input( $input_attrs );
			}
			$output .= '</div>';
			if ( count( $options ) > 5 ) {
				$output .= '<p class="classified-see-more-category collapsed" data-more="' . esc_attr__( "See More", "classified-pro" ) . '"data-less="' . esc_attr__( "See Less", "classified-pro" ) . '">';
				$output .= esc_html__( "See More", "classified-pro" ) . '</p>';
			}
			$output .= '</div>
			</div>';
		}

		return apply_filters( "cubewp/frontend/{$args['name']}/field", $output, $args );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}