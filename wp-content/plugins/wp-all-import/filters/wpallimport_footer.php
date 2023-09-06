<?php

function pmxi_wpallimport_footer($html){
	$src = WP_ALL_IMPORT_ROOT_URL.'/static/img/f_logo_RGB-Blue_250.png';
	$text = __('Discuss, share your work, and learn from the best.', 'wp_all_export_plugin');
	$created =  esc_html__('Created by', 'wp_all_export_plugin');
	$upgrade_text = __( 'Find out more about the Pro edition of WP All Import.', 'wp_all_import_plugin' );

	$html = <<<EOT
	<div class="wpallimport-footer">
	<div class="wpallimport-footer-left-column wpallimport-text-link">
	<a href="http://www.wpallimport.com/wordpress-xml-csv-import/?utm_source=import-plugin-free&utm_medium=help&utm_campaign=upgrade-to-pro" target="_blank" >{$upgrade_text}</a>
	</div>
	<div class="wpallimport-soflyy">
		<a href="http://soflyy.com/" target="_blank" class="wpallimport-created-by">{$created}<span></span></a>
	</div>
	<div class="wpallimport-cta-text-link">
	    <a href="https://www.facebook.com/groups/wpallimport" target="_blank" ><img src="{$src}" alt="Find us on Facebook"/></a>
        <p><a href="https://www.facebook.com/groups/wpallimport" target="_blank" >{$text}</a></p>
    </div>
	</div>
EOT;

	return $html;
}