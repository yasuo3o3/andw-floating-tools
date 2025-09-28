<?php
if (!defined('ABSPATH')) {
    exit;
}

class Andw_SVG_Icons {
    /**
     * 内蔵SVGアイコン（FontAwesome代替）
     */
    public static function get_builtin_icons() {
        return array(
            'shopping-cart' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1z"/></svg>',

            'envelope' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>',

            'list' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>',

            'arrow-up' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>',

            'phone' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>',

            'clipboard-check' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>',

            'bars' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>',

            'home' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>',

            'star' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>',

            'heart' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>',
        );
    }

    /**
     * SVGアイコンHTML生成
     */
    public static function get_icon_html($icon_name, $additional_classes = '') {
        $icons = self::get_builtin_icons();

        if (!isset($icons[$icon_name])) {
            // フォールバック
            $icon_name = 'star';
        }

        $svg = $icons[$icon_name];

        // SVGに必要な属性を追加
        $svg = str_replace(
            '<svg',
            '<svg width="20" height="20" aria-hidden="true" class="andw-svg-icon ' . esc_attr($additional_classes) . '"',
            $svg
        );

        return $svg;
    }

    /**
     * ボタンタイプごとのSVGアイコン取得
     */
    public static function get_button_icon($button_type) {
        $icon_map = array(
            'apply' => 'shopping-cart',
            'contact' => 'envelope',
            'toc' => 'list',
            'top' => 'arrow-up',
        );

        $icon_name = isset($icon_map[$button_type]) ? $icon_map[$button_type] : 'star';
        return self::get_icon_html($icon_name);
    }

    /**
     * 管理画面用の選択肢
     */
    public static function get_select_options() {
        $icons = self::get_builtin_icons();
        $options = array();

        foreach ($icons as $key => $svg) {
            $label = ucwords(str_replace('-', ' ', $key));
            $options[$key] = $label;
        }

        return $options;
    }
}