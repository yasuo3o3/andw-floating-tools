<?php
if (!defined('ABSPATH')) {
    exit;
}

class Andw_Floating_Tools_Settings {
    private $option_group = 'andw_floating_tools_settings';
    private $option_name = 'andw_floating_tools_options';
    private $page_slug = 'andw-floating-tools-settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function add_admin_menu() {
        add_options_page(
            __('andW Floating Tools 設定', 'andw-floating-tools'),
            __('Floating Tools', 'andw-floating-tools'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page')
        );
    }

    public function init_settings() {
        register_setting(
            $this->option_group,
            $this->option_name,
            array($this, 'sanitize_options')
        );

        add_settings_section(
            'andw_floating_tools_general',
            __('基本設定', 'andw-floating-tools'),
            array($this, 'render_general_section'),
            $this->page_slug
        );

        add_settings_field(
            'enabled_buttons',
            __('有効なボタン', 'andw-floating-tools'),
            array($this, 'render_enabled_buttons_field'),
            $this->page_slug,
            'andw_floating_tools_general'
        );

        add_settings_field(
            'button_order',
            __('ボタンの並び順', 'andw-floating-tools'),
            array($this, 'render_button_order_field'),
            $this->page_slug,
            'andw_floating_tools_general'
        );


        add_settings_field(
            'layout_desktop',
            __('デスクトップレイアウト', 'andw-floating-tools'),
            array($this, 'render_layout_desktop_field'),
            $this->page_slug,
            'andw_floating_tools_general'
        );

        add_settings_section(
            'andw_floating_tools_position',
            __('位置設定', 'andw-floating-tools'),
            array($this, 'render_position_section'),
            $this->page_slug
        );

        add_settings_field(
            'offset_desktop',
            __('デスクトップオフセット', 'andw-floating-tools'),
            array($this, 'render_offset_desktop_field'),
            $this->page_slug,
            'andw_floating_tools_position'
        );

        add_settings_field(
            'offset_mobile',
            __('モバイルオフセット', 'andw-floating-tools'),
            array($this, 'render_offset_mobile_field'),
            $this->page_slug,
            'andw_floating_tools_position'
        );

        add_settings_field(
            'offset_tablet',
            __('タブレットオフセット', 'andw-floating-tools'),
            array($this, 'render_offset_tablet_field'),
            $this->page_slug,
            'andw_floating_tools_position'
        );

        add_settings_section(
            'andw_floating_tools_toc',
            __('目次設定', 'andw-floating-tools'),
            array($this, 'render_toc_section'),
            $this->page_slug
        );

        add_settings_field(
            'toc_default_depth',
            __('既定の深さ', 'andw-floating-tools'),
            array($this, 'render_toc_depth_field'),
            $this->page_slug,
            'andw_floating_tools_toc'
        );

        add_settings_field(
            'toc_scroll_offset',
            __('スクロールオフセット', 'andw-floating-tools'),
            array($this, 'render_toc_offset_field'),
            $this->page_slug,
            'andw_floating_tools_toc'
        );

        add_settings_field(
            'toc_display_mode',
            __('表示モード', 'andw-floating-tools'),
            array($this, 'render_toc_display_mode_field'),
            $this->page_slug,
            'andw_floating_tools_toc'
        );

        add_settings_field(
            'sheet_settings',
            __('アンカーシート設定', 'andw-floating-tools'),
            array($this, 'render_sheet_settings_fields'),
            $this->page_slug,
            'andw_floating_tools_toc'
        );

        add_settings_section(
            'andw_floating_tools_cta',
            __('CTAボタン設定', 'andw-floating-tools'),
            array($this, 'render_cta_section'),
            $this->page_slug
        );

        add_settings_field(
            'apply_settings',
            __('お申し込みボタン', 'andw-floating-tools'),
            array($this, 'render_apply_fields'),
            $this->page_slug,
            'andw_floating_tools_cta'
        );

        add_settings_field(
            'contact_settings',
            __('お問い合わせボタン', 'andw-floating-tools'),
            array($this, 'render_contact_fields'),
            $this->page_slug,
            'andw_floating_tools_cta'
        );

        add_settings_section(
            'andw_floating_tools_icons',
            __('アイコン設定', 'andw-floating-tools'),
            array($this, 'render_icons_section'),
            $this->page_slug
        );

        add_settings_field(
            'fontawesome_apply',
            __('お申し込みアイコン', 'andw-floating-tools'),
            array($this, 'render_fontawesome_field'),
            $this->page_slug,
            'andw_floating_tools_icons',
            array('button_type' => 'apply')
        );

        add_settings_field(
            'fontawesome_contact',
            __('お問い合わせアイコン', 'andw-floating-tools'),
            array($this, 'render_fontawesome_field'),
            $this->page_slug,
            'andw_floating_tools_icons',
            array('button_type' => 'contact')
        );

        add_settings_field(
            'fontawesome_toc',
            __('目次アイコン', 'andw-floating-tools'),
            array($this, 'render_fontawesome_field'),
            $this->page_slug,
            'andw_floating_tools_icons',
            array('button_type' => 'toc')
        );

        add_settings_field(
            'fontawesome_top',
            __('ページトップアイコン', 'andw-floating-tools'),
            array($this, 'render_fontawesome_field'),
            $this->page_slug,
            'andw_floating_tools_icons',
            array('button_type' => 'top')
        );

        add_settings_field(
            'icon_display_method',
            __('アイコン表示方式', 'andw-floating-tools'),
            array($this, 'render_icon_display_method_field'),
            $this->page_slug,
            'andw_floating_tools_icons'
        );

        add_settings_section(
            'andw_floating_tools_utm',
            __('UTM設定', 'andw-floating-tools'),
            array($this, 'render_utm_section'),
            $this->page_slug
        );

        add_settings_field(
            'utm_enabled',
            __('UTM自動付与', 'andw-floating-tools'),
            array($this, 'render_utm_enabled_field'),
            $this->page_slug,
            'andw_floating_tools_utm'
        );

        add_settings_field(
            'utm_params',
            __('UTMパラメータ', 'andw-floating-tools'),
            array($this, 'render_utm_params_fields'),
            $this->page_slug,
            'andw_floating_tools_utm'
        );

        add_settings_section(
            'andw_floating_tools_design',
            __('デザイン設定', 'andw-floating-tools'),
            array($this, 'render_design_section'),
            $this->page_slug
        );

        add_settings_field(
            'preset_id',
            __('プリセット', 'andw-floating-tools'),
            array($this, 'render_preset_field'),
            $this->page_slug,
            'andw_floating_tools_design'
        );

        add_settings_field(
            'z_index',
            __('z-index', 'andw-floating-tools'),
            array($this, 'render_z_index_field'),
            $this->page_slug,
            'andw_floating_tools_design'
        );
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_' . $this->page_slug) {
            return;
        }

        wp_enqueue_script('jquery-ui-sortable');
        wp_add_inline_script('jquery-ui-sortable', '
            jQuery(document).ready(function($) {
                $("#andw-button-order-list").sortable({
                    update: function() {
                        var order = $(this).sortable("toArray", {attribute: "data-button"});
                        $("#button_order_input").val(order.join(","));
                    }
                });
            });
        ');
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'andw_floating_tools_messages',
                'andw_floating_tools_message',
                __('設定を保存しました。', 'andw-floating-tools'),
                'updated'
            );
        }

        settings_errors('andw_floating_tools_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields($this->option_group);
                do_settings_sections($this->page_slug);
                submit_button(__('変更を保存', 'andw-floating-tools'));
                ?>
            </form>
        </div>
        <?php
    }

    public function render_general_section() {
        echo '<p>' . esc_html__('基本的な表示設定を行います。フローティングツールはサイト全体で表示され、ブロックが配置されたページではブロックの設定が優先されます。', 'andw-floating-tools') . '</p>';
    }

    public function render_position_section() {
        echo '<p>' . esc_html__('各デバイスでの位置調整を行います。', 'andw-floating-tools') . '</p>';
    }

    public function render_toc_section() {
        echo '<p>' . esc_html__('目次機能の設定を行います。デフォルトでアンカーシート（画面下部に表示）モードになっています。', 'andw-floating-tools') . '</p>';
    }

    public function render_cta_section() {
        echo '<p>' . esc_html__('お申し込み・お問い合わせボタンの設定を行います。', 'andw-floating-tools') . '</p>';
    }

    public function render_icons_section() {
        echo '<p>' . esc_html__('各ボタンのアイコンをFontAwesome のUnicodeコードで自由に設定できます。', 'andw-floating-tools');
        echo '<br>' . esc_html__('4-6桁の英数字コード（例: f46c、f0e0）を入力してください。', 'andw-floating-tools');
        echo '<br><a href="https://fontawesome.com/search?o=r&m=free" target="_blank">FontAwesome アイコンを検索する</a></p>';

        // FontAwesome検出状況を表示
        if (class_exists('Andw_FontAwesome_Handler')) {
            $fa_handler = Andw_FontAwesome_Handler::get_instance();
            $detection_info = $fa_handler->get_detection_info();

            if ($detection_info['detected']) {
                echo '<div style="background: #d1ecf1; padding: 10px; border-radius: 4px; margin-bottom: 15px;">';
                echo '<strong>✅ FontAwesome検出済み:</strong> ';
                echo 'バージョン ' . esc_html($detection_info['version']) . ' ';
                echo '（ソース: ' . esc_html($detection_info['source']) . '）';
                echo '</div>';
            } else {
                echo '<div style="background: #fff3cd; padding: 10px; border-radius: 4px; margin-bottom: 15px;">';
                echo '<strong>ℹ️ FontAwesome自動読み込み:</strong> ';
                echo 'FontAwesome 6.5.1 CDNを自動で読み込みます。';
                echo '</div>';
            }
        }

        // 使い方ガイド
        echo '<div style="background: #f0f6ff; padding: 15px; border-radius: 4px; margin-bottom: 15px; border-left: 4px solid #0073aa;">';
        echo '<h4 style="margin-top: 0;">📖 使い方ガイド</h4>';
        echo '<ol style="margin-bottom: 0;">';
        echo '<li><a href="https://fontawesome.com/search?o=r&m=free" target="_blank">FontAwesome公式サイト</a>でお好みのアイコンを検索</li>';
        echo '<li>アイコンをクリックして詳細ページを開く</li>';
        echo '<li><strong>Unicode</strong> 欄に表示されるコード（例: f46c）をコピー</li>';
        echo '<li>下記のテキストフィールドに貼り付けて保存</li>';
        echo '</ol>';
        echo '</div>';
    }

    public function render_fontawesome_field($args) {
        $button_type = $args['button_type'];
        $options = get_option($this->option_name, array());
        $fontawesome_icons = isset($options['fontawesome_icons']) ? $options['fontawesome_icons'] : array();
        $current_unicode = isset($fontawesome_icons[$button_type]) ? $fontawesome_icons[$button_type] : '';

        // Unicode 入力フィールド
        echo '<input type="text" name="' . esc_attr($this->option_name) . '[fontawesome_icons][' . esc_attr($button_type) . ']" ';
        echo 'value="' . esc_attr($current_unicode) . '" ';
        echo 'class="regular-text" ';
        echo 'placeholder="例: f46c, f0e0" ';
        echo 'pattern="[a-fA-F0-9]{1,6}" ';
        echo 'maxlength="6" ';
        echo 'style="font-family: monospace;">';

        // リアルタイムプレビュー用のJavaScript
        $field_id = 'fontawesome_' . $button_type;
        echo '<div id="preview_' . $field_id . '" style="margin-top: 10px;">';
        if (!empty($current_unicode)) {
            echo '<strong>プレビュー:</strong> ';
            echo Andw_FontAwesome_Icons::get_preview_html($current_unicode);
        }
        echo '</div>';

        // JavaScript でリアルタイムプレビュー
        echo '<script>
        (function() {
            var input = document.querySelector(\'input[name="' . esc_js($this->option_name) . '[fontawesome_icons][' . esc_js($button_type) . ']"]\');
            var preview = document.getElementById(\'preview_' . $field_id . '\');

            if (input && preview) {
                input.addEventListener(\'input\', function() {
                    var unicode = this.value.trim();
                    if (unicode && /^[a-fA-F0-9]{1,6}$/.test(unicode)) {
                        preview.innerHTML = \'<strong>プレビュー:</strong> <span style="font-size: 18px;"><i class="fa-solid fa-fw" style="font-family: \\\'Font Awesome 6 Free\\\', \\\'FontAwesome\\\', \\\'Font Awesome 5 Free\\\', sans-serif; font-weight: 900;">&#x\' + unicode + \';</i></span> <code>\' + unicode + \'</code>\';
                    } else if (unicode === \'\') {
                        preview.innerHTML = \'<span style="color: #666;">（デフォルトアイコンを使用）</span>\';
                    } else {
                        preview.innerHTML = \'<span style="color: #d63638;">無効なコード形式</span>\';
                    }
                });
            }
        })();
        </script>';

        // ヘルプテキスト
        echo '<div class="description" style="margin-top: 10px;">';
        echo Andw_FontAwesome_Icons::get_admin_help_text();
        echo '</div>';
    }

    public function render_icon_display_method_field() {
        $options = get_option($this->option_name, array());
        $current_method = isset($options['icon_display_method']) ? $options['icon_display_method'] : 'fontawesome';

        echo '<div style="margin: 15px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">';

        // FontAwesome方式
        echo '<label style="display: block; margin-bottom: 10px;">';
        echo '<input type="radio" name="' . esc_attr($this->option_name) . '[icon_display_method]" value="fontawesome" ';
        checked($current_method, 'fontawesome');
        echo '> <strong>FontAwesome (推奨)</strong>';
        echo '</label>';
        echo '<p style="margin-left: 20px; color: #666;">Unicode入力で自由にアイコン選択。FontAwesome CDNまたはプラグインが必要。</p>';

        // SVG方式
        echo '<label style="display: block; margin-bottom: 10px;">';
        echo '<input type="radio" name="' . esc_attr($this->option_name) . '[icon_display_method]" value="svg" ';
        checked($current_method, 'svg');
        echo '> <strong>内蔵SVG</strong>';
        echo '</label>';
        echo '<p style="margin-left: 20px; color: #666;">FontAwesome不要で確実に表示。アイコン選択肢は限定的。</p>';

        echo '</div>';

        // 方式別の説明
        echo '<div class="description">';
        echo '<h4>💡 どちらを選ぶべきか？</h4>';
        echo '<table style="width: 100%; border-collapse: collapse; margin: 10px 0;">';
        echo '<tr style="background: #f0f0f0;"><th style="border: 1px solid #ddd; padding: 8px;">項目</th><th style="border: 1px solid #ddd; padding: 8px;">FontAwesome</th><th style="border: 1px solid #ddd; padding: 8px;">内蔵SVG</th></tr>';
        echo '<tr><td style="border: 1px solid #ddd; padding: 8px;">表示確実性</td><td style="border: 1px solid #ddd; padding: 8px;">△ 環境依存</td><td style="border: 1px solid #ddd; padding: 8px;">○ 確実</td></tr>';
        echo '<tr><td style="border: 1px solid #ddd; padding: 8px;">アイコン種類</td><td style="border: 1px solid #ddd; padding: 8px;">○ 豊富</td><td style="border: 1px solid #ddd; padding: 8px;">△ 限定的</td></tr>';
        echo '<tr><td style="border: 1px solid #ddd; padding: 8px;">外部依存</td><td style="border: 1px solid #ddd; padding: 8px;">△ CDN/プラグイン必要</td><td style="border: 1px solid #ddd; padding: 8px;">○ 不要</td></tr>';
        echo '<tr><td style="border: 1px solid #ddd; padding: 8px;">読み込み速度</td><td style="border: 1px solid #ddd; padding: 8px;">△ 追加CSS</td><td style="border: 1px solid #ddd; padding: 8px;">○ 軽量</td></tr>';
        echo '</table>';
        echo '</div>';
    }


    public function render_utm_section() {
        echo '<p>' . esc_html__('UTMパラメータの自動付与設定を行います。', 'andw-floating-tools') . '</p>';
    }

    public function render_design_section() {
        echo '<p>' . esc_html__('見た目とスタイルの設定を行います。', 'andw-floating-tools') . '</p>';
    }

    public function render_enabled_buttons_field() {
        $options = get_option($this->option_name, array());
        $enabled = isset($options['enabled_buttons']) ? $options['enabled_buttons'] : array();
        $buttons = array(
            'top' => __('ページトップへ', 'andw-floating-tools'),
            'apply' => __('お申し込み', 'andw-floating-tools'),
            'contact' => __('お問い合わせ', 'andw-floating-tools'),
            'toc' => __('目次', 'andw-floating-tools'),
        );

        foreach ($buttons as $key => $label) {
            $checked = in_array($key, $enabled, true) ? 'checked' : '';
            echo '<label><input type="checkbox" name="' . esc_attr($this->option_name) . '[enabled_buttons][]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($label) . '</label><br>';
        }
    }

    public function render_button_order_field() {
        $options = get_option($this->option_name, array());
        $order = isset($options['button_order']) ? $options['button_order'] : array('top', 'apply', 'contact', 'toc');
        $buttons = array(
            'top' => __('ページトップへ', 'andw-floating-tools'),
            'apply' => __('お申し込み', 'andw-floating-tools'),
            'contact' => __('お問い合わせ', 'andw-floating-tools'),
            'toc' => __('目次', 'andw-floating-tools'),
        );

        echo '<input type="hidden" id="button_order_input" name="' . esc_attr($this->option_name) . '[button_order]" value="' . esc_attr(implode(',', $order)) . '">';
        echo '<ul id="andw-button-order-list" style="list-style: none; padding: 0;">';
        foreach ($order as $button_key) {
            if (isset($buttons[$button_key])) {
                echo '<li data-button="' . esc_attr($button_key) . '" style="background: #f0f0f0; padding: 8px; margin: 4px 0; cursor: move; border-radius: 4px;">' . esc_html($buttons[$button_key]) . '</li>';
            }
        }
        echo '</ul>';
        echo '<p class="description">' . esc_html__('ドラッグ&ドロップで順序を変更できます。', 'andw-floating-tools') . '</p>';
    }

    public function render_layout_desktop_field() {
        $options = get_option($this->option_name, array());
        $layout = isset($options['layout_desktop']) ? $options['layout_desktop'] : 'stack-vertical-right-center';
        $layouts = array(
            'stack-vertical-right-center' => __('右端・高さ中央・縦積み', 'andw-floating-tools'),
            'bottom-right-inline' => __('右下・横並び', 'andw-floating-tools'),
        );

        foreach ($layouts as $key => $label) {
            $checked = $layout === $key ? 'checked' : '';
            echo '<label><input type="radio" name="' . esc_attr($this->option_name) . '[layout_desktop]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($label) . '</label><br>';
        }
    }

    public function render_offset_desktop_field() {
        $this->render_offset_field('offset_desktop', __('デスクトップ', 'andw-floating-tools'));
    }

    public function render_offset_mobile_field() {
        $this->render_offset_field('offset_mobile', __('モバイル', 'andw-floating-tools'));
    }

    public function render_offset_tablet_field() {
        $this->render_offset_field('offset_tablet', __('タブレット', 'andw-floating-tools'));
    }

    private function render_offset_field($field_name, $label) {
        $options = get_option($this->option_name, array());
        $offset = isset($options[$field_name]) ? $options[$field_name] : array('bottom' => 16, 'right' => 16);

        echo '<label>' . esc_html__('下', 'andw-floating-tools') . ': <input type="number" name="' . esc_attr($this->option_name) . '[' . $field_name . '][bottom]" value="' . esc_attr($offset['bottom']) . '" min="0" max="999" style="width: 80px;"> px</label>&nbsp;&nbsp;';
        echo '<label>' . esc_html__('右', 'andw-floating-tools') . ': <input type="number" name="' . esc_attr($this->option_name) . '[' . $field_name . '][right]" value="' . esc_attr($offset['right']) . '" min="0" max="999" style="width: 80px;"> px</label>';
    }

    public function render_toc_depth_field() {
        $options = get_option($this->option_name, array());
        $depth = isset($options['toc_default_depth']) ? $options['toc_default_depth'] : 2;
        $depths = array(
            2 => 'H2',
            3 => 'H2-H3',
            4 => 'H2-H4',
        );

        foreach ($depths as $key => $label) {
            $checked = $depth == $key ? 'checked' : '';
            echo '<label><input type="radio" name="' . esc_attr($this->option_name) . '[toc_default_depth]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($label) . '</label><br>';
        }
    }

    public function render_toc_offset_field() {
        $options = get_option($this->option_name, array());
        $offset = isset($options['toc_scroll_offset']) ? $options['toc_scroll_offset'] : 72;

        echo '<input type="number" name="' . esc_attr($this->option_name) . '[toc_scroll_offset]" value="' . esc_attr($offset) . '" min="0" max="999" style="width: 80px;"> px';
        echo '<p class="description">' . esc_html__('固定ヘッダーの高さに応じて調整してください。', 'andw-floating-tools') . '</p>';
    }

    public function render_toc_display_mode_field() {
        $options = get_option($this->option_name, array());
        $mode = isset($options['toc_display_mode']) ? $options['toc_display_mode'] : 'anchor-sheet';
        $modes = array(
            'anchor-sheet' => __('アンカーシート（ボタン直上に展開）', 'andw-floating-tools'),
            'drawer' => __('右側ドロワー', 'andw-floating-tools'),
            'anchor-panel' => __('アンカーパネル', 'andw-floating-tools'),
        );

        foreach ($modes as $key => $label) {
            $checked = $mode === $key ? 'checked' : '';
            echo '<label><input type="radio" name="' . esc_attr($this->option_name) . '[toc_display_mode]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($label) . '</label><br>';
        }
    }

    public function render_sheet_settings_fields() {
        $options = get_option($this->option_name, array());
        $sheet_max_width = isset($options['sheet_max_width']) ? $options['sheet_max_width'] : 480;
        $max_height_vh = isset($options['max_height_vh']) ? $options['max_height_vh'] : 33;
        $gap_right = isset($options['gap_right']) ? $options['gap_right'] : 12;
        $gap_left = isset($options['gap_left']) ? $options['gap_left'] : 16;
        $anchor_offset_y = isset($options['anchor_offset_y']) ? $options['anchor_offset_y'] : 8;
        $initial_state = isset($options['initial_state']) ? $options['initial_state'] : 'closed';

        echo '<table class="form-table" style="margin: 0;">';
        echo '<tr><th>' . esc_html__('最大幅', 'andw-floating-tools') . '</th><td><input type="number" name="' . esc_attr($this->option_name) . '[sheet_max_width]" value="' . esc_attr($sheet_max_width) . '" min="200" max="800" style="width: 80px;"> px</td></tr>';
        echo '<tr><th>' . esc_html__('最大高さ', 'andw-floating-tools') . '</th><td><input type="number" name="' . esc_attr($this->option_name) . '[max_height_vh]" value="' . esc_attr($max_height_vh) . '" min="20" max="100" style="width: 80px;"> vh<br><small>' . esc_html__('画面の高さに対する割合（33vh = 画面の約3分の1）', 'andw-floating-tools') . '</small></td></tr>';
        echo '<tr><th>' . esc_html__('右余白', 'andw-floating-tools') . '</th><td><input type="number" name="' . esc_attr($this->option_name) . '[gap_right]" value="' . esc_attr($gap_right) . '" min="0" max="100" style="width: 80px;"> px</td></tr>';
        echo '<tr><th>' . esc_html__('左余白', 'andw-floating-tools') . '</th><td><input type="number" name="' . esc_attr($this->option_name) . '[gap_left]" value="' . esc_attr($gap_left) . '" min="0" max="100" style="width: 80px;"> px</td></tr>';
        echo '<tr><th>' . esc_html__('ボタン間隔', 'andw-floating-tools') . '</th><td><input type="number" name="' . esc_attr($this->option_name) . '[anchor_offset_y]" value="' . esc_attr($anchor_offset_y) . '" min="0" max="50" style="width: 80px;"> px</td></tr>';
        echo '<tr><th>' . esc_html__('初期状態', 'andw-floating-tools') . '</th><td>';
        echo '<label><input type="radio" name="' . esc_attr($this->option_name) . '[initial_state]" value="closed"' . ($initial_state === 'closed' ? ' checked' : '') . '> ' . esc_html__('閉じた状態', 'andw-floating-tools') . '</label>&nbsp;&nbsp;';
        echo '<label><input type="radio" name="' . esc_attr($this->option_name) . '[initial_state]" value="peek"' . ($initial_state === 'peek' ? ' checked' : '') . '> ' . esc_html__('ピーク状態（少し見える）', 'andw-floating-tools') . '</label>';
        echo '</td></tr>';
        echo '</table>';
        echo '<p class="description">' . esc_html__('目次がボタン直上に表示されるアンカーシートモードの詳細設定です。幅や高さを調整できます。', 'andw-floating-tools') . '</p>';
    }

    public function render_apply_fields() {
        $this->render_cta_fields('apply', __('お申し込み', 'andw-floating-tools'));
    }

    public function render_contact_fields() {
        $this->render_cta_fields('contact', __('お問い合わせ', 'andw-floating-tools'));
    }

    private function render_cta_fields($type, $label) {
        $options = get_option($this->option_name, array());
        $url = isset($options[$type . '_url']) ? $options[$type . '_url'] : '';
        $button_label = isset($options[$type . '_label']) ? $options[$type . '_label'] : $label;
        $target = isset($options[$type . '_target']) ? $options[$type . '_target'] : '_blank';

        echo '<table class="form-table" style="margin: 0;">';
        echo '<tr><th>URL</th><td><input type="url" name="' . esc_attr($this->option_name) . '[' . $type . '_url]" value="' . esc_attr($url) . '" style="width: 100%; max-width: 400px;"></td></tr>';
        echo '<tr><th>' . esc_html__('ラベル', 'andw-floating-tools') . '</th><td><input type="text" name="' . esc_attr($this->option_name) . '[' . $type . '_label]" value="' . esc_attr($button_label) . '" style="width: 200px;"></td></tr>';
        echo '<tr><th>' . esc_html__('ターゲット', 'andw-floating-tools') . '</th><td>';
        echo '<label><input type="radio" name="' . esc_attr($this->option_name) . '[' . $type . '_target]" value="_self"' . ($target === '_self' ? ' checked' : '') . '> ' . esc_html__('同じウィンドウ', 'andw-floating-tools') . '</label>&nbsp;&nbsp;';
        echo '<label><input type="radio" name="' . esc_attr($this->option_name) . '[' . $type . '_target]" value="_blank"' . ($target === '_blank' ? ' checked' : '') . '> ' . esc_html__('新しいウィンドウ', 'andw-floating-tools') . '</label>';
        echo '</td></tr>';
        echo '</table>';
    }

    public function render_utm_enabled_field() {
        $options = get_option($this->option_name, array());
        $enabled = isset($options['utm_enabled']) ? $options['utm_enabled'] : false;

        echo '<label><input type="checkbox" name="' . esc_attr($this->option_name) . '[utm_enabled]" value="1"' . ($enabled ? ' checked' : '') . '> ' . esc_html__('CTAボタンにUTMパラメータを自動付与する', 'andw-floating-tools') . '</label>';
    }

    public function render_utm_params_fields() {
        $options = get_option($this->option_name, array());
        $source = isset($options['utm_source']) ? $options['utm_source'] : 'widget';
        $medium = isset($options['utm_medium']) ? $options['utm_medium'] : 'button';
        $campaign = isset($options['utm_campaign']) ? $options['utm_campaign'] : '';

        echo '<table class="form-table" style="margin: 0;">';
        echo '<tr><th>utm_source</th><td><input type="text" name="' . esc_attr($this->option_name) . '[utm_source]" value="' . esc_attr($source) . '" style="width: 200px;"></td></tr>';
        echo '<tr><th>utm_medium</th><td><input type="text" name="' . esc_attr($this->option_name) . '[utm_medium]" value="' . esc_attr($medium) . '" style="width: 200px;"></td></tr>';
        echo '<tr><th>utm_campaign</th><td><input type="text" name="' . esc_attr($this->option_name) . '[utm_campaign]" value="' . esc_attr($campaign) . '" style="width: 200px;"></td></tr>';
        echo '</table>';
    }

    public function render_preset_field() {
        $options = get_option($this->option_name, array());
        $preset = isset($options['preset_id']) ? $options['preset_id'] : 'default';
        $presets = array(
            'default' => __('デフォルト', 'andw-floating-tools'),
            'light' => __('ライト', 'andw-floating-tools'),
            'dark' => __('ダーク', 'andw-floating-tools'),
            'round-small' => __('丸型・小', 'andw-floating-tools'),
            'round-medium' => __('丸型・中', 'andw-floating-tools'),
            'round-large' => __('丸型・大', 'andw-floating-tools'),
            'square-small' => __('角型・小', 'andw-floating-tools'),
            'square-medium' => __('角型・中', 'andw-floating-tools'),
            'square-large' => __('角型・大', 'andw-floating-tools'),
        );

        echo '<select name="' . esc_attr($this->option_name) . '[preset_id]">';
        foreach ($presets as $key => $label) {
            $selected = $preset === $key ? 'selected' : '';
            echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    public function render_z_index_field() {
        $options = get_option($this->option_name, array());
        $z_index = isset($options['z_index']) ? $options['z_index'] : 999;

        echo '<input type="number" name="' . esc_attr($this->option_name) . '[z_index]" value="' . esc_attr($z_index) . '" min="1" max="9999" style="width: 100px;">';
        echo '<p class="description">' . esc_html__('他の要素との重なり順を調整してください。', 'andw-floating-tools') . '</p>';
    }

    public function sanitize_options($input) {
        $sanitized = array();

        if (isset($input['enabled_buttons'])) {
            $sanitized['enabled_buttons'] = andw_sanitize_enabled_buttons($input['enabled_buttons']);
        }

        if (isset($input['button_order'])) {
            if (is_string($input['button_order'])) {
                $sanitized['button_order'] = andw_sanitize_button_order(explode(',', $input['button_order']));
            } else {
                $sanitized['button_order'] = andw_sanitize_button_order($input['button_order']);
            }
        }

        if (isset($input['layout_desktop'])) {
            $sanitized['layout_desktop'] = andw_sanitize_layout_desktop($input['layout_desktop']);
        }

        if (isset($input['offset_desktop'])) {
            $sanitized['offset_desktop'] = andw_sanitize_offset($input['offset_desktop']);
        }

        if (isset($input['offset_mobile'])) {
            $sanitized['offset_mobile'] = andw_sanitize_offset($input['offset_mobile']);
        }

        if (isset($input['offset_tablet'])) {
            $sanitized['offset_tablet'] = andw_sanitize_offset($input['offset_tablet']);
        }

        if (isset($input['toc_default_depth'])) {
            $sanitized['toc_default_depth'] = andw_sanitize_toc_depth($input['toc_default_depth']);
        }

        if (isset($input['toc_scroll_offset'])) {
            $sanitized['toc_scroll_offset'] = andw_sanitize_toc_offset($input['toc_scroll_offset']);
        }

        if (isset($input['apply_url'])) {
            $sanitized['apply_url'] = andw_sanitize_url($input['apply_url']);
        }

        if (isset($input['apply_label'])) {
            $sanitized['apply_label'] = andw_sanitize_text($input['apply_label']);
        }

        if (isset($input['apply_target'])) {
            $sanitized['apply_target'] = andw_sanitize_target($input['apply_target']);
        }

        if (isset($input['contact_url'])) {
            $sanitized['contact_url'] = andw_sanitize_url($input['contact_url']);
        }

        if (isset($input['contact_label'])) {
            $sanitized['contact_label'] = andw_sanitize_text($input['contact_label']);
        }

        if (isset($input['contact_target'])) {
            $sanitized['contact_target'] = andw_sanitize_target($input['contact_target']);
        }

        $sanitized['utm_enabled'] = isset($input['utm_enabled']);

        if (isset($input['utm_source'])) {
            $sanitized['utm_source'] = andw_sanitize_utm_key($input['utm_source']);
        }

        if (isset($input['utm_medium'])) {
            $sanitized['utm_medium'] = andw_sanitize_utm_key($input['utm_medium']);
        }

        if (isset($input['utm_campaign'])) {
            $sanitized['utm_campaign'] = andw_sanitize_utm_key($input['utm_campaign']);
        }

        if (isset($input['preset_id'])) {
            $sanitized['preset_id'] = andw_sanitize_preset_id($input['preset_id']);
        }

        if (isset($input['z_index'])) {
            $sanitized['z_index'] = andw_sanitize_z_index($input['z_index']);
        }

        if (isset($input['toc_display_mode'])) {
            $sanitized['toc_display_mode'] = andw_sanitize_toc_display_mode($input['toc_display_mode']);
        }

        if (isset($input['sheet_max_width'])) {
            $sanitized['sheet_max_width'] = andw_sanitize_sheet_max_width($input['sheet_max_width']);
        }

        if (isset($input['max_height_vh'])) {
            $sanitized['max_height_vh'] = andw_sanitize_max_height_vh($input['max_height_vh']);
        }

        if (isset($input['gap_right'])) {
            $sanitized['gap_right'] = andw_sanitize_gap($input['gap_right']);
        }

        if (isset($input['gap_left'])) {
            $sanitized['gap_left'] = andw_sanitize_gap($input['gap_left']);
        }

        if (isset($input['anchor_offset_y'])) {
            $sanitized['anchor_offset_y'] = andw_sanitize_anchor_offset_y($input['anchor_offset_y']);
        }

        if (isset($input['initial_state'])) {
            $sanitized['initial_state'] = andw_sanitize_initial_state($input['initial_state']);
        }


        // アイコン表示方式のサニタイゼーション
        if (isset($input['icon_display_method'])) {
            $allowed_methods = array('fontawesome', 'svg');
            $sanitized['icon_display_method'] = in_array($input['icon_display_method'], $allowed_methods, true) ?
                $input['icon_display_method'] : 'fontawesome';
        }

        // FontAwesome Unicode のサニタイゼーション
        if (isset($input['fontawesome_icons']) && is_array($input['fontawesome_icons'])) {
            $sanitized['fontawesome_icons'] = array();
            $allowed_buttons = array('apply', 'contact', 'toc', 'top');

            foreach ($allowed_buttons as $button_type) {
                if (isset($input['fontawesome_icons'][$button_type])) {
                    $unicode_input = sanitize_text_field($input['fontawesome_icons'][$button_type]);

                    // Unicode コードのサニタイゼーション
                    $sanitized_unicode = preg_replace('/[^a-fA-F0-9]/', '', $unicode_input);
                    $sanitized_unicode = strtolower($sanitized_unicode);

                    // 長さ制限（1-6文字）
                    if (strlen($sanitized_unicode) > 6) {
                        $sanitized_unicode = substr($sanitized_unicode, 0, 6);
                    }

                    // 空文字列または有効なUnicodeパターンのみ許可
                    if (empty($sanitized_unicode) || preg_match('/^[a-f0-9]{1,6}$/', $sanitized_unicode)) {
                        $sanitized['fontawesome_icons'][$button_type] = $sanitized_unicode;
                    } else {
                        // 無効な場合は空文字（デフォルト使用）
                        $sanitized['fontawesome_icons'][$button_type] = '';
                    }

                    // デバッグログ
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("ANDW FontAwesome Unicode Save Debug - {$button_type}: '{$unicode_input}' -> '{$sanitized['fontawesome_icons'][$button_type]}'");
                    }
                }
            }
        }

        return $sanitized;
    }
}