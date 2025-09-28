=== andW Floating Tools ===
Contributors: netservice
Tags: floating, tools, toc, table-of-contents, cta
Requires at least: 6.3
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 0.01
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

右下フローティング群とTOCドロワーを提供するプラグイン。

== Description ==

andW Floating Toolsは、Webサイトのユーザビリティを向上させる4つのフローティングボタンとTOCドロワー機能を提供します。

= 主な機能 =

* **4種類のボタン**
  * ページトップへ（50vh超で表示、上スクロールで即表示）
  * お申し込み（CTA）
  * お問い合わせ（CTA）
  * 目次（TOCドロワー表示）

* **レスポンシブ対応**
  * Desktop: 右端・高さ中央・縦積み（デフォルト）または右下・横並び
  * Mobile/Tablet: 右下・横並び固定
  * 端末別オフセット調整可能

* **目次機能**
  * H2/H3/H4の見出しから自動生成
  * 既存IDを優先、無い場合はスラッグ生成
  * 右側ドロワーUI、フォーカストラップ対応
  * スクロールオフセット調整可能（固定ヘッダー対応）

* **CTAボタン**
  * URL・ラベル・ターゲット設定
  * UTM自動付与機能（既存UTMは保護）

* **アクセシビリティ**
  * フォーカスリング、Esc閉じ、ARIA属性
  * prefers-reduced-motion対応

* **Gutenbergブロック**
  * 投稿単位での設定上書き
  * エディタサイドバーで詳細設定

= デザインプリセット =

* 形状: 丸型・角型
* サイズ: S/M/L
* テーマ: ライト・ダーク
* z-index調整可能

== Installation ==

1. プラグインファイルを `/wp-content/plugins/andw-floating-tools/` ディレクトリにアップロード
2. WordPress管理画面でプラグインを有効化
3. 「設定」→「Floating Tools」で設定を行う

== Frequently Asked Questions ==

= ボタンが表示されません =

以下をご確認ください：
* 管理画面で該当ボタンが有効になっているか
* CTAボタンの場合、URLが設定されているか
* TOCボタンの場合、ページに見出し（H2-H4）があるか

= 目次が生成されません =

* ページにH2以上の見出しがあることを確認してください
* 管理画面またはブロック設定で目次の深さ設定を確認してください

= モバイルでレイアウトが崩れます =

* Mobile/Tabletは常に右下・横並びレイアウトです
* オフセット設定で位置を調整してください

= UTMパラメータが重複しています =

* 本プラグインは既存のUTMパラメータがある場合は追加しません
* URLに既にUTMが含まれている場合は、UTM機能をOFFにしてください

== Screenshots ==

1. フローティングボタン（デスクトップ・縦積み）
2. フローティングボタン（モバイル・横並び）
3. TOCドロワー
4. 管理画面設定
5. Gutenbergブロック設定

== Changelog ==

= 0.01 =
* 初回リリース
* 4種類のフローティングボタン実装
* TOCドロワー機能
* レスポンシブ対応
* Gutenbergブロック
* アクセシビリティ対応

== Upgrade Notice ==

= 0.01 =
初回リリースです。

== 設定方法 ==

= 基本設定 =

1. 「設定」→「Floating Tools」にアクセス
2. 「有効なボタン」で表示するボタンを選択
3. 「ボタンの並び順」をドラッグ&ドロップで調整
4. 「デスクトップレイアウト」を選択

= 位置調整 =

* オフセット設定でDesktop/Mobile/Tabletごとに位置を調整
* 数値はピクセル単位（0-999）

= TOC設定 =

* 既定の深さ: H2/H2-H3/H2-H4から選択
* スクロールオフセット: 固定ヘッダーの高さを設定

= CTAボタン設定 =

* お申し込み・お問い合わせボタンのURL・ラベル・ターゲットを設定
* UTM自動付与をONにすると指定したパラメータを自動追加

= 投稿単位の設定上書き =

1. 投稿編集画面でGutenbergブロック「Floating Tools Settings」を追加
2. サイドバーで詳細設定を行う
3. 空の項目はサイト既定設定を使用

= アイコンのカスタマイズ =

各ボタンのアイコンは管理画面から簡単に変更できます。

**設定方法:**
1. 管理画面「設定」→「Floating Tools」
2. 「アイコン設定」セクション
3. SVGコードを貼り付けて保存

**おすすめアイコンサイト:**
* Heroicons (https://heroicons.com/)
* Feather Icons (https://feathericons.com/)
* Tabler Icons (https://tabler-icons.io/)
* Lucide (https://lucide.dev/)

**使用方法:**
SVGコード全体またはタグの中身だけ、どちらでも貼り付けできます。
自動で適切な属性に変換されて表示されます。

== 技術仕様 ==

* WordPress 6.3以上
* PHP 7.4以上
* jQuery不使用（Vanilla JavaScript）
* 軽量設計（1JS+1CSS、gzip圧縮時10KB以下目標）
* セキュリティ対応（nonce、sanitize/escape）
* i18n対応

== ライセンス ==

このプラグインはGPLv2またはそれ以降のバージョンでライセンスされています。