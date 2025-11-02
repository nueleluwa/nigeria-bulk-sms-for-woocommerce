# GitHub Repository Setup Guide

Complete guide to setting up your production GitHub repository for Nigeria Bulk SMS for WooCommerce.

## 📋 Repository Setup Checklist

### 1. Create Repository

```bash
# On GitHub.com
1. Click "New Repository"
2. Name: nigeria-bulk-sms-for-woocommerce
3. Description: Send automated & bulk SMS notifications to WooCommerce customers via Nigeria Bulk SMS API
4. Public repository (for WordPress.org)
5. Initialize with README: No (we have our own)
6. Add .gitignore: No (we have our own)
7. Choose License: GNU General Public License v2.0
8. Click "Create Repository"
```

### 2. Prepare Local Repository

```bash
# Extract your plugin
cd /path/to/your/plugins
unzip nigeria-bulk-sms-for-woocommerce-SUBMISSION-READY.zip
cd nigeria-bulk-sms-for-woocommerce-FINAL

# Initialize git
git init
git add .
git commit -m "Initial commit: v1.1.0 - WordPress.org ready"

# Add GitHub remote
git remote add origin https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce.git

# Push to GitHub
git branch -M main
git push -u origin main
```

### 3. Add Documentation Files

Copy these files to your repository root:

```bash
# Core documentation (from outputs folder)
cp README.md /path/to/repo/
cp CONTRIBUTING.md /path/to/repo/
cp CHANGELOG.md /path/to/repo/
cp SECURITY.md /path/to/repo/
cp CODE_OF_CONDUCT.md /path/to/repo/
cp LICENSE /path/to/repo/
cp .gitignore /path/to/repo/

# Commit documentation
git add .
git commit -m "docs: Add comprehensive documentation"
git push
```

### 4. Create GitHub Issue Templates

```bash
# Create directory
mkdir -p .github/ISSUE_TEMPLATE

# Create templates (see GITHUB_TEMPLATES_GUIDE.md)
# Copy templates:
# - .github/ISSUE_TEMPLATE/bug_report.md
# - .github/ISSUE_TEMPLATE/feature_request.md
# - .github/ISSUE_TEMPLATE/question.md
# - .github/PULL_REQUEST_TEMPLATE.md

# Commit templates
git add .github/
git commit -m "chore: Add GitHub issue templates"
git push
```

### 5. Configure Repository Settings

#### General Settings
1. Go to Settings > General
2. Features:
   - ✅ Issues
   - ✅ Projects  
   - ✅ Wiki (optional)
   - ✅ Discussions (optional)
3. Pull Requests:
   - ✅ Allow squash merging
   - ✅ Allow rebase merging
   - ✅ Allow auto-merge
   - ✅ Automatically delete head branches

#### Branches
1. Go to Settings > Branches
2. Add branch protection rule:
   - Branch name pattern: `main`
   - ✅ Require pull request reviews before merging
   - ✅ Require status checks to pass
   - ✅ Require branches to be up to date
   - ✅ Include administrators

#### Labels
Create custom labels:
- `bug` (red) - Something isn't working
- `enhancement` (blue) - New feature or request
- `documentation` (yellow) - Documentation improvements
- `good first issue` (green) - Good for newcomers
- `help wanted` (green) - Extra attention needed
- `question` (purple) - Further information requested
- `wontfix` (white) - This will not be worked on
- `security` (red) - Security related
- `wordpress.org` (blue) - WordPress.org specific

### 6. Set Up GitHub Actions (Optional)

Create `.github/workflows/test.yml`:

```yaml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php: ['7.4', '8.0', '8.1', '8.2']
        wordpress: ['5.9', '6.0', '6.1', '6.2', '6.3', '6.4']
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php }}
        
    - name: WordPress Coding Standards
      run: |
        composer global require "wp-coding-standards/wpcs"
        phpcs --standard=WordPress --extensions=php .
```

### 7. Create Releases

#### Create v1.1.0 Release

```bash
# Tag the release
git tag -a v1.1.0 -m "Version 1.1.0 - WordPress.org Compliance"
git push origin v1.1.0
```

On GitHub:
1. Go to Releases > Create new release
2. Choose tag: v1.1.0
3. Release title: Version 1.1.0 - WordPress.org Ready
4. Description:
```markdown
## 🎉 Major Update - WordPress.org Compliance

This is a major rewrite to ensure full compliance with WordPress.org requirements.

### ✨ Highlights
- ✅ All 11 WordPress.org submission issues resolved
- ✅ Comprehensive security improvements
- ✅ No external dependencies
- ✅ Professional code quality

### 📦 Installation
Download `nigeria-bulk-sms-for-woocommerce.zip` and install via WordPress admin.

### 📚 Documentation
See [README.md](README.md) for full documentation.

### 🔒 Security
See [SECURITY.md](SECURITY.md) for security policy.

### Full Changelog
See [CHANGELOG.md](CHANGELOG.md) for detailed changes.
```
5. Attach: nigeria-bulk-sms-for-woocommerce-SUBMISSION-READY.zip
6. Click "Publish release"

### 8. Repository Description & Topics

#### Description
```
Send automated & bulk SMS notifications to WooCommerce customers via Nigeria Bulk SMS API. Features: order notifications, bulk campaigns, customer segmentation, analytics dashboard.
```

#### Topics (tags)
```
wordpress
wordpress-plugin
woocommerce
sms
bulk-sms
nigeria
notifications
e-commerce
php
woocommerce-extension
sms-gateway
order-notifications
customer-engagement
```

#### Website
```
https://wordpress.org/plugins/nigeria-bulk-sms-for-woocommerce/
```
(Add after WordPress.org approval)

### 9. Social Preview Image

Create and upload:
- Size: 1280x640px
- Content: Plugin logo + tagline
- Location: Settings > General > Social Preview

### 10. README Badges

Add to top of README.md:
```markdown
![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/nigeria-bulk-sms-for-woocommerce)
![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/nigeria-bulk-sms-for-woocommerce)
![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/stars/nigeria-bulk-sms-for-woocommerce)
![GitHub issues](https://img.shields.io/github/issues/nueleluwa/nigeria-bulk-sms-for-woocommerce)
![GitHub pull requests](https://img.shields.io/github/issues-pr/nueleluwa/nigeria-bulk-sms-for-woocommerce)
![License](https://img.shields.io/github/license/nueleluwa/nigeria-bulk-sms-for-woocommerce)
```

## 📁 Final Repository Structure

```
nigeria-bulk-sms-for-woocommerce/
├── .github/
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.md
│   │   ├── feature_request.md
│   │   └── question.md
│   ├── workflows/
│   │   └── test.yml (optional)
│   └── PULL_REQUEST_TEMPLATE.md
├── admin/
│   ├── class-nbsms-admin.php
│   ├── css/
│   │   └── admin.css
│   └── js/
│       ├── admin.js
│       └── chart.min.js
├── includes/
│   ├── class-nbsms-api.php
│   ├── class-nbsms-bulk.php
│   ├── class-nbsms-core.php
│   ├── class-nbsms-db.php
│   ├── class-nbsms-logs.php
│   ├── class-nbsms-notifications.php
│   ├── class-nbsms-opt-in.php
│   ├── class-nbsms-settings.php
│   └── class-nbsms-template-parser.php
├── languages/
│   └── nigeria-bulk-sms-for-woocommerce.pot
├── templates/
│   ├── admin-bulk.php
│   ├── admin-logs.php
│   ├── admin-settings.php
│   ├── admin-template-form.php
│   ├── admin-template-view.php
│   ├── admin-templates-list.php
│   └── admin-testing.php
├── .gitignore
├── CHANGELOG.md
├── CODE_OF_CONDUCT.md
├── CONTRIBUTING.md
├── LICENSE
├── README.md
├── SECURITY.md
├── nigeria-bulk-sms-for-woocommerce.php
├── readme.txt
└── uninstall.php
```

## 🚀 Post-Setup Tasks

### After WordPress.org Approval

1. **Update Repository**
   - Add WordPress.org badge
   - Update website URL
   - Add WordPress.org stats badges

2. **Link Repositories**
   - Add GitHub link in readme.txt
   - Add WordPress.org link in README.md

3. **Set Up Sync**
   - Consider GitHub to SVN sync for updates

### Regular Maintenance

1. **Issues**
   - Respond within 48 hours
   - Label appropriately
   - Close resolved issues

2. **Pull Requests**
   - Review within 7 days
   - Provide constructive feedback
   - Merge when ready

3. **Releases**
   - Create release for each version
   - Update CHANGELOG.md
   - Tag properly

4. **Documentation**
   - Keep README.md updated
   - Update CHANGELOG.md
   - Respond to questions

## 📞 Support Channels

After setup, direct users to:

1. **GitHub Issues** - Bug reports & feature requests
2. **WordPress.org Forum** - Support questions
3. **Documentation** - README.md & Wiki
4. **Security** - security@yourdomain.com

## ✅ Verification Checklist

Before going public:

- [ ] All documentation files added
- [ ] Issue templates configured
- [ ] Labels created
- [ ] Branch protection enabled
- [ ] Release v1.1.0 created
- [ ] .gitignore configured
- [ ] LICENSE file present
- [ ] README.md comprehensive
- [ ] CONTRIBUTING.md clear
- [ ] SECURITY.md complete
- [ ] Social preview set
- [ ] Topics added
- [ ] Description set

## 🎉 You're Ready!

Your repository is now professionally set up and ready for:
- Open source contributions
- WordPress.org submission
- Community engagement
- Long-term maintenance

---

**Questions?** Open an issue on GitHub or check CONTRIBUTING.md

**Last Updated:** November 2, 2025
