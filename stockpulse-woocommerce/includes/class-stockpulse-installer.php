<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class StockPulse_Installer {
    
    public static function activate() {
        self::create_tables();
        self::create_default_options();
        
        flush_rewrite_rules();
    }
    
    public static function deactivate() {
        flush_rewrite_rules();
    }
    
    public static function uninstall() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'stockpulse_subscriptions';
        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
        
        delete_option( 'stockpulse_db_version' );
        delete_option( 'stockpulse_admin_notifications' );
    }
    
    private static function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'stockpulse_subscriptions';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            product_id bigint(20) unsigned NOT NULL,
            email varchar(100) NOT NULL,
            status enum('pending','notified') DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            notified_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY product_id (product_id),
            KEY email (email),
            KEY status (status),
            KEY product_status (product_id, status)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
        update_option( 'stockpulse_db_version', STOCKPULSE_VERSION );
    }
    
    private static function create_default_options() {
        add_option( 'stockpulse_admin_notifications', array() );
    }
}