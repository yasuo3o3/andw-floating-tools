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
  - Desktop: 右端・下基準・縦積み（デフォルト）または右下・横並び
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
5. 「表示制御」でフローティングツールの表示方法を選択

### 表示制御について

フローティングツールの表示方法を2つから選択できます：

- **すべてのページに表示（グローバル表示）** - デフォルト設定
  - 投稿、固定ページ、アーカイブページなど、サイト全体でフローティングツールを表示
  - 従来通りの動作

- **ブロック設置ページのみ表示**
  - 「Floating Tools Settings」ブロックが配置されているページでのみ表示
  - 特定のページにだけフローティングツールを表示したい場合に最適
  - 投稿編集画面でブロックを追加した場合にのみ、そのページでフローティングツールが表示される

### 投稿単位の設定上書き

1. 投稿編集画面でGutenbergブロック「Floating Tools Settings」を追加
2. サイドバーで詳細設定を行う
3. 空の項目はサイト既定設定を使用

## カスタマイズ方法

### ボタンのアイコンを変更する

ボタンのアイコンはFontAwesome のUnicodeコードを直接入力する方式で、自由にカスタマイズできます。

#### 管理画面での設定

1. WordPress管理画面 → **設定** → **Floating Tools**
2. **アイコン設定** セクション
3. 各ボタンのテキストフィールドにUnicodeコードを入力（例: `f46c`）
4. リアルタイムプレビューで確認
5. 保存

#### FontAwesome Unicode 入力について

- **自由度の高い選択** - FontAwesome 全アイコンから自由に選択可能
- **Unicode直接入力** - 4-6桁の英数字コード（例: f46c, f0e0）
- **リアルタイムプレビュー** - 入力と同時にアイコン表示確認
- **確実な表示** - FontAwesome 6 対応で安定表示

#### よく使われるアイコンのUnicodeコード

**お申し込みボタン:**
- `f07a` - 🛒 ショッピングカート
- `f290` - 🛍️ ショッピングバッグ
- `f09d` - 💳 クレジットカード
- `f46c` - 📋 クリップボードチェック

**お問い合わせボタン:**
- `f0e0` - ✉️ メール
- `f086` - 💬 コメント
- `f095` - 📞 電話
- `f590` - 🎧 ヘッドセット

**目次ボタン:**
- `f03a` - 📋 リスト
- `f0ca` - 📝 箇条書き
- `f0c9` - ☰ ハンバーガーメニュー
- `f518` - 📖 本を開く

**ページトップボタン:**
- `f062` - ⬆️ 上矢印
- `f077` - ⬆️ シェブロン上
- `f135` - 🚀 ロケット

#### アイコンの探し方

1. [FontAwesome 公式サイト](https://fontawesome.com/search?o=r&m=free) でアイコンを検索
2. 気に入ったアイコンをクリック
3. **Unicode** 欄に表示されるコード（例: `f46c`）をコピー
4. 管理画面のテキストフィールドに貼り付け

#### 設定例

```
お申し込みボタン: f07a （ショッピングカート）
お問い合わせボタン: f0e0 （メール）
目次ボタン: f03a （リスト）
ページトップボタン: f062 （上矢印）
```

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
- FontAwesome 6.5.1 対応（自動読み込み・競合回避）

## ファイル構成

```
andw-floating-tools/
├── andw-floating-tools.php          # メインプラグインファイル
├── includes/
│   ├── settings.php                 # 設定画面
│   ├── render.php                   # フロントエンド出力
│   ├── toc.php                      # TOC生成機能
│   ├── sanitization.php             # サニタイゼーション
│   ├── fontawesome-handler.php      # FontAwesome管理
│   ├── fontawesome-icons.php        # アイコン定義
│   └── icon-helper.php              # レガシーアイコン
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