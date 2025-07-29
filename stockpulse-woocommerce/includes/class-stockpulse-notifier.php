<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class StockPulse_Notifier {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'woocommerce_product_set_stock_status', array( $this, 'handle_stock_status_change' ), 10, 3 );
    }
    
    public function handle_stock_status_change( $product_id, $stock_status, $product ) {
        if ( $stock_status === 'outofstock' ) {
            $this->notify_admin_out_of_stock( $product );
        } elseif ( $stock_status === 'instock' ) {
            $this->notify_customers_back_in_stock( $product );
        }
    }
    
    private function notify_admin_out_of_stock( $product ) {
        if ( stockpulse_has_admin_been_notified( $product->get_id() ) ) {
            return;
        }
        
        $admin_email = get_option( 'admin_email' );
        $subject = sprintf( 
            __( '[%s] Product Out of Stock: %s', 'stockpulse-woocommerce' ), 
            get_bloginfo( 'name' ), 
            $product->get_name() 
        );
        
        $message = $this->get_admin_email_content( $product );
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo( 'name' ) . ' <' . $admin_email . '>'
        );
        
        $sent = wp_mail( $admin_email, $subject, $message, $headers );
        
        if ( $sent ) {
            stockpulse_mark_admin_notified( $product->get_id() );
        }
    }
    
    private function get_admin_email_content( $product ) {
        $product_name = $product->get_name();
        $product_id = $product->get_id();
        $product_sku = $product->get_sku();
        $edit_link = admin_url( 'post.php?post=' . $product_id . '&action=edit' );
        $timestamp = current_time( 'mysql' );
        
        ob_start();
        ?>
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #333;"><?php esc_html_e( 'Product Out of Stock Alert', 'stockpulse-woocommerce' ); ?></h2>
            
            <p><?php esc_html_e( 'The following product has gone out of stock:', 'stockpulse-woocommerce' ); ?></p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background-color: #f5f5f5;"><strong><?php esc_html_e( 'Product Name:', 'stockpulse-woocommerce' ); ?></strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html( $product_name ); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background-color: #f5f5f5;"><strong><?php esc_html_e( 'Product ID:', 'stockpulse-woocommerce' ); ?></strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html( $product_id ); ?></td>
                </tr>
                <?php if ( $product_sku ) : ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background-color: #f5f5f5;"><strong><?php esc_html_e( 'SKU:', 'stockpulse-woocommerce' ); ?></strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html( $product_sku ); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background-color: #f5f5f5;"><strong><?php esc_html_e( 'Timestamp:', 'stockpulse-woocommerce' ); ?></strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html( $timestamp ); ?></td>
                </tr>
            </table>
            
            <p style="margin-top: 20px;">
                <a href="<?php echo esc_url( $edit_link ); ?>" style="display: inline-block; padding: 10px 20px; background-color: #2271b1; color: white; text-decoration: none; border-radius: 3px;">
                    <?php esc_html_e( 'Edit Product', 'stockpulse-woocommerce' ); ?>
                </a>
            </p>
            
            <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
            
            <p style="color: #666; font-size: 12px;">
                <?php esc_html_e( 'This is an automated notification from StockPulse for WooCommerce.', 'stockpulse-woocommerce' ); ?>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function notify_customers_back_in_stock( $product ) {
        $subscriptions = stockpulse_get_pending_subscriptions( $product->get_id() );
        
        if ( empty( $subscriptions ) ) {
            return;
        }
        
        foreach ( $subscriptions as $subscription ) {
            $this->send_customer_notification( $subscription, $product );
        }
    }
    
    private function send_customer_notification( $subscription, $product ) {
        $subject = sprintf(
            __( '[%s] %s is back in stock!', 'stockpulse-woocommerce' ),
            get_bloginfo( 'name' ),
            $product->get_name()
        );
        
        $message = $this->get_customer_email_content( $product );
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>'
        );
        
        $sent = wp_mail( $subscription->email, $subject, $message, $headers );
        
        if ( $sent ) {
            stockpulse_mark_subscription_notified( $subscription->id );
        }
    }
    
    private function get_customer_email_content( $product ) {
        $product_name = $product->get_name();
        $product_url = get_permalink( $product->get_id() );
        $store_name = get_bloginfo( 'name' );
        
        ob_start();
        ?>
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #333;"><?php echo esc_html( $product_name ); ?> <?php esc_html_e( 'is back in stock!', 'stockpulse-woocommerce' ); ?></h2>
            
            <p><?php esc_html_e( 'Hi there,', 'stockpulse-woocommerce' ); ?></p>
            
            <p><?php esc_html_e( 'Good news! The product you were waiting for is now back in stock:', 'stockpulse-woocommerce' ); ?></p>
            
            <div style="background-color: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 5px;">
                <p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Product:', 'stockpulse-woocommerce' ); ?></strong> <?php echo esc_html( $product_name ); ?></p>
                <p style="margin: 0;"><strong><?php esc_html_e( 'Link:', 'stockpulse-woocommerce' ); ?></strong> <a href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_url( $product_url ); ?></a></p>
            </div>
            
            <p><?php esc_html_e( 'Hurry before it sells out again!', 'stockpulse-woocommerce' ); ?></p>
            
            <p style="margin-top: 30px;">
                <a href="<?php echo esc_url( $product_url ); ?>" style="display: inline-block; padding: 12px 30px; background-color: #2271b1; color: white; text-decoration: none; border-radius: 3px;">
                    <?php esc_html_e( 'View Product', 'stockpulse-woocommerce' ); ?>
                </a>
            </p>
            
            <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
            
            <p style="color: #666; font-size: 12px;">
                — <?php echo esc_html( $store_name ); ?> <?php esc_html_e( 'Team', 'stockpulse-woocommerce' ); ?>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
}