# Contributing to Nigeria Bulk SMS for WooCommerce

First off, thank you for considering contributing to Nigeria Bulk SMS for WooCommerce! It's people like you that make this plugin better for everyone.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Pull Request Process](#pull-request-process)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Features](#suggesting-features)
- [Translation](#translation)

---

## 📜 Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

### Our Pledge

We pledge to make participation in our project a harassment-free experience for everyone, regardless of age, body size, disability, ethnicity, gender identity and expression, level of experience, nationality, personal appearance, race, religion, or sexual identity and orientation.

### Our Standards

**Examples of behavior that contributes to a positive environment:**
- Using welcoming and inclusive language
- Being respectful of differing viewpoints and experiences
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

**Examples of unacceptable behavior:**
- The use of sexualized language or imagery
- Trolling, insulting/derogatory comments, and personal or political attacks
- Public or private harassment
- Publishing others' private information without explicit permission
- Other conduct which could reasonably be considered inappropriate

---

## 🤝 How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

**Use this template:**

```markdown
**Describe the bug**
A clear and concise description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

**Expected behavior**
A clear and concise description of what you expected to happen.

**Screenshots**
If applicable, add screenshots to help explain your problem.

**Environment:**
- WordPress Version: [e.g. 6.4]
- WooCommerce Version: [e.g. 8.5]
- PHP Version: [e.g. 8.1]
- Plugin Version: [e.g. 1.1.0]
- Browser: [e.g. Chrome 120]

**Error Messages**
Any error messages from:
- WordPress debug.log
- Browser console
- Plugin logs

**Additional context**
Add any other context about the problem here.
```

### Suggesting Features

Feature requests are welcome! Before suggesting a feature:

1. Check if it's already been suggested
2. Consider if it fits the plugin's scope
3. Think about how it would benefit most users

**Use this template:**

```markdown
**Is your feature request related to a problem?**
A clear description of what the problem is. Ex. I'm always frustrated when [...]

**Describe the solution you'd like**
A clear and concise description of what you want to happen.

**Describe alternatives you've considered**
Alternative solutions or features you've considered.

**Would you be willing to contribute this feature?**
- [ ] Yes, I can submit a pull request
- [ ] No, but I can help test it
- [ ] No, just suggesting

**Additional context**
Add any other context or screenshots about the feature request here.
```

### Improving Documentation

Documentation improvements are always welcome! This includes:
- Fixing typos or unclear wording
- Adding examples
- Improving setup guides
- Translating documentation

---

## 💻 Development Setup

### Prerequisites

- WordPress 5.0+
- WooCommerce 5.0+
- PHP 7.4+
- Node.js 14+ (for build tools, if needed)
- Git
- Code editor (VS Code recommended)

### Setting Up Development Environment

1. **Fork and Clone**

```bash
# Fork the repository on GitHub, then:
git clone https://github.com/YOUR-USERNAME/nigeria-bulk-sms-for-woocommerce.git
cd nigeria-bulk-sms-for-woocommerce
```

2. **Add Upstream Remote**

```bash
git remote add upstream https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce.git
```

3. **Install Dependencies**

```bash
# Download Chart.js
wget https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js \
  -O admin/js/chart.min.js
```

4. **Set Up Test Environment**

```bash
# Use Local by Flywheel, XAMPP, or Docker
# Install WordPress and WooCommerce
# Symlink or copy plugin to wp-content/plugins/
```

5. **Enable Debug Mode**

Add to `wp-config.php`:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
```

### Development Workflow

1. **Create a Branch**

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b fix/your-bug-fix
```

2. **Make Changes**
- Write clean, documented code
- Follow WordPress coding standards
- Test thoroughly

3. **Commit Changes**

```bash
git add .
git commit -m "Add feature: your feature description"
```

4. **Push to Your Fork**

```bash
git push origin feature/your-feature-name
```

5. **Create Pull Request**
- Go to GitHub
- Click "New Pull Request"
- Fill in the PR template

---

## 📝 Coding Standards

### WordPress Coding Standards

This plugin follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/):

**PHP:**
- Use tabs for indentation
- Opening braces on the same line
- Yoda conditions: `if ( 'yes' === $value )`
- Single quotes for strings (unless interpolation needed)
- Space after keywords: `if ( condition )`

**Example:**
```php
<?php
/**
 * Function description
 *
 * @param string $param Parameter description
 * @return bool Return value description
 */
function nbsms_example_function( $param ) {
    if ( 'value' === $param ) {
        return true;
    }
    return false;
}
```

**JavaScript:**
- Use camelCase for variables and functions
- Use tabs for indentation
- Add semicolons
- Use strict mode

**Example:**
```javascript
(function($) {
    'use strict';
    
    var pluginName = 'nbsms';
    
    function initialize() {
        // Code here
    }
    
    $(document).ready(function() {
        initialize();
    });
    
})(jQuery);
```

**CSS:**
- Use hyphens for class names
- Lowercase for properties
- One property per line

**Example:**
```css
.nbsms-container {
    display: flex;
    flex-direction: column;
    padding: 20px;
}
```

### Documentation Standards

**PHPDoc Comments:**
```php
/**
 * Short description (required)
 *
 * Long description (optional)
 *
 * @since 1.0.0
 * @param string $param1 Description of param1
 * @param int    $param2 Description of param2
 * @return bool Description of return value
 */
function nbsms_function_name( $param1, $param2 ) {
    // Function code
}
```

**Inline Comments:**
```php
// Single line comment

/*
 * Multi-line comment
 * with multiple lines
 */
```

### Naming Conventions

**Functions:**
- Prefix with `nbsms_`
- Use lowercase and underscores
- Examples: `nbsms_send_sms()`, `nbsms_get_template()`

**Classes:**
- Prefix with `NBSMS_`
- Use title case
- Examples: `NBSMS_Admin`, `NBSMS_API`

**Variables:**
- Use lowercase and underscores
- Be descriptive
- Examples: `$message_content`, `$customer_phone`

**Constants:**
- All uppercase
- Use underscores
- Examples: `NBSMS_VERSION`, `NBSMS_PLUGIN_DIR`

---

## 🔄 Pull Request Process

### Before Submitting

1. **Update Documentation**
   - Update README.md if needed
   - Update inline code comments
   - Update CHANGELOG.md

2. **Test Thoroughly**
   - Test on multiple WordPress versions
   - Test on multiple PHP versions (7.4, 8.0, 8.1, 8.2)
   - Test with WooCommerce HPOS enabled and disabled
   - Test all affected functionality

3. **Check Code Quality**
   - Run PHPCS (WordPress coding standards)
   - Check for PHP errors
   - Check for JavaScript errors
   - Validate HTML/CSS

4. **Ensure Compatibility**
   - No breaking changes for existing users
   - Backward compatible if possible
   - Database changes properly handled

### PR Template

When creating a pull request, use this template:

```markdown
## Description
Brief description of the changes

## Type of Change
- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to change)
- [ ] Documentation update

## Related Issue
Closes #(issue number)

## Testing
- [ ] I have tested this on WordPress 5.0+
- [ ] I have tested this on WooCommerce 5.0+
- [ ] I have tested this with WooCommerce HPOS
- [ ] I have tested this on PHP 7.4+
- [ ] No PHP errors or warnings
- [ ] No JavaScript errors in console

## Checklist
- [ ] My code follows the WordPress coding standards
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have made corresponding changes to the documentation
- [ ] My changes generate no new warnings
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] New and existing unit tests pass locally with my changes
- [ ] Any dependent changes have been merged and published

## Screenshots (if applicable)
Add screenshots here
```

### Review Process

1. Maintainers will review your PR
2. You may be asked to make changes
3. Once approved, your PR will be merged
4. You'll be added to the contributors list!

### After Your PR is Merged

1. Delete your feature branch (optional)
2. Pull the latest changes
3. Celebrate! 🎉

---

## 🐛 Reporting Bugs

### Security Vulnerabilities

**DO NOT** open a public issue for security vulnerabilities. Instead:
- Email the maintainer directly
- Provide details about the vulnerability
- Allow time for a fix before public disclosure

### Regular Bugs

1. **Search Existing Issues** - Check if already reported
2. **Use the Bug Template** - Provide all requested information
3. **Be Specific** - Include steps to reproduce
4. **Be Patient** - Maintainers will respond as soon as possible

---

## 💡 Suggesting Features

### Good Feature Requests Include:

1. **Clear Use Case** - Who benefits and how?
2. **Detailed Description** - What should it do?
3. **Example Implementation** - How might it work?
4. **Alternatives Considered** - What else did you think about?

### Feature Request Process

1. Open an issue with the feature request template
2. Discuss with maintainers and community
3. If approved, it will be added to the roadmap
4. You or someone else can implement it!

---

## 🌍 Translation

### Contributing Translations

We welcome translations in all languages!

1. **Download POT File**
   - Get `languages/nigeria-bulk-sms-for-woocommerce.pot`

2. **Translate**
   - Use [Poedit](https://poedit.net/) or similar tool
   - Translate all strings
   - Save as `.po` and `.mo` files

3. **Submit Translation**
   - Create a PR with your translation files
   - Or email them to the maintainer
   - Or use GlotPress (when available on WordPress.org)

### Translation Guidelines

- Use natural, conversational language
- Be consistent with terminology
- Consider cultural context
- Test in the plugin before submitting

---

## 🧪 Testing

### Manual Testing

Required for all contributions:

1. Install plugin on test site
2. Test the specific feature/fix
3. Test related functionality
4. Check for PHP errors
5. Check for JavaScript errors
6. Test on different browsers

### Automated Testing

(To be implemented in future versions)

---

## 📦 Release Process

For maintainers:

1. Update version number in:
   - Main plugin file
   - readme.txt
   - package.json (if applicable)

2. Update CHANGELOG.md

3. Create git tag:
```bash
git tag -a v1.x.x -m "Version 1.x.x"
git push origin v1.x.x
```

4. Create GitHub release

5. Update WordPress.org:
   - Update readme.txt
   - Upload new ZIP file
   - Update assets (screenshots, banners)

---

## 📚 Additional Resources

### WordPress Development
- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WooCommerce Documentation](https://woocommerce.com/documentation/)

### Tools
- [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) - Code sniffer
- [Poedit](https://poedit.net/) - Translation tool
- [Query Monitor](https://wordpress.org/plugins/query-monitor/) - Debug plugin

### Community
- [WordPress Stack Exchange](https://wordpress.stackexchange.com/)
- [WooCommerce Community](https://woocommerce.com/community/)

---

## 🏆 Recognition

All contributors will be:
- Added to the contributors list in README.md
- Mentioned in release notes
- Given credit in the plugin description (for significant contributions)

---

## 📞 Questions?

If you have questions about contributing:

1. Check this guide first
2. Review existing issues and PRs
3. Open an issue with the "question" label
4. Contact the maintainer directly

---

## 🙏 Thank You!

Thank you for taking the time to contribute! Your effort helps make this plugin better for everyone in the Nigerian e-commerce community.

**Together, we can build something amazing!** 🚀

---

**Last Updated:** November 2, 2025  
**Maintained by:** Emmanuel Eluwa ([@nueleluwa](https://github.com/nueleluwa))
