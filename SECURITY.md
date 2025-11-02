# Security Policy

## Supported Versions

We release patches for security vulnerabilities. Which versions are eligible for receiving such patches depends on the CVSS v3.0 Rating:

| Version | Supported          |
| ------- | ------------------ |
| 1.1.x   | :white_check_mark: |
| 1.0.x   | :x:                |
| < 1.0   | :x:                |

## Reporting a Vulnerability

The Nigeria Bulk SMS for WooCommerce team takes security bugs seriously. We appreciate your efforts to responsibly disclose your findings, and will make every effort to acknowledge your contributions.

### Where to Report

**DO NOT** open a public GitHub issue for security vulnerabilities.

Instead, please report security vulnerabilities by emailing:
- **Email:** [Your security email]
- **Subject:** `[SECURITY] Nigeria Bulk SMS for WooCommerce - Brief Description`

### What to Include

Please include the following information in your report:

1. **Description** - A clear description of the vulnerability
2. **Impact** - What can an attacker do with this vulnerability?
3. **Reproduction Steps** - Detailed steps to reproduce the issue
4. **Affected Versions** - Which versions are affected?
5. **Proof of Concept** - If possible, include PoC code or screenshots
6. **Suggested Fix** - If you have ideas on how to fix it
7. **Your Contact Information** - How can we reach you for follow-up?

### What to Expect

1. **Acknowledgment** - We will acknowledge receipt within 48 hours
2. **Investigation** - We will investigate and validate the vulnerability
3. **Updates** - We will keep you informed of our progress
4. **Resolution** - We will work on a fix and release it as soon as possible
5. **Credit** - We will credit you in the release notes (if you want)

### Timeline

- **Initial Response:** Within 48 hours
- **Status Update:** Within 7 days
- **Fix Timeline:** Varies based on severity
  - Critical: Within 7-14 days
  - High: Within 14-30 days
  - Medium: Within 30-60 days
  - Low: Next regular release

## Security Best Practices for Users

### Plugin Security

1. **Keep Updated** - Always use the latest version
2. **Use Strong Credentials** - Use strong passwords for your Nigeria Bulk SMS API
3. **Limit Access** - Only give admin access to trusted users
4. **Enable HTTPS** - Always use HTTPS for your WordPress site
5. **Regular Backups** - Maintain regular backups of your database

### WordPress Security

1. **Update WordPress** - Keep WordPress core updated
2. **Update PHP** - Use PHP 7.4 or higher (8.0+ recommended)
3. **Security Plugins** - Consider using security plugins like Wordfence
4. **Strong Passwords** - Use strong, unique passwords
5. **Two-Factor Authentication** - Enable 2FA for admin accounts
6. **File Permissions** - Set proper file permissions (644 for files, 755 for directories)

### API Security

1. **Secure Credentials** - Never share your Nigeria Bulk SMS API credentials
2. **Environment Variables** - Consider storing credentials in environment variables
3. **Access Control** - Limit who has access to API settings
4. **Monitor Usage** - Regularly check your SMS usage and logs
5. **Rotate Credentials** - Periodically change your API credentials

## Security Features in the Plugin

### Current Security Measures

✅ **Input Validation**
- All user input is sanitized before processing
- `wp_unslash()` applied before sanitization
- Type casting for numeric values

✅ **Output Escaping**
- All output is escaped using appropriate functions
- `esc_html()`, `esc_attr()`, `esc_url()`, `esc_js()`
- Context-aware escaping

✅ **Nonce Verification**
- All forms use nonce verification
- All AJAX requests verify nonces
- Unique nonces per action

✅ **Prepared Statements**
- All database queries use `$wpdb->prepare()`
- No raw SQL queries with user input
- SQL injection prevention

✅ **Capability Checks**
- All admin functions check user capabilities
- `manage_woocommerce` capability required
- Proper permission enforcement

✅ **Data Encryption**
- Sensitive data stored securely
- API credentials protected
- Database encryption ready

✅ **Rate Limiting**
- SMS sending rate limits
- API request throttling
- Queue management

## Known Security Considerations

### SMS Content
- SMS messages may contain customer data
- Messages are logged for tracking purposes
- Logs can be purged automatically after X days

### API Credentials
- Stored in WordPress options table
- Transmitted securely via HTTPS
- Never exposed to frontend

### Customer Data
- Phone numbers are stored for SMS delivery
- Customer consent managed via opt-in system
- GDPR compliant with proper data handling

## Vulnerability Disclosure Policy

### Our Commitment

We commit to:
- Acknowledging receipt within 48 hours
- Providing regular status updates
- Crediting researchers who report responsibly
- Releasing fixes as quickly as possible
- Communicating transparently with users

### Researcher Recognition

We appreciate security researchers and will:
- Publicly thank you (if you wish)
- Credit you in release notes
- List you in our Hall of Fame (coming soon)
- Work with you on disclosure timeline

### Coordinated Disclosure

We prefer coordinated disclosure:
1. Report to us privately
2. We investigate and develop a fix
3. We release the fix
4. We publish a security advisory
5. You may publish your research

We request a minimum of 90 days for coordinated disclosure.

## Security Hall of Fame

*No vulnerabilities reported yet - be the first!*

<!-- Future entries will be listed here -->

## Security Updates

Security updates will be:
- Released immediately for critical issues
- Announced on WordPress.org
- Posted on GitHub releases
- Emailed to plugin users (if possible)

## Contact

- **Security Email:** [Your security email]
- **General Email:** [Your general email]
- **GitHub:** [@nueleluwa](https://github.com/nueleluwa)

## Additional Resources

### WordPress Security
- [WordPress Security](https://wordpress.org/support/article/hardening-wordpress/)
- [WooCommerce Security](https://woocommerce.com/document/woocommerce-security/)

### Responsible Disclosure
- [OWASP Vulnerability Disclosure Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Vulnerability_Disclosure_Cheat_Sheet.html)

### Security Standards
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WordPress Plugin Security](https://developer.wordpress.org/plugins/security/)

---

**Last Updated:** November 2, 2025  
**Version:** 1.1.0

Thank you for helping keep Nigeria Bulk SMS for WooCommerce and its users safe!
