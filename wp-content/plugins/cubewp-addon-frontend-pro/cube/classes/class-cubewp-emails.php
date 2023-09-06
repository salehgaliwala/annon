<?php
defined( 'ABSPATH' ) || exit;

/**
 * CubeWp_Emails
 */
class CubeWp_Emails {

	public static $post_type = 'email_template';

	private static $user_roles_for = array();

	private static $post_types_for = array();

	public function __construct() {
		add_filter( 'cubewp/posttypes/new', array( $this, 'register_email_template_post_type' ) );
        add_filter( 'post_updated_messages', array( $this, 'remove_view_email_template_action' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'remove_email_template_page' ) );
		add_filter( 'cubewp/options/sections', array( $this, 'cubewp_settings_emails_section' ) );
		add_action( 'add_meta_boxes', array( $this, 'cubewp_email_template_shortcodes_metabox' ) );
        $this->cubewp_send_post_emails_handler();
        add_action( 'transition_post_status', array( $this, 'cubewp_send_email_on_post_status_change' ), 10, 3 );
		add_action( 'cubewp/after/user/registration', function ( $user_id ) {
			self::cubewp_send_user_emails_handler( $user_id, 'new' );
		} );
		add_action( 'cubewp/after/user/profile/update', function ( $user_id ) {
			self::cubewp_send_user_emails_handler( $user_id, 'update' );
		} );
		add_filter( 'enter_title_here', array( $this, 'change_title_placeholder' ), 10, 2 );
		add_filter( 'default_content', array( $this, 'default_post_content' ), 10, 2 );

		add_filter( 'manage_' . self::$post_type . '_posts_columns', array( $this, 'email_post_type_columns' ) );
		add_action( 'manage_' . self::$post_type . '_posts_custom_column', array(
			$this,
			'email_post_type_column_content'
		), 10, 2 );
		add_action( 'admin_notices', array( $this, 'display_email_template_notice' ) );
	}

	private function cubewp_send_post_emails_handler() {
		$post_emails = cubewp_get_all_post_type_email_templates();
		if ( ! empty( $post_emails ) && is_array( $post_emails ) ) {
			$post_types = array();
			foreach ( $post_emails as $template_id ) {
				$admin_post_types = get_post_meta( $template_id, 'admin_email_post_types', true );
				$user_post_types  = get_post_meta( $template_id, 'user_email_post_types', true );
				$post_types       = array_merge( array_filter( array_unique( array_merge( $admin_post_types, $user_post_types ) ) ), $post_types );
			}
			if ( ! empty( $post_types ) ) {
				$post_types = array_filter( array_unique( $post_types ) );
				foreach ( $post_types as $post_type ) {
					add_filter( "cubewp/{$post_type}/after/submit/actions", array(
						$this,
						'cubewp_send_emails_after_post_submission'
					), 1, 2 );
				}
			}
		}
	}

	private static function cubewp_send_user_emails_handler( $user_id, $type ) {
		$admin_notification = $user_notification = false;
		if ( $type == 'new' ) {
			$admin_notification = cubewp_get_email_template_by_user_id( $user_id, 'admin', 'new-user' );
			$user_notification  = cubewp_get_email_template_by_user_id( $user_id, 'user', 'new-user' );
		} elseif ( $type == 'update' ) {
			$admin_notification = cubewp_get_email_template_by_user_id( $user_id, 'admin', 'user-updated' );
			$user_notification  = cubewp_get_email_template_by_user_id( $user_id, 'user', 'user-updated' );
		}
		if ( $admin_notification ) {
			$email_to = get_post_meta( $admin_notification, 'admin_email', true );
			$email_to = ! empty( $email_to ) && is_email( $email_to ) ? $email_to : get_option( 'admin_email' );
			self::cubewp_send_email( $email_to, $admin_notification, $user_id, false );
		}
		if ( $user_notification ) {
			self::cubewp_send_email( false, $user_notification, $user_id, false );
		}
	}

	public static function cubewp_send_email( $email_to, $template_id, $user_id, $post_id ) {
		if ( ! if_cubewp_emails_enabled() ) {
			return false;
		}
		$email_subject = get_the_title( $template_id );
		$email_content = get_the_content( '', '', $template_id );
		if ( ! $user_id && $post_id ) {
			$user_id = get_post_field( 'post_author', $post_id );
		}
		if ( ! $email_to && $user_id ) {
			$user_obj = get_userdata( $user_id );
			if ( ! is_wp_error( $user_obj ) && ! empty( $user_obj ) ) {
				$email_to = $user_obj->user_email;
			}
		}

		$email_subject = self::cubewp_render_email_shortcodes( $email_subject, $user_id, $post_id, true );
		$email_content = self::cubewp_render_email_shortcodes( $email_content, $user_id, $post_id );

		return cubewp_send_mail( $email_to, $email_subject, $email_content );
	}

	private static function cubewp_render_email_shortcodes( $content, $user_id = false, $post_id = false, $html_entity_decode = false ) {
		if ( ! $user_id && $post_id ) {
			$user_id = get_post_field( 'post_author', $post_id );
		}
		$content = str_replace( '{website_title}', get_bloginfo( 'name' ), $content );
		$content = str_replace( '{website_url}', home_url(), $content );
		if ( $user_id ) {
			$user_obj = get_userdata( $user_id );
			if ( ! is_wp_error( $user_obj ) && ! empty( $user_obj ) ) {
				$content = str_replace( '{user_name}', $user_obj->display_name, $content );
				$content = str_replace( '{user_url}', get_author_posts_url( $user_obj->ID ), $content );
			}
		}
		if ( $post_id ) {
			$content = str_replace( '{post_title}', get_the_title( $post_id ), $content );
			$content = str_replace( '{post_url}', esc_url( get_permalink( $post_id ) ), $content );
			$content = str_replace( '{post_date}', get_the_date( '', $post_id ), $content );
			$content = str_replace( '{post_excerpt}', get_the_excerpt( $post_id ), $content );
			$content = str_replace( '{post_image_url}', get_the_post_thumbnail_url( $post_id ), $content );
			$content = str_replace( '{post_type_label}', get_post_type( $post_id ), $content );
		}
		$content = (string) apply_filters( 'cubewp/email/render/shortcodes', $content, $user_id, $post_id );
		if ( $html_entity_decode ) {
			$content = html_entity_decode( $content, ENT_QUOTES, get_option( 'blog_charset' ) );
		}

		return $content;
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	public function remove_email_template_page() {
		if ( ! current_user_can( 'administrator' ) ) {
			remove_menu_page( 'edit.php?post_type=email_template' );
		}
	}

	public function remove_view_email_template_action( $messages ) {
        if ( isset( $messages[ self::$post_type ] ) ) {
            $messages[ self::$post_type ][1] = esc_html__( 'Email Template updated.', 'cubewp-framework' );
            $messages[ self::$post_type ][6] = esc_html__( 'Email Template published.', 'cubewp-framework' );
            $messages[ self::$post_type ][8] = esc_html__( 'Email Template submitted.', 'cubewp-framework' );
            $messages[ self::$post_type ][10] = esc_html__( 'Email Template draft updated.', 'cubewp-framework' );
        }

        return $messages;
	}

	public function email_post_type_columns( $columns ) {
		$new_column['email_recipient'] = esc_html__( 'Email Recipient', 'cubewp-frontend' );
		$new_column['email_type']      = esc_html__( 'Email Type', 'cubewp-frontend' );
		$position                      = array_search( 'date', array_keys( $columns ) );

		return array_slice( $columns, 0, $position, true ) + $new_column + array_slice( $columns, $position, null, true );
	}

	public function email_post_type_column_content( $column, $post_id ) {
		if ( 'email_recipient' == $column || 'email_type' == $column ) {
			$email_recipient = get_post_meta( $post_id, 'email_recipient', true );
			if ( 'email_recipient' == $column ) {
				echo esc_html( $email_recipient );
			} else {
				$email_type = get_post_meta( $post_id, $email_recipient . '_email_types', true );
				echo esc_html( $email_type );
			}
		}
	}

	public function default_post_content( $content, $post ) {
		$post_type = $post->post_type;
		if ( $post_type == 'email_template' && empty( $content ) ) {
			return esc_html__( 'Email Content', 'cubewp-frontend' );
		}

		return $content;
	}

	public function change_title_placeholder( $title_placeholder, $post ) {
		$post_type = $post->post_type;
		if ( $post_type == 'email_template' ) {
			return esc_html__( 'Email Subject', 'cubewp-frontend' );
		}

		return $title_placeholder;
	}

	public function cubewp_email_template_shortcodes_metabox() {
		if ( if_cubewp_emails_enabled() ) {
			add_meta_box( 'cubewp-email-template-shortcodes-metabox', __( 'Shortcodes', 'cubewp-frontend' ), array(
				$this,
				'cubewp_email_template_shortcode_metabox_render'
			), self::$post_type, 'side' );
		}
	}

	public function cubewp_email_template_shortcode_metabox_render( $post ) {
		if ( isset( $post->ID ) ) {
			$shortcodes = $this->cubewp_email_template_shortcodes();
			?>
            <div class="cubewp-email-template-shortcodes">
				<?php
				if ( ! empty( $shortcodes ) && is_array( $shortcodes ) ) {
					foreach ( $shortcodes as $shortcode ) {
						$value = $shortcode['shortcode'];
						$label = $shortcode['label'];
						?>
                        <div class="cubewp-email-template-shortcode">
                            <span class="cubewp-email-template-shortcode-label"><?php echo esc_html( $label ); ?></span>
                            <span class="cubewp-email-template-shortcode-value"><?php echo esc_html( $value ); ?></span>
                        </div>
						<?php
					}
				}
				?>
            </div>
			<?php
		}
	}

	private function cubewp_email_template_shortcodes() {
		$shortcodes   = array();
		$shortcodes[] = array(
			'label'     => esc_html__( 'Website Title', 'cubewp-frontend' ),
			'shortcode' => '{website_title}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'Website URL', 'cubewp-frontend' ),
			'shortcode' => '{website_url}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'User Name', 'cubewp-frontend' ),
			'shortcode' => '{user_name}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'User Profile URL', 'cubewp-frontend' ),
			'shortcode' => '{user_url}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'Post Title (If Applicable)', 'cubewp-frontend' ),
			'shortcode' => '{post_title}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'Post URL (If Applicable)', 'cubewp-frontend' ),
			'shortcode' => '{post_url}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'Post Date (If Applicable)', 'cubewp-frontend' ),
			'shortcode' => '{post_date}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'Post Excerpt (If Applicable)', 'cubewp-frontend' ),
			'shortcode' => '{post_excerpt}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'Post Thumbnail URL (If Applicable)', 'cubewp-frontend' ),
			'shortcode' => '{post_image_url}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__( 'Post Type Label (If Applicable)', 'cubewp-frontend' ),
			'shortcode' => '{post_type_label}',
		);

		return (array) apply_filters( 'cubewp/email/shortcodes', $shortcodes );
	}

	public function register_email_template_post_type( $post_types ) {
		if ( if_cubewp_emails_enabled() ) {
			$post_types[ self::$post_type ] = array(
				'label'               => esc_html__( 'Email Templates', 'cubewp-frontend' ),
				'singular'            => esc_html__( 'Email Template', 'cubewp-frontend' ),
				'icon'                => 'dashicons-email',
				'slug'                => self::$post_type,
				'description'         => '',
				'supports'            => array( 'title', 'editor' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => false,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'query_var'           => false,
				'rewrite'             => false,
				'rewrite_slug'        => '',
				'rewrite_withfront'   => false,
				'show_in_rest'        => false,
			);

			$this->email_template_custom_fields();
		}

		return $post_types;
	}

	private function email_template_custom_fields() {
		global $cwpOptions;
		$cwpOptions  = ! empty( $cwpOptions ) ? $cwpOptions : get_option( 'cwpOptions' );
		$save_fields = isset( $cwpOptions['refresh_email_fields'] ) ? $cwpOptions['refresh_email_fields'] : false;
		$settings    = get_posts( array(
			'name'        => 'email_template_settings',
			'post_type'   => 'cwp_form_fields',
			'post_status' => 'private',
			'fields'      => 'id',
			'numberposts' => 1,
			'meta_key'    => '_cwp_group_types',
			'meta_value'  => 'email_template',
		) );
		$settings_id = count( $settings ) > 0 ? $settings[0]->ID : '';
		if ( empty( $settings_id ) ) {
			$settings = array(
				'post_title'   => wp_strip_all_tags( __( 'Email Template Settings', 'cubewp-classified' ) ),
				'post_name'    => 'email_template_settings',
				'post_content' => 'Custom fields for email template settings.',
				'post_status'  => 'private',
				'post_author'  => 1,
				'post_type'    => 'cwp_form_fields'
			);
			// Insert the post into the database
			$settings_id = wp_insert_post( $settings );
			update_post_meta( $settings_id, '_cwp_group_visibility', 'secure' );
			update_post_meta( $settings_id, '_cwp_group_types', 'email_template' );
			update_post_meta( $settings_id, '_cwp_group_order', 1 );
			$save_fields = true;
		}
		if ( $save_fields ) {
			$setting_fields         = $this->email_template_setting_fields( $settings_id );
			$settings_custom_fields = array();
			foreach ( $setting_fields as $key => $setting_field ) {
				$settings_custom_fields[] = $key;
				CubeWp_Custom_Fields_Processor::set_option( $key, $setting_field );
			}
			update_post_meta( $settings_id, '_cwp_group_fields', implode( ',', $settings_custom_fields ) );
		}
	}

	private function email_template_setting_fields( $settings_id ) {
		$fields                           = array();
		$fields['email_recipient']        = array(
			'label'                => __( 'Email Recipient', 'cubewp-classified' ),
			'name'                 => 'email_recipient',
			'type'                 => 'dropdown',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( array(
				'label' => array(
					esc_html__( 'Admin', 'cubewp-frontend' ),
					esc_html__( 'User', 'cubewp-frontend' ),
				),
				'value' => array(
					'admin',
					'user'
				),
			) ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'id'                   => 'email_recipient',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => false,
			'conditional_operator' => '!empty',
			'conditional_value'    => '',
			'group_id'             => $settings_id
		);
		$fields['admin_email']            = array(
			'label'                => __( 'Admin Email', 'cubewp-classified' ),
			'name'                 => 'admin_email',
			'type'                 => 'email',
			'default_value'        => '',
			'description'          => esc_html__( 'Enter an alternative email address for admin notifications. If left blank, the website admin email will be used.', 'cubewp-frontend' ),
			'placeholder'          => esc_html__( 'Email or leave empty.', 'cubewp-frontend' ),
			'options'              => json_encode( array() ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => false,
			'validation_msg'       => '',
			'id'                   => 'admin_email',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => true,
			'conditional_field'    => 'email_recipient',
			'conditional_operator' => '==',
			'conditional_value'    => 'admin',
			'group_id'             => $settings_id
		);
		$fields['admin_email_types']      = array(
			'label'                => __( 'Type', 'cubewp-classified' ),
			'name'                 => 'admin_email_types',
			'type'                 => 'dropdown',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( self::cubewp_email_types( 'admin' ) ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'id'                   => 'admin_email_types',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => true,
			'conditional_field'    => 'email_recipient',
			'conditional_operator' => '==',
			'conditional_value'    => 'admin',
			'group_id'             => $settings_id
		);
		$fields['user_email_types']       = array(
			'label'                => __( 'Type', 'cubewp-classified' ),
			'name'                 => 'user_email_types',
			'type'                 => 'dropdown',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( self::cubewp_email_types( 'user' ) ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'id'                   => 'user_email_types',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => true,
			'conditional_field'    => 'email_recipient',
			'conditional_operator' => '==',
			'conditional_value'    => 'user',
			'group_id'             => $settings_id
		);
		$post_types                       = self::cubewp_email_post_types();
		$fields['admin_email_post_types'] = array(
			'label'                => __( 'Post Types', 'cubewp-classified' ),
			'name'                 => 'admin_email_post_types',
			'type'                 => 'dropdown',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( $post_types ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'multiple'             => true,
			'select2_ui'           => true,
			'id'                   => 'admin_email_post_types',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => true,
			'conditional_field'    => 'admin_email_types',
			'conditional_operator' => '==',
			'conditional_value'    => implode( '--OR--', self::$post_types_for['admin'] ),
			'group_id'             => $settings_id
		);
		$fields['user_email_post_types']  = array(
			'label'                => __( 'Post Types', 'cubewp-classified' ),
			'name'                 => 'user_email_post_types',
			'type'                 => 'dropdown',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( $post_types ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'multiple'             => true,
			'select2_ui'           => true,
			'id'                   => 'user_email_post_types',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => true,
			'conditional_field'    => 'user_email_types',
			'conditional_operator' => '==',
			'conditional_value'    => implode( '--OR--', self::$post_types_for['user'] ),
			'group_id'             => $settings_id
		);
		$user_roles                       = self::cubewp_email_user_roles();
		$fields['admin_email_user_roles'] = array(
			'label'                => __( 'User Roles', 'cubewp-classified' ),
			'name'                 => 'admin_email_user_roles',
			'type'                 => 'dropdown',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( $user_roles ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'multiple'             => true,
			'select2_ui'           => true,
			'id'                   => 'admin_email_user_roles',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => true,
			'conditional_field'    => 'admin_email_types',
			'conditional_operator' => '==',
			'conditional_value'    => implode( '--OR--', self::$user_roles_for['admin'] ),
			'group_id'             => $settings_id
		);
		$fields['user_email_user_roles']  = array(
			'label'                => __( 'User Roles', 'cubewp-classified' ),
			'name'                 => 'user_email_user_roles',
			'type'                 => 'dropdown',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode( $user_roles ),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'multiple'             => true,
			'select2_ui'           => true,
			'id'                   => 'user_email_user_roles',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => true,
			'conditional_field'    => 'user_email_types',
			'conditional_operator' => '==',
			'conditional_value'    => implode( '--OR--', self::$user_roles_for['user'] ),
			'group_id'             => $settings_id
		);

		return $fields;
	}

	private static function cubewp_email_types( $_recipient ) {
		$email_types = array(
			// For Admin
			array(
				'name'      => 'new-post',
				'label'     => esc_html__( 'New Post Submitted', 'cubewp-frontend' ),
				'recipient' => 'admin',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'post-updated',
				'label'     => esc_html__( 'Post Updated', 'cubewp-frontend' ),
				'recipient' => 'admin',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'post-published',
				'label'     => esc_html__( 'Post Published', 'cubewp-frontend' ),
				'recipient' => 'admin',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'post-expired',
				'label'     => esc_html__( 'Post Expired', 'cubewp-frontend' ),
				'recipient' => 'admin',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'new-user',
				'label'     => esc_html__( 'New User Registration', 'cubewp-frontend' ),
				'recipient' => 'admin',
				'sub_field' => 'user_roles'
			),
			array(
				'name'      => 'user-updated',
				'label'     => esc_html__( 'User Updated', 'cubewp-frontend' ),
				'recipient' => 'admin',
				'sub_field' => 'user_roles'
			),
			// For Users
			array(
				'name'      => 'new-post',
				'label'     => esc_html__( 'User Post Submitted', 'cubewp-frontend' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'post-updated',
				'label'     => esc_html__( 'User Post Updated', 'cubewp-frontend' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'post-published',
				'label'     => esc_html__( 'Post Published', 'cubewp-frontend' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'post-expired',
				'label'     => esc_html__( 'Post Expired', 'cubewp-frontend' ),
				'recipient' => 'user',
				'sub_field' => 'post_types'
			),
			array(
				'name'      => 'new-user',
				'label'     => esc_html__( 'User Registered', 'cubewp-frontend' ),
				'recipient' => 'user',
				'sub_field' => 'user_roles'
			),
			array(
				'name'      => 'user-updated',
				'label'     => esc_html__( 'User Updated', 'cubewp-frontend' ),
				'recipient' => 'user',
				'sub_field' => 'user_roles'
			),
		);
		$email_types = ( array ) apply_filters( 'cubewp/email/types', $email_types );
		$return      = array();
		$key         = 0;
		foreach ( $email_types as $type ) {
			$value     = isset( $type['name'] ) ? $type['name'] : '';
			$label     = isset( $type['label'] ) ? $type['label'] : '';
			$recipient = isset( $type['recipient'] ) ? $type['recipient'] : '';
			$sub_field = isset( $type['sub_field'] ) ? $type['sub_field'] : false;
			if ( ! empty( $value ) && ! empty( $label ) && ! empty( $recipient ) ) {
				if ( $recipient == $_recipient ) {
					$return['label'][ $key ] = $label;
					$return['value'][ $key ] = $value;
					if ( ! empty( $sub_field ) ) {
						if ( $sub_field == 'post_types' ) {
							self::$post_types_for[ $recipient ][] = $value;
						} elseif ( $sub_field == 'user_roles' ) {
							self::$user_roles_for[ $recipient ][] = $value;
						}
					}
					$key ++;
				}
			}
		}

		return $return;
	}

	private static function cubewp_email_post_types() {
		$post_types = CWP_all_post_types();
		$post_types = apply_filters( 'cubewp/email/type/post_types', $post_types );
		$return     = array();
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			$key = 0;
			foreach ( $post_types as $type => $label ) {
				$return['label'][ $key ] = $label;
				$return['value'][ $key ] = $type;
				$key ++;
			}
		}

		return $return;
	}

	private static function cubewp_email_user_roles() {
		$user_roles = cwp_get_user_roles();
		$user_roles = apply_filters( 'cubewp/email/type/user_roles', $user_roles );
		$return     = array();
		if ( ! empty( $user_roles ) && is_array( $user_roles ) ) {
			$key = 0;
			foreach ( $user_roles as $type => $data ) {
				$return['label'][ $key ] = $data['name'];
				$return['value'][ $key ] = $type;
				$key ++;
			}
		}

		return $return;
	}

	public function cubewp_settings_emails_section( $sections ) {
		$sections['emails-settings'] = array(
			'title'  => __( 'Emails', 'cubewp-frontend' ),
			'id'     => 'emails-settings',
			'icon'   => 'dashicons-email',
			'fields' => array(
				array(
					'id'      => 'enable_emails',
					'type'    => 'switch',
					'title'   => __( 'Enable Emails', 'cubewp-frontend' ),
					'desc'    => __( 'Enable if you want to send cubewp emails.', 'cubewp-frontend' ),
					'default' => '1',
				),
				array(
					'id'       => 'refresh_email_fields',
					'type'     => 'switch',
					'title'    => __( 'Refresh Email Fields', 'cubewp-frontend' ),
					'desc'     => __( 'If you have added a new CubeWP addon and want to load new email settings, you can enable this option and then reload the page. After that, you may disable it again.', 'cubewp-frontend' ),
					'default'  => '0',
					'required' => array(
						array( 'enable_emails', 'equals', '1' )
					)
				)
			)
		);

		return $sections;
	}

	public function cubewp_send_emails_after_post_submission( $return, $post ) {
		$post_id = isset( $post['post_id'] ) ? $post['post_id'] : 0;
		if ( ! empty( $post_id ) ) {
			if ( isset( $_REQUEST['cwp_user_form']['pid'] ) && ! empty( $_REQUEST['cwp_user_form']['pid'] ) ) {
				$admin_notification = cubewp_get_email_template_by_post_id( $post_id, 'admin', 'post-updated' );
				$user_notification  = cubewp_get_email_template_by_post_id( $post_id, 'user', 'post-updated' );
			} else {
				$admin_notification = cubewp_get_email_template_by_post_id( $post_id, 'admin', 'new-post' );
				$user_notification  = cubewp_get_email_template_by_post_id( $post_id, 'user', 'new-post' );
			}

			if ( $user_notification ) {
				self::cubewp_send_email( false, $user_notification, false, $post_id );
			}
			if ( $admin_notification ) {
				$email_to = get_post_meta( $admin_notification, 'admin_email', true );
				$email_to = ! empty( $email_to ) && is_email( $email_to ) ? $email_to : get_option( 'admin_email' );
				self::cubewp_send_email( $email_to, $admin_notification, false, $post_id );
			}
		}

		return $return;
	}

	public function cubewp_send_email_on_post_status_change( $new_status, $old_status, $post ) {
		$post_type = $post->post_type;
		$post_id   = $post->ID;
        if ( $old_status == 'auto-draft' && ! wp_is_post_revision( $post ) ) {
            $admin_notification = cubewp_get_email_template_by_post_id( $post_id, 'admin', 'new-post' );
            $user_notification  = cubewp_get_email_template_by_post_id( $post_id, 'user', 'new-post' );
            if ( $user_notification ) {
                self::cubewp_send_email( false, $user_notification, false, $post_id );
            }
            if ( $admin_notification ) {
                $email_to = get_post_meta( $admin_notification, 'admin_email', true );
                $email_to = ! empty( $email_to ) && is_email( $email_to ) ? $email_to : get_option( 'admin_email' );
                self::cubewp_send_email( $email_to, $admin_notification, false, $post_id );
            }
        } else if ( $new_status == 'publish' && $old_status != 'publish' ) {
			global $cwpOptions;
			$cwpOptions = empty( $cwpOptions ) ? get_option( 'cwpOptions' ) : $cwpOptions;
			if ( isset( $cwpOptions['post_admin_approved'][ $post_type ] ) && $cwpOptions['post_admin_approved'][ $post_type ] == 'pending' ) {
				$admin_notification = cubewp_get_email_template_by_post_id( $post_id, 'admin', 'post-published' );
				if ( $admin_notification ) {
					$email_to = get_post_meta( $admin_notification, 'admin_email', true );
					$email_to = ! empty( $email_to ) && is_email( $email_to ) ? $email_to : get_option( 'admin_email' );
					self::cubewp_send_email( $email_to, $admin_notification, false, $post_id );
				}
				$user_notification = cubewp_get_email_template_by_post_id( $post_id, 'user', 'post-published' );
				if ( $user_notification ) {
					$author_email = get_the_author_meta( 'user_email', $post->post_author );
					self::cubewp_send_email( $author_email, $user_notification, $post->post_author, $post_id );
				}
			}
		} else if ( $new_status == 'expired' && $old_status == 'publish' ) {
			$admin_notification = cubewp_get_email_template_by_post_id( $post_id, 'admin', 'post-expired' );
			if ( $admin_notification ) {
				$email_to = get_post_meta( $admin_notification, 'admin_email', true );
				$email_to = ! empty( $email_to ) && is_email( $email_to ) ? $email_to : get_option( 'admin_email' );
				self::cubewp_send_email( $email_to, $admin_notification, false, $post_id );
			}
			$user_notification = cubewp_get_email_template_by_post_id( $post_id, 'user', 'post-expired' );
			if ( $user_notification ) {
				$author_email = get_the_author_meta( 'user_email', $post->post_author );
				self::cubewp_send_email( $author_email, $user_notification, $post->post_author, $post_id );
			}
		}
	}

	public function display_email_template_notice() {
		$screen = get_current_screen();
		if ( $screen->post_type === self::$post_type ) {
			   ?>
			   <div class="notice notice-info" style="display: flex;align-items: center;">
                   <span class="dashicons dashicons-info-outline" style="margin: 0 10px 0 0;font-size: 35px;width: 35px;height: 35px;color: #72aee6;"></span>
                   <p>
                       <?php esc_html_e( 'If you\'re encountering missing options or settings, please go to CubeWP Settings > Email and enable the "Refresh Email Fields" option.', 'cubewp-framework' ); ?>
                       <br>
                       <?php esc_html_e( "Once you've acquired the necessary options or settings, feel free to disable this option.", 'cubewp-framework' ); ?>
                   </p>
			   </div>
			   <?php
		}
	}
}