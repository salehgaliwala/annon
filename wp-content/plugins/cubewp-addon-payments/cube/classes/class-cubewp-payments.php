<?php

/**
 * CubeWP payments.
 *
 * @package cubewp-addon-payments/cube/classes
 * @version 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists('CubeWp_Payments') ) {
    /**
     * CubeWp_Payments
     */
    class CubeWp_Payments {
        public function __construct() {
        }
                
        /**
         * Method process_payment
         *
         * @param array $payment_args
         *
         * @return string url
         * @since  1.0.0
         */
        public function process_payment($payment_args = array()) {
            global $wpdb, $woocommerce;
            extract($payment_args);
           
            $post_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID " . $wpdb->prefix . "posts FROM " . $wpdb->prefix . "posts
                    INNER JOIN " . $wpdb->prefix . "postmeta ON " . $wpdb->prefix . "postmeta.post_id = " . $wpdb->prefix . "posts.ID
                    WHERE (". $wpdb->prefix ."postmeta.meta_key = 'planID' AND ". $wpdb->prefix ."postmeta.meta_value = '%d')", $plan_id
                )
            );
            if(empty($post_id)){
                $post = array(
                    'post_author'   =>  1,
                    'post_content'  =>  '',
                    'post_status'   =>  "wc-hidden",
                    'post_title'    =>  $plan_name,
                    'post_parent'   =>  '',
                    'post_type'     =>  "product",
                );
                $post_id = wp_insert_post($post);
            }
            if( isset($post_id) && $post_id > 0 ){
                
                update_post_meta($post_id, '_stock_status', 'instock');
                update_post_meta($post_id, '_regular_price', $price);
                update_post_meta($post_id, 'planID', $plan_id);
                update_post_meta($post_id, '_price', $price);
                update_post_meta($post_id, 'payment_args', $payment_args);
                update_post_meta($post_id, '_virtual', 'yes');
                update_post_meta($post_id, '_visibility', 'hidden');
                wp_set_object_terms( $post_id, array('exclude-from-catalog', 'exclude-from-search'), 'product_visibility' );
                
                $woocommerce->cart->empty_cart();
                $woocommerce->cart->add_to_cart($post_id, 1);
                $checkout_url = wc_get_checkout_url();
                return $checkout_url;
            }
            
        }

        /**
         * Method process_single_payment
         *
         * @param array $payment_args
         *
         * @return string url
         * @since  1.0.0
         */
        public function process_single_payment($payment_args = array()) {
            global $wpdb, $woocommerce;
            extract($payment_args);
           
            $product_id = post_exists( $product_name.'_'.get_current_user_id(), '', '', 'product', 'wc-hidden' );

            if(empty($product_id)){
                $post = array(
                    'post_author'   =>  1,
                    'post_content'  =>  '',
                    'post_status'   =>  "wc-hidden",
                    'post_title'    =>  $product_name.'_'.get_current_user_id(),
                    'post_parent'   =>  '',
                    'post_type'     =>  "product",
                );
                $product_id = wp_insert_post($post);
            }
            if( isset($product_id) && $product_id > 0 ){
                
                update_post_meta($product_id, '_stock_status', 'instock');
                update_post_meta($product_id, '_regular_price', $price);
                update_post_meta($product_id, '_price', $price);
                update_post_meta($product_id, 'payment_args', $payment_args);
                update_post_meta($product_id, '_virtual', 'yes');
                update_post_meta($product_id, '_visibility', 'hidden');
                wp_set_object_terms( $product_id, array('exclude-from-catalog', 'exclude-from-search'), 'product_visibility' );
                
                $woocommerce->cart->empty_cart();
                $woocommerce->cart->add_to_cart($product_id, 1);
                $checkout_url = wc_get_checkout_url();
                return $checkout_url;
            }
            
        }

        /**
         * Method create_cube_order_table
         *
         * @return void
         * @since  1.0.0
         */
        public function create_cube_order_table(){
            global $wpdb;
            
            $charsetCollate = $wpdb->get_charset_collate();
            $wpdb->query("CREATE TABLE IF NOT EXISTS `". $wpdb->prefix ."cube_orders` (
                `ID` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `userID` bigint(20) NOT NULL DEFAULT '0',
                `planID` bigint(20) DEFAULT NULL,
                `planType` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `gateway` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `total` decimal(10,2) DEFAULT NULL,
                `wooOrderID` bigint(20) DEFAULT '0',
                `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                INDEX (`userID`),
    		INDEX (`planID`)
            ) $charsetCollate");
            
            $wpdb->query("CREATE TABLE IF NOT EXISTS `". $wpdb->prefix ."cube_order_meta` (
                `ID` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `orderID` bigint(200) NOT NULL,
                `meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `meta_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                INDEX (`orderID`)
            ) $charsetCollate");
        }

        /**
         * Method create_order
         *
         * @param int $order_id
         * @param int $product_id
         *
         * @return void
         * @since  1.0.0
         */
        public function create_order( $order_id, $product_id = 0 ){
            global $wpdb;
            
            $this->create_cube_order_table();
            
            $payment_args  =  get_post_meta($order_id, 'payment_args', true);
            $planID        =  get_post_meta($product_id, 'planID', true);
            $planDuration  =  get_post_meta($planID, 'plan_duration', true);
            $plan_type     =  get_post_meta($planID, 'plan_type', true);
            
            $results = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM ". $wpdb->prefix ."cube_orders 
                    WHERE userID=%d 
                    AND planID=%d 
                    AND status=%s 
                    AND planType=%s 
                    ORDER BY ID DESC", get_current_user_id(), $planID, 'pending', $plan_type
                ), ARRAY_A
            );
            
            if(isset($results) && !empty($results)){
                
                $wpdb->update(
                    $wpdb->prefix.'cube_orders',
                    [
                        'gateway'           =>  get_post_meta($order_id, '_payment_method', true),
                        'wooOrderID'        =>  $order_id,
                        'updatedAt'         =>  current_time('Y-m-d H:i:s')
                    ],
                    [
                        'ID' => $results['ID']
                    ],
                    [
                        '%s',
                        '%s'
                    ],
                    [
                        '%d',
                    ]
                );
                
                $wpdb->update(
                    $wpdb->prefix.'cube_order_meta',
                    [
                        'planName'        =>  esc_html(get_the_title($planID)),
                        'planDuration'    =>  $planDuration,
                        'currency'        =>  maybe_serialize(get_post_meta($order_id, '_order_currency', true)),
                        'discount'        =>  maybe_serialize(get_post_meta($order_id, '_cart_discount', true)),
                        'tax'             =>  maybe_serialize(get_post_meta($order_id, '_order_tax', true))
                    ],
                    [
                        'orderID' => $results['orderID']
                    ],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    ],
                    [
                        '%d',
                    ]
                );
            }else{
                $status    = $wpdb->insert(
                    $wpdb->prefix.'cube_orders',
                    [
                        'userID'           =>  get_post_meta($order_id, '_customer_user', true),
                        'planID'           =>  $planID,
                        'planType'         =>  get_post_meta($planID, 'plan_type', true),
                        'gateway'          =>  get_post_meta($order_id, '_payment_method', true),
                        'status'           =>  'pending',
                        'transaction_id'   =>  '',
                        'total'            =>  get_post_meta($order_id, '_order_total', true),
                        'wooOrderID'       =>  !empty($order_id) ? $order_id : 0,
                        'updatedAt'        =>  current_time('Y-m-d H:i:s')
                    ],
                    [
                        '%d',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    ]
                );
                $orderID = $wpdb->insert_id;

                if(isset($payment_args['post_id'])){
                    $wpdb->insert(
                        $wpdb->prefix.'cube_order_meta',
                        [
                            'orderID'    => abs($orderID),
                            'meta_key'   => 'postID',
                            'meta_value' => $payment_args['post_id']
                        ],
                        [
                            '%d',
                            '%s',
                            '%s'
                        ]
                    );
                }

                $wpdb->insert(
                    $wpdb->prefix.'cube_order_meta',
                    [
                        'orderID'    => abs($orderID),
                        'meta_key'   => 'planName',
                        'meta_value' => esc_html(get_the_title($planID))
                    ],
                    [
                        '%d',
                        '%s',
                        '%s'
                    ]
                );

                $wpdb->insert(
                    $wpdb->prefix.'cube_order_meta',
                    [
                        'orderID'    => abs($orderID),
                        'meta_key'   => 'planDuration',
                        'meta_value' => $planDuration
                    ],
                    [
                        '%d',
                        '%s',
                        '%s'
                    ]
                );

                $wpdb->insert(
                    $wpdb->prefix.'cube_order_meta',
                    [
                        'orderID'    => abs($orderID),
                        'meta_key'   => 'currency',
                        'meta_value' => maybe_serialize(get_post_meta($order_id, '_order_currency', true))
                    ],
                    [
                        '%d',
                        '%s',
                        '%s'
                    ]
                );

                $wpdb->insert(
                    $wpdb->prefix.'cube_order_meta',
                    [
                        'orderID'    => abs($orderID),
                        'meta_key'   => 'discount',
                        'meta_value' => maybe_serialize(get_post_meta($order_id, '_cart_discount', true))
                    ],
                    [
                        '%d',
                        '%s',
                        '%s'
                    ]
                );
                $wpdb->insert(
                    $wpdb->prefix.'cube_order_meta',
                    [
                        'orderID'    => abs($orderID),
                        'meta_key'   => 'tax',
                        'meta_value' => maybe_serialize(get_post_meta($order_id, '_order_tax', true))
                    ],
                    [
                        '%d',
                        '%s',
                        '%s'
                    ]
                );
            }
        }

        /**
         * Method update_order
         *
         * @param int $order_id 
         *
         * @return void
         * @since  1.0.0
         */
        public function update_order( $order_id ){
            global $wpdb, $cwpOptions;
            
            $payment_args  =  get_post_meta($order_id, 'payment_args', true);
            $results = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM ". $wpdb->prefix ."cube_orders 
                    WHERE wooOrderID=%d 
                    ORDER BY ID DESC", $order_id
                ), ARRAY_A
            );
            
            if(isset($payment_args['post_id'])){
                $post_type = get_post_type( $payment_args['post_id'] );
                $post_admin_approved  =  isset($cwpOptions['post_admin_approved'][ $post_type ]) ? $cwpOptions['post_admin_approved'][ $post_type ] : 'pending';
                
                $post_id  =  $payment_args['post_id'];
                $plan_id  =  $payment_args['plan_id'];
                
                $plan_duration  =  cwp_plan_duration($plan_id);
                if( $plan_duration > 0 ){
                    $post_expired   = strtotime("+". $plan_duration." days", strtotime(current_time('Y-m-d H:i:s')));
                    update_post_meta($post_id, 'post_expired', $post_expired);
                }
                update_post_meta($post_id, 'payment_status', 'paid');
                if ( get_post_status( $post_id ) != 'publish' ) {
	                if( $post_admin_approved == 'publish' ){
	                    $post_args = array(
	                        'ID'           => $post_id,
	                        'post_status'  => 'publish',
	                    );
	                    wp_update_post($post_args);
	                }
				}
                if( $post_admin_approved == 'no' ){
                    $post_args = array(
                        'ID'           => $post_id,
                        'post_status'  => 'publish',
                    );
                    wp_update_post($post_args);
                }
            }
            
            $wpdb->update(
                $wpdb->prefix.'cube_orders',
                [
                    'status'           =>  'complete',
                    'transaction_id'   =>  get_post_meta($order_id, '_transaction_id', true)
                ],
                [
                    'wooOrderID' => $order_id
                ],
                [
                    '%s',
                    '%s'
                ],
                [
                    '%d',
                ]
            );
            
        }
                
        /**
         * Method get_order
         *
         * @param int $wooOrderID [explicite description]
         *
         * @return string
         * @since  1.0.0
         */
        public static function get_order( $wooOrderID = 0 ){
            global $wpdb;
            
            $table_name = $wpdb->prefix.'cube_orders';
            $orderID = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID FROM $table_name WHERE wooOrderID=%d ORDER BY ID DESC", $wooOrderID
                )
            );

            if (empty($orderID)) {
                return false;
            }
            return maybe_unserialize($orderID);
        }
                
        /**
         * Method get_orderMeta
         *
         * @param int $orderID
         * @param array $meta_key
         *
         * @return string
         * @since  1.0.0
         */
        public static function get_orderMeta( $orderID = 0, $meta_key = '' ){
            global $wpdb;
            
            $table_name = $wpdb->prefix.'cube_order_meta';
            $orderMetaID = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID FROM $table_name WHERE orderID=%d AND meta_key=%s ORDER BY ID DESC", $orderID, $meta_key
                )
            );

            if (empty($orderMetaID)) {
                return false;
            }
            return maybe_unserialize($orderMetaID);
        }
        
    }
    
}
