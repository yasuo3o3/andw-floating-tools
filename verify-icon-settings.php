<?php
/**
 * アイコン設定の検証ツール（フロントエンド用）
 * 任意のページの末尾に追加されるデバッグ情報を表示
 */

// WordPressが読み込まれていない場合は処理を停止
if (!defined('ABSPATH')) {
    exit;
}

// 管理者のみアクセス可能
if (!current_user_can('manage_options')) {
    return;
}

// アイコン設定検証クラス
class Andw_Icon_Settings_Verifier {
    public static function verify_and_display() {
        echo '<div style="position: fixed; bottom: 0; left: 0; right: 0; background: #333; color: #fff; padding: 20px; z-index: 99999; max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px;">';
        echo '<h3 style="margin: 0 0 10px 0; color: #fff;">ANDW アイコン設定デバッグ情報</h3>';

        // 現在の設定を取得
        $options = get_option('andw_floating_tools_options', array());

        echo '<h4 style="margin: 10px 0 5px 0; color: #ff0;">全設定:</h4>';
        echo '<pre style="background: #222; padding: 10px; margin: 5px 0; border: 1px solid #555; max-height: 150px; overflow-y: auto;">';
        print_r($options);
        echo '</pre>';

        // カスタムSVGパスの確認
        $custom_svg_paths = isset($options['custom_svg_paths']) ? $options['custom_svg_paths'] : array();
        echo '<h4 style="margin: 10px 0 5px 0; color: #ff0;">カスタムSVGパス:</h4>';
        echo '<pre style="background: #222; padding: 10px; margin: 5px 0; border: 1px solid #555;">';
        if (empty($custom_svg_paths)) {
            echo 'カスタムSVGパスが設定されていません';
        } else {
            print_r($custom_svg_paths);
        }
        echo '</pre>';

        // 各ボタンタイプのアイコン取得テスト
        $button_types = array('apply', 'contact', 'toc', 'top');
        echo '<h4 style="margin: 10px 0 5px 0; color: #ff0;">アイコン取得テスト:</h4>';

        foreach ($button_types as $button_type) {
            echo '<div style="margin: 10px 0; border: 1px solid #555; padding: 10px;">';
            echo '<strong>' . $button_type . ':</strong><br>';

            if (!empty($custom_svg_paths[$button_type])) {
                $svg_content = trim($custom_svg_paths[$button_type]);
                echo '設定値: ' . esc_html($svg_content) . '<br>';

                // SVGタグの判定
                if (strpos($svg_content, '<svg') !== false) {
                    echo '判定: SVGタグ全体<br>';
                    $result = preg_replace(
                        '/<svg[^>]*>/i',
                        '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">',
                        $svg_content
                    );
                } else {
                    echo '判定: SVGタグの中身のみ<br>';
                    $result = sprintf(
                        '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">%s</svg>',
                        $svg_content
                    );
                }

                echo '処理結果: ' . esc_html($result) . '<br>';
                echo 'プレビュー: ' . $result;
            } else {
                echo 'カスタムアイコン未設定';
            }
            echo '</div>';
        }

        // リアルタイム取得テスト
        echo '<h4 style="margin: 10px 0 5px 0; color: #ff0;">リアルタイム取得テスト:</h4>';
        $realtime_options = get_option('andw_floating_tools_options', array());
        $realtime_custom_svg = isset($realtime_options['custom_svg_paths']) ? $realtime_options['custom_svg_paths'] : array();
        echo '<pre style="background: #222; padding: 10px; margin: 5px 0; border: 1px solid #555;">';
        echo 'リアルタイム取得結果:' . "\n";
        print_r($realtime_custom_svg);
        echo '</pre>';

        echo '<button onclick="this.parentElement.style.display=\'none\'" style="position: absolute; top: 10px; right: 10px; background: #666; color: #fff; border: none; padding: 5px 10px; cursor: pointer;">閉じる</button>';
        echo '</div>';
    }
}

// 管理者でWP_DEBUGが有効な場合のみ表示
if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options')) {
    add_action('wp_footer', array('Andw_Icon_Settings_Verifier', 'verify_and_display'));
}