# andW Floating Tools

右下フローティング群とTOCドロワーを提供するWordPressプラグイン。

## 機能概要

andW Floating Toolsは、Webサイトのユーザビリティを向上させる4つのフローティングボタンとTOC機能を提供します。

### 主な機能

- **4種類のボタン**
  - ページトップへ（50vh超で表示、上スクロールで即表示）
  - お申し込み（CTA）
  - お問い合わせ（CTA）
  - 目次（TOCアンカーシート表示）

- **レスポンシブ対応**
  - Desktop: 右端・高さ中央・縦積み（デフォルト）または右下・横並び
  - Mobile/Tablet: 右下・横並び固定
  - 端末別オフセット調整可能

- **目次機能**
  - H2/H3/H4の見出しから自動生成
  - アンカーシートまたは右側ドロワーUI
  - フォーカストラップ対応
  - スクロールオフセット調整可能（固定ヘッダー対応）

- **CTAボタン**
  - URL・ラベル・ターゲット設定
  - UTM自動付与機能（既存UTMは保護）

- **アクセシビリティ**
  - フォーカスリング、Esc閉じ、ARIA属性
  - prefers-reduced-motion対応

- **Gutenbergブロック**
  - 投稿単位での設定上書き
  - エディタサイドバーで詳細設定

## インストール

1. プラグインファイルを `/wp-content/plugins/andw-floating-tools/` ディレクトリにアップロード
2. WordPress管理画面でプラグインを有効化
3. 「設定」→「Floating Tools」で設定を行う

## 基本設定

### 管理画面での設定

1. 「設定」→「Floating Tools」にアクセス
2. 「有効なボタン」で表示するボタンを選択
3. 「ボタンの並び順」をドラッグ&ドロップで調整
4. 「デスクトップレイアウト」を選択

### 投稿単位の設定上書き

1. 投稿編集画面でGutenbergブロック「Floating Tools Settings」を追加
2. サイドバーで詳細設定を行う
3. 空の項目はサイト既定設定を使用

## カスタマイズ方法

### ボタンのアイコンを変更する

ボタンのアイコンは管理画面から簡単に変更できます。

#### 管理画面での設定

1. WordPress管理画面 → **設定** → **Floating Tools**
2. **アイコン設定** セクション
3. 各ボタンの **SVGパス** フィールドに好きなアイコンを貼り付け
4. 保存

#### おすすめアイコンサイト

- **[Heroicons](https://heroicons.com/)** - 無料、シンプル、綺麗
- **[Feather Icons](https://feathericons.com/)** - 軽量で統一感がある
- **[Tabler Icons](https://tabler-icons.io/)** - 大量の無料アイコン
- **[Lucide](https://lucide.dev/)** - 高品質

#### 使用方法

**2つの方法で貼り付け可能：**

1. **SVGコード全体をそのまま貼り付け（推奨）**
```xml
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 11-1 9"/><path d="m19 11-4-7"/><path d="M2 11h20"/></svg>
```

2. **SVGタグの中身だけ**
```xml
<path d="m15 11-1 9"/><path d="m19 11-4-7"/><path d="M2 11h20"/>
```

どちらでも自動で正しく処理されます。

### ボタンの色を変更する

ボタンの色は `assets/css/app.css` で定義されています。

```css
/* assets/css/app.css 101-131行目付近 */

/* お申し込みボタン（現在: 緑） */
.andw-button-apply {
    background: #059669;
}
.andw-button-apply:hover {
    background: #047857;
}

/* お問い合わせボタン（現在: 赤） */
.andw-button-contact {
    background: #dc2626;
}
.andw-button-contact:hover {
    background: #b91c1c;
}

/* 目次ボタン（現在: 紫） */
.andw-button-toc {
    background: #7c3aed;
}
.andw-button-toc:hover {
    background: #6d28d9;
}

/* ページトップボタン（現在: グレー） */
.andw-button-top {
    background: #374151;
}
.andw-button-top:hover {
    background: #1f2937;
}
```

#### 色の変更例

```css
/* オレンジに変更 */
.andw-button-apply {
    background: #ff6b35;
}
.andw-button-apply:hover {
    background: #e55a2b;
}

/* ブルーに変更 */
.andw-button-contact {
    background: #3b82f6;
}
.andw-button-contact:hover {
    background: #2563eb;
}
```

### プリセットスタイル

ライトテーマやダークテーマ、サイズバリエーションも用意されています：

```css
/* ライトプリセット（473行目付近） */
.andw-preset-light .andw-button-apply {
    background: white;
    color: #059669;
    border-color: #059669;
}

/* ダークプリセット（504行目付近） */
.andw-preset-dark .andw-floating-button {
    background: #111827;
    color: white;
    border: 1px solid #374151;
}

/* サイズバリエーション（516行目付近） */
.andw-preset-square-small .andw-floating-button {
    width: 44px;
    height: 44px;
}
```

## 技術仕様

- WordPress 6.3以上
- PHP 7.4以上
- jQuery不使用（Vanilla JavaScript）
- 軽量設計（1JS+1CSS）
- セキュリティ対応（nonce、sanitize/escape）
- i18n対応

## ファイル構成

```
andw-floating-tools/
├── andw-floating-tools.php          # メインプラグインファイル
├── includes/
│   ├── settings.php                 # 設定画面
│   ├── render.php                   # フロントエンド出力
│   ├── toc.php                      # TOC生成機能
│   └── sanitization.php             # サニタイゼーション
├── assets/
│   ├── js/app.js                    # フロントエンドJS
│   └── css/app.css                  # スタイルシート
├── blocks/
│   └── toc/                         # Gutenbergブロック
└── languages/                       # 国際化ファイル
```

## 開発

### デバッグモード

WordPressの `WP_DEBUG` を有効にすると、詳細なログが出力されます。

### フックとフィルター

プラグインでは以下のWordPressフックを提供しています：

```php
// フロントエンド出力前にカスタマイズ
add_filter('andw_floating_tools_options', function($options) {
    // $optionsを変更
    return $options;
});

// TOC生成をカスタマイズ
add_filter('andw_floating_tools_toc_headings', function($headings) {
    // $headingsを変更
    return $headings;
});
```

## ライセンス

このプラグインはGPLv2またはそれ以降のバージョンでライセンスされています。

## サポート

技術的な質問やバグ報告は、[Netservice](https://netservice.jp/) までお問い合わせください。