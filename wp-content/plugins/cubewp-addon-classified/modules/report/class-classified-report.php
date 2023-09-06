<?php
defined( 'ABSPATH' ) || exit;

/**
 * Classified Report Class.
 *
 * @class Classified_Report
 */
class Classified_Report {

	private static $reported_post_id = 0;
	private static $reported_user_id = 0;

	private static $report_post_statues = array();

	public function __construct() {
		add_action( 'init', array( $this, 'classified_report_post_types' ) );

		add_filter( 'display_post_states', array( $this, 'classified_display_post_status_with_title' ) );

		add_action( 'post_submitbox_misc_actions', array( $this, 'classified_report_add_to_metabox' ) );

		add_action( 'post_submitbox_start', array( $this, 'classified_report_add_actions_to_metabox' ) );

		add_action( 'wp_insert_post_data', array( $this, 'classified_report_change_report_status' ), 10, 2 );

		add_filter( 'cubewp/builder/post_types', array( $this, 'classified_report_post_type_into_builder' ), 10, 2 );

		add_filter( 'cubewp/cubewp-report/form/fields', array( $this, 'classified_report_post_type_form_attrs' ) );

		// Adding Email Type
		// add_filter( 'cubewp/email/types', array( $this, 'classified_email_types' ) );

		add_filter( 'cubewp/builder/post_type/switcher', array(
			$this,
			'classified_report_post_type_association'
		), 10, 2 );

		add_filter( 'cubewp/cubewp-report/before/submit/actions', array(
			$this,
			'classified_before_report_post_type_form_submission'
		) );

		add_filter( 'cubewp/cubewp-report/after/post/create', array(
			$this,
			'classified_report_post_type_form_submission'
		), 10, 2 );

		add_filter( 'cubewp/cubewp-report/after/submit/actions', array(
			$this,
			'classified_after_report_post_type_form_submission'
		) );
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}

	public function classified_email_types( $email_types ) {
		$new_types = array(
			array(
				'name'      => 'report-submitted',
				'label'     => esc_html__( 'Report Submitted', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'report-received',
				'label'     => esc_html__( 'Report Received', 'cubewp-classified' ),
				'recipient' => 'admin',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'reported-post-deleted',
				'label'     => esc_html__( 'Reported Post Deleted (To Post Author)', 'cubewp-classified' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
		);

		return array_merge( $email_types, $new_types );
	}

	public function classified_display_post_status_with_title( $states ) {
		global $post;
		if ( ! empty( $post->post_status ) && in_array( $post->post_status, self::$report_post_statues ) ) {
			?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('#post-status-display').text('<?php echo esc_html( $post->post_status ) ?>');
                });
            </script>
			<?php

			return array( $post->post_status );
		}

		return $states;
	}

	public function classified_report_change_report_status( $data, $postarr ) {
		if ( isset( $postarr['cubewp-change-report-status'] ) && ! empty( $postarr['cubewp-change-report-status'] ) ) {
			$action  = sanitize_text_field( $postarr['cubewp-change-report-status'] );
			$post_id = $postarr['ID'];
			if ( $action == 'accept' ) {
				$report_type = get_post_meta( $post_id, 'cubewp_report_type', true );
				if ( $report_type == 'post' ) {
					$reported_post_id = get_post_meta( $post_id, 'cubewp_reported_post', true );
					$reported_post    = array(
						'ID'          => $reported_post_id,
						'post_status' => 'reported',
					);
					wp_update_post( $reported_post );
				} else if ( $report_type == 'user' ) {
					$reported_user_id = get_post_meta( $post_id, 'cubewp_reported_user', true );
					$user_data        = array(
						'ID'   => $reported_user_id,
						'role' => 'reported'
					);
					wp_update_user( $user_data );
				}
				$data['post_status'] = 'accepted';
			} else {
				$data['post_status'] = 'rejected';
			}
		}

		return $data;
	}

	public function classified_report_add_actions_to_metabox( $post ) {
		if ( isset( $post->post_type ) && $post->post_type == 'cubewp-report' ) {
			if ( $post->post_status == 'publish' ) {
				?>
                <div class="alignleft">
                    <button type="submit" class="button" name="cubewp-change-report-status" value="accept">
						<?php esc_html_e( 'Accept Report', 'cubewp-classified' ); ?>
                    </button>
                </div>
                <div class="alignright">
                    <button type="submit" class="button-primary" name="cubewp-change-report-status" value="reject">
						<?php esc_html_e( 'Reject Report', 'cubewp-classified' ); ?>
                    </button>
                </div>
				<?php
			} else {
				?>
                <p>
                    <strong>
						<?php
						echo sprintf( esc_html__( 'This Report is %s.', 'cubewp-classified' ), $post->post_status );
						?>
                    </strong>
                </p>
				<?php
			}
			?>
            <!--suppress CssUnusedSymbol -->
            <style>
                #delete-action,
                #publishing-action {
                    display: none;
                }
            </style>
			<?php
		}
	}

	public function classified_report_add_to_metabox( $post ) {
		if ( isset( $post->post_type ) && $post->post_type == 'cubewp-report' ) {
			$report_type = get_post_meta( $post->ID, 'cubewp_report_type', true );
			if ( $report_type == 'post' ) {
				$reported_post = get_post_meta( $post->ID, 'cubewp_reported_post', true );
				?>
                <div class="misc-pub-section misc-pub-reported-post">
                    <span class="dashicons dashicons-flag"></span>
                    <span id="reported-post">
                        <?php esc_html_e( 'Reported Post', 'cubewp-classified' ); ?>
                    </span>
                    <a href="<?php echo esc_url( get_edit_post_link( $reported_post ) ) ?>" role="button"
                       target="_blank">
                        <span aria-hidden="true"><?php echo get_the_title( $reported_post ) ?></span>
                    </a>
                </div>
				<?php
			} else if ( $report_type == 'user' ) {
				$reported_user = get_post_meta( $post->ID, 'cubewp_reported_user', true );
				?>
                <div class="misc-pub-section misc-pub-reported-post">
                    <span class="dashicons dashicons-flag"></span>
                    <span id="reported-post">
                        <?php esc_html_e( 'Reported User', 'cubewp-classified' ); ?>
                    </span>
                    <a href="<?php echo esc_url( get_edit_profile_url( $reported_user ) ) ?>" role="button"
                       target="_blank">
                        <span aria-hidden="true"><?php echo classified_get_userdata( $reported_user, 'short_name' ) ?></span>
                    </a>
                </div>
				<?php
			}
		}
	}

	public function classified_report_post_type_form_attrs() {
		if ( is_author() ) {
			$user_id = get_queried_object_id();

			return '<input type="hidden" name="cwp_user_form[cubewp_reported_user]" value="' . absint( $user_id ) . '">';
		} else {
			global $post;
			$post_id = $post->ID ?? get_the_ID();

			return '<input type="hidden" name="cwp_user_form[cubewp_reported_post]" value="' . absint( $post_id ) . '">';
		}
	}

	public function classified_before_report_post_type_form_submission( $__POST ) {
		$reported_post = sanitize_text_field( $__POST['cwp_user_form']['cubewp_reported_post'] ?? 0 );
		$reported_user = sanitize_text_field( $__POST['cwp_user_form']['cubewp_reported_user'] ?? 0 );
		if ( ! $reported_post && ! $reported_user ) {
			wp_send_json( array(
				'type' => 'error',
				'msg'  => esc_html__( 'Sorry! Something went wrong, Please try again later.', 'cubewp-classified' ),
			) );
		}
		if ( $reported_user ) {
			self::$reported_user_id = $reported_user;
			if ( $reported_user == get_current_user_id() ) {
				wp_send_json( array(
					'type' => 'error',
					'msg'  => esc_html__( 'Sorry! You cannot report yourself.', 'cubewp-classified' ),
				) );
			}

			$args    = array(
				'post_type'    => array( 'cubewp-report' ),
				'post_status'  => array( 'publish' ),
				'author'       => get_current_user_id(),
				'fields'       => 'ids',
				'meta_key'     => 'cubewp_reported_user',
				'meta_value'   => $reported_user,
				'meta_compare' => '='
			);
			$reports = get_posts( $args );
			if ( count( $reports ) > 0 ) {
				wp_send_json( array(
					'type'        => 'error',
					'msg'         => esc_html__( 'Sorry! You have already reported this user.', 'cubewp-classified' ),
					'redirectURL' => get_author_posts_url( $reported_user )
				) );
			}
		} else if ( $reported_post ) {
			$post_author_id         = classified_get_post_author( $reported_post );
			self::$reported_post_id = $reported_post;
			if ( $post_author_id == get_current_user_id() ) {
				wp_send_json( array(
					'type' => 'error',
					'msg'  => esc_html__( 'Sorry! You cannot report your own post.', 'cubewp-classified' ),
				) );
			}

			$args    = array(
				'post_type'    => array( 'cubewp-report' ),
				'post_status'  => array( 'publish' ),
				'author'       => get_current_user_id(),
				'fields'       => 'ids',
				'meta_key'     => 'cubewp_reported_post',
				'meta_value'   => $reported_post,
				'meta_compare' => '='
			);
			$reports = get_posts( $args );
			if ( count( $reports ) > 0 ) {
				wp_send_json( array(
					'type'        => 'error',
					'msg'         => esc_html__( 'Sorry! You have already reported this post.', 'cubewp-classified' ),
					'redirectURL' => get_permalink( $reported_post )
				) );
			}
		}
	}

	public function classified_report_post_type_form_submission( $post_id, $__POST ) {
		$reported_user = sanitize_text_field( $__POST['cwp_user_form']['cubewp_reported_user'] ?? 0 );
		$reported_post = sanitize_text_field( $__POST['cwp_user_form']['cubewp_reported_post'] ?? 0 );
		$report_target = '';
		if ( $reported_post ) {
			$reported_by = get_post_meta( $reported_post, 'cubewp_reported_by', true );
			$reported_by = ! empty( $reported_by ) && is_array( $reported_by ) ? $reported_by : array();
			update_post_meta( $post_id, 'cubewp_reported_post', $reported_post );
			$reported_by[] = $post_id;
			update_post_meta( $reported_post, 'cubewp_reported_by', $reported_by );
			$report_target = get_the_title( $reported_post );
			update_post_meta( $post_id, 'cubewp_report_type', 'post' );
		} else if ( $reported_user ) {
			$reported_by = get_user_meta( $reported_user, 'cubewp_reported_by', true );
			$reported_by = ! empty( $reported_by ) && is_array( $reported_by ) ? $reported_by : array();
			update_post_meta( $post_id, 'cubewp_reported_user', $reported_user );
			$reported_by[] = $post_id;
			update_user_meta( $reported_user, 'cubewp_reported_by', $reported_by );
			$report_target = classified_get_userdata( $reported_user, 'short_name' );
			update_post_meta( $post_id, 'cubewp_report_type', 'user' );
		}

		$publish_post = array(
			'ID'          => $post_id,
			'post_status' => 'publish',
		);
		if ( empty( get_the_title( $post_id ) ) ) {
			$publish_post['post_title'] = sprintf( esc_html__( '%s Reported %s', 'cubewp-classified' ), classified_get_userdata( classified_get_post_author( $post_id ), 'name' ), $report_target );
		}
		wp_update_post( $publish_post );
	}

	public function classified_after_report_post_type_form_submission() {
		if ( self::$reported_post_id ) {
			return array(
				'type'        => 'success',
				'msg'         => esc_html__( 'Success! This post is reported successfully.', 'cubewp-classified' ),
				'redirectURL' => get_permalink( self::$reported_post_id )
			);
		} else if ( self::$reported_user_id ) {
			return array(
				'type'        => 'success',
				'msg'         => esc_html__( 'Success! This user is reported successfully.', 'cubewp-classified' ),
				'redirectURL' => get_author_posts_url( self::$reported_user_id ),
			);
		} else {
			return array(
				'type' => 'success',
				'msg'  => esc_html__( 'Success! Reported successfully.', 'cubewp-classified' ),
			);
		}
	}

	public function classified_report_post_type_association( $options, $post_type ) {
		if ( $post_type == 'cubewp-report' ) {
			$new_options     = [];
			$return['title'] = esc_html__( 'Assign Report Form To', 'cubewp-classified' );
			$void_types      = apply_filters( 'classified_report_voided_types', array( 'cwp_booster' ) );
			foreach ( CWP_all_post_types( 'post_types' ) as $slug => $type ) {
				if ( $slug != 'cubewp-report' && ! in_array( $slug, $void_types ) ) {
					$new_options[ 'report_' . $slug ] = $type;
				}
			}
			foreach ( cwp_get_user_roles_name() as $slug => $type ) {
				if ( $slug != 'cubewp-report' && ! in_array( $slug, $void_types ) ) {
					$new_options[ 'report_' . $slug ] = $type;
				}
			}
			$return['options'] = $new_options;

			return array_merge( $return, $options );
		}

		return $options;
	}

	public function classified_report_post_type_into_builder( $defaults, $request_from ) {
		if ( 'post_types' == $request_from ) {
			$defaults['cubewp-report'] = __( 'Reports', 'cubewp-classified' );
		}

		return $defaults;
	}

	public function classified_report_post_types() {
		$labels = array(
			'name'                  => _x( 'Reports', 'Post type general name', 'cubewp-classified' ),
			'singular_name'         => _x( 'Report', 'Post type singular name', 'cubewp-classified' ),
			'menu_name'             => _x( 'Reports', 'Admin Menu text', 'cubewp-classified' ),
			'name_admin_bar'        => _x( 'Report', 'Add New on Toolbar', 'cubewp-classified' ),
			'add_new'               => __( 'Add Report', 'cubewp-classified' ),
			'add_new_item'          => __( 'Add New Report', 'cubewp-classified' ),
			'new_item'              => __( 'New Report', 'cubewp-classified' ),
			'edit_item'             => __( 'Edit Report', 'cubewp-classified' ),
			'view_item'             => __( 'View Report', 'cubewp-classified' ),
			'all_items'             => __( 'All Reports', 'cubewp-classified' ),
			'search_items'          => __( 'Search Reports', 'cubewp-classified' ),
			'parent_item_colon'     => __( 'Parent Reports:', 'cubewp-classified' ),
			'not_found'             => __( 'No reports found.', 'cubewp-classified' ),
			'not_found_in_trash'    => __( 'No reports found in Trash.', 'cubewp-classified' ),
			'featured_image'        => _x( 'Report Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'cubewp-classified' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'cubewp-classified' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'cubewp-classified' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'cubewp-classified' ),
			'archives'              => _x( 'Report archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'cubewp-classified' ),
			'insert_into_item'      => _x( 'Insert into report', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'cubewp-classified' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this report', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'cubewp-classified' ),
			'filter_items_list'     => _x( 'Filter reports list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'cubewp-classified' ),
			'items_list_navigation' => _x( 'Reports list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'cubewp-classified' ),
			'items_list'            => _x( 'Reports list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'cubewp-classified' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'menu_icon'          => 'dashicons-flag',
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'cubewp-report' ),
			'capability_type'    => 'post',
			'capabilities'       => array(
				'create_posts' => false,
			),
			'map_meta_cap'       => true,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'author' ),
		);

		register_post_type( 'cubewp-report', $args );

		register_post_status( 'accepted', array(
			'label'                     => _x( 'Accepted', 'Post status general name', 'cubewp-classified' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'publicly_queryable'        => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Accepted <span class="count">(%s)</span>', 'Accepted <span class="count">(%s)</span>' ),
		) );
		register_post_status( 'rejected', array(
			'label'                     => _x( 'Rejected', 'Post status general name', 'cubewp-classified' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'publicly_queryable'        => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>' ),
		) );
		register_post_status( 'reported', array(
			'label'                     => _x( 'Reported', 'Post status general name', 'cubewp-classified' ),
			'public'                    => false,
			'publicly_queryable'        => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Reported <span class="count">(%s)</span>', 'Reported <span class="count">(%s)</span>' ),
		) );
		add_role( 'reported', 'Reported User', array(
			'read' => false
		) );
		self::$report_post_statues = array(
			'accepted',
			'rejected',
			'reported'
		);
	}
}