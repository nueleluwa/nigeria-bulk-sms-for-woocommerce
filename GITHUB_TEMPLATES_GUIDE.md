# GitHub Issue Templates

Create these files in `.github/ISSUE_TEMPLATE/` directory:

## 1. Bug Report Template

**File:** `.github/ISSUE_TEMPLATE/bug_report.md`

```markdown
---
name: Bug Report
about: Create a report to help us improve
title: '[BUG] '
labels: bug
assignees: ''
---

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

**Environment (please complete the following information):**
- WordPress Version: [e.g. 6.4]
- WooCommerce Version: [e.g. 8.5]
- Plugin Version: [e.g. 1.1.0]
- PHP Version: [e.g. 8.1]
- Server: [e.g. Apache, Nginx]
- Browser: [e.g. Chrome 120]
- HPOS Enabled: [Yes/No]

**Error Messages**
Please include any error messages from:
- WordPress debug.log
- Browser console
- Plugin logs (Nigeria Bulk SMS > Logs)

**Additional context**
Add any other context about the problem here.
```

## 2. Feature Request Template

**File:** `.github/ISSUE_TEMPLATE/feature_request.md`

```markdown
---
name: Feature Request
about: Suggest an idea for this project
title: '[FEATURE] '
labels: enhancement
assignees: ''
---

**Is your feature request related to a problem? Please describe.**
A clear and concise description of what the problem is. Ex. I'm always frustrated when [...]

**Describe the solution you'd like**
A clear and concise description of what you want to happen.

**Describe alternatives you've considered**
A clear and concise description of any alternative solutions or features you've considered.

**Would you be willing to contribute this feature?**
- [ ] Yes, I can submit a pull request
- [ ] No, but I can help test it
- [ ] No, just suggesting

**Use case**
Describe who would benefit from this feature and how they would use it.

**Additional context**
Add any other context or screenshots about the feature request here.
```

## 3. Question Template

**File:** `.github/ISSUE_TEMPLATE/question.md`

```markdown
---
name: Question
about: Ask a question about the plugin
title: '[QUESTION] '
labels: question
assignees: ''
---

**Your Question**
Please describe your question clearly.

**What I've tried**
What have you already tried or looked at?
- [ ] I've read the README.md
- [ ] I've searched existing issues
- [ ] I've checked the WordPress.org support forum

**Context**
Any additional information that might help answer your question.

**Environment (if relevant):**
- WordPress Version: [e.g. 6.4]
- WooCommerce Version: [e.g. 8.5]
- Plugin Version: [e.g. 1.1.0]
```

## 4. Pull Request Template

**File:** `.github/PULL_REQUEST_TEMPLATE.md`

```markdown
## Description
Brief description of the changes

## Type of Change
- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to change)
- [ ] Documentation update
- [ ] Code refactoring
- [ ] Performance improvement

## Related Issue
Closes #(issue number)

## Motivation and Context
Why is this change required? What problem does it solve?

## How Has This Been Tested?
- [ ] Tested on WordPress 5.0+
- [ ] Tested on WooCommerce 5.0+
- [ ] Tested with WooCommerce HPOS enabled
- [ ] Tested with WooCommerce HPOS disabled
- [ ] Tested on PHP 7.4
- [ ] Tested on PHP 8.0+
- [ ] No PHP errors or warnings
- [ ] No JavaScript errors in console
- [ ] All existing features still work

## Screenshots (if applicable)
Add screenshots here

## Checklist
- [ ] My code follows the WordPress coding standards
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have made corresponding changes to the documentation
- [ ] My changes generate no new warnings
- [ ] I have added PHPDoc comments to new functions
- [ ] I have tested this on a clean WordPress + WooCommerce installation
- [ ] I have updated the CHANGELOG.md (if applicable)

## Additional Notes
Any additional information or context
```

---

## How to Create These Templates

1. Create `.github/ISSUE_TEMPLATE/` directory in your repository
2. Create each file listed above
3. Copy the markdown content into each file
4. Commit and push to GitHub
5. GitHub will automatically use these templates

## Example Directory Structure

```
.github/
├── ISSUE_TEMPLATE/
│   ├── bug_report.md
│   ├── feature_request.md
│   └── question.md
└── PULL_REQUEST_TEMPLATE.md
```

## Benefits

- Standardized issue reporting
- All necessary information collected upfront
- Easier triage and response
- Better collaboration
- Faster resolution of issues
