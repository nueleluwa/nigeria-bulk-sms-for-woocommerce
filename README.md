# Nigeria Bulk SMS for WooCommerce

![WordPress Plugin Version](https://img.shields.io/badge/version-1.1.0-blue.svg)
![WordPress Compatibility](https://img.shields.io/badge/wordpress-5.0%2B-blue.svg)
![WooCommerce Compatibility](https://img.shields.io/badge/woocommerce-5.0%2B-purple.svg)
![PHP Version](https://img.shields.io/badge/php-7.4%2B-blue.svg)
![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)

A comprehensive SMS notification system that integrates seamlessly with WooCommerce to send automated order notifications, customer updates, and bulk SMS campaigns to Nigerian customers via the Nigeria Bulk SMS API.

---

## 🚀 Features

### Automated Notifications
- **Order Status Alerts** - Automatic SMS for order status changes (pending, processing, completed, shipped, etc.)
- **Payment Confirmations** - Instant payment received notifications
- **Shipping Updates** - Real-time shipping and tracking information
- **Custom Triggers** - Create notifications for any WooCommerce event

### Bulk SMS Campaigns
- **Customer Segmentation** - Target customers by order history, location, and purchase behavior
- **Scheduled Sending** - Plan campaigns for optimal delivery times
- **Template Management** - Create and reuse message templates
- **Campaign Analytics** - Track delivery rates and engagement

### Advanced Features
- **Queue Management** - Reliable message delivery with automatic retry logic
- **Priority System** - Ensure urgent messages are sent first
- **Analytics Dashboard** - Comprehensive reporting and insights
- **Comprehensive Logging** - Detailed logs of all SMS activities
- **Opt-in/Opt-out Management** - GDPR-compliant customer preferences
- **HPOS Compatible** - Full support for WooCommerce High-Performance Order Storage
- **Multilingual Ready** - Translation-ready with proper i18n

---

## 📋 Requirements

- WordPress 5.0 or higher
- WooCommerce 5.0 or higher
- PHP 7.4 or higher
- Nigeria Bulk SMS API account ([Sign up here](https://portal.nigeriabulksms.com/))

---

## 📦 Installation

### Automatic Installation (Recommended)

1. Log in to your WordPress admin panel
2. Navigate to **Plugins > Add New**
3. Search for "Nigeria Bulk SMS for WooCommerce"
4. Click **Install Now** and then **Activate**

### Manual Installation

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins > Add New > Upload Plugin**
4. Choose the downloaded ZIP file and click **Install Now**
5. Activate the plugin after installation

### Via GitHub

```bash
cd wp-content/plugins/
git clone https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce.git
cd nigeria-bulk-sms-for-woocommerce
# Download Chart.js
wget https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js -O admin/js/chart.min.js
```

Then activate the plugin through the WordPress admin panel.

---

## ⚙️ Configuration

### 1. Get API Credentials

1. Sign up at [Nigeria Bulk SMS Portal](https://portal.nigeriabulksms.com/)
2. Navigate to your account settings
3. Copy your API Username and Password
4. Register a Sender ID (if you haven't already)

### 2. Configure the Plugin

1. In WordPress admin, go to **Nigeria Bulk SMS > Settings**
2. Navigate to the **API Settings** tab
3. Enter your credentials:
   - **API Username** - Your Nigeria Bulk SMS username
   - **API Password** - Your Nigeria Bulk SMS password
   - **Sender ID** - Your registered sender ID (max 11 characters)
   - **Connection Timeout** - Default is 30 seconds
4. Click **Test Connection** to verify your credentials
5. Save your settings

### 3. Configure Notifications

1. Go to the **Notifications** tab
2. Enable the notifications you want to send:
   - Order Received
   - Order Processing
   - Order Completed
   - Order Shipped
   - Order Cancelled
   - Payment Received
   - And more...
3. Assign templates to each notification
4. Set any conditions (optional)
5. Save your notification settings

### 4. Create Message Templates

1. Go to **Nigeria Bulk SMS > Templates**
2. Click **Add New Template**
3. Enter a template name
4. Write your message using available variables:
   - `{customer_name}` - Customer's name
   - `{order_id}` - Order number
   - `{order_total}` - Order total amount
   - `{order_status}` - Current order status
   - `{site_name}` - Your store name
   - `{tracking_number}` - Shipping tracking number
   - And more...
5. Save the template

---

## 🎯 Usage

### Automatic Notifications

Once configured, the plugin automatically sends SMS notifications based on WooCommerce events. No additional action required!

### Sending Bulk SMS

1. Go to **Nigeria Bulk SMS > Bulk SMS**
2. Select your target audience:
   - All customers
   - Customers with orders
   - Customers by location
   - Customers by order status
   - Custom phone numbers
3. Choose a message template or write a custom message
4. Preview your message
5. Schedule or send immediately
6. Click **Send SMS**

### Sending Test SMS

1. Go to **Nigeria Bulk SMS > Testing**
2. Enter a test phone number
3. Write a test message
4. Click **Send Test SMS**
5. Check the logs for delivery status

### Viewing Logs

1. Go to **Nigeria Bulk SMS > Logs**
2. View all SMS activities with:
   - Delivery status
   - Recipient information
   - Message content
   - Cost per message
   - Delivery time
   - Error messages (if any)
3. Filter logs by status, date, or search terms
4. Export logs for reporting

---

## 📊 Template Variables

Use these variables in your message templates for dynamic content:

### Customer Variables
- `{customer_name}` - Customer's full name
- `{customer_first_name}` - Customer's first name
- `{customer_last_name}` - Customer's last name
- `{customer_email}` - Customer's email address
- `{customer_phone}` - Customer's phone number

### Order Variables
- `{order_id}` - Order number
- `{order_total}` - Order total amount with currency
- `{order_subtotal}` - Order subtotal
- `{order_tax}` - Order tax amount
- `{order_shipping}` - Shipping cost
- `{order_status}` - Current order status
- `{order_date}` - Order date
- `{payment_method}` - Payment method used

### Shipping Variables
- `{shipping_address}` - Full shipping address
- `{shipping_city}` - Shipping city
- `{shipping_state}` - Shipping state
- `{shipping_postcode}` - Shipping postcode
- `{tracking_number}` - Shipping tracking number

### Store Variables
- `{site_name}` - Your store name
- `{site_url}` - Your store URL
- `{store_email}` - Store email address
- `{store_phone}` - Store phone number

### Product Variables (for order items)
- `{product_names}` - List of product names
- `{product_quantity}` - Total quantity of items

---

## 🔐 Security Features

- **Nonce Verification** - All forms and AJAX requests are protected
- **Input Sanitization** - All user input is sanitized before processing
- **Output Escaping** - All output is escaped to prevent XSS attacks
- **Prepared Statements** - All database queries use prepared statements
- **Capability Checks** - Only authorized users can access admin features
- **Opt-in System** - GDPR-compliant customer consent management
- **Data Encryption** - Sensitive data is stored securely

---

## 🛠️ Development

### Building from Source

```bash
# Clone the repository
git clone https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce.git
cd nigeria-bulk-sms-for-woocommerce

# Download Chart.js dependency
wget https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js \
  -O admin/js/chart.min.js

# Create production ZIP
zip -r nigeria-bulk-sms-for-woocommerce.zip . \
  -x "*.git*" "*.DS_Store" "*.md" "node_modules/*"
```

### Folder Structure

```
nigeria-bulk-sms-for-woocommerce/
├── admin/                          # Admin functionality
│   ├── class-nbsms-admin.php      # Main admin class
│   ├── css/
│   │   └── admin.css              # Admin styles
│   └── js/
│       ├── admin.js               # Admin scripts
│       └── chart.min.js           # Chart.js library
├── includes/                       # Core functionality
│   ├── class-nbsms-api.php        # API wrapper
│   ├── class-nbsms-bulk.php       # Bulk SMS handler
│   ├── class-nbsms-core.php       # Core plugin class
│   ├── class-nbsms-db.php         # Database operations
│   ├── class-nbsms-logs.php       # Logging system
│   ├── class-nbsms-notifications.php  # Notification handler
│   ├── class-nbsms-opt-in.php     # Opt-in/out management
│   ├── class-nbsms-settings.php   # Settings management
│   └── class-nbsms-template-parser.php  # Template parser
├── templates/                      # Admin page templates
│   ├── admin-bulk.php             # Bulk SMS page
│   ├── admin-logs.php             # Logs page
│   ├── admin-settings.php         # Settings page
│   ├── admin-template-form.php    # Template editor
│   ├── admin-template-view.php    # Template viewer
│   ├── admin-templates-list.php   # Templates list
│   └── admin-testing.php          # Testing page
├── languages/                      # Translation files
├── nigeria-bulk-sms-for-woocommerce.php  # Main plugin file
├── readme.txt                      # WordPress.org readme
├── uninstall.php                   # Uninstall cleanup
└── LICENSE.txt                     # GPL v2+ license
```

### Coding Standards

This plugin follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/):
- PHP: [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- JavaScript: [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- CSS: [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)

---

## 🧪 Testing

### Manual Testing

1. Install on a test WordPress + WooCommerce site
2. Configure API credentials
3. Create a test order
4. Verify SMS notifications are sent
5. Check logs for delivery confirmation
6. Test bulk SMS functionality
7. Verify all admin pages load correctly

### Testing Checklist

- [ ] Plugin activates without errors
- [ ] API connection test works
- [ ] Automatic notifications send correctly
- [ ] Bulk SMS sends successfully
- [ ] Templates save and load correctly
- [ ] Logs display properly
- [ ] Charts render on logs page
- [ ] No PHP errors in debug.log
- [ ] No JavaScript console errors
- [ ] Works with WooCommerce HPOS

---

## 📱 Supported SMS Gateways

Currently supports:
- **Nigeria Bulk SMS** - Primary gateway (https://portal.nigeriabulksms.com/)

*Note: This plugin is specifically designed for the Nigeria Bulk SMS API. Support for additional gateways may be added in future versions.*

---

## 🌍 Internationalization

The plugin is fully translation-ready with proper internationalization:
- Text domain: `nigeria-bulk-sms-for-woocommerce`
- Translation files: `/languages/`
- POT file included for easy translation

### Contributing Translations

1. Download the `.pot` file from `/languages/`
2. Use [Poedit](https://poedit.net/) to create translations
3. Submit via pull request or contact the developer

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### Reporting Bugs

1. Check if the issue already exists in [GitHub Issues](https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce/issues)
2. If not, create a new issue with:
   - Clear description of the bug
   - Steps to reproduce
   - Expected behavior
   - Actual behavior
   - WordPress/WooCommerce/PHP versions
   - Any error messages

### Suggesting Features

1. Open a new issue with the `enhancement` label
2. Clearly describe the feature and its use case
3. Explain how it would benefit users

### Submitting Pull Requests

1. Fork the repository
2. Create a new branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Follow WordPress coding standards
5. Test thoroughly
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Development Guidelines

- Follow WordPress coding standards
- Add PHPDoc comments to all functions
- Write clear commit messages
- Test on multiple WordPress/WooCommerce versions
- Ensure backward compatibility
- Update documentation as needed

---

## 📝 Changelog

### Version 1.1.0 (November 2, 2025)

**Major Update - WordPress.org Compliance**

#### Fixed
- Removed Composer dependency - standalone API implementation
- Fixed text domain throughout (now: `nigeria-bulk-sms-for-woocommerce`)
- Added output escaping to all templates
- Implemented nonce verification for all forms and AJAX requests
- Added `wp_unslash()` before all input sanitization
- Prepared all database queries with `$wpdb->prepare()`
- Implemented database caching with `wp_cache_get()`/`wp_cache_set()`
- Fixed script enqueuing to use proper WordPress methods
- Removed external CDN dependencies (Chart.js now local)

#### Changed
- Plugin name: "Nigeria Bulk SMS for WooCommerce" (WordPress.org compliant)
- Plugin slug: "nigeria-bulk-sms-for-woocommerce" (no restricted terms)
- Improved security with comprehensive input/output handling
- Enhanced performance with database caching
- Better error handling and logging

#### Security
- Fixed all XSS vulnerabilities
- Fixed all CSRF vulnerabilities
- Fixed all SQL injection vulnerabilities
- Added capability checks for all admin functions
- Improved data validation and sanitization

#### Documentation
- Complete rewrite of readme.txt for WordPress.org
- New production README.md for GitHub
- Added comprehensive inline code documentation
- Created detailed setup guides

### Version 1.0.2 (Previous)
- Initial public release
- Automated order notifications
- Bulk SMS campaigns
- Template management
- Analytics dashboard
- Comprehensive logging
- HPOS compatibility

---

## 🆘 Support

### Documentation
- [Installation Guide](#-installation)
- [Configuration Guide](#%EF%B8%8F-configuration)
- [Usage Guide](#-usage)
- [Template Variables](#-template-variables)

### Getting Help

1. **Check Documentation** - Most questions are answered in this README
2. **WordPress.org Forums** - [Plugin Support Forum](https://wordpress.org/support/plugin/nigeria-bulk-sms-for-woocommerce/)
3. **GitHub Issues** - [Report bugs or request features](https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce/issues)
4. **Nigeria Bulk SMS Support** - For API-related issues: [Contact Nigeria Bulk SMS](https://portal.nigeriabulksms.com/)

### Common Issues

**SMS not sending?**
- Verify API credentials are correct
- Check account balance in Nigeria Bulk SMS portal
- Review logs for error messages
- Test connection in Settings page

**Plugin won't activate?**
- Ensure WooCommerce is installed and activated
- Check PHP version (7.4+ required)
- Check WordPress version (5.0+ required)
- Review server error logs

**Charts not displaying?**
- Ensure Chart.js file exists at `admin/js/chart.min.js`
- Check browser console for JavaScript errors
- Clear browser cache

---

## 📄 License

This plugin is licensed under the GNU General Public License v2.0 or later.

```
Nigeria Bulk SMS for WooCommerce
Copyright (C) 2025 Emmanuel Eluwa

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
```

See [LICENSE.txt](LICENSE.txt) for full license text.

---

## 🙏 Acknowledgments

- **Nigeria Bulk SMS** - For providing the SMS API
- **WordPress Community** - For excellent documentation and support
- **WooCommerce Team** - For the robust e-commerce platform
- **Chart.js** - For beautiful charts and graphs
- **Contributors** - Thanks to everyone who has contributed to this project

---

## 📞 Contact

**Developer:** Emmanuel Eluwa  
**GitHub:** [@nueleluwa](https://github.com/nueleluwa)  
**Plugin URI:** [https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce](https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce)  
**WordPress.org:** [Plugin Page](https://wordpress.org/plugins/nigeria-bulk-sms-for-woocommerce/) *(pending approval)*

---

## ⭐ Show Your Support

If you find this plugin useful, please:
- ⭐ Star this repository on GitHub
- 📝 Leave a review on WordPress.org (after it's approved)
- 🐛 Report bugs and suggest features
- 🔀 Contribute code via pull requests
- 📢 Share with others who might benefit

---

## 🚀 Roadmap

### Planned Features
- [ ] Support for additional SMS gateways
- [ ] WhatsApp integration
- [ ] SMS scheduling calendar view
- [ ] Advanced customer segmentation
- [ ] A/B testing for campaigns
- [ ] SMS templates marketplace
- [ ] REST API for external integrations
- [ ] Mobile app for managing campaigns
- [ ] Multi-language SMS support
- [ ] AI-powered message optimization

### Under Consideration
- Integration with popular form plugins
- Support for MMS (multimedia messages)
- Two-factor authentication via SMS
- Customer reply handling
- Advanced analytics and reporting
- Integration with CRM systems

*Have a feature request? [Open an issue](https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce/issues/new) and let us know!*

---

**Made with ❤️ for the Nigerian e-commerce community**

*Empowering businesses to connect with customers through SMS*
