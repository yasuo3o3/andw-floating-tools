<?php
/**
 * Plugin Name: andW Floating Tools
 * Description: 右下フローティング群とTOCドロワーを提供するプラグイン
 * Version: 0.01
 * Author: Netservice
 * Author URI: https://netservice.jp/
 * License: GPLv2 or later
 * Text Domain: andw-floating-tools
 * Requires at least: 6.3
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('OF_FLOATING_TOOLS_VERSION', '0.01');
define('OF_FLOATING_TOOLS_PLUGIN_FILE', __FILE__);
define('OF_FLOATING_TOOLS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OF_FLOATING_TOOLS_PLUGIN_URL', plugin_dir_url(__FILE__));

class Of_Floating_Tools {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        register_activation_hook(__FILE__, array($this, 'on_activation'));
        register_deactivation_hook(__FILE__, array($this, 'on_deactivation'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('andw-floating-tools', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function init() {
        $this->load_includes();

        if (class_exists('Of_Floating_Tools_Settings')) {
            new Of_Floating_Tools_Settings();
        }

        if (class_exists('Of_Floating_Tools_Render')) {
            new Of_Floating_Tools_Render();
        }

        $this->register_blocks();
    }

    private function load_includes() {
        require_once OF_FLOATING_TOOLS_PLUGIN_DIR . 'includes/sanitization.php';
        require_once OF_FLOATING_TOOLS_PLUGIN_DIR . 'includes/settings.php';
        require_once OF_FLOATING_TOOLS_PLUGIN_DIR . 'includes/toc.php';
        require_once OF_FLOATING_TOOLS_PLUGIN_DIR . 'includes/render.php';
    }

    private function register_blocks() {
        if (function_exists('register_block_type')) {
            register_block_type(OF_FLOATING_TOOLS_PLUGIN_DIR . 'blocks/toc');
        }
    }

    public function enqueue_scripts() {
        if (!$this->should_load_assets()) {
            return;
        }

        wp_enqueue_script(
            'of-floating-tools-app',
            OF_FLOATING_TOOLS_PLUGIN_URL . 'assets/js/app.js',
            array(),
            OF_FLOATING_TOOLS_VERSION,
            array('strategy' => 'defer')
        );

        wp_enqueue_style(
            'of-floating-tools-app',
            OF_FLOATING_TOOLS_PLUGIN_URL . 'assets/css/app.css',
            array(),
            OF_FLOATING_TOOLS_VERSION
        );

        wp_localize_script('of-floating-tools-app', 'ofFloatingTools', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('of_floating_tools_nonce'),
            'i18n' => array(
                'topLabel' => __('ページトップへ', 'andw-floating-tools'),
                'tocLabel' => __('目次', 'andw-floating-tools'),
                'closeLabel' => __('閉じる', 'andw-floating-tools'),
            ),
        ));
    }

    private function should_load_assets() {
        if (is_admin()) {
            return false;
        }

        return is_singular() || is_home() || is_archive();
    }

    public function on_activation() {
        $default_options = array(
            'enabled_buttons' => array('top', 'toc', 'apply', 'contact'),
            'button_order' => array('top', 'apply', 'contact', 'toc'),
            'layout_desktop' => 'stack-vertical-right-center',
            'offset_desktop' => array('bottom' => 16, 'right' => 16),
            'offset_mobile' => array('bottom' => 16, 'right' => 16),
            'offset_tablet' => array('bottom' => 16, 'right' => 16),
            'toc_default_depth' => 2,
            'toc_scroll_offset' => 72,
            'apply_url' => '',
            'apply_label' => __('お申し込み', 'andw-floating-tools'),
            'apply_target' => '_blank',
            'contact_url' => '',
            'contact_label' => __('お問い合わせ', 'andw-floating-tools'),
            'contact_target' => '_blank',
            'utm_enabled' => false,
            'utm_source' => 'widget',
            'utm_medium' => 'button',
            'utm_campaign' => '',
            'preset_id' => 'default',
            'z_index' => 999,
        );

        add_option('of_floating_tools_options', $default_options);
    }

    public function on_deactivation() {
        // プラグイン停止時の処理
    }
}

Of_Floating_Tools::get_instance();