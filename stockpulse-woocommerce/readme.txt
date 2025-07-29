=== StockPulse for WooCommerce ===
Contributors: yourname
Tags: woocommerce, inventory, stock, notifications, email alerts
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Smart inventory alerts for admins and customers - get notified when products go out of stock or come back in stock.

== Description ==

StockPulse for WooCommerce is a lightweight plugin that enhances your store's inventory management by providing automatic email notifications for stock changes.

= Key Features =

* **Admin Out-of-Stock Alerts**: Automatically notifies store administrators when products go out of stock
* **Customer Back-in-Stock Notifications**: Allows customers to subscribe for notifications when out-of-stock products become available again
* **Smart Duplicate Prevention**: Prevents sending duplicate notifications
* **Clean Email Templates**: Professional HTML email templates for both admin and customer notifications
* **Secure Subscription Forms**: AJAX-powered forms with nonce verification and rate limiting
* **Variable Product Support**: Works with both simple and variable products

= How It Works =

1. When a product goes out of stock, the admin receives an email with product details and a direct edit link
2. Customers see a subscription form on out-of-stock product pages
3. When products are restocked, all subscribed customers receive notification emails
4. The plugin tracks notification status to prevent duplicate emails

== Installation ==

1. Upload the `stockpulse-woocommerce` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WooCommerce is installed and activated
4. The plugin will start working automatically - no configuration needed!

== Frequently Asked Questions ==

= Does this plugin require any configuration? =

No, StockPulse works out of the box with sensible defaults. Admin emails are sent to the store admin email address configured in WordPress settings.

= Can customers subscribe to variable product notifications? =

Currently, the plugin supports notifications at the product level. Variation-level notifications are planned for a future release.

= How are duplicate notifications prevented? =

The plugin tracks which products have triggered admin notifications and which customer subscriptions have been notified, preventing duplicate emails unless the product status changes again.

= Is the plugin GDPR compliant? =

The plugin stores only email addresses and product preferences. Consider adding appropriate privacy policy disclosures about email collection and usage.

= Can I customize the email templates? =

Currently, email templates are built into the plugin. Template customization is planned for a future release.

== Screenshots ==

1. Customer subscription form on out-of-stock product page
2. Admin notification email example
3. Customer back-in-stock notification email

== Changelog ==

= 1.0.0 =
* Initial release
* Admin out-of-stock email notifications
* Customer back-in-stock subscription system
* AJAX-powered subscription forms
* Rate limiting for security
* HTML email templates

== Upgrade Notice ==

= 1.0.0 =
Initial release of StockPulse for WooCommerce.