<?php
// helper function to get download link for add-on from route site.
function cwp_get_item_download_link($license){
	$api_params = array(
		'edd_action' => 'get_version',
		'license'    => ! empty( $license->key ) ? $license->key : '',
		'item_id'    => isset( $license->download_id ) ? $license->download_id : false,
		'url'        => site_url()
	);
	$api_url = 'https://cubewp.com/';
	// call to route url for getting down-loadable link against each add-on
	$request = wp_remote_post( $api_url , array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( ! is_wp_error( $request ) ) {
		$request = json_decode( wp_remote_retrieve_body( $request ) , true );
	}
	if( isset($request['download_link']) ){
		return $request['download_link'];
	}else{
		return false;
	}
}

//helper function to download and install plugin from respective link
function cwp_plugin_activate($download, $slug , $order_id = null , $base = null )
{
    $plugDir = WP_PLUGIN_DIR . '/' . $slug;
    if (!file_exists($plugDir))
    {
        global $wp_filesystem;
        if ( ! $wp_filesystem ) {
            WP_Filesystem();
        }  
		if( !empty($order_id) ){
			$plugin_zip = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $slug.'-'.$order_id.'.zip';
		}else{
			$plugin_zip = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $slug.'.zip';
		}
       
        $extract_path = WP_PLUGIN_DIR;
        $cwp_extract_path = str_replace( ABSPATH, $wp_filesystem->abspath(), $extract_path  );
        $cwp_plugin_zip = str_replace( ABSPATH, $wp_filesystem->abspath(), $plugin_zip  );
		//download file to plugins folder
        wp_remote_get($download, array(
            'stream' => true,
            'timeout' => 90,
            'filename' => $plugin_zip
        ));
        if (is_file($plugin_zip)) 
        {
            if (unzip_file($cwp_plugin_zip, $cwp_extract_path))  //unzip file in plugins folder
            {
                wp_cache_flush();
				if( empty($base) ){
					$base = str_replace('-addon','',str_replace('-pro','',$slug));
					if( $slug == 'cubewp-framework' ){
						$base = 'cube';
					}
				}
				activate_plugin($plugDir.'/'.$base.'.php');
                $wp_filesystem->delete($cwp_plugin_zip);
            }
        }
    }
}

// helper function to activate cubewp framework
function cwp_activate_directory_plugin( $slug , $base){
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    $check_file = dirname(__FILE__).'/'.$slug;
	if (!file_exists($check_file))
    {
		// get download file link from wp plugin repository.
        $api = plugins_api( 'plugin_information',
            array(
                'slug' => $slug,
                'fields' => array(
                    'short_description' => false,
                    'sections' => false,
                    'requires' => false,
                    'rating' => false,
                    'ratings' => false,
                    'downloaded' => false,
                    'last_updated' => false,
                    'added' => false,
                    'tags' => false,
                    'compatibility' => false,
                    'homepage' => false,
                    'donate_link' => false,
                ),
            )
        );
        if (!is_wp_error($api))
        {
            $download = $api->download_link;
			// helper function call to download and activate cubewp framework
            cwp_plugin_activate( $download, $slug , null , $base );
        }
	}
}
