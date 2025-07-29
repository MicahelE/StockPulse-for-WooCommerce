<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function stockpulse_get_subscription( $product_id, $email ) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'stockpulse_subscriptions';
    
    return $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $table_name WHERE product_id = %d AND email = %s",
        $product_id,
        $email
    ) );
}

function stockpulse_add_subscription( $product_id, $email ) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'stockpulse_subscriptions';
    
    $existing = stockpulse_get_subscription( $product_id, $email );
    
    if ( $existing && $existing->status === 'pending' ) {
        return array( 'success' => false, 'message' => __( 'You are already subscribed to notifications for this product.', 'stockpulse-woocommerce' ) );
    }
    
    if ( $existing && $existing->status === 'notified' ) {
        $wpdb->update(
            $table_name,
            array( 'status' => 'pending', 'created_at' => current_time( 'mysql' ) ),
            array( 'id' => $existing->id )
        );
        return array( 'success' => true, 'message' => __( 'You have been re-subscribed to notifications for this product.', 'stockpulse-woocommerce' ) );
    }
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'product_id' => $product_id,
            'email' => $email,
            'status' => 'pending',
            'created_at' => current_time( 'mysql' )
        ),
        array( '%d', '%s', '%s', '%s' )
    );
    
    if ( $result === false ) {
        return array( 'success' => false, 'message' => __( 'Failed to subscribe. Please try again.', 'stockpulse-woocommerce' ) );
    }
    
    return array( 'success' => true, 'message' => __( 'You have been subscribed to notifications for this product.', 'stockpulse-woocommerce' ) );
}

function stockpulse_get_pending_subscriptions( $product_id ) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'stockpulse_subscriptions';
    
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM $table_name WHERE product_id = %d AND status = 'pending'",
        $product_id
    ) );
}

function stockpulse_mark_subscription_notified( $subscription_id ) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'stockpulse_subscriptions';
    
    return $wpdb->update(
        $table_name,
        array(
            'status' => 'notified',
            'notified_at' => current_time( 'mysql' )
        ),
        array( 'id' => $subscription_id ),
        array( '%s', '%s' ),
        array( '%d' )
    );
}

function stockpulse_has_admin_been_notified( $product_id ) {
    $notifications = get_option( 'stockpulse_admin_notifications', array() );
    
    if ( isset( $notifications[ $product_id ] ) ) {
        $product = wc_get_product( $product_id );
        if ( $product && $product->is_in_stock() ) {
            unset( $notifications[ $product_id ] );
            update_option( 'stockpulse_admin_notifications', $notifications );
            return false;
        }
        return true;
    }
    
    return false;
}

function stockpulse_mark_admin_notified( $product_id ) {
    $notifications = get_option( 'stockpulse_admin_notifications', array() );
    $notifications[ $product_id ] = current_time( 'timestamp' );
    update_option( 'stockpulse_admin_notifications', $notifications );
}