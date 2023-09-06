<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Loop_Builder
 */
class CubeWp_Loop_Builder {
	private static $cubewp_loop_field_prefix = 'loop_';

	private static $cubewp_loop_switcher_options = array();

	private static $cubewp_loop_builder_form = array();

	/**
	 * CubeWp_Loop_Builder Constructor.
	 */
	public function __construct() {
		add_action( 'cubewp_loop_builder', array( $this, 'cubewp_loop_builder_ui' ) );
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	/**
	 * Method cubewp_loop_builder_ui
	 *
	 * @return void
	 */
	public function cubewp_loop_builder_ui() {
		self::cubewp_loop_builder_set_switcher_options();
		?>
		<div class="cubewp-content">
			<?php
			self::cubewp_loop_builder_title_bar();
			?>
			<section id="cwpform-builder" class="cwpform-builder cubewp-builder-post_type">
				<?php
				self::cubewp_loop_builder_sidebar();
				self::cubewp_loop_builder_content();
				?>
			</section>
		</div>
		<?php
	}

	private static function cubewp_loop_builder_set_switcher_options() {
		$post_types       = CWP_all_post_types( 'post_types' );
		$_loop_styles     = array(
			'default-style' => esc_html__( 'Default Style', 'cubewp-frontend' )
		);
		$switcher_options = array();
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type => $label ) {
				$switcher_options[ $post_type ]['label']       = $label;
				$loop_styles                                   = apply_filters( "cubewp/loop/builder/{$post_type}/styles", array() );
				$loop_styles                                   = ! empty( $loop_styles ) && is_array( $loop_styles ) ? $loop_styles : array();
				$loop_styles                                   = array_merge( $_loop_styles, $loop_styles );
				$switcher_options[ $post_type ]['loop-styles'] = $loop_styles;
			}
		}

		self::$cubewp_loop_switcher_options = $switcher_options;

		$form_options                   = CWP()->get_form( 'loop_builder' );
		self::$cubewp_loop_builder_form = $form_options;
	}

	private static function cubewp_loop_builder_title_bar() {
		?>
		<section id="cubewp-title-bar">
			<h1><?php esc_html_e( 'Post Loop Generator (Beta)', 'cubewp-frontend' ); ?></h1>
			<div class="shoftcode-area">
				<div class="cwpform-shortcode"></div>
				<button class="button-primary cwpform-get-shortcode">
					<?php esc_html_e( 'Save Changes', 'cubewp-frontend' ); ?>
				</button>
			</div>
		</section>
		<?php
	}

	private static function cubewp_loop_builder_sidebar() {
		$post_types = self::$cubewp_loop_switcher_options;
		?>
		<div class="cubewp-builder-sidebar">
			<?php
			self::cubewp_loop_builder_switcher_options();
			?>
			<div class="cubewp-builder-sidebar-groups-widgets">
				<?php
				if ( ! empty( $post_types ) && is_array( $post_types ) ) {
					foreach ( $post_types as $post_type => $data ) {
						$loop_styles = $data['loop-styles'] ?? array();
						?>
						<div class="sidebar-type-<?php echo esc_attr( $post_type ); ?> cubewp-tab-switcher-target cubewp-switcher-tab-<?php echo esc_attr( $post_type ); ?>">
							<?php
							if ( ! empty( $loop_styles ) && is_array( $loop_styles ) ) {
								foreach ( $loop_styles as $name => $label ) {
									$id = $post_type . '-' . $name;
									?>
									<div class="cubewp-tab-switcher-target cubewp-switcher-tab-<?php echo esc_attr( $id ); ?>"></div>
									<?php
								}
							}
							self::cubewp_loop_builder_default_fields( $post_type );
							self::cubewp_loop_builder_custom_fields( $post_type );
							?>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
	}

	private static function cubewp_loop_builder_switcher_options() {
		$post_types = self::$cubewp_loop_switcher_options;
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			$loop_style_switcher = '';
			?>
			<div class="cubewp-builder-sidebar-option">
				<label for="cubewp-builder-cpt"><?php esc_html_e( 'Select Post Type', 'cubewp-frontend' ); ?></label>
				<select name="cubewp-builder-cpt" id="cubewp-builder-cpt"
				        class="cubewp-tab-switcher cubewp-tab-switcher-trigger-on-load cubewp-tab-switcher-have-child">
					<?php
					foreach ( $post_types as $post_type => $data ) {
						$label       = $data['label'];
						$loop_styles = $data['loop-styles'] ?? array();
						?>
						<option data-switcher-target="cubewp-switcher-tab-<?php echo esc_attr( $post_type ); ?>"
						        value="<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php
						if ( ! empty( $loop_styles ) && is_array( $loop_styles ) ) {
							ob_start();
							?>
							<div
								class="cubewp-switcher-tab-<?php echo esc_attr( $post_type ); ?> cubewp-tab-switcher-target">
								<div class="cubewp-builder-sidebar-option">
									<label for="cubewp-builder-<?php echo esc_attr( $post_type ); ?>-plan">Select Loop
										Style</label>
									<select name="cubewp-builder-<?php echo esc_attr( $post_type ); ?>-plan"
									        id="cubewp-builder-<?php echo esc_attr( $post_type ); ?>-plan"
									        class="cubewp-tab-switcher">
										<?php
										foreach ( $loop_styles as $loop_style => $_label ) {
											$id = $post_type . '-' . $loop_style;
											?>
											<option
												data-switcher-target="cubewp-switcher-tab-<?php echo esc_attr( $id ); ?>"
												value="<?php echo esc_attr( $loop_style ); ?>"><?php echo esc_html( $_label ); ?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
							<?php
							$loop_style_switcher .= ob_get_clean();
						}
					}
					?>
				</select>
			</div>
			<?php
			echo cubewp_core_data( $loop_style_switcher );
		} else {
			?>
			<h4><?php esc_html_e( 'No Post Type Found', 'cubewp-frontend' ); ?></h4>
			<?php
		}
	}

	private static function cubewp_loop_builder_default_fields( $post_type ) {
		$wp_default_fields                = cubewp_post_type_default_fields( $post_type );
		$wp_default_fields['the_excerpt'] = array(
			'label' => __( "Excerpt", "cubewp-frontend" ),
			'name'  => 'the_excerpt',
			'type'  => 'wysiwyg_editor',
		);
		$wp_default_fields['post_link']   = array(
			'label' => __( "Post Link", "cubewp-frontend" ),
			'name'  => 'post_link',
			'type'  => 'url',
		);
		$wp_default_fields['the_date']    = array(
			'label' => __( "Post Date", "cubewp-frontend" ),
			'name'  => 'the_date',
			'type'  => 'date',
		);
		$taxonomies                       = get_object_taxonomies( $post_type, 'objects' );
		if ( ! empty( $wp_default_fields ) ) {
			?>
			<div class="cubewp-builder-section cubewp-expand-container active-expanded">
				<div class="cubewp-builder-section-header">
					<h3><?php esc_html_e( 'WordPress Default Fields', 'cubewp-frontend' ); ?></h3>
					<div class="cubewp-builder-section-actions">
						<span
							class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger expanded"></span>
					</div>
				</div>
				<div class="cubewp-loop-builder-fields cubewp-expand-target">
					<?php
					foreach ( $wp_default_fields as $field ) {
						$label = $field['label'];
						$name  = $field['name'];
						self::cubewp_get_loop_shortcode_field( $name, $label );
					}
					?>
				</div>
			</div>
			<?php
		}
		if ( ! empty( $taxonomies ) && ! is_wp_error( $taxonomies ) ) {
			?>
			<div class="cubewp-builder-section cubewp-expand-container">
				<div class="cubewp-builder-section-header">
					<h3><?php esc_html_e( 'Taxonomies', 'cubewp-frontend' ); ?></h3>
					<div class="cubewp-builder-section-actions">
						<span
							class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
					</div>
				</div>
				<div class="cubewp-loop-builder-fields cubewp-expand-target">
					<?php
					foreach ( $taxonomies as $taxonomy ) {
						$label = $taxonomy->label;
						$name  = $taxonomy->name;
						$link  = $taxonomy->name . '_tax_link';
						self::cubewp_get_loop_shortcode_field( array( $name, $link ), $label );
					}
					?>
				</div>
			</div>
			<?php
		}
		?>
		<div class="cubewp-builder-section cubewp-expand-container">
			<div class="cubewp-builder-section-header">
				<h3><?php esc_html_e( 'Author', 'cubewp-frontend' ); ?></h3>
				<div class="cubewp-builder-section-actions">
					<span
						class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
				</div>
			</div>
			<div class="cubewp-loop-builder-fields cubewp-expand-target">
				<?php
				self::cubewp_get_loop_shortcode_field( 'author_name', esc_html__( 'Author Name', 'cubewp-frontend' ) );
				self::cubewp_get_loop_shortcode_field( 'author_link', esc_html__( 'Author Link', 'cubewp-frontend' ) );
				self::cubewp_get_loop_shortcode_field( 'author_avatar', esc_html__( 'Author Avatar', 'cubewp-frontend' ) );
				?>
			</div>
		</div>
		<div class="cubewp-builder-section cubewp-expand-container">
			<div class="cubewp-builder-section-header">
				<h3><?php esc_html_e( 'CubeWP UI', 'cubewp-frontend' ); ?></h3>
				<div class="cubewp-builder-section-actions">
					<span
						class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
				</div>
			</div>
			<div class="cubewp-loop-builder-fields cubewp-expand-target">
				<?php
				self::cubewp_get_loop_shortcode_field( 'post_save', esc_html__( 'Add | Remove Save', 'cubewp-frontend' ) );
				?>
			</div>
		</div>
		<?php
	}

	private static function cubewp_get_loop_shortcode_field( $name, $label ) {
		$shortcodes = self::cubewp_get_loop_shortcode( $name );
		?>
		<div class="cubewp-loop-builder-field">
			<p class="cubewp-loop-builder-field-label"><?php echo esc_html( $label ); ?></p>
			<?php
			if ( ! empty( $shortcodes ) && is_array( $shortcodes ) ) {
				foreach ( $shortcodes as $shortcode ) {
					$is_marquee = strlen( $shortcode ) > 25;
					?>
					<div class="cubewp-loop-builder-field-shortcode <?php echo $is_marquee ? 'have-marquee' : ''; ?>"
						id="cubewp-loop-field-<?php echo esc_attr( $shortcode ); ?>">
						<span><?php echo esc_html( $shortcode ); ?></span>
					</div>
					<?php
				}
			} else {
				$is_marquee = strlen( $shortcodes ) > 25;
				?>
				<div class="cubewp-loop-builder-field-shortcode <?php echo $is_marquee ? 'have-marquee' : ''; ?>"
				     id="cubewp-loop-field-<?php echo esc_attr( $name ); ?>">
					<span><?php echo esc_html( $shortcodes ); ?></span>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	private static function cubewp_get_loop_shortcode( $name ) {
		if ( ! empty( $name ) && is_array( $name ) ) {
			$return = array();
			foreach ( $name as $shortcode ) {
				$return[] = '[' . self::$cubewp_loop_field_prefix . $shortcode . ']';
			}

			return $return;
		} else {
			return '[' . self::$cubewp_loop_field_prefix . $name . ']';
		}
	}

	private static function cubewp_loop_builder_custom_fields( $post_type ) {
		$groups = cwp_get_groups_by_post_type( $post_type );
		if ( ! empty( $groups ) ) {
			foreach ( $groups as $group_id ) {
				$fields = get_post_meta( $group_id, '_cwp_group_fields', true );
				$terms  = get_post_meta( $group_id, '_cwp_group_terms', true );
				$fields = ! empty( $fields ) ? explode( ',', $fields ) : array();
				$terms  = ! empty( $terms ) ? explode( ',', $terms ) : array();
				if ( empty( $fields ) ) {
					continue;
				}
				?>
				<div class="cubewp-builder-section cubewp-expand-container">
					<div class="cubewp-builder-section-header">
						<h3><?php echo get_the_title( $group_id ); ?></h3>
						<?php
						if ( ! empty( $terms ) ) {
							$separator = '';
							$_terms    = '';
							foreach ( $terms as $term ) {
								$term_data = get_term( $term );
								if ( isset( $term_data->name ) && ! empty( $term_data->name ) ) {
									$_terms    .= $separator . $term_data->name;
									$separator = ',';
								}
							}
							?>
							<div class="cwp-icon-helpTip">
								<span class="dashicons dashicons-editor-help"></span>
								<div class="cwp-ctp-toolTips drop-left">
									<div class="cwp-ctp-toolTip">
										<h4><?php esc_html_e( 'Associated Taxonomies', 'cubewp-frontend' ); ?></h4>
										<p class="cwp-ctp-tipContent"><?php echo esc_html( $_terms ); ?></p>
									</div>
								</div>
							</div>
							<?php
						}
						?>
						<div class="cubewp-builder-section-actions">
							<span
								class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
						</div>
					</div>
					<div class="cubewp-loop-builder-fields cubewp-expand-target">
						<?php
						$voided_fields = array(
							'gallery',
							'review_star',
							'repeating_field',
							'user',
							'taxonomy',
							'post',
						);
						$voided_fields = apply_filters( 'cubewp/loop/builder/voided/field/types', $voided_fields );
						foreach ( $fields as $field ) {
							$field = get_field_options( $field );
							if ( ! isset( $field['name'] ) || in_array( $field['type'], $voided_fields ) ) {
								continue;
							}
							$label = $field['label'];
							$name  = $field['name'];
							self::cubewp_get_loop_shortcode_field( $name, $label );
						}
						?>
					</div>
				</div>
				<?php
			}
		}
	}

	private static function cubewp_loop_builder_content() {
		$post_types = self::$cubewp_loop_switcher_options;
		?>
		<div class="cubewp-builder-container">
			<div class="cubewp-builder">
				<?php
				if ( ! empty( $post_types ) && is_array( $post_types ) ) {
					foreach ( $post_types as $post_type => $data ) {
						$loop_styles = $data['loop-styles'] ?? array();
						?>
						<div id="type-<?php echo esc_html( $post_type ); ?>"
						     class="cubewp-type-container cubewp-switcher-tab-<?php echo esc_html( $post_type ); ?> cubewp-tab-switcher-target">
							<?php
							if ( ! empty( $loop_styles ) && is_array( $loop_styles ) ) {
								foreach ( $loop_styles as $name => $label ) {
									$id = $post_type . '-' . $name;
									?>
									<div id="plan-<?php echo esc_html( $id ); ?>"
									     class="cubewp-plan-tab cubewp-switcher-tab-<?php echo esc_html( $id ); ?> cubewp-tab-switcher-target"
									     data-id="<?php echo esc_html( $name ); ?>"
									     data-type="<?php echo esc_html( $name ); ?>">
										<?php
										self::cubewp_loop_builder_area( $post_type, $name );
										?>
									</div>
									<?php
								}
							} else {
								self::cubewp_loop_builder_area( $post_type, '' );
							}
							?>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
	}

	private static function cubewp_loop_builder_area( $post_type, $style ) {
		$form_options         = self::$cubewp_loop_builder_form;
		$loop_container_class = '';
		$loop_is_primary      = 'no';
		$loop_layout_html     = '';
		if ( ! empty( $form_options[ $post_type ] ) ) {
			$form_options = $form_options[ $post_type ];
			if ( ! empty( $form_options[ $style ] ) ) {
				$form_options = $form_options[ $style ];
			}
			$loop_layout_html     = $form_options['loop-layout-html'];
			$loop_container_class = $form_options['form']['loop-container-class'] ?? '';
			$loop_is_primary      = $form_options['form']['loop-is-primary'] ?? 'no';
		}
		?>

		<div class="cubewp-builder-container-topbar">
			<button class="button form-settings-form">
				<span class="dashicons dashicons-admin-generic"></span>
				<?php esc_html_e( 'Loop Settings', 'cubewp-frontend' ); ?>
			</button>
		</div>

		<div class="cubewp-builder-area cubewp-post-loop-generator">

			<div class="form-settings">
				<div class="cwpform-settings">
					<div class="cwpform-setting-label">
						<h2><?php esc_html_e( 'Loop Settings', 'cubewp-frontend' ); ?></h2>
					</div>
					<div class="cwpform-setting-fields">
						<div class="cwpform-setting-field" style="display: none;">
							<label><?php esc_html_e( 'Loop Container Classes', 'cubewp-frontend' ); ?></label>
							<?php
							$input_attrs = array(
								'class'       => 'form-field',
								'name'        => 'loop-container-class',
								'value'       => $loop_container_class,
								'extra_attrs' => 'data-name="loop-container-class"',
							);
							echo cwp_render_text_input( $input_attrs );
							?>
						</div>
						<div class="cwpform-setting-field">
							<label><?php esc_html_e( 'Enable Loop For This Post Type', 'cubewp-frontend' ); ?></label>
							<?php
							$options     = array(
								'no'  => esc_html__( 'No', 'cubewp-frontend' ),
								'yes' => esc_html__( 'Yes', 'cubewp-frontend' ),
							);
							$input_attrs = array(
								'class'       => 'form-field loop-is-primary',
								'name'        => 'loop-is-primary',
								'value'       => $loop_is_primary,
								'options'     => $options,
								'extra_attrs' => 'data-name="loop-is-primary"',
							);
							echo cwp_render_dropdown_input( $input_attrs );
							?>
						</div>
					</div>
				</div>
			</div>

			<div class="cubewp-builder-sections">
				<div class="cubewp-builder-section cubewp-expand-container active-expanded">
					<div class="cubewp-builder-section-header">
						<h3><?php esc_html_e( 'Loop Layout HTML', 'cubewp-frontend' ); ?></h3>
					</div>
					<div class="cubewp-builder-section-fields cubewp-expand-target">
						<div class="cubewp-builder-group-widget">
							<input type="hidden" class="field-name" value="loop-builder">
							<div class="cubewp-loop-builder-editor-container">
								<input type="hidden" class="cubewp-loop-builder-editor-value group-field"
								       data-name="loop-layout-html"
								       value='<?php echo cubewp_core_data( stripslashes( $loop_layout_html ) ); ?>'>
								<div class="cubewp-loop-builder-editor"
								     id="<?php echo esc_attr( 'cubewp-loop-builder-' . $post_type . '-' . $style . '-editor' ); ?>"></div>
							</div>
						</div>
					</div>
				</div>
				<p><span
						class="dashicons dashicons-info-outline"></span> <?php esc_html_e( 'Add CSS or JS into CubeWP settings > CSS & JS.', 'cubewp-frontend' ); ?>
				</p>
				<p>
				<span class="dashicons dashicons-info-outline"></span> <?php echo sprintf( esc_html__( '%sLearn more%s about Loop Builder.', 'cubewp-classified' ), '<a href="https://support.cubewp.com/docs/cubewp-frontend/how-the-loop-builder-works/" target="_blank">', '</a>' ); ?>
				</p>
				<?php
					do_action( 'cubewp/loop/builder/area', $post_type, $style );
				?>
			</div>
		</div>
		<?php
		self::cubewp_loop_builder_hidden_fields( $post_type );
	}

	protected static function cubewp_loop_builder_hidden_fields( $form_relation ) {
		$hidden_fields = array(
			array(
				'class' => 'form-relation',
				'name'  => 'form_relation',
				'value' => $form_relation,
			),
			array(
				'class' => 'form-type',
				'name'  => 'form_type',
				'value' => 'loop_builder',
			),
		);
		foreach ( $hidden_fields as $field ) {
			echo cwp_render_hidden_input( $field );
		}
	}
}