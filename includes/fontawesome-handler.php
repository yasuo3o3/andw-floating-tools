<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

class Andw_FontAwesome_Handler {
    private static $instance = null;
    private $fa_detected = false;
    private $fa_version = null;
    private $fa_source = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_head', array($this, 'detect_fontawesome'), 1);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_fontawesome_if_needed'), 999);
    }

    /**
     * 既存のFontAwesome使用を検出
     */
    public function detect_fontawesome() {
        // 1. FontAwesome プラグインの検出
        $this->detect_fontawesome_plugins();

        // 2. 登録済みスタイルからFontAwesome検出
        global $wp_styles;
        if ($wp_styles && !$this->fa_detected) {
            foreach ($wp_styles->registered as $handle => $style) {
                if ($this->is_fontawesome_style($style->src, $handle)) {
                    $this->fa_detected = true;
                    $this->fa_source = $handle;
                    $this->fa_version = $this->extract_version($style->src);

                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("ANDW FontAwesome Debug - Detected existing FontAwesome: {$handle} (version: {$this->fa_version})");
                    }
                    break;
                }
            }
        }

        // 3. HTMLソース内でCDN使用検出
        if (!$this->fa_detected) {
            add_action('wp_head', array($this, 'detect_fontawesome_in_dom'), 999);
        }
    }

    /**
     * FontAwesome プラグインの検出
     */
    private function detect_fontawesome_plugins() {
        // よく使われるFontAwesome プラグインを検出
        $fa_plugins = array(
            'font-awesome/index.php' => 'Font Awesome Official',
            'better-font-awesome/better-font-awesome.php' => 'Better Font Awesome',
            'easy-fontawesome/easy-fontawesome.php' => 'Easy FontAwesome',
            'font-awesome-4-menus/font-awesome-4-menus.php' => 'Font Awesome 4 Menus',
            'fontawesome/fontawesome.php' => 'FontAwesome Plugin'
        );

        foreach ($fa_plugins as $plugin_file => $plugin_name) {
            if (is_plugin_active($plugin_file)) {
                $this->fa_detected = true;
                $this->fa_source = 'plugin-' . dirname($plugin_file);
                $this->fa_version = 'plugin';

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("ANDW FontAwesome Debug - Detected FontAwesome plugin: {$plugin_name}");
                }
                break;
            }
        }
    }

    /**
     * DOM内でFontAwesome CDN検出
     */
    public function detect_fontawesome_in_dom() {
        if (!$this->fa_detected) {
            ob_start(array($this, 'scan_html_for_fontawesome'));
        }
    }

    public function scan_html_for_fontawesome($html) {
        // FontAwesome CDNパターンを検索
        $fa_patterns = array(
            '/cdnjs\.cloudflare\.com\/ajax\/libs\/font-awesome/i',
            '/use\.fontawesome\.com/i',
            '/maxcdn\.bootstrapcdn\.com\/font-awesome/i',
            '/kit\.fontawesome\.com/i'
        );

        foreach ($fa_patterns as $pattern) {
            if (preg_match($pattern, $html)) {
                $this->fa_detected = true;
                $this->fa_source = 'cdn';

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("ANDW FontAwesome Debug - Detected FontAwesome CDN in HTML");
                }
                break;
            }
        }

        return $html;
    }

    /**
     * 必要に応じてFontAwesome読み込み
     */
    public function enqueue_fontawesome_if_needed() {
        if (!$this->fa_detected) {
            $this->enqueue_fontawesome();
        } else {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("ANDW FontAwesome Debug - Skipping FontAwesome load (already detected)");
            }
        }
    }

    /**
     * FontAwesome読み込み
     */
    private function enqueue_fontawesome() {
        // FontAwesome 6.5.1 CDN (最新安定版)
        wp_enqueue_style(
            'andw-fontawesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
            array(),
            '6.5.1'
        );

        // FontAwesome 6 の重要なCSSプロパティを追加
        $custom_css = '
        .fa-solid, .fas {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
        }
        .fa-fw {
            text-align: center;
            width: 1.25em;
        }
        ';

        wp_add_inline_style('andw-fontawesome', $custom_css);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("ANDW FontAwesome Debug - Loading FontAwesome 6.5.1 with custom CSS");
        }
    }

    /**
     * FontAwesome関連スタイルかチェック
     */
    private function is_fontawesome_style($src, $handle) {
        $fa_indicators = array(
            'font-awesome',
            'fontawesome',
            'fa-',
            'awesome'
        );

        $check_string = strtolower($src . ' ' . $handle);

        foreach ($fa_indicators as $indicator) {
            if (strpos($check_string, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * URLからバージョン抽出
     */
    private function extract_version($src) {
        if (preg_match('/font-awesome\/([0-9\.]+)/', $src, $matches)) {
            return $matches[1];
        }
        return 'unknown';
    }

    /**
     * FontAwesome検出状況を取得
     */
    public function get_detection_info() {
        return array(
            'detected' => $this->fa_detected,
            'version' => $this->fa_version,
            'source' => $this->fa_source
        );
    }

    /**
     * FontAwesome利用可能かチェック
     */
    public function is_available() {
        return true; // 検出されない場合は自動で読み込むため常にtrue
    }
}

