<?php
/**
 * FontAwesome アイコン設定の検証ツール（フロントエンド用）
 * 任意のページの末尾に追加されるデバッグ情報を表示
 */

// WordPressが読み込まれていない場合は処理を停止
if (!defined('ABSPATH')) {
    exit;
}

// 管理者のみアクセス可能（WordPressの初期化後に呼ばれるため安全）
if (!function_exists('current_user_can') || !current_user_can('manage_options')) {
    return;
}

// アイコン設定検証クラス
class Andw_Icon_Settings_Verifier {
    public static function verify_and_display() {
        echo '<div style="position: fixed; bottom: 0; left: 0; right: 0; background: #333; color: #fff; padding: 20px; z-index: 99999; max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px;">';
        echo '<h3 style="margin: 0 0 10px 0; color: #fff;">ANDW FontAwesome アイコンデバッグ情報</h3>';

        // 現在の設定を取得
        $options = get_option('andw_floating_tools_options', array());

        echo '<h4 style="margin: 10px 0 5px 0; color: #ff0;">全設定:</h4>';
        echo '<pre style="background: #222; padding: 10px; margin: 5px 0; border: 1px solid #555; max-height: 150px; overflow-y: auto;">';
        echo esc_html(wp_json_encode($options, JSON_PRETTY_PRINT));
        echo '</pre>';

        // FontAwesome アイコン設定の確認
        $fontawesome_icons = isset($options['fontawesome_icons']) ? $options['fontawesome_icons'] : array();
        echo '<h4 style="margin: 10px 0 5px 0; color: #ff0;">FontAwesome アイコン設定:</h4>';
        echo '<pre style="background: #222; padding: 10px; margin: 5px 0; border: 1px solid #555;">';
        if (empty($fontawesome_icons)) {
            echo 'FontAwesome アイコンが設定されていません';
        } else {
            echo esc_html(wp_json_encode($fontawesome_icons, JSON_PRETTY_PRINT));
        }
        echo '</pre>';

        // FontAwesome 検出状況
        echo '<h4 style="margin: 10px 0 5px 0; color: #ff0;">FontAwesome 検出状況:</h4>';
        echo '<div style="background: #222; padding: 10px; margin: 5px 0; border: 1px solid #555;">';
        if (class_exists('Andw_FontAwesome_Handler')) {
            $fa_handler = Andw_FontAwesome_Handler::get_instance();
            $detection_info = $fa_handler->get_detection_info();

            echo '検出済み: ' . ($detection_info['detected'] ? 'はい' : 'いいえ') . '<br>';
            if ($detection_info['detected']) {
                echo 'バージョン: ' . esc_html($detection_info['version']) . '<br>';
                echo 'ソース: ' . esc_html($detection_info['source']) . '<br>';
            }
        } else {
            echo 'FontAwesome ハンドラーが読み込まれていません';
        }
        echo '</div>';

        // 各ボタンタイプのアイコン取得テスト
        $button_types = array('apply', 'contact', 'toc', 'top');
        echo '<h4 style="margin: 10px 0 5px 0; color: #ff0;">アイコン表示テスト:</h4>';

        foreach ($button_types as $button_type) {
            echo '<div style="margin: 10px 0; border: 1px solid #555; padding: 10px;">';
            echo '<strong>' . esc_html($button_type) . ':</strong><br>';

            $custom_icon = isset($fontawesome_icons[$button_type]) ? $fontawesome_icons[$button_type] : '';

            if (!empty($custom_icon)) {
                echo '設定値: ' . esc_html($custom_icon) . '<br>';

                if (class_exists('Andw_FontAwesome_Icons')) {
                    $icon_html = Andw_FontAwesome_Icons::get_button_icon($button_type, $custom_icon);
                    echo '処理結果: ' . esc_html($icon_html) . '<br>';
                    echo 'プレビュー: ' . wp_kses_post($icon_html);
                } else {
                    echo 'FontAwesome アイコンクラスが読み込まれていません';
                }
            } else {
                echo 'カスタムアイコン未設定（デフォルト使用）<br>';

                if (class_exists('Andw_FontAwesome_Icons')) {
                    $default_icon_html = Andw_FontAwesome_Icons::get_button_icon($button_type, '');
                    echo 'デフォルトアイコン: ' . wp_kses_post($default_icon_html);
                }
            }
            echo '</div>';
        }

        echo '<button onclick="this.parentElement.style.display=\'none\'" style="position: absolute; top: 10px; right: 10px; background: #666; color: #fff; border: none; padding: 5px 10px; cursor: pointer;">閉じる</button>';
        echo '</div>';
    }
}

// 管理者でWP_DEBUGが有効な場合のみ表示（関数存在確認付き）
if (defined('WP_DEBUG') && WP_DEBUG &&
    function_exists('current_user_can') && function_exists('wp_get_current_user') &&
    current_user_can('manage_options')) {
    add_action('wp_footer', array('Andw_Icon_Settings_Verifier', 'verify_and_display'));
}