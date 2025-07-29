<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;
?>

<div class="stockpulse-subscription-form">
    <h3><?php esc_html_e( 'Notify me when back in stock', 'stockpulse-woocommerce' ); ?></h3>
    <p><?php esc_html_e( 'Enter your email address and we\'ll notify you when this product is available again.', 'stockpulse-woocommerce' ); ?></p>
    
    <form id="stockpulse-notify-form" class="stockpulse-form">
        <div class="stockpulse-form-group">
            <input type="email" 
                   id="stockpulse-email" 
                   name="email" 
                   class="stockpulse-email-input" 
                   placeholder="<?php esc_attr_e( 'Your email address', 'stockpulse-woocommerce' ); ?>" 
                   required>
            <input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">
            <button type="submit" class="stockpulse-submit-btn">
                <?php esc_html_e( 'Notify Me', 'stockpulse-woocommerce' ); ?>
            </button>
        </div>
        <div class="stockpulse-message" style="display: none;"></div>
    </form>
</div>