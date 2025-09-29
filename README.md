# andW Floating Tools

A WordPress plugin that provides floating buttons and table of contents drawer functionality to enhance website usability.

![Version](https://img.shields.io/badge/version-0.1.1-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.3%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPL%20v2%2B-green.svg)

## About This Plugin

This plugin is being prepared for submission to the **WordPress.org Plugin Directory**. It has been developed following WordPress coding standards and security best practices, and has undergone comprehensive plugin audit compliance testing.

## Features Overview

andW Floating Tools provides four floating buttons and table of contents functionality to improve website usability.

### Main Features

- **4 Types of Floating Buttons**
  - Back to Top (appears after 50vh scroll, instant display on upward scroll)
  - Apply/Registration (CTA)
  - Contact (CTA)
  - Table of Contents (anchor sheet display)

- **Responsive Design**
  - Desktop: Right edge, bottom-based, vertical stack (default) or bottom-right inline
  - Mobile/Tablet: Bottom-right inline layout (fixed)
  - Device-specific offset adjustments available

- **Table of Contents**
  - Auto-generated from H2/H3/H4 headings
  - Anchor sheet or right-side drawer UI
  - Focus trap support
  - Scroll offset adjustment (for fixed headers)

- **CTA Buttons**
  - URL, label, and target settings
  - Automatic UTM parameter addition (preserves existing UTM)

- **Accessibility**
  - Focus rings, Esc key closing, ARIA attributes
  - prefers-reduced-motion support

- **Gutenberg Block**
  - Per-post setting overrides
  - Detailed configuration in editor sidebar

## Installation

### From WordPress.org (Coming Soon)
1. Go to WordPress Admin → Plugins → Add New
2. Search for "andW Floating Tools"
3. Install and activate the plugin
4. Go to Settings → Floating Tools to configure

### Manual Installation
1. Upload plugin files to `/wp-content/plugins/andw-floating-tools/` directory
2. Activate the plugin from WordPress admin
3. Go to Settings → Floating Tools to configure

## Quick Start

### Admin Configuration

1. Navigate to **Settings** → **Floating Tools**
2. Select which buttons to enable under **Enabled Buttons**
3. Drag and drop to adjust **Button Order**
4. Choose **Desktop Layout** (vertical stack or horizontal inline)
5. Configure **Display Control** method

### Display Control Options

Choose how floating tools are displayed:

- **Show on All Pages (Global Display)** - Default setting
  - Displays floating tools site-wide (posts, pages, archives)
  - Traditional behavior

- **Show Only on Block-Enabled Pages**
  - Only displays where "Floating Tools Settings" block is placed
  - Perfect for showing tools on specific pages only
  - Requires adding the block to each desired page

### Per-Post Customization

1. Add "Floating Tools Settings" Gutenberg block in post editor
2. Configure settings in the editor sidebar
3. Empty fields will use site default settings

## Customization

### Icon Customization

Button icons can be freely customized using FontAwesome Unicode codes.

#### Admin Configuration

1. Go to WordPress Admin → **Settings** → **Floating Tools**
2. Navigate to **Icon Settings** section
3. Enter Unicode codes in text fields (e.g., `f46c`)
4. Preview icons in real-time
5. Save settings

#### FontAwesome Unicode Input

- **High Flexibility** - Choose from all FontAwesome icons
- **Direct Unicode Input** - 4-6 digit alphanumeric codes (e.g., f46c, f0e0)
- **Real-time Preview** - Instant icon display confirmation
- **Reliable Display** - FontAwesome 6 compatible for stable rendering

#### Popular Icon Unicode Codes

**Apply/Registration Button:**
- `f07a` - 🛒 Shopping Cart
- `f290` - 🛍️ Shopping Bag
- `f09d` - 💳 Credit Card
- `f46c` - 📋 Clipboard Check

**Contact Button:**
- `f0e0` - ✉️ Envelope
- `f086` - 💬 Comment
- `f095` - 📞 Phone
- `f590` - 🎧 Headset

**Table of Contents Button:**
- `f03a` - 📋 List
- `f0ca` - 📝 List UL
- `f0c9` - ☰ Bars
- `f518` - 📖 Book Open

**Back to Top Button:**
- `f062` - ⬆️ Arrow Up
- `f077` - ⬆️ Chevron Up
- `f135` - 🚀 Rocket

#### How to Find Icons

1. Search icons on [FontAwesome official site](https://fontawesome.com/search?o=r&m=free)
2. Click on your preferred icon
3. Copy the **Unicode** code (e.g., `f46c`)
4. Paste into admin text field

#### Configuration Example

```
Apply Button: f07a (Shopping Cart)
Contact Button: f0e0 (Envelope)
TOC Button: f03a (List)
Top Button: f062 (Arrow Up)
```

### Color Customization

Button colors are defined in `assets/css/app.css`:

```css
/* Apply Button (Current: Green) */
.andw-button-apply {
    background: #059669;
}
.andw-button-apply:hover {
    background: #047857;
}

/* Contact Button (Current: Red) */
.andw-button-contact {
    background: #dc2626;
}
.andw-button-contact:hover {
    background: #b91c1c;
}

/* TOC Button (Current: Purple) */
.andw-button-toc {
    background: #7c3aed;
}
.andw-button-toc:hover {
    background: #6d28d9;
}

/* Top Button (Current: Gray) */
.andw-button-top {
    background: #374151;
}
.andw-button-top:hover {
    background: #1f2937;
}
```

## Technical Specifications

- **WordPress**: 6.3 or higher
- **PHP**: 7.4 or higher
- **JavaScript**: Vanilla JS (no jQuery dependency)
- **Performance**: Lightweight design (1 JS + 1 CSS file)
- **Security**: Nonce verification, input sanitization, output escaping
- **Internationalization**: i18n ready
- **FontAwesome**: 6.5.1 bundled (auto-loading with conflict prevention)

## File Structure

```
andw-floating-tools/
├── andw-floating-tools.php          # Main plugin file
├── includes/
│   ├── settings.php                 # Admin settings page
│   ├── render.php                   # Frontend rendering
│   ├── toc.php                      # Table of contents generation
│   ├── sanitization.php             # Input sanitization
│   ├── fontawesome-handler.php      # FontAwesome management
│   ├── fontawesome-icons.php        # Icon definitions
│   └── icon-helper.php              # Legacy icon support
├── assets/
│   ├── js/app.js                    # Frontend JavaScript
│   ├── css/app.css                  # Stylesheets
│   └── vendor/fontawesome/          # Bundled FontAwesome files
├── blocks/
│   └── toc/                         # Gutenberg block
└── languages/                       # Internationalization files
```

## Developer Information

### Plugin Hooks & Filters

The plugin provides WordPress hooks for customization:

```php
// Customize frontend options
add_filter('andw_floating_tools_options', function($options) {
    // Modify $options
    return $options;
});

// Customize TOC generation
add_filter('andw_floating_tools_toc_headings', function($headings) {
    // Modify $headings
    return $headings;
});
```

### Debug Mode

Enable WordPress `WP_DEBUG` for detailed logging and debug information.

## License

This plugin is licensed under GPLv2 or later.

## Support & Contributing

- **Issues & Bug Reports**: Please report issues via GitHub Issues (coming soon)
- **Technical Support**: Contact [Netservice](https://netservice.jp/)
- **WordPress.org Support**: Available after plugin directory submission

---

## 日本語ドキュメント (Japanese Documentation)

### 概要

andW Floating Toolsは、Webサイトのユーザビリティを向上させる4つのフローティングボタンとTOC機能を提供するWordPressプラグインです。WordPress.org プラグインディレクトリへの提出を予定しており、WordPress コーディング規約とセキュリティベストプラクティスに従って開発されています。

### 主な機能

- **4種類のフローティングボタン**: ページトップへ、お申し込み（CTA）、お問い合わせ（CTA）、目次
- **レスポンシブ対応**: デスクトップは縦積み/横並び選択可、モバイル・タブレットは横並び固定
- **目次機能**: H2/H3/H4見出しから自動生成、アンカーシート表示、フォーカストラップ対応
- **アクセシビリティ**: ARIA属性、prefers-reduced-motion対応
- **Gutenbergブロック**: 投稿単位での設定上書き
- **アイコンカスタマイズ**: FontAwesome Unicodeコード直接入力方式

### インストール・設定

1. プラグインを有効化
2. 「設定」→「Floating Tools」で基本設定
3. 必要に応じて投稿編集画面で「Floating Tools Settings」ブロックを追加して個別設定

### 技術仕様

- WordPress 6.3以上、PHP 7.4以上
- Vanilla JavaScript（jQuery不使用）
- セキュリティ対応（nonce検証、エスケープ処理）
- FontAwesome 6.5.1同梱