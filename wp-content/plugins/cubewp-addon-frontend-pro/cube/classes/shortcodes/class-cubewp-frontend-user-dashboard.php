<?php

/**
 * User dashboard frontend forms shortcode.
 *
 * @package cubewp-addon-frontend/cube/classes/shortcodes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_User_Dashboard
 */
class CubeWp_Frontend_User_Dashboard {
	private static $cwp_userdash = '';
	private static $user_role = '';

	public function __construct() {
		$formOption         = CWP()->cubewp_options( 'cwp_userdash' );
		self::$cwp_userdash = ! empty($formOption) ? $formOption : array();
		self::$user_role    = cwp_get_current_user_roles();
		add_shortcode('cwp_dashboard', array($this, 'cwp_dashboard_callback'));
		add_filter('cwp/dashboard/tab/headings', array($this, 'cwp_dashboard_tab_headings'), 9, 3);
		add_filter('cwp/dashboard/single/tab/content/output', array($this, 'get_single_content_type'), 9, 3);
		add_filter('cwp/dashboard/tab/content', array($this, 'cwp_dashboard_tab_content'), 9, 3);
		add_action( 'wp_ajax_cubewp_delete_post', array($this,'cubewp_delete_post'));
	}
	
	/**
	 * Method cwp_dashboard_callback
	 *
	 * @param array $params 
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function cwp_dashboard_callback($params = array()) {
		if ( ! is_user_logged_in()) {
			$alert_content=esc_html('You must have logged-in to view dashboard page.','cubewp-frontend');
			return '<div class="cwp-container">'.cwp_alert_ui( $alert_content, 'info' ).'</div>';
		}
		extract(shortcode_atts(array(
			'tab_ids' => '',
		), $params));

		$tab_ids = ! empty(self::get_tab_id($params)) ? self::get_tab_id($params) : array();
		$cubewp_dashboard = '';
        $cubewp_dashboard = apply_filters('cwp/dashboard/output', self::cwp_dashboard_output($tab_ids, self::$cwp_userdash), $tab_ids, self::$cwp_userdash);
		return $cubewp_dashboard;
	}
	
	/**
	 * Method get_tab_id
	 *
	 * @param array $params
	 *
	 * @return array
	 * @since  1.0.0
	 */
	private function get_tab_id($params = array()) {
		$tab_ids = array();
		if (isset($params['tab_ids']) && ! empty($params['tab_ids'])) {
			$tab_ids = explode(", ", $params['tab_ids']);
		}
		if (empty($tab_ids) && ! empty(self::$cwp_userdash)) {
			foreach (self::$cwp_userdash as $tab_id => $fields) {
				$tab_ids[] = $tab_id;
			}
		}

		return $tab_ids;
	}
	
	/**
	 * Method cwp_dashboard_output
	 *
	 * @param array $tab_ids 
	 * @param array $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cwp_dashboard_output(array $tab_ids, array $data) {
		if (empty($tab_ids)) {
			return '<p>' . esc_html__("Dashboard is empty!.", "cubewp-frontend") . '</p>';
		}

		ob_start();
		?>
        <div class="cwp-user-dashboard">
            <ul class="cwp-user-dashboard-tabs">
				<?php echo apply_filters('cwp/dashboard/tab/headings', ' ', $tab_ids, $data); ?>
            </ul>
            <div class="cwp-user-dashboard-tab-content-container">
				<?php echo apply_filters('cwp/dashboard/tab/content', ' ', $tab_ids, $data); ?>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}
	
	/**
	 * Method cwp_dashboard_tab_headings
	 *
	 * @param null $content
	 * @param array $tab_ids 
	 * @param array $data 
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cwp_dashboard_tab_headings($content, array $tab_ids, array $data) {
		$tab_heading = '';
		$tab_icon = '';
		if ( ! empty($tab_ids)) {
			$counter = 1;
			foreach ($tab_ids as $tab_id) {
				if (self::check_dependency($tab_id) == true) {
					if (isset(self::$cwp_userdash[$tab_id]['icon']) && ! empty(self::$cwp_userdash[$tab_id]['icon'])) {
						$tab_icon = '<i class="dashicons ' . self::$cwp_userdash[$tab_id]['icon'] . '"></i>';
					}else{
                        $tab_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box" viewBox="0 0 16 16">
                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                        </svg>';
                    }
					$class = '';
					$get_tab_id = isset($_GET['tab_id']) ? sanitize_text_field($_GET['tab_id']) : '';
					if (empty($get_tab_id) || ! in_array($get_tab_id, $tab_ids)) {
						if ($counter == 1) {
							$class .= ' cwp-active-tab';
						}
					}else {
						if ($get_tab_id == $tab_id) {
							$class .= ' cwp-active-tab';
						}
					}
					if (self::$cwp_userdash[$tab_id]['content_type'] == 'logout') {
						$tab_heading .= '<li class="' . $class . '"><a class="cwp-not-tab" href="' . wp_logout_url(get_permalink()) . '">' . $tab_icon . ' ' . self::$cwp_userdash[$tab_id]['title'] . '</a></li>';
					} else {
						$tab_heading .= '<li class="' . $class . '"><a data-toggle="tab" href="#' . $tab_id . '">' . $tab_icon . ' ' . self::$cwp_userdash[$tab_id]['title'] . '</a></li>';
					}
					$counter ++;
				}
			}
		}

		return apply_filters('cwp/dashboard/tab/heading/output', $tab_heading);
	}
	
	/**
	 * Method cwp_dashboard_tab_content
	 *
	 * @param null $content 
	 * @param array $tab_ids 
	 * @param array $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function cwp_dashboard_tab_content($content, array $tab_ids, array $data) {
		$tab_content = '';
		if ( ! empty($tab_ids)) {
			$counter = 1;
			foreach ($tab_ids as $tab_id) {
				$class = 'cwp-user-dashboard-tab-content';
				$get_tab_id = isset($_GET['tab_id']) ? sanitize_text_field($_GET['tab_id']) : '';
				if (empty($get_tab_id) || ! in_array($get_tab_id, $tab_ids)) {
					if ($counter == 1) {
						$class .= ' cwp-active-tab-content';
					}
				}else {
					if ($get_tab_id == $tab_id) {
						$class .= ' cwp-active-tab-content';
					}
				}
				$tab_content .= '<div id="' . $tab_id . '" class="' . $class . '">';
				if (self::check_dependency($tab_id) == true) {
					if (function_exists('cwp_dashboard_' . self::$cwp_userdash[$tab_id]['content_type'] . '_tab')) {
						$tab_content .= call_user_func('cwp_dashboard_' . self::$cwp_userdash[$tab_id]['content_type'] . '_tab', $tab_id);
					} else {
						$tab_content .= apply_filters('cwp/dashboard/single/tab/content/output', ' ', self::$cwp_userdash[$tab_id], $tab_id);
					}
				}
				$tab_content .= '</div>';
				$counter ++;
			}
		}

		return apply_filters('cwp/dashboard/tab/content/output', $tab_content);
	}
	
	/**
	 * Method check_dependency
	 *
	 * @param string $tab_id
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public function check_dependency($tab_id = '') {
		if (self::$cwp_userdash[$tab_id]['user_role'] == self::$user_role || self::$cwp_userdash[$tab_id]['user_role'] == '') {
			return true;
		}

		return false;
	}
	
	/**
	 * Method get_single_content_type
	 *
	 * @param null $content 
	 * @param array $data 
	 * @param string $tab_id 
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_single_content_type($content, $data = '', $tab_id = '') {
		$func = 'get_' . $data['content_type'] . '_tab';
		if (method_exists(__CLASS__, $func)) {
			ob_start();
			echo self::$func($tab_id);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	}
	
	/**
	 * Method get_logout_tab
	 *
	 * @param string $tab_id
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function get_logout_tab($tab_id = '') {
		return '';
	}
	
	/**
	 * Method get_custom_tab_tab
	 *
	 * @param string $tab_id
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_custom_tab_tab($tab_id = '') {
		if (self::$cwp_userdash[$tab_id]['content_type'] == 'custom_tab'):
			return self::$cwp_userdash[$tab_id]['content'];
		endif;
	}
	
	/**
	 * Method get_shortcode_tab
	 *
	 * @param string $tab_id
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_shortcode_tab($tab_id = '') {
		if (self::$cwp_userdash[$tab_id]['content_type'] == 'shortcode'):
			return do_shortcode(self::$cwp_userdash[$tab_id]['content']);
		endif;
	}
	
	/**
	 * Method get_saved_tab
	 *
	 * @param string $tab_id
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_saved_tab($tab_id = '') {
		if (self::$cwp_userdash[$tab_id]['content_type'] == 'saved'):
            $args = self::get_post_query($tab_id);
			if(empty($args)){
				?>
                <div class="cwp-empty-posts"><img class="cwp-empty-img" src="<?php echo  esc_url(CWP_PLUGIN_URI.'cube/assets/frontend/images/no-result.png') ?>" alt=""><h2><?php echo esc_html__("No Saved Posts", "cubewp-frontend");?></h2><p><?php echo esc_html__("There are no posts saved yet.", "cubewp-frontend");?></p></div>
            	<?php
			}
            $page_num     =  isset($_GET['page_num']) ? $_GET['page_num'] : 1;
            $post_type    =  isset($args['post_type']) ? $args['post_type'] : '';
            $query = new CubeWp_Query($args);
		    $posts = $query->cubewp_post_query();
			if (isset($posts) && ! empty($posts) && $posts->have_posts()) {
				
				?>
                <div class="cwp-table-responsive">
                    <table class="cwp-user-dashboard-tables">
                        <tr class="cwp-dashboard-list-head">
                            <th class="cwp-saved-list-title"><?php esc_html_e("TITLE","cubewp-frontend") ?></th>
                            <th><?php esc_html_e("ACTIONS","cubewp-frontend") ?></th>
                        </tr>
						<?php while ($posts->have_posts()): $posts->the_post();
							$post_id = get_the_id();
						?>
                            <tr>
								<td class="cwp-dashboard-list-title-content">
									<?php echo self::get_post_details($post_id); ?>
                                </td>
                                <td>
									<div class="cwp-dasboard-list-action">
										<span class="cwp-dashboard-tooltip"><?php esc_html_e('Remove', 'cubewp-frontend'); ?></span>
                                    	<span class="cwp-main cwp-saved-post" data-pid="<?= $post_id ?>" data-action="remove">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
												<path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
											</svg>
										</span>
									</div>
                                </td>
                            </tr>
						<?php endwhile;
                        $pagination_args = array(
                            'total_posts'    => $posts->found_posts,
                            'posts_per_page' => 10,
                            'page_num'       => $page_num
                        );
                        echo apply_filters('cubewp_frontend_posts_pagination', '', $pagination_args);
                        ?>
                    </table>
                </div>
			<?php }else{ ?>
                <div class="cwp-empty-posts"><img class="cwp-empty-img" src="<?php echo  esc_url(CWP_PLUGIN_URI.'cube/assets/frontend/images/no-result.png') ?>" alt=""><h2><?php echo esc_html__("No Saved Posts", "cubewp-frontend");?></h2><p><?php echo esc_html__("There are no posts saved yet.", "cubewp-frontend");?></p></div>
            <?php }
			wp_reset_postdata();
			wp_reset_query();
		endif;
	}
	
	/**
	 * Method get_post_query
	 *
	 * @param string $tab_id
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function get_post_query($tab_id = '') {
		$savedPosts = CubeWp_Saved::cubewp_get_saved_posts();
		$args       = array();
		$posts      = array();
		if (self::$cwp_userdash[$tab_id]['content_type'] == 'saved' && ! empty($savedPosts)) {
			$args['post__in']    = $savedPosts;
			$args['post_type']   = get_post_types();
			$args['post_status'] = 'publish';
            $args['archive'] = 'false';
            $args['page_num']      = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
            $args['posts_per_page'] = '10';
		} else if (self::$cwp_userdash[$tab_id]['content_type'] == 'post_types') {
			$args['post_type']   = isset(self::$cwp_userdash[$tab_id]['content']) ? self::$cwp_userdash[$tab_id]['content'] : 'none';
			$args['post_status'] = array( 'pending', 'publish', 'expired' );
            $args['archive'] = 'false';
            $args['page_num']      = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
            $args['posts_per_page'] = '10';
			$args['author']      = get_current_user_id();
		}

		return $args;
	}
	
	/**
	 * Method get_page_content_tab
	 *
	 * @param string $tab_id 
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_page_content_tab($tab_id = '') {
		if (self::$cwp_userdash[$tab_id]['content_type'] == 'page_content'):
			if (isset(self::$cwp_userdash[$tab_id]['content']) && self::$cwp_userdash[$tab_id]['content'] != '') {
				$post_data = get_post(self::$cwp_userdash[$tab_id]['content']);

				return apply_filters('the_content', $post_data->post_content);
			}
		endif;
	}
	
	/**
	 * Method get_post_types_tab
	 *
	 * @param string $tab_id
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_post_types_tab($tab_id = '') {
		if (self::$cwp_userdash[$tab_id]['content_type'] == 'post_types'):
			global $cwpOptions;
            $args = self::get_post_query($tab_id);
            $page_num     =  isset($_GET['page_num']) ? $_GET['page_num'] : 1;
            $is_archive_page =  isset($args['archive']) ? $args['archive'] : '';
            $post_type    =  isset($args['post_type']) ? $args['post_type'] : '';
            $query = new CubeWp_Query($args);
		    $posts = $query->cubewp_post_query();
			if (isset($posts) && ! empty($posts) && $posts->have_posts()) { ?>
                <div class="cwp-table-responsive">
                    <table class="cwp-user-dashboard-tables">
                        <tr class="cwp-dashboard-list-head">
                            <th class="cwp-dashboard-listing-title"><?php esc_html_e("TITLE","cubewp-frontend") ?></th>
							<th><?php esc_html_e("PUBLISHED DATE","cubewp-frontend") ?></th>
                            <th><?php esc_html_e("VIEWS","cubewp-frontend") ?></th>
							<th><?php esc_html_e("STATUS","cubewp-frontend") ?></th>
							<th><?php esc_html_e("ACTIONS","cubewp-frontend") ?></th>
                        </tr>
						<?php while ($posts->have_posts()): $posts->the_post();
                        $post_id = get_the_id();
                        $post_type = get_post_type($post_id);
                        $submit_edit_page = isset($cwpOptions['submit_edit_page'][$post_type]) ? $cwpOptions['submit_edit_page'][$post_type] : '';
                        $submit_edit_page = get_permalink($submit_edit_page);
                        ?>
                            <tr>
                                <td class="cwp-dashboard-list-title-content">
									<?php echo self::get_post_details($post_id); ?>
                                </td>
								<td>
									<?php $p_date=get_the_date( 'M j, Y' );
										  echo esc_html($p_date);
									?>
								</td>
								<td>
								<?php $status = get_post_meta($post_id, 'cubewp_post_views', true);
									if(empty($status)){
										$status= 0;
									}
										echo $status;
								?>
								</td>
								<td>
									<?php $status = get_post_status ($post_id);?>
									<div class="cwp-dasboard-list-status">
										<span class="cwp-dashboard-tooltip"><?php echo esc_html($status) ?></span>
										<span class="cwp-<?php echo esc_html($status) ?>-list status-dot"></span>
									</div>
								</td>
                                <td>
									<?php if (is_post_publicly_viewable($post_id)) { ?>
									<div class="cwp-dasboard-list-action">
										<span class="cwp-dashboard-tooltip"><?php esc_html_e('View', 'cubewp-frontend'); ?></span>
										<a class="cwp-user-dashboard-tab-content-post-action cwp-post-action-view"
										href="<?php echo esc_url(get_permalink($post_id)); ?>"
										type="button" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
												<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
												<path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
											</svg>
										</a>
									</div>
									<?php } ?>
									<div class="cwp-dasboard-list-action">
										<span class="cwp-dashboard-tooltip"><?php esc_html_e('Edit', 'cubewp-frontend'); ?></span>
										<?php if (isset($submit_edit_page) && !empty($submit_edit_page)) { ?>
											<a class="cwp-user-dashboard-tab-content-post-action cwp-post-action-edit"
											href="<?php echo add_query_arg('pid', $post_id, $submit_edit_page); ?>"
											type="button" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
											<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
											<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
											
											</a>
										<?php } ?>
									</div>
									<div class="cwp-dasboard-list-action">
										<span class="cwp-dashboard-tooltip"><?php esc_html_e('Delete', 'cubewp-frontend'); ?></span>
										<button type="button" class="cwp-user-dashboard-tab-content-post-action cwp-post-action-delete" data-pid="<?php echo esc_attr($post_id) ?>" >
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
												<path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
											</svg>
										</button>
									</div>
                                </td>
                            </tr>
						<?php endwhile; ?>
                    </table>
					<?php
					$pagination_args = array(
						'total_posts'    => $posts->found_posts,
						'posts_per_page' => 10,
						'page_num'       => $page_num,
						'archive'     => $is_archive_page,
						'query_string' => '&tab_id='.$tab_id,
					);
					echo apply_filters('cubewp_frontend_posts_pagination', '', $pagination_args);
					?>
                </div>
			<?php }else{ ?>
                <div class="cwp-empty-posts"><img class="cwp-empty-img" src="<?php echo  esc_url(CWP_PLUGIN_URI.'cube/assets/frontend/images/no-result.png') ?>" alt=""><h2><?php echo sprintf(esc_html__("No %s Found", "cubewp-frontend"), $post_type)?></h2><p><?php echo sprintf(esc_html__("There are no posts associated with %s.", "cubewp-frontend"), $post_type)?></p></div>
            <?php }
			wp_reset_postdata();
			wp_reset_query();
		endif;

	}

	    
    /**
	 * Method get_post_details
	 *
     *@param int $post_id
	 * @return html
	 * @since  1.0.0
	 */
	public static function get_post_details($post_id){
        $post_type = get_post_type($post_id);
        $size = "50-50";
        $thumnail_url = get_the_post_thumbnail_url($post_id,'thumbnail');
        ob_start();
        ?>
        <?php if(!empty($thumnail_url )){ ?>
        <img class="cwp-dashboard-fet-img" src="<?= $thumnail_url ?>" alt="feature image">
        <?php }else{ ?>
        <img class="cwp-dashboard-fet-img" src="<?= CUBEWP_FRONTEND_PLUGIN_URL ?>/cube/assets/frontend/images/default-fet-image.png" alt="feature image">
        <?php } ?>
        <div class="cwp-dashboard-list-content">
		<?php
        if( !empty(get_the_title($post_id))){
        ?>
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a>
        <?php
        }else{ ?>
            <p><?php esc_html_e('Post Deleted', 'cubewp-frontend'); ?></p>
        <?php }
        $taxonomies = get_object_taxonomies( $post_type, 'objects' );
        foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){
            $terms = get_the_terms($post_id, $taxonomy_slug );
            if ( ! empty( $terms ) ) {?>
                <ul class='cwp-loop-terms'>
                <?php foreach ( $terms as $term ) {?>
                    <li><a href="<?php echo esc_url(get_term_link( $term->slug, $taxonomy_slug )) ?>"><?php echo esc_html($term->name) ?></a></li>
                <?php } ?>
                </ul>
            <?php	}
        } ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
	/**
	 * Method cubewp_delete_post
	 *
	 * @return array json
	 * @since  1.0.0
	 */
	public function cubewp_delete_post(){
		if(! wp_verify_nonce( $_POST['security_nonce'], 'cubewp_delete_post' )){
			wp_send_json( array('msg'=> esc_html('Security Verification Failed.','cubewp-frontend'),'type'=>'error') );
		}
		$post_id= sanitize_text_field( $_POST['post_id'] );
		$trash_post=wp_trash_post($post_id);
		if($trash_post){
			wp_send_json( array('msg'=> esc_html('Successfully Deleted.','cubewp-frontend'),'type'=>'success') );
		}
		wp_send_json( array('msg'=> esc_html('Something Went Wrong.','cubewp-frontend'),'type'=>'error') );
	}
	
	public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}