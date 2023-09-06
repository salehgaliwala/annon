<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Query.
 *
 * @class Classified_Query
 */
class Classified_Query {
	public function Query( $args = array() ) {
		global $classified_category_taxonomies, $classified_post_types;
		$taxonomies = $classified_category_taxonomies;
		$args['post_type']   = $args['post_type'] ?? $classified_post_types;
		$args['post_status'] = $args['post_status'] ?? array( 'publish' );
		$meta_query = array();
		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query;
		}
		$tax_relation = 'AND';
		if ( isset( $args['recommended'] ) && ! empty( $args['recommended'] ) ) {
			if ( class_exists( 'Classified_Personalization' ) && classified_get_setting( 'classified_personalization' ) ) {
				$args['locations_terms'] = Classified_Personalization::classified_get_personalized_terms( 'locations' );
				$args['categories_terms'] = Classified_Personalization::classified_get_personalized_terms( 'categories' );
				$searched = Classified_Personalization::classified_get_personalized_terms( 'searched' );
				$args['s'] = implode( '|', $searched );
				add_filter( 'posts_search', 'classified_personalized_terms_search', 10, 2 );
				$tax_relation = classified_get_setting( 'classified_personalize_relation' );
			}
		}
		$cat_query = array();
		if ( isset( $args['categories_terms'] ) && ! empty( $args['categories_terms'] ) ) {
			foreach ( $taxonomies as $category_taxonomy ) {
				$cat_query[] = array(
					'taxonomy' => $category_taxonomy,
					'field'    => 'term_id',
					'terms'    => $args['categories_terms'],
					'compare'  => 'IN'
				);
			}
			$cat_query['relation'] = "OR";
			$tax_query[]           = $cat_query;
			unset( $args['categories_terms'] );
		}
		if ( isset( $args['locations_terms'] ) && ! empty( $args['locations_terms'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'locations',
				'field'    => 'term_id',
				'terms'    => $args['locations_terms'],
				'compare'  => 'IN'
			);
			unset( $args['locations_terms'] );
		}
		if ( ! empty( $tax_query ) ) {
			$tax_query['relation'] = $tax_relation;
			$args['tax_query']     = $tax_query;
		}
		$args = apply_filters( 'classified_items_query_args', $args );

		$return = ( new WP_Query( $args ) );
		if ( isset( $args['recommended'] ) && ! empty( $args['recommended'] ) ) {
			remove_filter( 'posts_search', 'classified_personalized_terms_search' );
		}

		return $return;
	}
}