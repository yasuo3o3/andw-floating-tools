<?php
/**
 * デバッグ用：FontAwesome アイコン設定の確認スクリプト
 * ブラウザで /wp-content/plugins/andw-floating-tools/debug-icon-settings.php にアクセス
 */

// WordPressを読み込み
require_once '../../../wp-load.php';

// 管理者権限チェック
if (!current_user_can('manage_options')) {
    die('権限がありません');
}

echo '<h1>FontAwesome アイコン設定デバッグ</h1>';

// 現在の設定を取得
$options = get_option('andw_floating_tools_options', array());

echo '<h2>全設定値:</h2>';
echo '<pre>';
print_r($options);
echo '</pre>';

echo '<h2>fontawesome_icons:</h2>';
$fontawesome_icons = isset($options['fontawesome_icons']) ? $options['fontawesome_icons'] : array();
echo '<pre>';
print_r($fontawesome_icons);
echo '</pre>';

// FontAwesome 検出状況
echo '<h2>FontAwesome 検出状況:</h2>';
if (class_exists('Andw_FontAwesome_Handler')) {
    $fa_handler = Andw_FontAwesome_Handler::get_instance();
    $detection_info = $fa_handler->get_detection_info();

    echo '<p><strong>検出済み:</strong> ' . ($detection_info['detected'] ? 'はい' : 'いいえ') . '</p>';
    if ($detection_info['detected']) {
        echo '<p><strong>バージョン:</strong> ' . esc_html($detection_info['version']) . '</p>';
        echo '<p><strong>ソース:</strong> ' . esc_html($detection_info['source']) . '</p>';
    }
} else {
    echo '<p>FontAwesome ハンドラーが読み込まれていません</p>';
}

// 各ボタンタイプをテスト
$button_types = array('apply', 'contact', 'toc', 'top');

echo '<h2>アイコン表示テスト:</h2>';
foreach ($button_types as $button_type) {
    echo '<h3>' . $button_type . ' ボタン:</h3>';

    $custom_icon = isset($fontawesome_icons[$button_type]) ? $fontawesome_icons[$button_type] : '';

    if (!empty($custom_icon)) {
        echo '<p>設定値: <code>' . esc_html($custom_icon) . '</code></p>';

        if (class_exists('Andw_FontAwesome_Icons')) {
            $icon_html = Andw_FontAwesome_Icons::get_button_icon($button_type, $custom_icon);
            echo '<p>結果: </p>';
            echo '<div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9; font-size: 24px;">';
            echo $icon_html;
            echo '</div>';
            echo '<p>HTML: <code>' . esc_html($icon_html) . '</code></p>';
        } else {
            echo '<p>FontAwesome アイコンクラスが読み込まれていません</p>';
        }
    } else {
        echo '<p>カスタムアイコンが設定されていません（デフォルトアイコンを使用）</p>';

        if (class_exists('Andw_FontAwesome_Icons')) {
            $default_icon_html = Andw_FontAwesome_Icons::get_button_icon($button_type, '');
            echo '<p>デフォルトアイコン: </p>';
            echo '<div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9; font-size: 24px;">';
            echo $default_icon_html;
            echo '</div>';
            echo '<p>HTML: <code>' . esc_html($default_icon_html) . '</code></p>';
        }
    }

    echo '<hr>';
}
?>