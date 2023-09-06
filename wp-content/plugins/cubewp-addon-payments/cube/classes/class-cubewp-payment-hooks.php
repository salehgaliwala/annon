<?php

/**
 * Payment hooks.
 *
 * @package cubewp-addon-payments/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists('CubeWp_Payment_Hooks') ) {    
    /**
     * CubeWp_Payment_Hooks
     */
    class CubeWp_Payment_Hooks {
        
        public function __construct() {
            add_filter('cubewp_check_post_payment_status', array($this, 'cubewp_check_post_payment_status'), 10, 3);
            add_filter('cubewp_check_single_payment_status', array($this, 'cubewp_check_single_payment_status'), 10, 2);
            add_filter('wp_ajax_cubewp_pay_post', array($this, 'cubewp_pay_post'), 10);
        }
                
        /**
         * Method cubewp_check_post_payment_status
         *
         * @param string $payment_status
         * @param int $plan_id 
         * @param int $post_id
         *
         * @return string
         * @since  1.0.0
         */
        public function cubewp_check_post_payment_status( $payment_status = '', $plan_id = 0, $post_id = 0 ) {
            global $wpdb;

            $orders_table     = $wpdb->prefix.'cube_orders';
            $order_meta_table = $wpdb->prefix.'cube_order_meta';

            if($wpdb->get_var("SHOW TABLES LIKE '$orders_table'") != $orders_table) {
                return 'pending';
            }
           
            $user_ID          = get_current_user_id();
            $plan_type        = get_post_meta($plan_id, 'plan_type', true);
            $no_of_posts      = get_post_meta($plan_id, 'no_of_posts', true);
            
            if( $plan_type == 'package' ){
                $results     = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT ". $orders_table .".ID as orderID FROM $orders_table 
                        WHERE ". $orders_table .".userID=%d 
                        AND ". $orders_table .".planID=%d 
                        AND ". $orders_table .".planType=%s
                        AND ". $orders_table .".status=%s 
                        ORDER BY ". $orders_table .".ID DESC", $user_ID, $plan_id, 'package', 'complete'
                    ), ARRAY_A
                );

                if(isset($results['orderID']) && !empty($results['orderID'])){
                    
                    $order_meta = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT COUNT(". $order_meta_table .".ID) as count_posts FROM $order_meta_table 
                            WHERE ". $order_meta_table .".orderID=%d 
                            AND ". $order_meta_table .".meta_key=%s 
                            ORDER BY ". $order_meta_table .".ID DESC", $results['orderID'], 'postID'
                        ), ARRAY_A
                    );
                    
                    if( isset($order_meta['count_posts']) && ($order_meta['count_posts'] < $no_of_posts || $no_of_posts == '') ){
                        $payment_status = get_post_meta($post_id, 'payment_status', true);
                        if( $payment_status == 'pending' ){
                            $wpdb->insert(
                                $wpdb->prefix.'cube_order_meta',
                                [
                                    'orderID'    => abs($results['orderID']),
                                    'meta_key'   => 'postID',
                                    'meta_value' => $post_id
                                ],
                                [
                                    '%d',
                                    '%s',
                                    '%s'
                                ]
                            );
                            $plan_duration  =  cwp_plan_duration($plan_id);
                            if( $plan_duration > 0 ){
                                $post_expired   = strtotime("+". $plan_duration." days", strtotime(current_time('Y-m-d H:i:s')));
                                update_post_meta($post_id, 'post_expired', $post_expired);
                            }
                            update_post_meta($post_id, 'payment_status', 'paid');
                        }
                        return 'paid';
                    }
                }
                return 'pending';
            }else{
                $results     = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT ". $order_meta_table .".meta_value as postID FROM $orders_table 
                        INNER JOIN ". $order_meta_table ." ON " . $order_meta_table . ".orderID = ". $orders_table. ".ID 
                        WHERE ". $orders_table .".userID=%d 
                        AND ". $orders_table .".planID=%d 
                        AND ". $orders_table .".planType=%s
                        AND ". $orders_table .".status=%s 
                        AND ". $order_meta_table .".meta_key=%s 
                        ORDER BY ". $order_meta_table .".ID DESC", $user_ID, $plan_id, 'pay_per_post', 'complete', 'postID'
                    ), OBJECT_K
                );

                if(isset($results) && !empty($results)){
                    $post_ids = array_keys( (array) $results );
                    if(isset($post_ids) && in_array($post_id, $post_ids)){
                        return 'paid';
                    }
                }
                return 'pending';
            }
            
        }

        /**
         * Method cubewp_check_single_payment_status
         *
         * @param string $payment_status
         * @param int $post_id
         *
         * @return string
         * @since  1.0.0
         */
        public function cubewp_check_single_payment_status( $payment_status = '', $post_id = 0 ) {
            global $wpdb;
            
            $orders_table     = $wpdb->prefix.'cube_orders';
            $order_meta_table = $wpdb->prefix.'cube_order_meta';
           
            $user_ID          = get_current_user_id();
            $order_ID = get_post_meta($post_id,'order_id',true);
            $order = new WC_Order($order_ID);
            $order_status = $order->get_status();

            if($order_status == 'completed'){
                return 'paid';
            }else{
                return 'pending';
            }
            
        }
                
        /**
         * Method cubewp_pay_post
         *
         * @return array
         * @since  1.0.0
         */
        public function cubewp_pay_post(){
            
            $post_id      = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
            $plan_id      =  get_post_meta($post_id, 'plan_id', true);
            $plan_price   =  get_post_meta($plan_id, 'plan_price', true);
            if( isset($plan_price) && $plan_price > 0 ){
                $payment_args = array(
                    'post_id'    =>    $post_id,
                    'plan_id'    =>    $plan_id,
                    'plan_name'  =>    esc_html(get_the_title($plan_id)),
                    'price'      =>    $plan_price
                );
                $cubwp_payments = new CubeWp_Payments();
                $checkout_url = $cubwp_payments->process_payment( $payment_args );
                wp_send_json(
                    array(
                        'type'        =>  'success',
                        'msg'         =>  sprintf(__('Success! %s is added successfully in cart', 'cubewp-payments'), get_post_type($post_id)),
                        'redirectURL' =>  $checkout_url
                    )
                );
            }
            
        }        

        public function cubewp_process_single_payment($args = array()){
            $price      = isset($args['price']) ? $args['price'] : 0;
            $post_id      = isset($args['post_id']) ? $args['post_id'] : 0;
            if( isset($price) && $price > 0 ){
                $args['payment_type'] = 'single_payment';
                $cubwp_payments = new CubeWp_Payments();
                $checkout_url = $cubwp_payments->process_single_payment( $args );
                wp_send_json(
                    array(
                        'type'        =>  'success',
                        'msg'         =>  sprintf(__('Success! %s is added successfully in cart', 'cubewp-payments'), get_post_type($post_id)),
                        'redirectURL' =>  $checkout_url
                    )
                );
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
