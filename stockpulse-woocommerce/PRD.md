# 📦 StockPulse for WooCommerce

> Smart inventory alerts for admins and customers.

---

## 🧠 Purpose

**StockPulse** is a lightweight WooCommerce plugin that enhances stock management by:
- Sending an **email notification to the store admin** when a product goes **out of stock**.
- Allowing **customers to subscribe** for **back-in-stock alerts** and notifying them via email when a product is restocked.

---

## 🧩 Features & Functional Requirements

### 🔔 1. Admin Out-of-Stock Notification

- **Trigger**: When a product's stock status changes from `instock` to `outofstock`.
- **Action**: Email the store admin (use `get_option('admin_email')`).
- **Email content includes**:
  - Product Name
  - Product ID or SKU
  - Timestamp
  - Admin link to edit product
- **Prevent**: Duplicate alerts unless the product was restocked in between.

---

### ✉️ 2. Customer Back-in-Stock Notification

#### 2.1. Opt-in Form on Product Page
- Display a simple subscription form **only on out-of-stock product pages**.
- Fields:
  - Customer email address
- Hook into the product page with `woocommerce_single_product_summary`.

#### 2.2. Store Subscriptions
- Save customer subscriptions using a **custom database table** or **custom post type**.
- Track:
  - Product ID
  - Customer email
  - Timestamp
  - Notification status (`pending`, `notified`)

#### 2.3. Trigger Notification When Restocked
- Detect when a product's stock changes from `outofstock` to `instock`.
- Query all `pending` subscriptions for that product.
- Send notification emails to each customer.
- Mark each subscription as `notified`.

#### 2.4. Customer Email Template

**Subject:**
[YourStore] {{ product_name }} is back in stock!
Hi there,

Good news! The product you were waiting for is now back in stock:

Product: {{ product_name }}
Link: {{ product_url }}

Hurry before it sells out again!

— Your Store Team

---

## 📂 Plugin Structure

stockpulse-woocommerce/
│
├── stockpulse-woocommerce.php # Main plugin file
├── includes/
│ ├── class-stockpulse-subscriber.php # Customer email logic
│ ├── class-stockpulse-notifier.php # Admin alert logic
│ ├── functions.php
├── templates/
│ └── optin-form.php # Frontend form template
├── readme.txt

---

## ⚙️ Technical Details

### Hooks
- `woocommerce_product_set_stock_status`
- `woocommerce_single_product_summary`
- `woocommerce_init` (for early plugin logic)

### Functions & Tools
- `wp_mail()`, `get_option()`, `get_permalink()`, `admin_url()`
- Sanitization: `sanitize_email()`, `esc_html()`, `esc_url()`
- Use `$wpdb` if storing subscribers in a custom table

---

## ⚠️ Edge Cases

- Avoid duplicate emails (track status).
- Handle simple and variable products.
- Optional: variation-level subscriptions.
- Protect the frontend form from spam (nonce + sanitization).
- Prevent sending email if product goes out-of-stock immediately after restock.

---

## ✅ QA Checklist

| Feature                                   | Pass/Fail |
|-------------------------------------------|-----------|
| Admin receives out-of-stock email         |           |
| Customer sees opt-in form on OOS products |           |
| Customer receives back-in-stock email     |           |
| No duplicate emails sent                  |           |
| Admin email contains correct info         |           |
| Customer subscription marked as notified  |           |

---

## 🌱 MVP vs Future Versions

| Feature                        | MVP | Future |
|-------------------------------|-----|--------|
| Admin out-of-stock alert      | ✅  |        |
| Customer back-in-stock alerts | ✅  |        |
| Variation-level alerts        |     | ✅     |
| Plugin settings UI            |     | ✅     |
| Admin subscriber management   |     | ✅     |

---

## 🛠 Deployment Instructions

1. Upload `stockpulse-woocommerce` to `/wp-content/plugins/`
2. Activate the plugin via WP Admin
3. Test stock status updates for both admin and customer notifications
4. Monitor outgoing emails via mail log plugin or SMTP setup

---

