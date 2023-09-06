<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Personalization Class.
 *
 * @class Classified_Personalization
 */
class Classified_Personalization {

	private static $cookie_name = 'classified_personalized_';

	public function __construct() {
		if ( classified_get_setting( 'classified_personalization' ) ) {
			add_action( 'cubewp_post_confirmation', array( $this, 'classified_single_page' ) );
			add_filter( "cubewp_frontend_search_data", array( $this, 'classified_archive_page' ), 11, 2 );
		}
		// Adding Settings Into CubeWP Settings For Personalization
		add_filter( 'cubewp/options/sections', array( $this, 'classified_personalization_settings' ), 13, 1 );
	}

	public static function classified_check_personalized_terms_exists() {
		foreach ( $_COOKIE as $cookie => $data ) {
			if ( str_contains( $cookie, self::$cookie_name ) ) {
				return true;
			}
		}

		return false;
	}

	public static function classified_get_personalized_terms( $id ) {
		return isset( $_COOKIE[ self::$cookie_name . $id ] ) && ! empty( $_COOKIE[ self::$cookie_name . $id ] ) ? explode( ',', sanitize_text_field( $_COOKIE[ self::$cookie_name . $id ] ) ) : array();
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_personalization_settings( $sections ) {
		$new_sections['classified_personalization'] = array(
			'title'  => __( 'Personalization', 'cubewp-classified' ),
			'id'     => 'classified_personalization',
			'icon'   => 'dashicons dashicons-buddicons-activity',
			'fields' => array(
				array(
					'id'      => 'classified_personalization',
					'title'   => __( 'Enable Personalization', 'cubewp-classified' ),
					'desc'    => __( 'Track and store user activity to show personalized items.', 'cubewp-classified' ),
					'type'    => 'switch',
					'default' => '0',
				),
				array(
					'id'       => 'classified_personalize_relation',
					'title'    => __( 'Taxonomies Relation', 'cubewp-classified' ),
					'desc'     => __( 'What relation you want between two or more taxonomies.', 'cubewp-classified' ),
					'type'     => 'select',
					'options'  => array(
						'AND' => esc_html__( "Ads Must Contains All Saved Terms And Keywords.", "cubewp-classified" ),
						'OR'  => esc_html__( "Ads With Any Saved Terms And Keyword.", "cubewp-classified" )
					),
					'default'  => 'OR',
					'required' => array(
						array( 'classified_personalization', 'equals', '1' )
					)
				),
			),
		);
		$new_section_pos                            = array_search( 'map', array_keys( $sections ) ) + 0;

		return array_merge( array_slice( $sections, 0, $new_section_pos ), $new_sections, array_slice( $sections, $new_section_pos ) );
	}

	public function classified_single_page( $post_id ) {
		$tax_types = array( 'categories', 'locations' );
		foreach ( $tax_types as $tax ) {
			if ( $tax == 'categories' ) {
				global $classified_category_taxonomies;
				$taxonomies = $classified_category_taxonomies;
			} else {
				$taxonomies = 'locations';
			}
			$post_terms = wp_get_post_terms( $post_id, $taxonomies, array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) && is_array( $post_terms ) ) {
				self::classified_set_cookie( $tax, $post_terms );
			}
		}
	}

	private static function classified_set_cookie( $id, $value ) {
		$personalized_terms = isset( $_COOKIE[ self::$cookie_name . $id ] ) && ! empty( $_COOKIE[ self::$cookie_name . $id ] ) ? explode( ',', sanitize_text_field( $_COOKIE[ self::$cookie_name . $id ] ) ) : array();
		$personalized_terms = implode( ',', array_slice( array_unique( array_merge( $personalized_terms, $value ) ), 0, 20 ) );
		setcookie( self::$cookie_name . $id, $personalized_terms, time() + MONTH_IN_SECONDS, '/' );
	}

	public function classified_archive_page( $output, $args ) {
		global $classified_category_taxonomies;
		$posted_data = $args['data'] ?? array();
		if ( isset( $posted_data['s'] ) && ! empty( $posted_data['s'] ) ) {
			$_s = sanitize_text_field( $posted_data['s'] );
			self::classified_set_cookie( 'searched', array( $_s ) );
		}
		if ( isset( $posted_data['locations'] ) && ! empty( $posted_data['locations'] ) ) {
			$locations = explode( ',', $posted_data['locations'] );
			if ( ! empty( $locations ) && is_array( $locations ) ) {
				foreach ( $locations as $location ) {
					$term = get_term_by( 'slug', $location, 'locations' );
					if ( ! is_wp_error( $term ) ) {
						self::classified_set_cookie( 'locations', array( $term->term_id ) );
					}
				}
			}
		}
		if ( ! empty( $classified_category_taxonomies ) && is_array( $classified_category_taxonomies ) ) {
			foreach ( $classified_category_taxonomies as $category ) {
				if ( isset( $posted_data[ $category ] ) && ! empty( $posted_data[ $category ] ) ) {
					$categories = explode( ',', $posted_data[ $category ] );
					if ( ! empty( $categories ) && is_array( $categories ) ) {
						foreach ( $categories as $_category ) {
							$term = get_term_by( 'slug', $_category, $category );
							if ( ! is_wp_error( $term ) ) {
								self::classified_set_cookie( 'categories', array( $term->term_id ) );
							}
						}
					}
					break;
				}
			}
		}

		return $output;
	}
}