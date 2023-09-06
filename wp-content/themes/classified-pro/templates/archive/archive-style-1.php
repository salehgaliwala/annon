<?php
defined( 'ABSPATH' ) || exit;

global $cubewp_frontend, $cwpOptions;

?>
<section class="classified-archive-container">
    <div class="container">
        <div class="row">
			<?php echo classified_breadcrumb(); ?>
			<?php if ( $cwpOptions['archive_filters'] && $cwpOptions['cubewp_archive'] ) { ?>
                <div class="col-12 col-lg-2">
                    <button class="classified-filled-btn classified-archive-show-filters"
                            data-shown-text="<?php esc_html_e( 'Show Filters', 'classified-pro' ); ?>"
                            data-hidden-text="<?php esc_html_e( 'Hide Filters', 'classified-pro' ); ?>">
                        <i class="fa-solid fa-filter" aria-hidden="true"></i>
						<?php esc_html_e( 'Show Filters', 'classified-pro' ); ?>
                    </button>
                    <div class="classified-search-filters-container cwp-search-filters">
                        <div class="classified-search-filters-title-and-reset">
                            <h5><?php esc_html_e( "Filters", "classified-pro" ); ?></h5>
                            <p class="clear-filters p-lg">
                                <i class="fa-solid fa-rotate" aria-hidden="true"></i>
								<?php esc_html_e( "Reset", "classified-pro" ); ?>
                            </p>
                        </div>
						<?php $cubewp_frontend::filters(); ?>
                    </div>
                </div>
			<?php } else {
				$cubewp_frontend::filters();
			} ?>
            <div class="col-12 <?php echo esc_attr( ( $cwpOptions['archive_filters'] && $cwpOptions['cubewp_archive'] ) ? 'col-lg-10' : 'col-lg-12' ); ?>">
                <div class="cwp-archive-container <?php echo classified_get_archive_layout(); ?>">
					<?php if ( ( $cwpOptions['archive_found_text'] || $cwpOptions['archive_sort_filter'] || $cwpOptions['archive_layout'] || $cwpOptions['archive_map'] ) && $cwpOptions['cubewp_archive'] ) { ?>
                        <div class="classified-archive-content-container">
                            <div class="classified-archive-content-header">
                                <div class="classified-archive-info-and-actions d-flex align-items-center flex-wrap justify-content-start">
									<?php if ( $cwpOptions['archive_found_text'] ) { ?>
                                        <h3 class="classified-archive-results-found cwp-total-results"></h3>
									<?php } ?>
                                    <div class="classified-archive-actions d-flex align-items-center flex-wrap justify-content-start ms-auto">
										<?php if ( $cwpOptions['archive_map'] ) { ?>
                                            <div class="classified-archive-map-toggle">
                                                <label for="classified-map-toggle">
                                                    <i class="fa-regular fa-map" aria-hidden="true"></i>
													<?php esc_html_e( "Show Map", "classified-pro" ); ?>
                                                </label>
                                                <div class="classified-toggle">
                                                    <input type="checkbox" id="classified-map-toggle">
                                                    <label for="classified-map-toggle"></label>
                                                </div>
                                            </div>
										<?php } ?>
										<?php if ( $cwpOptions['archive_layout'] ) { ?>
                                            <div class="classified-archive-grid-list-switcher d-flex align-items-center flex-wrap justify-content-start">
                                                <div class="cwp-archive-toggle-Listing-style">
                                                    <div class="listing-switcher grid-view">
                                                        <i class="fa-solid fa-grip" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="listing-switcher list-view">
                                                        <i class="fa-solid fa-list" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
										<?php } ?>
										<?php if ( $cwpOptions['archive_sort_filter'] ) { ?>
                                            <div class="classified-archive-sort">
                                                <label for="cwp-sorting-filter"><?php esc_html_e( "Sort by:", "classified-pro" ); ?> </label>
												<?php echo cubewp_core_data( $cubewp_frontend->sorting_filter() ); ?>
                                            </div>
										<?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php } ?>
                    <div class="cwp-search-result-output"></div>
                    <div class="cwp-archive-content-map"></div>
                </div>
            </div>
        </div>
    </div>
</section>