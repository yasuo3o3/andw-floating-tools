<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * アイコンヘルパークラス
 * アイコン名で簡単にSVGを取得できる
 */
class Andw_Floating_Tools_Icons {

    /**
     * 利用可能なアイコンの一覧
     */
    public static function get_available_icons() {
        return array(
            'clipboard' => 'クリップボード（現在のお申し込み）',
            'document' => 'ドキュメント',
            'form' => 'フォーム',
            'plus' => 'プラス',
            'check' => 'チェック',
            'mail' => 'メール',
            'phone' => '電話',
            'edit' => '編集',
            'user' => 'ユーザー',
            'arrow-up' => '上矢印（現在のページトップ）',
            'list' => 'リスト（現在の目次）',
            'contact' => 'お問い合わせ（現在のお問い合わせ）',
        );
    }

    /**
     * アイコン名からSVGを取得
     */
    public static function get_icon($icon_name, $size = 24) {
        $icons = array(
            'clipboard' => '<path d="M9 11H7v9a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2v2H9v2zm0-4V5a2 2 0 012-2h2a2 2 0 012 2v2h3a1 1 0 011 1v1H6V8a1 1 0 011-1h2z"/>',
            'document' => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM14 8V4l4 4h-4z"/>',
            'form' => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM16 18H8v-2h8v2zm0-4H8v-2h8v2zm0-4H8V8h8v2z"/>',
            'plus' => '<path d="M12 4v8m0 0v8m0-8h8m-8 0H4" stroke="currentColor" stroke-width="2" fill="none"/>',
            'check' => '<path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" fill="none"/>',
            'mail' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
            'phone' => '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>',
            'edit' => '<path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>',
            'user' => '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'arrow-up' => '<path d="M7 14l5-5 5 5"/>',
            'list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
            'contact' => '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
        );

        if (!isset($icons[$icon_name])) {
            return self::get_icon('clipboard', $size); // フォールバック
        }

        return sprintf(
            '<svg width="%d" height="%d" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">%s</svg>',
            $size,
            $size,
            $icons[$icon_name]
        );
    }
}