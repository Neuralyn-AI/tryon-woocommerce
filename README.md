# Neuralyn TRYON Connector for WooCommerce

Virtual fitting room integration that allows e-commerce customers to try on clothes virtually before purchasing using AI-powered body detection technology.

## Features

- **Virtual Try-On**: Customers can see how clothing items look on them using AI-powered body detection
- **Multiple Button Placements**: Configure where the Try-On button appears on product pages
- **Customer Classification**: Automatically classify customers as guests, registered users, or buyers
- **Product Tabs Integration**: Add Try-On functionality as a product tab
- **Shop Listing Support**: Show Try-On buttons on shop and category pages
- **HPOS Compatible**: Full support for WooCommerce High-Performance Order Storage
- **Multi-language Support**: English, Portuguese (Brazil), and Spanish translations included

## Requirements

- WordPress 6.0+
- WooCommerce 7.0+
- PHP 7.4+
- Active Neuralyn TRYON subscription
- HTTPS enabled

## Installation

1. Upload the `neuralyn-tryon` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **WooCommerce > Settings > Neuralyn TRYON** to configure the plugin
4. Enter your license key from your [Neuralyn Dashboard](https://neuralyn.ai/dashboard)

## Configuration

### License Key

Enter your 36-character license key obtained from the Neuralyn Dashboard. The key is required for the Try-On button to appear on your store.

### Button Placement

Select where the Try-On button should appear:

| Hook | Description | Context |
|------|-------------|---------|
| Product Summary | Displayed in the product summary area, after the title | Product Page |
| After Add to Cart Button | Displayed immediately after the Add to Cart button | Product Page |
| Product Thumbnails | Displayed in the product gallery thumbnails area | Product Page |
| After Product Summary | Displayed after the entire product summary section | Product Page |
| Before Add to Cart Form | Displayed before the add to cart form | Product Page |
| Product Tabs | Adds a new "Virtual Try-On" tab | Product Page |
| Shop Loop Item | Displayed after each product in shop/category listings | Shop Listing |

### Customer Classification

Configure which order statuses qualify a customer as a "buyer":

- **Guest**: Not logged in
- **Registered**: Logged in, but no qualifying orders
- **Buyer**: Has at least one order with qualifying status (default: Completed, Processing)

## Page Exclusions

The Try-On button is automatically hidden on:
- Cart page
- Checkout page
- Order received/thank you page
- Order pay page

## SDK Configuration

The plugin automatically injects the following configuration on product pages:

```javascript
window.TRYON_CONFIG = {
    licenseKey: "your-license-key",
    customerId: "123",
    customerUUID: "uuid-v4-string",
    customerType: "guest|registered|buyer",
    loginUrl: "https://yourstore.com/my-account",
    platform: "woocommerce"
};
```

## Plugin Structure

```
neuralyn-tryon/
├── neuralyn-tryon.php                    # Main plugin file
├── includes/
│   ├── class-neuralyn-tryon.php          # Main plugin class
│   ├── class-neuralyn-tryon-admin.php    # Admin settings
│   ├── class-neuralyn-tryon-frontend.php # Frontend rendering
│   └── class-neuralyn-tryon-customer.php # Customer handling
├── templates/
│   ├── button.php                        # Try-On button template
│   └── widget.php                        # SDK loader template
├── languages/                            # Translation files
└── readme.txt                            # WordPress plugin readme
```

## Hooks & Filters

### Actions

The plugin registers callbacks for these WooCommerce hooks based on your settings:

- `woocommerce_single_product_summary`
- `woocommerce_after_add_to_cart_button`
- `woocommerce_product_thumbnails`
- `woocommerce_after_single_product_summary`
- `woocommerce_before_add_to_cart_form`
- `woocommerce_after_shop_loop_item`

### Filters

- `woocommerce_product_tabs` - Adds Virtual Try-On tab when enabled

## Customer UUID

Each registered customer is assigned a unique UUID v4 identifier stored in user meta (`neuralyn_tryon_uuid`). This UUID is used for:

- Tracking virtual try-on sessions
- Associating photos across sessions
- Analytics and reporting

## Support

- **Dashboard**: https://neuralyn.ai/dashboard
- **Product Info**: https://neuralyn.ai/en/products/tryon
- **Support Email**: support@neuralyn.ai

## License

Commercial license. See https://neuralyn.ai/files/woocommerce/license.txt
