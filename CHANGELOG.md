# Changelog

All notable changes to Nigeria Bulk SMS for WooCommerce will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Support for additional SMS gateways
- WhatsApp integration
- Advanced customer segmentation
- A/B testing for campaigns
- REST API endpoints

---

## [1.1.0] - 2025-11-02

### 🎉 Major Update - WordPress.org Compliance & Security Hardening

This is a major rewrite to ensure full compliance with WordPress.org plugin requirements and implement comprehensive security improvements.

### Added
- Database caching using `wp_cache_get()` and `wp_cache_set()`
- Proper nonce verification for all forms and AJAX requests
- Comprehensive input sanitization with `wp_unslash()`
- Output escaping for all user-facing content
- PHPDoc documentation for all functions and classes
- Proper uninstall cleanup script
- WordPress.org compliant readme.txt
- Comprehensive GitHub documentation (README.md, CONTRIBUTING.md, CHANGELOG.md)

### Changed
- **BREAKING:** Plugin name changed from "WooCommerce Nigeria Bulk SMS" to "Nigeria Bulk SMS for WooCommerce" (WordPress.org compliant)
- **BREAKING:** Plugin slug changed from "wc-nigeria-bulk-sms" to "nigeria-bulk-sms-for-woocommerce"
- **BREAKING:** Text domain changed to "nigeria-bulk-sms-for-woocommerce" throughout
- Removed Composer dependency - implemented standalone API wrapper
- Removed external CDN dependencies - Chart.js now bundled locally
- Improved database query performance with prepared statements
- Enhanced error handling and logging
- Refactored code structure for better maintainability

### Fixed
- **Security:** Fixed XSS vulnerabilities by implementing proper output escaping
- **Security:** Fixed CSRF vulnerabilities by adding nonce verification
- **Security:** Fixed SQL injection vulnerabilities by using prepared statements
- **Security:** Added capability checks for all admin operations
- Text domain mismatches across all files
- Missing `wp_unslash()` calls before sanitization
- Direct database queries without `$wpdb->prepare()`
- Scripts not properly enqueued via `wp_enqueue_script()`
- Template variables not properly escaped
- Form data processed without security checks

### Security
- Implemented comprehensive input validation
- Added output escaping (esc_html, esc_attr, esc_url, esc_js)
- Added nonce verification for all state-changing operations
- Implemented prepared SQL statements for all database operations
- Added capability checks for admin functions
- Sanitized all user input with appropriate WordPress functions
- Removed all external resource dependencies

### Removed
- Composer dependency (ossycodes/nigeriabulksms-php)
- External CDN loading for Chart.js
- composer.json file
- Unnecessary development files from production builds

### Developer Notes
- All functions now follow WordPress coding standards
- Comprehensive inline documentation added
- Database operations now use caching where appropriate
- All AJAX handlers properly secured
- Template files properly organized

---

## [1.0.2] - 2024-10-XX

### Added
- Initial public release
- Automated SMS notifications for WooCommerce orders
- Bulk SMS campaign functionality
- Message template management system
- Customer segmentation and targeting
- SMS queue management with retry logic
- Comprehensive activity logging
- Analytics dashboard with charts
- Customer opt-in/opt-out management
- HPOS (High-Performance Order Storage) compatibility
- Nigeria Bulk SMS API integration
- Multiple order status notifications:
  - Order Received
  - Order Processing
  - Order Completed
  - Order Shipped
  - Order Cancelled
  - Order Refunded
  - Payment Received
- Template variables for dynamic content
- API connection testing
- Balance checking functionality
- Test SMS sending

### Features
- **Admin Dashboard:** Overview of SMS activities and statistics
- **Settings Page:** API configuration and general settings
- **Templates Manager:** Create and manage reusable SMS templates
- **Bulk SMS:** Send campaigns to segmented customer groups
- **Logs Viewer:** Detailed logs with filtering and search
- **Testing Tool:** Test SMS sending before going live

### Technical
- PHP 7.4+ support
- WordPress 5.0+ compatibility
- WooCommerce 5.0+ compatibility
- MySQL database tables for logs, templates, and queue
- AJAX-based admin interface
- Chart.js for analytics visualization
- Responsive admin design

---

## [1.0.1] - 2024-09-XX (Internal)

### Fixed
- Minor bug fixes
- Improved error handling
- Database table creation issues

---

## [1.0.0] - 2024-08-XX (Internal)

### Added
- Initial development version
- Basic SMS sending functionality
- WooCommerce integration
- Nigeria Bulk SMS API wrapper

---

## Version History Summary

| Version | Date | Type | Status |
|---------|------|------|--------|
| 1.1.0 | 2025-11-02 | Major | Current |
| 1.0.2 | 2024-10-XX | Minor | Previous |
| 1.0.1 | 2024-09-XX | Patch | Internal |
| 1.0.0 | 2024-08-XX | Major | Internal |

---

## Upgrade Guide

### Upgrading from 1.0.x to 1.1.0

**Important Changes:**
1. Plugin slug has changed - you may need to deactivate and reactivate
2. Text domain has changed - any custom translations need updating
3. No Composer installation required anymore
4. Chart.js is now bundled - ensure it's present in `admin/js/chart.min.js`

**Migration Steps:**
1. Backup your database before upgrading
2. Backup your plugin settings
3. Deactivate the old version
4. Delete the old plugin folder
5. Install the new version (1.1.0)
6. Activate the plugin
7. Verify your API credentials in settings
8. Test SMS sending functionality
9. Review your notification templates
10. Check logs for any issues

**Data Preservation:**
- All settings are preserved
- All logs are preserved
- All templates are preserved
- Queue items are preserved

**Breaking Changes:**
- Plugin folder name changed (update any hardcoded paths)
- Text domain changed (update any custom translations)
- Function prefixes remain the same (no code changes needed)

---

## Support & Resources

- **Documentation:** [README.md](README.md)
- **Contributing:** [CONTRIBUTING.md](CONTRIBUTING.md)
- **Issues:** [GitHub Issues](https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce/issues)
- **Support Forum:** [WordPress.org](https://wordpress.org/support/plugin/nigeria-bulk-sms-for-woocommerce/)

---

## Deprecation Notices

### Version 1.1.0
- None

### Future Deprecations
- None currently planned

---

## Contributors

### Version 1.1.0
- Emmanuel Eluwa ([@nueleluwa](https://github.com/nueleluwa)) - Complete rewrite and WordPress.org compliance

### Version 1.0.2
- Emmanuel Eluwa ([@nueleluwa](https://github.com/nueleluwa)) - Initial release

---

## License

This project is licensed under the GNU General Public License v2.0 or later - see the [LICENSE.txt](LICENSE.txt) file for details.

---

**Note:** For the most up-to-date information, always refer to the [latest version on GitHub](https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce).

[Unreleased]: https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce/releases/tag/v1.0.2
