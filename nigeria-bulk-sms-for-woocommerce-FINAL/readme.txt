=== Nigeria Bulk SMS for WooCommerce ===
Contributors: nueleluwa
Tags: woocommerce, sms, nigeria, bulk sms, notifications
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Send automated and bulk SMS notifications to your WooCommerce customers via Nigeria Bulk SMS API.

== Description ==

Nigeria Bulk SMS for WooCommerce is a comprehensive SMS notification system that integrates seamlessly with your WooCommerce store. Send automated order notifications, customer updates, and bulk SMS campaigns to your Nigerian customers.

### Features

* **Automated Order Notifications** - Send SMS for order status changes (pending, processing, completed, etc.)
* **Bulk SMS Campaigns** - Send promotional messages to segmented customer groups
* **Customer Segmentation** - Target customers by order history, location, and purchase behavior
* **Template Management** - Create and manage reusable SMS templates with dynamic variables
* **Scheduled Sending** - Schedule SMS campaigns for optimal delivery times
* **Analytics Dashboard** - Track message delivery, success rates, and campaign performance
* **Comprehensive Logging** - Detailed logs of all SMS activities
* **Opt-in Management** - Respect customer preferences with built-in opt-in/opt-out functionality
* **Queue Management** - Reliable message delivery with retry logic and priority queuing
* **HPOS Compatible** - Full support for WooCommerce High-Performance Order Storage

### Nigeria Bulk SMS API

This plugin requires an account with Nigeria Bulk SMS (https://portal.nigeriabulksms.com/). You'll need:
* API Username
* API Password
* Sender ID

### Supported Notifications

* Order Received
* Order Processing
* Order Completed
* Order Shipped
* Order Cancelled
* Order Refunded
* Payment Received
* Low Stock Alerts
* Customer Registration Welcome
* Custom Events

### Template Variables

Use dynamic placeholders in your templates:
* {customer_name} - Customer's name
* {order_id} - Order number
* {order_total} - Order amount
* {order_status} - Current order status
* {site_name} - Your store name
* {tracking_number} - Shipping tracking number
* And more...

### Privacy

This plugin:
* Stores SMS logs in your WordPress database
* Sends customer phone numbers to Nigeria Bulk SMS API for message delivery
* Allows customers to opt-out of SMS notifications
* Complies with WordPress.org privacy guidelines

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/nigeria-bulk-sms-for-woocommerce/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Nigeria Bulk SMS > Settings to configure your API credentials
4. Configure notification templates and preferences
5. Test the connection to ensure everything is working

### Manual Installation

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded zip file and click Install Now
5. Activate the plugin after installation

### Requirements

* WordPress 5.0 or higher
* WooCommerce 5.0 or higher  
* PHP 7.4 or higher
* Nigeria Bulk SMS API account

== Frequently Asked Questions ==

= Do I need a Nigeria Bulk SMS account? =

Yes, you need to register for an account at https://portal.nigeriabulksms.com/ to get your API credentials.

= How much does it cost to send SMS? =

SMS costs are determined by Nigeria Bulk SMS. Check their pricing at https://portal.nigeriabulksms.com/

= Can I send SMS to international numbers? =

This plugin is optimized for Nigerian phone numbers. For international SMS, please contact Nigeria Bulk SMS support.

= How do I add custom template variables? =

You can use any WooCommerce order or customer data. Available variables are shown in the template editor.

= Is this plugin GDPR compliant? =

The plugin includes opt-in/opt-out functionality. You're responsible for ensuring your use complies with applicable privacy laws.

= Can I schedule bulk SMS campaigns? =

Yes, you can schedule SMS campaigns for future delivery from the Bulk SMS page.

= How are failed messages handled? =

Failed messages are automatically retried based on your retry settings, with detailed logging for troubleshooting.

== Screenshots ==

1. Dashboard with analytics and statistics
2. Settings page for API configuration
3. Message templates management
4. Bulk SMS campaign interface
5. SMS logs and delivery reports
6. Customer segmentation options

== Changelog ==

= 1.1.0 =
* Complete rewrite for WordPress.org submission compliance
* Removed Composer dependency
* Fixed all security issues (nonce verification, input sanitization, output escaping)
* Fixed text domain throughout plugin
* Renamed plugin to comply with WordPress.org naming guidelines
* Added proper database query preparation and caching
* Fixed script enqueuing
* Removed external CDN dependencies
* Improved HPOS compatibility
* Enhanced error handling and logging
* Code quality improvements

= 1.0.2 =
* Initial release
* Automated order notifications
* Bulk SMS campaigns
* Template management
* Analytics dashboard
* Comprehensive logging
* HPOS compatibility

== Upgrade Notice ==

= 1.1.0 =
Major update with security improvements and WordPress.org compliance. Recommended for all users.

== Privacy Policy ==

Nigeria Bulk SMS for WooCommerce stores the following data:

* SMS logs (recipient phone numbers, message content, delivery status)
* Customer opt-in/opt-out preferences
* Template configurations

The plugin sends customer phone numbers and messages to Nigeria Bulk SMS API for delivery. By using this plugin, you agree to Nigeria Bulk SMS's terms of service and privacy policy.

== Support ==

For support, please visit: https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce/issues

== Credits ==

* Developed by Emmanuel Eluwa
* Nigeria Bulk SMS API by Nigeria Bulk SMS
