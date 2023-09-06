<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode For Classified Items.
 *
 * @class Classified_Items_Shortcode
 */
class Classified_Multi_Search_Shortcode {
	public function __construct() {
		add_shortcode( 'classified_multi_search_shortcode', array( $this, 'classified_multi_search_callback' ) );
		add_filter( 'classified_multi_search_shortcode_output', array( $this, 'classified_multi_search' ), 10, 2 );
	}

	public static function classified_multi_search( $output, $parameters ) {
		$search_layout     = $parameters['search_layout'] ?? 'style-1';
		$multi_search_tabs = $parameters['search_tabs'] ?? array();
		ob_start();
		if ( ! empty( $multi_search_tabs ) && is_array( $multi_search_tabs ) ) {
			?>
            <div class="classified-multi-search-container <?php echo sprintf( esc_attr( 'classified-multi-search-%s' ), $search_layout ) ?>">
				<?php
				if ( count( $multi_search_tabs ) > 1 ) {
					?>
                    <ul class="nav nav-tabs classified-multi-search-tabs">
						<?php
						foreach ( $multi_search_tabs as $key => $search_tab ) {
							$tab_text      = $search_tab['classified_multi_search_tab_text'];
							$icon_class    = $search_tab['classified_multi_search_tab_icon'] ?? '';
							$widget_image  = $search_tab['classified_multi_search_tab_bg_image'] ?? array();
							$widget_bg_url = $widget_image['url'] ?? false;
							$widget_bg_alt = $widget_image['alt'] ?? '';
							$active_class  = $key == 0 ? 'active' : '';
							$rand_id       = $search_tab['_id'];
							?>
                            <li class="nav-item classified-multi-search-tab">
                                <button class="nav-link <?php echo esc_attr( $active_class ); ?> classified-multi-search-tab-btn"
                                        id="classified-search-<?php echo esc_attr( $rand_id ); ?>" data-bs-toggle="tab"
                                        data-bs-target="#classified-search-<?php echo esc_attr( $rand_id ); ?>-target"
                                        type="button">
									<?php
									if ( ! empty( $widget_bg_url ) ) {
										?>
                                        <img style="display: none;" src="<?php echo esc_url( $widget_bg_url ); ?>"
                                             alt="<?php echo esc_url( $widget_bg_alt ); ?>">
										<?php
									}
									if ( ! empty( $icon_class ) ) {
										?>
                                        <i class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
										<?php
									}
									echo esc_html( $tab_text );
									?>
                                </button>
                            </li>
							<?php
						}
						?>
                    </ul>
					<?php
				}
				?>
                <div class="tab-content classified-multi-search-tab-content <?php echo ( count( $multi_search_tabs ) > 1 && count( $multi_search_tabs ) < 4 ) ? 'classified-multi-search-have-tabs' : ''; ?>">
					<?php
					foreach ( $multi_search_tabs as $key => $search_tab ) {
						$tab_type     = $search_tab['classified_multi_search_post_type'];
						$tab_info     = $search_tab['classified_multi_search_tab_info'] ?? '';
						$active_class = $key == 0 ? 'active' : '';
						$rand_id      = $search_tab['_id'];
						?>
                        <div class="tab-pane <?php echo esc_attr( $active_class ); ?> classified-multi-search-tab-content"
                             id="classified-search-<?php echo esc_attr( $rand_id ); ?>-target"
                             role="tabpanel">
                            <div class="classified-visible-on-load">
								<?php
								echo do_shortcode( '[cwpSearch type="' . $tab_type . '"]' );
								if ( $search_layout != 'style-1' && ! empty( $tab_info ) ) {
									?>
                                    <div class="classified-search-tab-info">
                                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
										<?php
										echo cubewp_core_data( $tab_info );
										?>
                                    </div>
									<?php
								}
								?>
                            </div>
                        </div>
						<?php
					}
					?>
                </div>
            </div>
			<?php

		}

		return ob_get_clean();
	}

	public function classified_multi_search_callback( $parameters ) {
		$title  = $parameters['title'] ?? '';
		$output = '<div class="classified-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="classified-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'classified_multi_search_shortcode_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}

	public static function init() {
		$ClassifiedClass = __CLASS__;
		new $ClassifiedClass;
	}
}