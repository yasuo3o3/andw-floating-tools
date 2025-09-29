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
        $help_text = '<p><strong>FontAwesome Unicode 入力:</strong></p>';
        $help_text .= '<p>4-6桁の英数字コードを入力してください（例: f46c, f0e0）<br>';
        $help_text .= '空欄の場合はデフォルトアイコンが使用されます。</p>';

        $help_text .= '<div style="padding: 10px; background: #f0f8ff; border: 1px solid #ccc; border-radius: 4px; margin: 10px 0;">';
        $help_text .= '<p><strong>参考例:</strong></p>';
        $help_text .= '<p>';
        $help_text .= '• 申し込み: <code>f07a</code> (ショッピングカート), <code>f46c</code> (クリップボードチェック)<br>';
        $help_text .= '• お問い合わせ: <code>f0e0</code> (メール), <code>f086</code> (コメント), <code>f095</code> (電話)<br>';
        $help_text .= '• 目次: <code>f03a</code> (リスト), <code>f0ca</code> (箇条書き), <code>f0c9</code> (ハンバーガーメニュー)<br>';
        $help_text .= '• ページトップ: <code>f062</code> (上矢印), <code>f077</code> (シェブロン上), <code>f135</code> (ロケット)';
        $help_text .= '</p>';
        $help_text .= '</div>';

        $help_text .= '<p><a href="https://fontawesome.com/search?o=r&m=free" target="_blank" class="button">🔍 FontAwesome公式サイトでアイコンを探す</a></p>';

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