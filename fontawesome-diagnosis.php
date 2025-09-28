<?php
/**
 * FontAwesome 診断ツール
 * ブラウザで /wp-content/plugins/andw-floating-tools/fontawesome-diagnosis.php にアクセス
 */

// WordPressを読み込み
require_once '../../../wp-load.php';

// 管理者権限チェック
if (!current_user_can('manage_options')) {
    die('権限がありません');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FontAwesome 診断</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .warning { background: #fff3cd; border-color: #fdbf47; }
        .error { background: #f8d7da; border-color: #f1aeb5; }
        .test-icon { font-size: 24px; margin: 0 10px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        code { background: #e9ecef; padding: 2px 4px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>

<h1>🔍 FontAwesome 診断レポート</h1>

<?php
// 1. 基本情報
echo '<div class="section">';
echo '<h2>📋 基本情報</h2>';
echo '<table>';
echo '<tr><th>項目</th><th>値</th></tr>';
echo '<tr><td>WordPress バージョン</td><td>' . get_bloginfo('version') . '</td></tr>';
echo '<tr><td>プラグイン バージョン</td><td>' . (defined('ANDW_FLOATING_TOOLS_VERSION') ? ANDW_FLOATING_TOOLS_VERSION : '不明') . '</td></tr>';
echo '<tr><td>現在のテーマ</td><td>' . wp_get_theme()->get('Name') . '</td></tr>';
echo '<tr><td>現在のURL</td><td>' . home_url() . '</td></tr>';
echo '</table>';
echo '</div>';

// 2. FontAwesome 検出状況
echo '<div class="section">';
echo '<h2>🔍 FontAwesome 検出状況</h2>';

if (class_exists('Andw_FontAwesome_Handler')) {
    $fa_handler = Andw_FontAwesome_Handler::get_instance();
    $detection_info = $fa_handler->get_detection_info();

    if ($detection_info['detected']) {
        echo '<div class="success">';
        echo '<strong>✅ FontAwesome検出済み</strong><br>';
        echo 'バージョン: ' . esc_html($detection_info['version']) . '<br>';
        echo 'ソース: ' . esc_html($detection_info['source']);
        echo '</div>';
    } else {
        echo '<div class="warning">';
        echo '<strong>⚠️ FontAwesome未検出</strong><br>';
        echo 'プラグインが自動でCDNを読み込みます';
        echo '</div>';
    }
} else {
    echo '<div class="error">';
    echo '<strong>❌ FontAwesome ハンドラークラスが読み込まれていません</strong>';
    echo '</div>';
}
echo '</div>';

// 3. 登録済みスタイル確認
echo '<div class="section">';
echo '<h2>📦 登録済みCSSスタイル</h2>';

global $wp_styles;
$fontawesome_styles = array();

if ($wp_styles && is_array($wp_styles->registered)) {
    foreach ($wp_styles->registered as $handle => $style) {
        $src = $style->src;
        if (stripos($handle, 'font') !== false || stripos($handle, 'awesome') !== false ||
            stripos($src, 'font-awesome') !== false || stripos($src, 'fontawesome') !== false) {
            $fontawesome_styles[$handle] = $src;
        }
    }
}

if (!empty($fontawesome_styles)) {
    echo '<table>';
    echo '<tr><th>ハンドル</th><th>URL</th></tr>';
    foreach ($fontawesome_styles as $handle => $src) {
        echo '<tr><td><code>' . esc_html($handle) . '</code></td><td>' . esc_html($src) . '</td></tr>';
    }
    echo '</table>';
} else {
    echo '<div class="warning">FontAwesome関連のCSSが見つかりませんでした</div>';
}
echo '</div>';

// 4. アイコンテスト
echo '<div class="section">';
echo '<h2>🎯 アイコン表示テスト</h2>';

$test_codes = array(
    'f062' => '上矢印',
    'f0e0' => 'メール',
    'f07a' => 'ショッピングカート',
    'f03a' => 'リスト',
    'f46c' => 'クリップボードチェック'
);

echo '<h3>1. 従来のクラス方式</h3>';
foreach ($test_codes as $code => $name) {
    echo '<div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd;">';
    echo '<strong>' . $name . ' (fa-solid):</strong> ';
    echo '<i class="fa-solid fa-fw" style="font-size: 24px;">&#x' . $code . ';</i> ';
    echo '<span style="margin-left: 10px; color: #666;">&#x' . $code . ';</span>';
    echo '</div>';
}

echo '<h3>2. 強制フォント指定方式</h3>';
foreach ($test_codes as $code => $name) {
    echo '<div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd;">';
    echo '<strong>' . $name . ' (強制指定):</strong> ';
    echo '<i style="font-family: \'Font Awesome 6 Free\', \'FontAwesome\', \'Font Awesome 5 Free\', sans-serif; font-weight: 900; font-size: 24px;">&#x' . $code . ';</i> ';
    echo '<span style="margin-left: 10px; color: #666;">&#x' . $code . ';</span>';
    echo '</div>';
}

echo '<h3>3. CDN直接テスト</h3>';
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">';
foreach ($test_codes as $code => $name) {
    echo '<div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd;">';
    echo '<strong>' . $name . ' (CDN直接):</strong> ';
    echo '<i class="fa-solid" style="font-size: 24px;">&#x' . $code . ';</i> ';
    echo '<span style="margin-left: 10px; color: #666;">&#x' . $code . ';</span>';
    echo '</div>';
}

echo '</div>';

// 5. プラグイン設定確認
echo '<div class="section">';
echo '<h2>⚙️ プラグイン設定</h2>';

$options = get_option('andw_floating_tools_options', array());
$fontawesome_icons = isset($options['fontawesome_icons']) ? $options['fontawesome_icons'] : array();

if (!empty($fontawesome_icons)) {
    echo '<table>';
    echo '<tr><th>ボタン</th><th>Unicodeコード</th><th>プレビュー</th></tr>';
    foreach ($fontawesome_icons as $button_type => $unicode) {
        echo '<tr>';
        echo '<td>' . esc_html($button_type) . '</td>';
        echo '<td><code>' . esc_html($unicode) . '</code></td>';
        echo '<td>';
        if (!empty($unicode)) {
            echo '<i style="font-family: \'Font Awesome 6 Free\', sans-serif; font-weight: 900; font-size: 20px;">&#x' . esc_html($unicode) . ';</i>';
        } else {
            echo '<span style="color: #666;">（デフォルト）</span>';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<div class="warning">FontAwesome アイコン設定が見つかりませんでした</div>';
}

echo '</div>';

// 6. 推奨事項
echo '<div class="section">';
echo '<h2>💡 推奨事項</h2>';

echo '<h3>問題が発生している場合の対処法：</h3>';
echo '<ol>';
echo '<li><strong>FontAwesome プラグインの検討</strong><br>';
echo '   推奨プラグイン: "Font Awesome" by Font Awesome Team<br>';
echo '   メリット: 確実な読み込み、競合回避、アップデート自動化</li>';

echo '<li><strong>テーマとの競合確認</strong><br>';
echo '   一部のテーマが独自のFontAwesome読み込みを行っている可能性</li>';

echo '<li><strong>キャッシュクリア</strong><br>';
echo '   ブラウザキャッシュ、WordPressキャッシュプラグインをクリア</li>';

echo '<li><strong>別のCDN試行</strong><br>';
echo '   jsDelivr CDN: <code>https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css</code></li>';
echo '</ol>';

echo '<h3>アイコンが表示されない場合のチェックポイント：</h3>';
echo '<ul>';
echo '<li>上記のアイコンテストで何も表示されない → FontAwesome未読み込み</li>';
echo '<li>一部のアイコンのみ表示されない → Unicodeコードが間違っている</li>';
echo '<li>四角い枠が表示される → フォントファイル読み込み失敗</li>';
echo '<li>管理画面では表示されるが本番では表示されない → CSS優先度の問題</li>';
echo '</ul>';

echo '</div>';

?>

<div class="section">
<h2>🔧 次のステップ</h2>
<p>上記の診断結果を確認して、以下から適切な対処法を選択してください：</p>

<div style="background: #f0f6ff; padding: 15px; border-radius: 4px; margin: 10px 0;">
<strong>推奨: FontAwesome プラグイン導入</strong><br>
- <a href="https://ja.wordpress.org/plugins/font-awesome/" target="_blank">Font Awesome 公式プラグイン</a>をインストール<br>
- プラグイン設定で「Use a Kit」または「Use CDN」を選択<br>
- 当プラグインのFontAwesome自動読み込みが無効化され、競合を回避
</div>

<div style="background: #fff3cd; padding: 15px; border-radius: 4px; margin: 10px 0;">
<strong>代替案: SVG方式への変更</strong><br>
- FontAwesome依存を避けてSVGアイコン直接埋め込み<br>
- より確実だが、アイコン選択の自由度が下がる
</div>
</div>

</body>
</html>