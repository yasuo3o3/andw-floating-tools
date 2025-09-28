<?php
if (!defined('ABSPATH')) {
    exit;
}

class Andw_FontAwesome_Icons {
    /**
     * 利用可能なFontAwesomeアイコン一覧
     */
    public static function get_available_icons() {
        return array(
            // 申し込み・ショッピング関連
            'shopping-cart' => array(
                'label' => '🛒 ショッピングカート',
                'class' => 'fas fa-shopping-cart'
            ),
            'shopping-bag' => array(
                'label' => '🛍️ ショッピングバッグ',
                'class' => 'fas fa-shopping-bag'
            ),
            'credit-card' => array(
                'label' => '💳 クレジットカード',
                'class' => 'fas fa-credit-card'
            ),
            'clipboard-check' => array(
                'label' => '📋 申し込み',
                'class' => 'fas fa-clipboard-check'
            ),
            'file-contract' => array(
                'label' => '📄 契約書',
                'class' => 'fas fa-file-contract'
            ),
            'handshake' => array(
                'label' => '🤝 握手',
                'class' => 'fas fa-handshake'
            ),

            // お問い合わせ関連
            'envelope' => array(
                'label' => '✉️ メール',
                'class' => 'fas fa-envelope'
            ),
            'comments' => array(
                'label' => '💬 コメント',
                'class' => 'fas fa-comments'
            ),
            'phone' => array(
                'label' => '📞 電話',
                'class' => 'fas fa-phone'
            ),
            'headset' => array(
                'label' => '🎧 サポート',
                'class' => 'fas fa-headset'
            ),
            'question-circle' => array(
                'label' => '❓ 質問',
                'class' => 'fas fa-question-circle'
            ),

            // 目次関連
            'list' => array(
                'label' => '📋 リスト',
                'class' => 'fas fa-list'
            ),
            'list-ul' => array(
                'label' => '📝 箇条書き',
                'class' => 'fas fa-list-ul'
            ),
            'bars' => array(
                'label' => '☰ ハンバーガーメニュー',
                'class' => 'fas fa-bars'
            ),
            'book-open' => array(
                'label' => '📖 本を開く',
                'class' => 'fas fa-book-open'
            ),
            'bookmark' => array(
                'label' => '🔖 ブックマーク',
                'class' => 'fas fa-bookmark'
            ),

            // ページトップ関連
            'arrow-up' => array(
                'label' => '⬆️ 上矢印',
                'class' => 'fas fa-arrow-up'
            ),
            'chevron-up' => array(
                'label' => '⬆️ シェブロン上',
                'class' => 'fas fa-chevron-up'
            ),
            'angle-up' => array(
                'label' => '⬆️ アングル上',
                'class' => 'fas fa-angle-up'
            ),
            'arrow-circle-up' => array(
                'label' => '⬆️ 円矢印上',
                'class' => 'fas fa-arrow-circle-up'
            ),
            'rocket' => array(
                'label' => '🚀 ロケット',
                'class' => 'fas fa-rocket'
            ),

            // その他汎用
            'home' => array(
                'label' => '🏠 ホーム',
                'class' => 'fas fa-home'
            ),
            'star' => array(
                'label' => '⭐ 星',
                'class' => 'fas fa-star'
            ),
            'heart' => array(
                'label' => '❤️ ハート',
                'class' => 'fas fa-heart'
            ),
            'info-circle' => array(
                'label' => 'ℹ️ 情報',
                'class' => 'fas fa-info-circle'
            ),
            'cog' => array(
                'label' => '⚙️ 設定',
                'class' => 'fas fa-cog'
            ),
        );
    }

    /**
     * デフォルトアイコン設定
     */
    public static function get_default_icons() {
        return array(
            'apply' => 'shopping-cart',
            'contact' => 'envelope',
            'toc' => 'list',
            'top' => 'arrow-up',
        );
    }

    /**
     * アイコンHTMLを生成
     */
    public static function get_icon_html($icon_key, $additional_classes = '') {
        $icons = self::get_available_icons();

        if (!isset($icons[$icon_key])) {
            // フォールバック：デフォルトアイコン
            $defaults = self::get_default_icons();
            $icon_key = isset($defaults[$icon_key]) ? $defaults[$icon_key] : 'question-circle';
        }

        if (!isset($icons[$icon_key])) {
            $icon_key = 'question-circle';
        }

        $icon_class = $icons[$icon_key]['class'];
        if (!empty($additional_classes)) {
            $icon_class .= ' ' . $additional_classes;
        }

        return '<i class="' . esc_attr($icon_class) . '" aria-hidden="true"></i>';
    }

    /**
     * ボタンタイプごとのアイコン取得
     */
    public static function get_button_icon($button_type, $custom_icon = '') {
        // カスタムアイコンが設定されている場合
        if (!empty($custom_icon)) {
            return self::get_icon_html($custom_icon);
        }

        // デフォルトアイコン
        $defaults = self::get_default_icons();
        $icon_key = isset($defaults[$button_type]) ? $defaults[$button_type] : 'question-circle';

        return self::get_icon_html($icon_key);
    }

    /**
     * 管理画面用アイコン選択オプション
     */
    public static function get_select_options() {
        $icons = self::get_available_icons();
        $options = array('' => '-- デフォルトアイコンを使用 --');

        foreach ($icons as $key => $icon) {
            $options[$key] = $icon['label'];
        }

        return $options;
    }
}