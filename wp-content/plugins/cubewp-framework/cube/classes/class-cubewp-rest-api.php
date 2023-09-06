<?php 
/**
 * CubeWp rest api for custom field data.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Rest_API
 */
class CubeWp_Rest_API extends WP_REST_Controller {

	//Query Type.
	const F_TYPE = 'fields_type';

	//Query source.
	const F_SOURCE = 'fields_source';

	//Query source.
	const F_INPUT_TYPE = 'fields_input_type';

	//Query Name.
	const F_NAME = 'field_name';

	//Query ID.
	const P_ID = 'post_id';

	public $namespace = '';
	public $base = '';
	public $custom_fields = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'cubewp-custom-fields/v1';
		$this->base = 'render';
		$this->custom_fields = 'custom_fields';
		$this->rest_field_init();
		$this->register_routes();
	}


	public function rest_field_init() {
		register_rest_field( self::get_types(), 'cubewp_post_meta', [
			'get_callback'    => [ __CLASS__, 'get_post_meta' ],
			'update_callback' => [ __CLASS__, 'update_post_meta' ],
		] );
		register_rest_field( self::get_types(), 'taxonomies', [
			'get_callback'    => [ __CLASS__, 'get_taxonomies' ],
			'update_callback' => '',
		] );
		register_rest_field( 'user', 'cubewp_user_meta', [
			'get_callback'    => [ __CLASS__, 'get_user_meta' ],
			'update_callback' => [ __CLASS__, 'update_user_meta' ],
		] );
		register_rest_field( self::get_types('taxonomy'), 'cubewp_term_meta', [
			'get_callback'    => [ __CLASS__, 'get_term_meta' ],
			'update_callback' => [ __CLASS__, 'update_term_meta' ],
		] );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_render_field' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->custom_fields,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_custom_fields' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
	}

	/**
	 * Checks if a given request has permission to access content.
	 *
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has search access, WP_Error object otherwise.
	 */
	public function get_permission_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Retrieves the query params for the search results.
	 *
	 * @return array Collection parameters.
	 */
	public function get_render_params() {
		$query_params  = parent::get_collection_params();
		
		$query_params[ self::F_TYPE  ] = array(
			'description' => __( 'The custom field Key.', 'cubewp-framework' ),
			'type'        => 'string',
		);
		$query_params[ self::F_SOURCE  ] = array(
			'description' => __( 'The source of the content', 'cubewp-framework' ),
			'type'        => 'string',
			'default'     => 'post',
		);
		$query_params[ self::F_NAME ] = array(
			'description' => __( 'The custom field Name.', 'cubewp-framework' ),
			'type'        => 'string',
		);
		$query_params[ self::F_INPUT_TYPE ] = array(
			'description' => __( 'The custom field Type.', 'cubewp-framework' ),
			'type'        => 'string',
		);
		$query_params[ self::P_ID ] = array(
			'description' => __( 'The custom field Name.', 'cubewp-framework' ),
			'type'        => 'string',
		);
		return $query_params;
	}

	/**
	 * Retrieves Custom field value.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_render_field( $request ) {
		$args = [];
		$args['f_type']  = $request->get_param( self::F_TYPE );
		$args['f_name']  = $request->get_param( self::F_NAME );
		$args['p_id'] = $request->get_param( self::P_ID );
		if($args['f_type']  == 'user_custom_fields'){
			$args['p_id']  = get_post_field ('post_author', $args['p_id']);
		}
		$value = get_any_field_value($args);
		return wp_send_json( $value );
	}

	/**
	 * Retrieves Custom fields.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_custom_fields( $request ) {
		$source        = $request->get_param( self::F_SOURCE );
		$type          = $request->get_param( self::F_TYPE );
		$input_type    = $request->get_param( self::F_INPUT_TYPE );
		$output = array();
		$output[''] = 'Select Field';
		if($type == 'post_custom_fields' || $type == 'user_custom_fields'){
			if(isset($source) && !empty($source)){
				if($type == 'post_custom_fields'){
					$groups = cwp_get_groups_by_post_type($source);
				}elseif($type == 'user_custom_fields'){
					$groups = cwp_get_groups_by_user_role($source);
				}
				if(isset($groups) && !empty($groups)){
					foreach($groups as $group){
						$fields = get_post_meta($group, '_cwp_group_fields', true);
						if($type == 'post_custom_fields'){
							$fields = isset($fields) && !empty($fields) ? explode(',', $fields) : '';
						}elseif($type == 'user_custom_fields'){
							$fields = isset($fields) && !empty($fields) ? json_decode($fields, true) : '';
						}
						if(is_array($fields)){
							foreach($fields as $field){
								if($type == 'post_custom_fields'){
									$option = get_field_options($field);
								}elseif($type == 'user_custom_fields'){
									$option = get_user_field_options($field);
								}
								$field_type = $option['type'];
								if($input_type){
									$input_typeArray = isset($input_type) && !empty($input_type) ? explode(',', $input_type) : array();
									if (in_array($field_type, $input_typeArray)) {
										$output[$field] = $option['label'];
									}
								}
							}
						}
					}
				}
			}
		}elseif($type == 'taxonomy_custom_fields'){
			// $fields = CWP()->get_custom_fields('taxonomy');
			// if(isset($fields) && !empty($fields)){
			// 	foreach($fields as $field){
			// 		if(is_array($fields)){
			// 			foreach($fields as $field){
			// 				$option = get_field_options($field);
			// 				$output[$field] = $option['label'];
			// 			}
			// 		}
			// 	}
			// }
		}
		return wp_send_json( $output );
	}
	
	
	/**
	 * Get post meta for the rest API.
	 *
	 * @param array $object Post object.
	 *
	 * @return array
	 */
	public static function get_post_meta( $object ) {
		$post_id   = $object['id'];
		$fields = CubeWp_Single_Cpt::cubewp_post_metas($post_id,true);
		return $fields;
	}

	/**
	 * Get all taxonomies and terms for the rest API.
	 *
	 * @param array $object Post object.
	 *
	 * @return array
	 */
	public static function get_taxonomies( $object ) {
		$post_id   = $object['id'];
		$post_terms = array();
		$taxonomies = get_object_taxonomies( get_post_type($post_id) );
		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$all_terms = get_the_terms( $post_id, $taxonomy );
				if(!is_wp_error( $all_terms ) && !empty( $all_terms )){
					foreach($all_terms as $all_term){
						$post_terms[] = $all_term->name;
					}
				}
			}
		}

		return isset( $post_terms ) && ! empty( $post_terms ) ? array_filter( $post_terms ) : array();
	}

	/**
	 * Update post meta for the rest API.
	 *
	 * @param string|array $data   Post meta values in either JSON or array format.
	 * @param object       $object Post object.
	 */
	public static function update_post_meta( $data, $object ) {
		$data = is_array( $data ) ? $data : array();
		foreach ( $data as $field_id => $value ) {
			$options = get_field_options($field_id);
			$meta_val = isset($value['meta_value']) ? $value['meta_value'] : '';
			if($options['type'] == 'google_address'){
				if(isset($meta_val['address']) && !empty($meta_val['address'])){
					update_post_meta( $object->ID, $field_id, $meta_val['address'] );
				}elseif(isset($meta_val['lat']) && !empty($meta_val['lat'])){
					update_post_meta( $object->ID, $field_id. '_lat', $meta_val['lat'] );
				}elseif(isset($meta_val['lng']) && !empty($meta_val['lng'])){
					update_post_meta( $object->ID, $field_id. '_lng', $meta_val['lng'] );
				}
			}elseif($options['type'] == 'repeating_field'){
				$repeater_vals =  get_post_meta( $object->ID, $field_id, true );
				$sub_meta_val = [];
				for($i = 0; $i < count($repeater_vals); $i++){
					foreach ( $repeater_vals[$i] as $k=> $sub_field ) {
						$org_val = $sub_field;
						$sub_data = $meta_val[$i][$k];
						$lat_key = str_replace("_lat","",$k);
						$lng_key = str_replace("_lng","",$k);
						$sub_lat_data = $meta_val[$i][$lat_key];
						$sub_lng_data = $meta_val[$i][$lng_key];
						$subOptions = get_field_options($k);
						if($subOptions['type'] == 'google_address'){
								if(isset($sub_data['value']['address']) && !empty($sub_data['value']['address'])){
									$sub_meta_val[$i][$k] = $sub_data['value']['address'];
								}else{
									$sub_meta_val[$i][$k] = $org_val;
								}
						}else{
							if(isset($sub_lat_data['value']['lat']) || isset($sub_lng_data['value']['lng'])){
								if(isset($sub_lat_data['value']['lat']) && !empty($sub_lat_data['value']['lat'])){
									$sub_meta_val[$i][$lat_key.'_lat'] = $sub_lat_data['value']['lat'];
								}
								if(isset($sub_lng_data['value']['lng']) && !empty($sub_lng_data['value']['lng'])){
									$sub_meta_val[$i][$lng_key.'_lng'] = $sub_lng_data['value']['lng'];
								}
							}else{
								if(isset($sub_data['value']) && !empty($sub_data['value'])){
									$sub_meta_val[$i][$k] = $sub_data['value'];
								}else{
									$sub_meta_val[$i][$k] = $org_val;
								}
							}
						}
					}
				}
				update_post_meta( $object->ID, $field_id, $sub_meta_val );
			}else{
				update_post_meta( $object->ID, $field_id, $meta_val );
			}
		}
	}

	/**
	 * Get term meta for the rest API.
	 *
	 * @param array $object Term object.
	 *
	 * @return array
	 */
	public static function get_term_meta( $object ) {
		$term_id = $object['id'];
		$term    = get_term( $term_id );
		if ( is_wp_error( $term ) || ! $term ) {
			return [];
		}

		return CubeWp_Taxonomy_Metabox::cubewp_taxonomy_metas($term->taxonomy,$term_id);
	}

	/**
	 * Update term meta for the rest API.
	 *
	 * @param string|array $data   Term meta values in either JSON or array format.
	 * @param object       $object Term object.
	 */
	public static function update_term_meta( $data, $object ) {
		$data = is_array( $data ) ? $data : array();
		foreach ( $data as $field_id => $value ) {
			$meta_val = isset($value['meta_value']) ? $value['meta_value'] : '';
			if(isset($meta_val['lng']) || isset($meta_val['lat']) || isset($meta_val['address'])){
				if(isset($meta_val['address']) && !empty($meta_val['address'])){
					update_term_meta( $object->term_id, $field_id, $meta_val['address'] );
				}elseif(isset($meta_val['lat']) && !empty($meta_val['lat'])){
					update_term_meta( $object->term_id, $field_id. '_lat', $meta_val['lat'] );
				}elseif(isset($meta_val['lng']) && !empty($meta_val['lng'])){
					update_term_meta( $object->term_id, $field_id. '_lng', $meta_val['lng'] );
				}
			}else{
				update_term_meta( $object->term_id, $field_id, $meta_val );
			}
		}
	}

	/**
	 * Get user meta for the rest API.
	 *
	 * @param array $object User object.
	 *
	 * @return array
	 */
	public static function get_user_meta( $object ) {
		$user_id   = $object['id'];
		if ( ! $user_id ) {
			return [];
		}

		return CubeWp_Custom_Fields::cubewp_user_metas($user_id,true);
	}

	/**
	 * Update user meta for the rest API.
	 *
	 * @param string|array $data   User meta values in either JSON or array format.
	 * @param object       $object User object.
	 */
	public static function update_user_meta( $data, $object ) {
		$data = is_array( $data ) ? $data : array();
		foreach ( $data as $field_id => $value ) {
			$options = get_user_field_options($field_id);
			$meta_val = isset($value['meta_value']) ? $value['meta_value'] : '';
			if($options['type'] == 'google_address'){
				if(isset($meta_val['address']) && !empty($meta_val['address'])){
					update_user_meta( $object->ID, $field_id, $meta_val['address'] );
				}elseif(isset($meta_val['lat']) && !empty($meta_val['lat'])){
					update_user_meta( $object->ID, $field_id. '_lat', $meta_val['lat'] );
				}elseif(isset($meta_val['lng']) && !empty($meta_val['lng'])){
					update_user_meta( $object->ID, $field_id. '_lng', $meta_val['lng'] );
				}
			}elseif($options['type'] == 'repeating_field'){
				$repeater_vals =  get_post_meta( $object->ID, $field_id, true );
				$sub_meta_val = [];
				for($i = 0; $i < count($repeater_vals); $i++){
					foreach ( $repeater_vals[$i] as $k=> $sub_field ) {
						$org_val = $sub_field;
						$sub_data = $meta_val[$i][$k];
						$lat_key = str_replace("_lat","",$k);
						$lng_key = str_replace("_lng","",$k);
						$sub_lat_data = $meta_val[$i][$lat_key];
						$sub_lng_data = $meta_val[$i][$lng_key];
						$subOptions = get_user_field_options($k);
						if($subOptions['type'] == 'google_address'){
								if(isset($sub_data['value']['address']) && !empty($sub_data['value']['address'])){
									$sub_meta_val[$i][$k] = $sub_data['value']['address'];
								}else{
									$sub_meta_val[$i][$k] = $org_val;
								}
						}else{
							if(isset($sub_lat_data['value']['lat']) || isset($sub_lng_data['value']['lng'])){
								if(isset($sub_lat_data['value']['lat']) && !empty($sub_lat_data['value']['lat'])){
									$sub_meta_val[$i][$lat_key.'_lat'] = $sub_lat_data['value']['lat'];
								}
								if(isset($sub_lng_data['value']['lng']) && !empty($sub_lng_data['value']['lng'])){
									$sub_meta_val[$i][$lng_key.'_lng'] = $sub_lng_data['value']['lng'];
								}
							}else{
								if(isset($sub_data['value']) && !empty($sub_data['value'])){
									$sub_meta_val[$i][$k] = $sub_data['value'];
								}else{
									$sub_meta_val[$i][$k] = $org_val;
								}
							}
						}
					}
				}
				update_user_meta( $object->ID, $field_id, $sub_meta_val );
			}else{
				update_user_meta( $object->ID, $field_id, $meta_val );
			}
		}
	}

	/**
	 * Get supported types in Rest API.
	 *
	 * @param string $type 'post' or 'taxonomy'.
	 *
	 * @return array
	 */
	private static function get_types( $type = 'post' ) {
		$types = get_post_types( [], 'objects' );
		if ( 'taxonomy' === $type ) {
			$types = get_taxonomies( [], 'objects' );
		}
		foreach ( $types as $type => $object ) {
			if ( empty( $object->show_in_rest ) ) {
				unset( $types[ $type ] );
			}
		}

		return array_keys( $types );
	}
	
	public static function init() {
		$CubeClass = __CLASS__;
        new $CubeClass;
	}

}