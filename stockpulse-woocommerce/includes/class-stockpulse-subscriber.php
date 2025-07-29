<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class StockPulse_Subscriber {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'woocommerce_single_product_summary', array( $this, 'display_subscription_form' ), 31 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_stockpulse_subscribe', array( $this, 'handle_subscription_ajax' ) );
        add_action( 'wp_ajax_nopriv_stockpulse_subscribe', array( $this, 'handle_subscription_ajax' ) );
    }
    
    public function display_subscription_form() {
        global $product;
        
        if ( ! $product || $product->is_in_stock() ) {
            return;
        }
        
        include STOCKPULSE_PLUGIN_DIR . 'templates/optin-form.php';
    }
    
    public function enqueue_scripts() {
        if ( is_product() ) {
            wp_enqueue_script( 
                'stockpulse-frontend', 
                STOCKPULSE_PLUGIN_URL . 'assets/js/frontend.js', 
                array( 'jquery' ), 
                STOCKPULSE_VERSION, 
                true 
            );
            
            wp_localize_script( 'stockpulse-frontend', 'stockpulse_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'stockpulse_subscribe_nonce' )
            ) );
            
            wp_enqueue_style( 
                'stockpulse-frontend', 
                STOCKPULSE_PLUGIN_URL . 'assets/css/frontend.css', 
                array(), 
                STOCKPULSE_VERSION 
            );
        }
    }
    
    public function handle_subscription_ajax() {
        check_ajax_referer( 'stockpulse_subscribe_nonce', 'nonce' );
        
        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        
        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'stockpulse-woocommerce' ) ) );
        }
        
        if ( ! $product_id || ! wc_get_product( $product_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid product.', 'stockpulse-woocommerce' ) ) );
        }
        
        $this->rate_limit_check( $email );
        
        $result = stockpulse_add_subscription( $product_id, $email );
        
        if ( $result['success'] ) {
            wp_send_json_success( array( 'message' => $result['message'] ) );
        } else {
            wp_send_json_error( array( 'message' => $result['message'] ) );
        }
    }
    
    private function rate_limit_check( $email ) {
        $transient_key = 'stockpulse_rate_' . md5( $email );
        $attempts = get_transient( $transient_key );
        
        if ( $attempts && $attempts >= 3 ) {
            wp_send_json_error( array( 'message' => __( 'Too many subscription attempts. Please try again later.', 'stockpulse-woocommerce' ) ) );
        }
        
        set_transient( $transient_key, $attempts + 1, 300 );
    }
}