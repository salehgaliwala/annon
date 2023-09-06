<?php
defined( 'ABSPATH' ) || exit;


/**
 * Classified Submit Edit Class.
 *
 * @class ClassifiedSubmitEdit
 */
class Classified_Frontend_Fields {
	public function __construct() {
		add_filter( 'cubewp/frontend/field/parametrs', array( $this, 'classified_frontend_radio_field' ), 5, 2 );
	}

	public function classified_frontend_radio_field( $args ) {
		if ( isset( $args['type'] ) && $args['type'] == 'radio' ) {
			$options       = cwp_convert_choices_to_array( $args['options'] );
			$options_count = count( $options ) ?? 0;
			if ( $options_count == 2 ) {
				if ( ! str_contains( $args['container_class'], 'cwp-field-radio-toggle' ) ) {
					$args['container_class'] .= ' cwp-field-radio-toggle ';
				}
			}
			if ( empty( $args['value'] ) && ( isset( $args['form_type'] ) && $args['form_type'] == 'search' ) ) {
				$args['value'] = array_key_first( $options );
			}
		} else {
			if ( isset( $args['type'] ) && $args['type'] != 'checkbox' ) {
				if ( ! str_contains( $args['class'], 'form-control' ) ) {
					$args['class'] .= ' form-control ';
				}
			}
		}
		$args['container_class'] = $args['container_class'] ?? '';
		if ( ! str_contains( $args['container_class'], 'classified-form-field' ) ) {
			$args['container_class'] .= ' classified-form-field ';
		}

		return $args;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}