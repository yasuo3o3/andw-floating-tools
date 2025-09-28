<?php
/**
 * デバッグ用：アイコン設定の確認スクリプト
 * ブラウザで /wp-content/plugins/andw-floating-tools/debug-icon-settings.php にアクセス
 */

// WordPressを読み込み
require_once '../../../wp-load.php';

// 管理者権限チェック
if (!current_user_can('manage_options')) {
    die('権限がありません');
}

echo '<h1>アイコン設定デバッグ</h1>';

// 現在の設定を取得
$options = get_option('andw_floating_tools_options', array());

echo '<h2>全設定値:</h2>';
echo '<pre>';
print_r($options);
echo '</pre>';

echo '<h2>custom_svg_paths:</h2>';
$custom_svg_paths = isset($options['custom_svg_paths']) ? $options['custom_svg_paths'] : array();
echo '<pre>';
print_r($custom_svg_paths);
echo '</pre>';

// 各ボタンタイプをテスト
$button_types = array('apply', 'contact', 'toc', 'top');

echo '<h2>アイコン表示テスト:</h2>';
foreach ($button_types as $button_type) {
    echo '<h3>' . $button_type . ' ボタン:</h3>';

    if (!empty($custom_svg_paths[$button_type])) {
        $svg_content = trim($custom_svg_paths[$button_type]);
        echo '<p>設定値: <code>' . esc_html($svg_content) . '</code></p>';

        // SVGタグ全体が含まれているかチェック
        if (strpos($svg_content, '<svg') !== false) {
            echo '<p>判定: SVGタグ全体</p>';
            $result = preg_replace(
                '/<svg[^>]*>/i',
                '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">',
                $svg_content
            );
        } else {
            echo '<p>判定: SVGタグの中身のみ</p>';
            $result = sprintf(
                '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">%s</svg>',
                $svg_content
            );
        }

        echo '<p>結果: </p>';
        echo '<div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">';
        echo $result;
        echo '</div>';
        echo '<p>HTML: <code>' . esc_html($result) . '</code></p>';
    } else {
        echo '<p>カスタムアイコンが設定されていません</p>';
    }

    echo '<hr>';
}
?>