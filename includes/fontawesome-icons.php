<?php
if (!defined('ABSPATH')) {
    exit;
}

class Andw_FontAwesome_Icons {
    /**
     * デフォルトアイコン設定（Unicode）
     */
    public static function get_default_icons() {
        return array(
            'apply' => 'f07a',     // shopping-cart
            'contact' => 'f0e0',   // envelope
            'toc' => 'f03a',       // list
            'top' => 'f062',       // arrow-up
        );
    }

    /**
     * よく使われるアイコンのサンプル（ユーザー参考用）
     */
    public static function get_popular_icons() {
        return array(
            // 申し込み・ショッピング関連
            'f07a' => 'ショッピングカート',
            'f290' => 'ショッピングバッグ',
            'f09d' => 'クレジットカード',
            'f46c' => 'クリップボードチェック',
            'f56c' => '契約書',
            'f2b5' => '握手',

            // お問い合わせ関連
            'f0e0' => 'メール',
            'f086' => 'コメント',
            'f095' => '電話',
            'f590' => 'ヘッドセット',
            'f059' => '質問',

            // 目次関連
            'f03a' => 'リスト',
            'f0ca' => '箇条書き',
            'f0c9' => 'ハンバーガーメニュー',
            'f518' => '本を開く',
            'f02e' => 'ブックマーク',

            // ページトップ関連
            'f062' => '上矢印',
            'f077' => 'シェブロン上',
            'f106' => 'アングル上',
            'f0aa' => '円矢印上',
            'f135' => 'ロケット',

            // その他汎用
            'f015' => 'ホーム',
            'f005' => '星',
            'f004' => 'ハート',
            'f05a' => '情報',
            'f013' => '設定',
        );
    }

    /**
     * FontAwesome Unicode からアイコンHTMLを生成
     */
    public static function get_icon_html($unicode_code, $additional_classes = '') {
        // Unicode コードをサニタイズ
        $unicode_code = self::sanitize_unicode($unicode_code);

        if (empty($unicode_code)) {
            // フォールバック：デフォルトアイコン
            $unicode_code = 'f059'; // question-circle
        }

        // FontAwesome 6 の新しいクラス形式を使用
        $icon_class = 'fa-solid fa-fw';
        if (!empty($additional_classes)) {
            $icon_class .= ' ' . $additional_classes;
        }

        // Unicode を使用したアイコン表示（複数のフォントファミリーでフォールバック）
        return '<i class="' . esc_attr($icon_class) . '" style="font-family: \'Font Awesome 6 Free\', \'FontAwesome\', \'Font Awesome 5 Free\', sans-serif; font-weight: 900;">&#x' . $unicode_code . ';</i>';
    }

    /**
     * ボタンタイプごとのアイコン取得
     */
    public static function get_button_icon($button_type, $custom_unicode = '') {
        // カスタムUnicodeが設定されている場合
        if (!empty($custom_unicode)) {
            return self::get_icon_html($custom_unicode);
        }

        // デフォルトUnicode
        $defaults = self::get_default_icons();
        $unicode = isset($defaults[$button_type]) ? $defaults[$button_type] : 'f059';

        return self::get_icon_html($unicode);
    }

    /**
     * Unicode コードのサニタイゼーション
     */
    private static function sanitize_unicode($unicode) {
        // 不要な文字を除去（英数字のみ許可）
        $unicode = preg_replace('/[^a-fA-F0-9]/', '', $unicode);

        // 長さ制限（FontAwesome は通常4文字）
        if (strlen($unicode) > 6) {
            $unicode = substr($unicode, 0, 6);
        }

        return strtolower($unicode);
    }

    /**
     * 管理画面用の説明テキスト
     */
    public static function get_admin_help_text() {
        $popular = self::get_popular_icons();
        $help_text = '<p><strong>FontAwesome Unicode 入力:</strong></p>';
        $help_text .= '<p>4-6桁の英数字コードを入力してください（例: f46c, f0e0）<br>';
        $help_text .= '空欄の場合はデフォルトアイコンが使用されます。</p>';

        $help_text .= '<p><strong>よく使われるアイコン（クリックでコピー）:</strong></p>';
        $help_text .= '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">';

        foreach ($popular as $code => $name) {
            $help_text .= '<span style="display: inline-block; margin: 2px 4px 2px 0; white-space: nowrap; cursor: pointer; padding: 2px 4px; border: 1px solid #ccc; border-radius: 3px; background: white;" ';
            $help_text .= 'onclick="navigator.clipboard.writeText(\'' . $code . '\'); this.style.background=\'#90EE90\'; setTimeout(() => this.style.background=\'white\', 1000);" ';
            $help_text .= 'title="クリックでコピー: ' . $code . '">';
            $help_text .= '<code>' . $code . '</code> ' . $name;
            $help_text .= '</span>';
        }

        $help_text .= '</div>';
        $help_text .= '<p><small>💡 上記のコードをクリックすると自動でコピーされます</small></p>';
        $help_text .= '<p><a href="https://fontawesome.com/search?o=r&m=free" target="_blank" class="button">🔍 FontAwesome公式サイトでもっと探す</a></p>';

        return $help_text;
    }

    /**
     * 管理画面用プレビュー
     */
    public static function get_preview_html($unicode) {
        if (empty($unicode)) {
            return '<span style="color: #666;">（未設定）</span>';
        }

        $icon_html = self::get_icon_html($unicode);
        return '<span style="font-size: 18px;">' . $icon_html . '</span> <code>' . $unicode . '</code>';
    }
}