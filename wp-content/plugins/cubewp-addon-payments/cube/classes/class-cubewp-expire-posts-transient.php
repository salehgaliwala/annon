<?php

/**
 * Post expiry.
 *
 * @package cubewp-addon-payments/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Expire_Posts_Transient
 */
class CubeWp_Expire_Posts_Transient {
	private static $transient = 'cubewp_transient_expire_posts';

	private static $duration = DAY_IN_SECONDS;

	/**
	 * CubeWp_Expire_Posts_Transient Constructor.
	 */
	public function __construct() {
		add_action('init', array($this, "cubewp_register_post_status"));
		add_filter('display_post_states', array($this, 'cubewp_display_status_with_title'));
		self::cubewp_transient_init();
	}
	
	/**
	 * Method cubewp_transient_init
	 *
	 * @return void
	 * @since  1.0.0
	 */
	private static function cubewp_transient_init() {
		if (false === (get_transient(self::$transient))) {
			$post_types = array();
			foreach (CWP_all_post_types() as $post_type => $label) {
				$post_types[] = $post_type;
			}
			$posts_ids     = get_posts(array(
				"post_type"   => $post_types,
				"post_status" => "publish",
				"posts_per_page" => "-1",
				"fields"      => "ids",
				'meta_query'  => array(
					'relation' => 'AND',
					array(
						'key'     => 'post_expired',
						'value'   => strtotime("now"),
						'compare' => '<=',
						'type' => 'NUMERIC'
					),
				),
			));
			$expired_posts = array();
			if ( ! empty($posts_ids) && is_array($posts_ids)) {
				foreach ($posts_ids as $post_id) {
					$post_expires_on = get_post_meta($post_id, "post_expired", true);
					$timestamp_now   = strtotime("now");
					if ($post_expires_on <= $timestamp_now) {
						$expired_posts[] = $post_id;
						wp_update_post(array(
							'ID'          => $post_id,
							'post_status' => 'expired'
						));
					}
				}
			}

			set_transient(self::$transient, $expired_posts, self::$duration);
		}
	}
	
	/**
	 * Method cubewp_register_post_status
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public static function cubewp_register_post_status() {
		register_post_status('expired', array(
			'label'                     => esc_html__('Expired', 'cubewp-payments'),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>'),
		));
	}
	
	/**
	 * Method cubewp_display_status_with_title
	 *
	 * @param array $statuses
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function cubewp_display_status_with_title($statuses) {
		global $post;
		if (isset($post->post_status) && $post->post_status == 'expired') {
			return array('Expired');
		}

		return $statuses;
	}

	/**
	 * Method init
	 *
	 * @return void
	 */
	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
}