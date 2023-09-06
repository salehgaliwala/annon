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

if(!class_exists('CubeWp_Price_Plans')){
    
    /**
     * CubeWp_Price_Plans
     */
    class CubeWp_Price_Plans {
        
        /**
         * Method __construct
         *
         * @return void
         */
        public function __construct() {

            add_action('init', array($this, 'cubewp_register_price_plan'), 20);
            add_action( 'add_meta_boxes', array($this, 'get_current_meta') );
            add_action( 'save_post', array($this, 'save_plan_fields'));
        }
        
        /**
         * Method cubewp_register_price_plan
         *
         * @return void
         * @since  1.0.0
         */
        public function cubewp_register_price_plan() {
            $labels = array(
                'name'                => _x( 'Pricing Plans', 'Post Type General Name', 'cubewp-payments' ),
                'singular_name'       => _x( 'Pricing Plan', 'Post Type Singular Name', 'cubewp-payments' ),
                'menu_name'           => __( 'Pricing Plans', 'cubewp-payments' ),
                'parent_item_colon'   => __( 'Parent Pricing Plan', 'cubewp-payments' ),
                'all_items'           => __( 'Pricing Plans', 'cubewp-payments' ),
                'view_item'           => __( 'View Pricing Plan', 'cubewp-payments' ),
                'add_new_item'        => __( 'Add New Pricing Plan', 'cubewp-payments' ),
                'add_new'             => __( 'Add New', 'cubewp-payments' ),
                'edit_item'           => __( 'Edit Pricing Plan', 'cubewp-payments' ),
                'update_item'         => __( 'Update Pricing Plan', 'cubewp-payments' ),
                'search_items'        => __( 'Search Pricing Plan', 'cubewp-payments' ),
                'not_found'           => __( 'Not Found', 'cubewp-payments' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'cubewp-payments' ),
            );
            $args = array(
                'label'               => __( 'Pricing Plans', 'cubewp-payments' ),
                'description'         => '',
                'labels'              => $labels,
                'supports'            => array( 'title'),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 30,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'show_in_rest' => true,
            );
            if(class_exists('WooCommerce')){
                register_post_type( 'price_plan', $args );
            }
        }

        
        /**
         * Method get_current_meta
         *
         * @return void
         * @since  1.0.0
         */
        public function get_current_meta(){
            add_meta_box( 'price_plan', __("Plan Fields", "cubewp-payments"), array($this, 'show_metaboxes'), 'price_plan', 'normal', 'low', 'price_plan' );
        }
                
        /**
         * Method show_metaboxes
         *
         * @param array $post 
         * @param array $args 
         *
         * @return string html
         * @since  1.0.0
         */
        public function show_metaboxes( $post, $args ){
            wp_enqueue_media();
            wp_enqueue_style('cubewp-metaboxes');
            wp_enqueue_script('cubewp-metaboxes');
            wp_enqueue_script('cubewp-metaboxes-validation');
        
            $output = '<table class="form-table cwp-metaboxes cwp-validation">';
                $output .= '<tbody>';
                    
                    $plan_price     = '';
                    $plan_duration  = '';
                    $plan_post_type = '';
                    $plan_type      = 'pay_per_post';
                    $no_of_posts    = '';
                    if(isset($post) && !empty($post)){
                        $plan_price         = get_post_meta( $post->ID, 'plan_price', true );
                        $plan_duration_type = get_post_meta( $post->ID, 'plan_duration_type', true );
                        $plan_duration      = get_post_meta( $post->ID, 'plan_duration', true );
                        $plan_post_type     = get_post_meta( $post->ID, 'plan_post_type', true );
                        $plan_type          = get_post_meta( $post->ID, 'plan_type', true );
                        $no_of_posts        = get_post_meta( $post->ID, 'no_of_posts', true );
                        $plan_text          = get_post_meta( $post->ID, 'plan_text', true );
                        $plan_image         = get_post_meta( $post->ID, 'plan_image', true );
                        $plan_hot           = get_post_meta( $post->ID, 'plan_hot', true );
                        if($plan_type == ''){
                            $plan_type      = 'pay_per_post';
                        }
                        if($plan_duration_type == ''){
                            $plan_duration_type      = 'lifetime';
                        }
                    }
                
                    $input_attrs = array(
                        'type'         => 'hidden',
                        'name'         => 'cwp_plan_nonce',
                        'value'        => wp_create_nonce( basename( __FILE__ ) ),
                    );
                    $output .= cwp_render_hidden_input( $input_attrs );
                    
                    $field                    =  array();
                    $field['label']           =  __("Price", "cubewp-payments");
                    $field['name']            =  'plan_price';
                    $field['custom_name']     =  'plan_meta[plan_price]';
                    $field['value']           =  $plan_price;
                    $field['type']            =  'text';
                    $field['wrap']            =  true;
                    $field['required']        =  1;
                    $output .= apply_filters( "cubewp/admin/post/number/field", '', $field );
                    
                    $field                    =  array();
                    $field['label']           =  __("Wocommerce associated product", "cubewp-payments");
                    $field['id']              =  'plan_duration_type';
                    $field['name']            =  'plan_duration_type';
                    $field['custom_name']     =  'plan_meta[plan_duration_type]';
                    $field['value']           =  $plan_duration_type;          
                    $field['required']        =  1;
                    $field['type']            =  'text';
                    $field['wrap']            =  true;
                    $output .= apply_filters( "cubewp/admin/post/number/field", '', $field );

                    $field                    =  array();
                    $field['label']           =  __("Days", "cubewp-payments");
                    $field['name']            =  'plan_duration';
                    $field['id']              =  'plan_duration';
                    $field['custom_name']     =  'plan_meta[plan_duration]';
                    $field['value']           =  $plan_duration;
                    $field['type']            =  'number';
                    $field['wrap']            =  true;
                    $field['required']        =  1;
                    $field['container_class'] =  'conditional-logic';
                    $output .= apply_filters( "cubewp/admin/post/number/field", '', $field );

                    $field                    =  array();
                    $field['label']           =  __("Assign to Post Type", "cubewp-payments");
                    $field['name']            =  'plan_post_type';
                    $field['custom_name']     =  'plan_meta[plan_post_type]';
                    $field['value']           =  $plan_post_type;
                    $field['options']         =  CWP_all_post_types('price_plan');
                    $field['select2_ui']      =  1;
                    $field['type']            =  'dropdown';
                    $field['wrap']            =  true;
                    $field['required']        =  1;
                    $output .= apply_filters( "cubewp/admin/post/dropdown/field", '', $field );
                    
                    $field                    =  array();
                    $field['label']           =  __("Select Plan Type", "cubewp-payments");
                    $field['id']              =  'plan_type';
                    $field['name']            =  'plan_type';
                    $field['custom_name']     =  'plan_meta[plan_type]';
                    $field['value']           =  $plan_type;
                    $field['options']         =  array( 'pay_per_post' => __('Pay Per Post', 'cubewp-payments'), 'package' => __('Package', 'cubewp-payments') );
                    $field['select2_ui']      =  1;
                    $field['required']        =  1;
                    $field['type']            =  'dropdown';
                    $field['wrap']            =  true;
                    $output .= apply_filters( "cubewp/admin/post/dropdown/field", '', $field );
                  
                    $field                    =  array();
                    $field['label']           =  __("No. of Posts", "cubewp-payments");
                    $field['id']              =  'no_of_posts';
                    $field['name']            =  'no_of_posts';
                    $field['custom_name']     =  'plan_meta[no_of_posts]';
                    $field['placeholder']     =  __("Enter Number of Posts in this Package", "cubewp-payments");
                    $field['value']           =  $no_of_posts;
                    $field['type']            =  'number';
                    $field['wrap']            =  true;
                    $field['required']        =  1;
                    $field['container_class'] =  'conditional-logic';
                    $output .= apply_filters( "cubewp/admin/post/number/field", '', $field );
            
                    $field                    =  array();
                    $field['label']           =  __("Plan Featured Text", "cubewp-payments");
                    $field['name']            =  'plan_text';
                    $field['id']              =  'plan_text';
                    $field['custom_name']     =  'plan_meta[plan_text]';
                    $field['placeholder']     =  __("Enter text for this plan", "cubewp-payments");
                    $field['value']           =  $plan_text;
                    $field['type']            =  'text';
                    $field['wrap']            =  true;
                    $output .= apply_filters( "cubewp/admin/post/text/field", '', $field );
            
                    $field                    =  array();
                    $field['label']           =  __("Plan Image", "cubewp-payments");
                    $field['name']            =  'plan_image';
                    $field['id']              =  'plan_image';
                    $field['custom_name']     =  'plan_meta[plan_image]';
                    $field['value']           =  $plan_image;
                    $field['type']            =  'image';
                    $field['wrap']            =  true;
                    $output .= apply_filters( "cubewp/admin/post/image/field", '', $field );
            
                    $field                    =  array();
                    $field['label']           =  __("Best Seller", "cubewp-payments");
                    $field['name']            =  'plan_hot';
                    $field['id']              =  'plan_hot';
                    $field['custom_name']     =  'plan_meta[plan_hot]';
                    $field['value']           =  $plan_hot;
                    $field['type']            =  'switch';
                    $field['wrap']            =  true;
                    $output .= apply_filters( "cubewp/admin/post/switch/field", '', $field );
                
                $output .= '</tbody>';
            $output .= '</table>';
            
            echo $output;
        }
                
        /**
         * Method save_plan_fields
         *
         * @param int $post_id 
         *
         * @return void
         * @since  1.0.0
         */
        public function save_plan_fields( $post_id ){
            if(isset($_POST['cwp_plan_nonce'])){
            
                // verify nonce
                if ( ! wp_verify_nonce( $_POST['cwp_plan_nonce'], basename( __FILE__ ) ) )
                    return $post_id;

                // check autosave
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                    return $post_id;
                
                $plan_meta = isset($_POST['plan_meta']) ? $_POST['plan_meta'] : array();
                if(isset($plan_meta) && !empty($plan_meta)){
                    foreach($plan_meta as $key => $val){
                        update_post_meta($post_id, $key, $val);
                    }
                }
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