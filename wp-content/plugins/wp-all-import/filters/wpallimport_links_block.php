<?php

function pmxi_wpallimport_links_block($html){
	$src =  WP_ALL_IMPORT_ROOT_URL . '/static/img/f_logo_RGB-Blue_250.png';
	$text = __( 'Discuss, share your work, and learn from the best.', 'wp_all_export_plugin' );
	$doc_text = __( 'Documentation', 'wp_all_import_plugin' );
	$sup_text = __( 'Support', 'wp_all_import_plugin' );

	$html = <<<EOT

	<div class="wpallimport-links">
				<a href="http://www.wpallimport.com/support/?utm_source=import-plugin-free&utm_medium=help&utm_campaign=premium-support" target="_blank">{$sup_text}</a> | <a href="http://www.wpallimport.com/documentation/?utm_source=import-plugin-free&utm_medium=help&utm_campaign=docs" target="_blank">{$doc_text}</a> 
			</div>

EOT;

	return $html;

}