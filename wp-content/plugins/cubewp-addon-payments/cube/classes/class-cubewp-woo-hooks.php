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

if(!class_exists('CubeWp_Woo_Hooks')){
    
    /**
     * CubeWp_Woo_Hooks
     */
    class CubeWp_Woo_Hooks extends CubeWp_Payments{
        
        /**
         * Method __construct
         *
         * @return void
         */
        public function __construct() {
            add_filter('woocommerce_billing_fields', array( $this, 'billing_fields' ), 20, 1);
            add_filter('woocommerce_shipping_fields', array( $this, 'shipping_fields' ), 20, 1);
            add_action('woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed' ), 1000);

            add_filter('woocommerce_order_status_pending_to_processing', array( $this, 'payment_complete' ));
            add_filter('woocommerce_order_status_on-hold_to_completed', array( $this, 'payment_complete' ));
            add_filter('woocommerce_order_status_on-hold_to_processing', array( $this, 'payment_complete' ));
            add_filter('woocommerce_order_status_pending_to_completed', array( $this, 'payment_complete' ));
            add_filter('woocommerce_order_status_processing_to_completed', array( $this, 'payment_complete' ));

            add_action('woocommerce_payment_complete', array( $this, 'payment_complete' ));
            add_action('woocommerce_order_status_processing', array( $this, 'payment_complete' ));
            add_filter('woocommerce_payment_complete_order_status', array( $this, 'payment_complete_order_status' ), 30, 2);
            add_action('woocommerce_order_status_cancelled', array( $this, 'order_status_cancelled' ));
            add_action('woocommerce_thankyou', array( $this, 'thankyou_page' ));
            add_filter("woocommerce_is_purchasable", array($this, "cubewp_is_purchasable"), 11, 2);
        }
        
        /**
         * Method billing_fields
         *
         * @param array $address_fields [explicite description]
         *
         * @return array
         * @since  1.0.0
         */
        public function billing_fields($address_fields) {
            $address_fields['billing_phone']['required']        = false;
            $address_fields['billing_country']['required']      = false;
            $address_fields['billing_first_name']['required']   = false;
            $address_fields['billing_last_name']['required']    = false;
            $address_fields['billing_email']['required']        = false;
            $address_fields['billing_address_1']['required']    = false;
            $address_fields['billing_city']['required']         = false;
            $address_fields['billing_postcode']['required']     = false;

            return $address_fields;
        }
        
        /**
         * Method shipping_fields
         *
         * @param array $address_fields
         *
         * @return array
         * @since  1.0.0
         */
        public function shipping_fields( $address_fields ) {
            $address_fields['order_comments']['required'] = false;
            return $address_fields;
        }
        
        /**
         * Method checkout_order_processed
         *
         * @param int $order_id
         *
         * @return void
         * @since  1.0.0
         */
        public function checkout_order_processed($order_id) {
            $order = new WC_Order($order_id);
            foreach ( $order->get_items() as $item ) {
                $product_id = $item['product_id'];
            }
            $payment_args = get_post_meta($product_id, 'payment_args', true);
            update_post_meta($order_id, 'payment_args', $payment_args);
            if(isset($payment_args['payment_type']) && $payment_args['payment_type'] == 'single_payment'){
                update_post_meta($payment_args['post_id'], 'payment_args', $payment_args);
                update_post_meta($payment_args['post_id'], 'order_id', $order_id);
            }else{
                $this->create_order( $order_id, $product_id );
            }
            
        }
                
        /**
         * Method payment_complete
         *
         * @param int $order_id
         *
         * @return void
         * @since  1.0.0
         */
        public function payment_complete($order_id) {
            $payment_args = get_post_meta($order_id, 'payment_args', true);
            if(isset($payment_args['payment_type']) && $payment_args['payment_type'] == 'single_payment'){
                update_post_meta($payment_args['post_id'], 'payment_args', $payment_args);
                update_post_meta($payment_args['post_id'], 'order_id', $order_id);
                do_action( 'cubewp_single_payment_completed', $order_id );
            }else{
                $this->update_order( $order_id );
            }
        }
        
        /**
         * Method payment_complete_order_status
         *
         * @param string $order_status
         * @param int $order_id
         *
         * @return string
         * @since  1.0.0
         */
        public function payment_complete_order_status($order_status, $order_id) {
            if($order_status == 'processing'){
                return 'completed';
            }else{
                return $order_status;
            }
            
        }
                
        /**
         * Method order_status_cancelled
         *
         * @param int $order_id
         *
         * @return void
         * @since  1.0.0
         */
        public function order_status_cancelled($order_id) {
            $order = new WC_Order($order_id);
            foreach( $order->get_items() as $item ){
                wp_delete_post($item['product_id']);
            }
            wp_delete_post($order_id);
            $return_url = get_option('wooCommerce_current_page');
            if(isset($return_url) && $return_url != '') {
                wp_redirect($return_url);
                exit;
            }
            wp_redirect(site_url());
            exit;
        }
                
        /**
         * Method thankyou_page
         *
         * @param int $order_id
         *
         * @return void
         * @since  1.0.0
         */
        public function thankyou_page($order_id) {
            
        }
        
        /**
         * Method cubewp_is_purchasable
         *
         * @param bool $is_purchasable
         * @param  array $this_instance
         *
         * @return bool
         * @since  1.0.0
         */
        public function cubewp_is_purchasable($is_purchasable, $this_instance) {
            if ( ! $is_purchasable) {
                $product_id = $this_instance->get_id();
                $product_args = get_post_meta($product_id, 'payment_args', true);
                $post_id = isset($product_args['post_id']) && !empty($product_args['post_id']) ? $product_args['post_id'] : '';
                if(empty($post_id)){
                    return false;
                }
                $post_author = get_post_field ('post_author', $post_id);
                $is_purchasable = get_current_user_id();
                if (get_current_user_id() == $post_author) {
                    $is_purchasable = true;
                }
            }
        
            return $is_purchasable;
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