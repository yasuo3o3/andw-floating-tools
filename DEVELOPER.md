# Developer Guide

This document provides development guidelines, testing procedures, and deployment instructions for andW Floating Tools WordPress plugin.

## Development Environment Setup

### Prerequisites

- **WordPress**: 6.3 or higher
- **PHP**: 7.4 or higher
- **Node.js**: 16+ (for development tools)
- **Composer**: For PHP dependencies (optional)

### Required Development Tools

```bash
# Install WordPress Plugin Check
wp plugin install plugin-check --activate

# Install PHP_CodeSniffer with WordPress standards
composer global require "squizlabs/php_codesniffer=*"
composer global require wp-coding-standards/wpcs
phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs

# Install additional quality tools
composer global require phpmd/phpmd
composer global require vimeo/psalm
```

## Code Quality & Standards

### PHP_CodeSniffer (PHPCS) Configuration

Create `.phpcs.xml.dist` in project root:

```xml
<?xml version="1.0"?>
<ruleset name="andW Floating Tools">
    <description>WordPress Coding Standards for andW Floating Tools</description>

    <file>.</file>

    <!-- Exclude vendor and build directories -->
    <exclude-pattern>*/vendor/*</exclude-pattern>
    <exclude-pattern>*/node_modules/*</exclude-pattern>
    <exclude-pattern>*/assets/vendor/*</exclude-pattern>

    <!-- WordPress Coding Standards -->
    <rule ref="WordPress">
        <!-- Allow short array syntax -->
        <exclude name="Generic.Arrays.DisallowShortArraySyntax"/>
        <!-- Allow multiple assignments -->
        <exclude name="Squiz.PHP.DisallowMultipleAssignments"/>
    </rule>

    <!-- WordPress Security Rules -->
    <rule ref="WordPress.Security"/>

    <!-- Minimum PHP version -->
    <config name="minimum_supported_wp_version" value="6.3"/>
    <config name="testVersion" value="7.4-"/>
</ruleset>
```

### Running Code Quality Checks

#### PHPCS (WordPress Coding Standards)
```bash
# Check all PHP files
phpcs --standard=WordPress .

# Check specific file
phpcs --standard=WordPress includes/settings.php

# Auto-fix fixable issues
phpcbf --standard=WordPress .

# Generate detailed report
phpcs --standard=WordPress --report=full --report-file=phpcs-report.txt .
```

#### WordPress Plugin Check
```bash
# Run comprehensive plugin audit
wp plugin-check run andw-floating-tools

# Check specific rules
wp plugin-check run andw-floating-tools --checks=plugin_repo

# Generate JSON report
wp plugin-check run andw-floating-tools --format=json > plugin-check-report.json
```

#### Additional Quality Tools
```bash
# PHP Mess Detector
phpmd . text codesize,unusedcode,naming

# Static Analysis (if Psalm is configured)
psalm --show-info=true
```

## Testing Procedures

### Manual Testing Checklist

#### Core Functionality
- [ ] Floating buttons display correctly on frontend
- [ ] Table of Contents generates from H2/H3/H4 headings
- [ ] Admin settings save and load properly
- [ ] Gutenberg block works in editor
- [ ] Icon customization functions correctly
- [ ] Responsive design works on all devices

#### Security Testing
- [ ] Nonce verification works on all forms
- [ ] Input sanitization prevents XSS
- [ ] Output escaping prevents code injection
- [ ] Admin capabilities are properly checked
- [ ] No sensitive data exposed in HTML/JS

#### Compatibility Testing
- [ ] Works with latest WordPress version
- [ ] Compatible with popular themes
- [ ] No JavaScript errors in console
- [ ] No PHP errors/warnings in debug log
- [ ] FontAwesome loads without conflicts

#### Accessibility Testing
- [ ] Keyboard navigation works
- [ ] Screen reader compatibility
- [ ] Focus indicators visible
- [ ] ARIA attributes properly set
- [ ] Color contrast meets WCAG standards

### Automated Testing Setup (Future Enhancement)

```bash
# Install PHPUnit for WordPress
composer require --dev phpunit/phpunit
composer require --dev yoast/phpunit-polyfills

# Install WordPress Test Suite
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

# Run tests
vendor/bin/phpunit
```

## Build & Deployment

### Pre-Release Checklist

1. **Code Quality**
   ```bash
   phpcs --standard=WordPress .
   wp plugin-check run andw-floating-tools
   ```

2. **Version Updates**
   ```bash
   # Update version numbers in:
   # - andw-floating-tools.php (Plugin header + ANDW_FLOATING_TOOLS_VERSION)
   # - readme.txt (Stable tag)
   # - blocks/toc/block.json
   # - CHANGELOG.txt (new entry)
   ```

3. **Security Review**
   - Verify all nonce implementations
   - Check input sanitization
   - Confirm output escaping
   - Review file permissions

4. **Testing**
   - Manual testing on fresh WordPress install
   - Test with common themes/plugins
   - Verify mobile responsiveness
   - Check accessibility compliance

### Release Process

#### Local Build
```bash
# 1. Ensure clean working directory
git status

# 2. Run quality checks
phpcs --standard=WordPress .
wp plugin-check run andw-floating-tools

# 3. Create release archive
zip -r andw-floating-tools-0.1.1.zip andw-floating-tools/ -x "*.git*" "node_modules/*" "*.md" "*.json" "*.xml"
```

#### WordPress.org Submission
```bash
# 1. Create SVN-ready structure
mkdir wp-org-submission
cp -r . wp-org-submission/trunk/
mkdir wp-org-submission/tags/0.1.1
cp -r . wp-org-submission/tags/0.1.1/

# 2. Clean unnecessary files
rm -rf wp-org-submission/trunk/.git
rm -rf wp-org-submission/tags/0.1.1/.git
# Remove development files: package.json, composer.json, etc.
```

### Git Workflow

```bash
# Feature development
git checkout -b feature/new-functionality
git add .
git commit -m "新機能を追加"

# Release preparation
git checkout main
git merge feature/new-functionality
git tag -a v0.1.1 -m "バージョン0.1.1リリース - レビューと修正対応"
git push origin main
git push origin v0.1.1
```

## CI/CD Implementation (Future Enhancement)

### GitHub Actions Configuration

Create `.github/workflows/quality-check.yml`:

```yaml
name: Quality Check

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  phpcs:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'
        tools: composer, phpcs

    - name: Install WPCS
      run: |
        composer global require wp-coding-standards/wpcs
        phpcs --config-set installed_paths ~/.composer/vendor/wp-coding-standards/wpcs

    - name: Run PHPCS
      run: phpcs --standard=WordPress .

  plugin-check:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3

    - name: Setup WordPress CLI
      run: |
        curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
        chmod +x wp-cli.phar
        sudo mv wp-cli.phar /usr/local/bin/wp

    - name: Install Plugin Check
      run: wp plugin install plugin-check --activate --allow-root

    - name: Run Plugin Check
      run: wp plugin-check run andw-floating-tools --allow-root
```

### Deployment Pipeline Goals

- **Automated Testing**: PHPUnit, PHPCS, Plugin Check
- **Security Scanning**: Vulnerability detection
- **Performance Testing**: Load time analysis
- **Compatibility Testing**: Multi-version WordPress testing
- **Automated Deployment**: WordPress.org SVN integration

## Development Best Practices

### Security Guidelines

1. **Always use nonces** for form submissions
2. **Sanitize all inputs** using WordPress functions
3. **Escape all outputs** with appropriate functions
4. **Check user capabilities** before sensitive operations
5. **Validate file uploads** and restrict file types

### Performance Guidelines

1. **Minimize HTTP requests** (combine CSS/JS files)
2. **Use WordPress caching** where appropriate
3. **Optimize database queries** (avoid loops in loops)
4. **Lazy load resources** when possible
5. **Profile with Query Monitor** plugin

### Code Style Guidelines

1. **Follow WordPress Coding Standards** strictly
2. **Use meaningful variable names** in English
3. **Add inline documentation** for complex logic
4. **Keep functions small** and single-purpose
5. **Use WordPress hooks** instead of direct modifications

## Troubleshooting

### Common Development Issues

1. **Plugin Check Failures**: Run `wp plugin-check run andw-floating-tools` for detailed errors
2. **PHPCS Violations**: Use `phpcbf` to auto-fix many issues
3. **JavaScript Errors**: Check browser console and use WordPress debug mode
4. **PHP Errors**: Enable `WP_DEBUG` and check error logs
5. **Permission Issues**: Verify file permissions and user capabilities

### Debug Mode Setup

Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

## Contact & Support

- **Developer**: Netservice (https://netservice.jp/)
- **Repository**: (Private during development)
- **WordPress.org**: (Pending submission)

---

**Last Updated**: 2025-01-29
**Plugin Version**: 0.1.1
**WordPress Tested**: 6.8