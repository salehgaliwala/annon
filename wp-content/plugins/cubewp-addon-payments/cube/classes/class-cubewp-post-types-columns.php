<?php

/**
 * CubeWP payment related post types columns.
 *
 * @package cubewp-addon-payments/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!class_exists('CubeWp_Post_Types_Columns')){    
    /**
     * CubeWp_Post_Types_Columns
     */
    class CubeWp_Post_Types_Columns {
        public function __construct() {
            add_filter("manage_price_plan_posts_columns", array($this, 'cubewp_filter_price_plan_columns') );
            add_action("manage_price_plan_posts_custom_column", array($this, 'cubewp_price_plan_column'), 20, 2);
            add_action('admin_init', array($this, 'manage_post_types_columns'));
        }
                
        /**
         * Method cubewp_filter_price_plan_columns
         *
         * @param array $columns
         *
         * @return array
         * @since  1.0.0
         */
        public function cubewp_filter_price_plan_columns( $columns ){
            
            $new_columns = array();
            foreach($columns as $key => $column){
                $new_columns[$key]  =  $column;
                if( $key == 'title' ){
                    $new_columns['plan_price']   =  __( 'Plan Price', 'cubewp-payments' );
                    $new_columns['plan_duration']           =  __( 'Plan Duration', 'cubewp-payments' );
                    $new_columns['plan_post_type']   =  __( 'Assosiated Post Type', 'cubewp-payments' );
                    $new_columns['plan_type']        =  __( 'Plan Type', 'cubewp-payments' );
                }
            }
            return $new_columns;
        }
                
        /**
         * Method cubewp_price_plan_column
         *
         * @param string $column 
         * @param int $post_id
         *
         * @return string
         * @since  1.0.0
         */
        public function cubewp_price_plan_column( $column, $post_id ){
            
            switch($column) {
                case 'plan_price':
                    $plan_price = get_post_meta( $post_id, 'plan_price', true );
                    if( $plan_price > 0 ){
                        echo cubewp_price($plan_price);
                    }else{
                        esc_html_e('Free', 'cubewp-payments');
                    }
                break;
                case 'plan_duration':
                    $plan_duration = cwp_plan_duration($post_id);
                    if( $plan_duration > 0 ){
                        echo sprintf(__('%s days', 'cubewp-payments'), $plan_duration);
                    }else{
                        esc_html_e('Lifetime', 'cubewp-payments');
                    }
                break;
                case 'plan_post_type':
                    $plan_post_type = get_post_meta($post_id, 'plan_post_type', true);
                    if( isset($plan_post_type) && !empty($plan_post_type) ){
                        $cwp_custom_types = get_option('cwp_custom_types');
                        echo isset($cwp_custom_types[$plan_post_type]['singular']) ? $cwp_custom_types[$plan_post_type]['singular'] : $plan_post_type;
                    }else{
                        echo '-';
                    }
                break;
                case 'plan_type':
                    $plan_type = get_post_meta($post_id, 'plan_type', true);
                    if($plan_type == 'package'){
                        esc_html_e('Package', 'cubewp-payments');
                    }else{
                        esc_html_e('Per Per Post', 'cubewp-payments');
                    }
                break;
            }
            
        }
                
        /**
         * Method manage_post_types_columns
         *
         * @return void
         * @since  1.0.0
         */
        public function manage_post_types_columns(){
            $cwp_custom_types = get_option('cwp_custom_types');
            if(isset($cwp_custom_types) && !empty($cwp_custom_types)){
                foreach($cwp_custom_types as $cwp_custom_type){
                    add_filter("manage_{$cwp_custom_type['slug']}_posts_columns", array($this, 'cubewp_filter_posts_columns') );
                    add_action("manage_{$cwp_custom_type['slug']}_posts_custom_column", array($this, 'cubewp_posts_column'), 20, 2);
                }
            }
        }
                
        /**
         * Method cubewp_filter_posts_columns
         *
         * @param array $columns
         *
         * @return array
         * @since  1.0.0
         */
        public function cubewp_filter_posts_columns( $columns ){
            
            foreach($columns as $key => $column){
                if( $key == 'date' ){
                    unset($columns['date']);
                    $columns['plan']             =  __( 'Associated Plan', 'cubewp-payments' );
                    $columns['payment_status']   =  __( 'Payment Status', 'cubewp-payments' );
                    $columns['published_date']   =  __( 'Published Date', 'cubewp-payments' );
                    
                    $columns['expire']           =  __( 'Expired On', 'cubewp-payments' );
                    
                }
            }
            
            return $columns;
            
        }
                
        /**
         * Method cubewp_posts_column
         *
         * @param string $column
         * @param int $post_id
         *
         * @return string
         * @since  1.0.0
         */
        public function cubewp_posts_column( $column, $post_id ){
            
            switch($column) {
                case 'published_date':
                    echo get_the_date('Y/m/d H:i a', $post_id);
                break;
                case 'plan':
                    $plan_id = get_post_meta($post_id, 'plan_id', true);
                    if( $plan_id > 0 ){
                        echo esc_html(get_the_title($plan_id));
                    }else{
                        esc_html_e('N/A', 'cubewp-payments');
                    }
                break;
                case 'expire':
                    $post_expired = get_post_meta($post_id, 'post_expired', true);
                    if( isset($post_expired) && !empty($post_expired) ){
                        echo date('Y/m/d', $post_expired);
                    }else{
                        esc_html_e('Lifetime', 'cubewp-payments');
                    }
                break;
                case 'payment_status':
                    $payment_status = get_post_meta($post_id, 'payment_status', true);
                    if( isset($payment_status) && !empty($payment_status) ){
                        echo cubewp_payment_status_label($payment_status);
                    }else{
                        esc_html_e('Free', 'cubewp-payments');
                    }
                break;
            }
            
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
}