<?php
/**
 * Plugin Name: StockPulse for WooCommerce
 * Plugin URI: https://yourwebsite.com/stockpulse
 * Description: Smart inventory alerts for admins and customers - get notified when products go out of stock or come back in stock.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: stockpulse-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'STOCKPULSE_VERSION', '1.0.0' );
define( 'STOCKPULSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STOCKPULSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STOCKPULSE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

class StockPulse_WooCommerce {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->check_dependencies();
        $this->includes();
        $this->init_hooks();
    }
    
    private function check_dependencies() {
        add_action( 'admin_notices', array( $this, 'check_woocommerce_active' ) );
    }
    
    public function check_woocommerce_active() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            ?>
            <div class="notice notice-error">
                <p><?php esc_html_e( 'StockPulse for WooCommerce requires WooCommerce to be installed and active.', 'stockpulse-woocommerce' ); ?></p>
            </div>
            <?php
        }
    }
    
    private function includes() {
        require_once STOCKPULSE_PLUGIN_DIR . 'includes/class-stockpulse-installer.php';
        require_once STOCKPULSE_PLUGIN_DIR . 'includes/class-stockpulse-notifier.php';
        require_once STOCKPULSE_PLUGIN_DIR . 'includes/class-stockpulse-subscriber.php';
        require_once STOCKPULSE_PLUGIN_DIR . 'includes/functions.php';
    }
    
    private function init_hooks() {
        register_activation_hook( __FILE__, array( 'StockPulse_Installer', 'activate' ) );
        register_deactivation_hook( __FILE__, array( 'StockPulse_Installer', 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( 'StockPulse_Installer', 'uninstall' ) );
        
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );
    }
    
    public function init() {
        load_plugin_textdomain( 'stockpulse-woocommerce', false, dirname( STOCKPULSE_PLUGIN_BASENAME ) . '/languages' );
    }
    
    public function woocommerce_init() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        
        StockPulse_Notifier::get_instance();
        StockPulse_Subscriber::get_instance();
    }
}

function stockpulse_woocommerce() {
    return StockPulse_WooCommerce::get_instance();
}

stockpulse_woocommerce();