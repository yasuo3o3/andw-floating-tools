<?php
if (!defined('ABSPATH')) {
    exit;
}

class Andw_Floating_Tools_Render {
    private $options;
    private $toc_instance;

    public function __construct() {
        $this->options = get_option('andw_floating_tools_options', array());
        $this->toc_instance = Andw_Floating_Tools_TOC::get_instance();

        add_action('wp_footer', array($this, 'render_floating_tools'));
        add_filter('wp_localize_script', array($this, 'localize_toc_data'), 10, 3);
    }

    public function render_floating_tools() {
        if (!$this->should_render()) {
            return;
        }

        $enabled_buttons = $this->get_enabled_buttons();
        $button_order = $this->get_button_order();

        if (empty($enabled_buttons)) {
            return;
        }

        $this->render_floating_buttons($enabled_buttons, $button_order);
        $this->render_toc_drawer();
        $this->render_custom_css();
    }

    private function should_render() {
        if (is_admin() || wp_doing_ajax()) {
            return false;
        }

        if (!is_singular() && !is_home() && !is_archive()) {
            return false;
        }

        // 常にフローティングツールを表示
        // ブロックが配置されたページではブロック設定が優先される
        return true;
    }


    private function get_enabled_buttons() {
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['enabled'])) {
            return andw_sanitize_enabled_buttons($block_attributes['enabled']);
        }

        return isset($this->options['enabled_buttons']) ? $this->options['enabled_buttons'] : array();
    }

    private function get_button_order() {
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['order'])) {
            return andw_sanitize_button_order($block_attributes['order']);
        }

        return isset($this->options['button_order']) ? $this->options['button_order'] : array('top', 'apply', 'contact', 'toc');
    }

    private function get_block_attributes() {
        global $post;

        if (!$post || !has_blocks($post->post_content)) {
            return array();
        }

        $blocks = parse_blocks($post->post_content);

        foreach ($blocks as $block) {
            if ($block['blockName'] === 'andw-floating-tools/toc') {
                return $block['attrs'] ?? array();
            }
        }

        return array();
    }

    private function render_floating_buttons($enabled_buttons, $button_order) {
        $layout_desktop = $this->get_layout_desktop();
        $preset_id = isset($this->options['preset_id']) ? $this->options['preset_id'] : 'default';

        $container_classes = array(
            'andw-floating-tools',
            'andw-preset-' . esc_attr($preset_id),
            'andw-layout-' . esc_attr($layout_desktop),
        );

        echo '<div class="' . esc_attr(implode(' ', $container_classes)) . '" role="complementary" aria-label="' . esc_attr__('フローティングツール', 'andw-floating-tools') . '">';

        $ordered_buttons = array();
        foreach ($button_order as $button_type) {
            if (in_array($button_type, $enabled_buttons, true)) {
                $ordered_buttons[] = $button_type;
            }
        }

        foreach ($ordered_buttons as $button_type) {
            $this->render_button($button_type);
        }

        echo '</div>';
    }

    private function get_layout_desktop() {
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['layoutDesktop'])) {
            return andw_sanitize_layout_desktop($block_attributes['layoutDesktop']);
        }

        return isset($this->options['layout_desktop']) ? $this->options['layout_desktop'] : 'stack-vertical-right-center';
    }

    private function render_button($button_type) {
        $button_config = $this->get_button_config($button_type);

        if (!$button_config) {
            return;
        }

        $classes = array(
            'andw-floating-button',
            'andw-button-' . esc_attr($button_type),
        );

        if ($button_type === 'top') {
            $classes[] = 'andw-scroll-trigger';
        }

        $attributes = array(
            'class' => implode(' ', $classes),
            'type' => 'button',
            'aria-label' => $button_config['aria_label'],
        );

        if ($button_type === 'toc') {
            $attributes['data-toc-toggle'] = 'true';
            $attributes['aria-expanded'] = 'false';
            $attributes['aria-controls'] = 'andw-toc-drawer';
        } elseif (!empty($button_config['url'])) {
            $attributes['data-url'] = $button_config['url'];
            $attributes['data-target'] = $button_config['target'];
        }

        echo '<button';
        foreach ($attributes as $attr => $value) {
            echo ' ' . esc_attr($attr) . '="' . esc_attr($value) . '"';
        }
        echo '>';

        echo $this->get_button_icon($button_type);

        if (!empty($button_config['label'])) {
            echo '<span class="andw-button-label">' . esc_html($button_config['label']) . '</span>';
        }

        echo '</button>';
    }

    private function get_button_config($button_type) {
        $block_attributes = $this->get_block_attributes();

        switch ($button_type) {
            case 'top':
                return array(
                    'aria_label' => __('ページトップへ', 'andw-floating-tools'),
                    'label' => __('トップへ', 'andw-floating-tools'),
                    'url' => '',
                    'target' => '',
                );

            case 'apply':
                $url = isset($block_attributes['applyUrl']) ? $block_attributes['applyUrl'] :
                       (isset($this->options['apply_url']) ? $this->options['apply_url'] : '');
                $label = isset($block_attributes['applyLabel']) ? $block_attributes['applyLabel'] :
                         (isset($this->options['apply_label']) ? $this->options['apply_label'] : __('お申し込み', 'andw-floating-tools'));
                $target = isset($block_attributes['applyTarget']) ? $block_attributes['applyTarget'] :
                          (isset($this->options['apply_target']) ? $this->options['apply_target'] : '_blank');

                if (empty($url)) {
                    return false;
                }

                return array(
                    'aria_label' => $label,
                    'label' => $label,
                    'url' => $this->process_url($url),
                    'target' => $target,
                );

            case 'contact':
                $url = isset($block_attributes['contactUrl']) ? $block_attributes['contactUrl'] :
                       (isset($this->options['contact_url']) ? $this->options['contact_url'] : '');
                $label = isset($block_attributes['contactLabel']) ? $block_attributes['contactLabel'] :
                         (isset($this->options['contact_label']) ? $this->options['contact_label'] : __('お問い合わせ', 'andw-floating-tools'));
                $target = isset($block_attributes['contactTarget']) ? $block_attributes['contactTarget'] :
                          (isset($this->options['contact_target']) ? $this->options['contact_target'] : '_blank');

                if (empty($url)) {
                    return false;
                }

                return array(
                    'aria_label' => $label,
                    'label' => $label,
                    'url' => $this->process_url($url),
                    'target' => $target,
                );

            case 'toc':
                return array(
                    'aria_label' => __('目次', 'andw-floating-tools'),
                    'label' => __('目次', 'andw-floating-tools'),
                    'url' => '',
                    'target' => '',
                );

            default:
                return false;
        }
    }

    private function process_url($url) {
        if (empty($url)) {
            return '';
        }

        $utm_enabled = isset($this->options['utm_enabled']) ? $this->options['utm_enabled'] : false;

        if (!$utm_enabled) {
            return esc_url($url);
        }

        $utm_params = array(
            'source' => isset($this->options['utm_source']) ? $this->options['utm_source'] : 'widget',
            'medium' => isset($this->options['utm_medium']) ? $this->options['utm_medium'] : 'button',
            'campaign' => isset($this->options['utm_campaign']) ? $this->options['utm_campaign'] : '',
        );

        $url_with_utm = andw_add_utm_to_url($url, $utm_params);

        return esc_url($url_with_utm);
    }

    private function get_button_icon($button_type) {
        $current_options = get_option('andw_floating_tools_options', array());
        $display_method = isset($current_options['icon_display_method']) ? $current_options['icon_display_method'] : 'fontawesome';


        if ($display_method === 'svg') {
            // 内蔵SVGアイコンを使用
            return Andw_SVG_Icons::get_button_icon($button_type);
        } else {
            // FontAwesome アイコンを使用
            $fontawesome_icons = isset($current_options['fontawesome_icons']) ? $current_options['fontawesome_icons'] : array();
            $custom_icon = isset($fontawesome_icons[$button_type]) ? $fontawesome_icons[$button_type] : '';


            return Andw_FontAwesome_Icons::get_button_icon($button_type, $custom_icon);
        }
    }

    private function render_toc_drawer() {
        $enabled_buttons = $this->get_enabled_buttons();

        if (!in_array('toc', $enabled_buttons, true)) {
            return;
        }

        $display_mode = $this->get_toc_display_mode();

        if ($display_mode === 'anchor-sheet') {
            $this->render_toc_anchor_sheet();
        } else {
            $this->render_toc_drawer_legacy();
        }
    }

    private function render_toc_anchor_sheet() {
        echo '<div id="andw-toc-anchor-sheet" class="andw-toc-anchor-sheet" role="dialog" aria-modal="true" aria-labelledby="andw-toc-title" aria-hidden="true">';
        echo '<div class="andw-toc-backdrop" data-toc-close="true"></div>';
        echo '<div class="andw-toc-sheet-content">';
        echo '<div class="andw-toc-header">';
        echo '<div class="andw-toc-handle"></div>';
        echo '<div id="andw-toc-title" class="andw-toc-title">' . esc_html__('目次', 'andw-floating-tools') . '</div>';
        echo '<button type="button" class="andw-toc-close" data-toc-close="true" aria-label="' . esc_attr__('目次を閉じる', 'andw-floating-tools') . '">';
        echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
        echo '</button>';
        echo '</div>';
        echo '<div class="andw-toc-body">';
        echo $this->toc_instance->render_toc_html();
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private function render_toc_drawer_legacy() {
        echo '<div id="andw-toc-drawer" class="andw-toc-drawer" role="dialog" aria-modal="true" aria-labelledby="andw-toc-title" aria-hidden="true">';
        echo '<div class="andw-toc-backdrop" data-toc-close="true"></div>';
        echo '<div class="andw-toc-content">';
        echo '<div class="andw-toc-header">';
        echo '<button type="button" class="andw-toc-close" data-toc-close="true" aria-label="' . esc_attr__('目次を閉じる', 'andw-floating-tools') . '">';
        echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
        echo '</button>';
        echo '</div>';
        echo '<div class="andw-toc-body">';
        echo $this->toc_instance->render_toc_html();
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private function get_toc_display_mode() {
        return isset($this->options['toc_display_mode']) ? $this->options['toc_display_mode'] : 'anchor-sheet';
    }

    private function render_custom_css() {
        $offset_desktop = $this->get_offset('desktop');
        $offset_mobile = $this->get_offset('mobile');
        $offset_tablet = $this->get_offset('tablet');
        $z_index = isset($this->options['z_index']) ? $this->options['z_index'] : 999;

        $sheet_settings = $this->get_sheet_settings();

        echo '<style id="andw-floating-tools-inline-css">';
        echo '.andw-floating-tools { z-index: ' . esc_attr($z_index) . '; }';

        echo '@media (min-width: 1024px) {';
        echo '.andw-floating-tools { bottom: ' . esc_attr($offset_desktop['bottom']) . 'px; right: ' . esc_attr($offset_desktop['right']) . 'px; }';
        echo '}';

        echo '@media (min-width: 768px) and (max-width: 1023px) {';
        echo '.andw-floating-tools { bottom: ' . esc_attr($offset_tablet['bottom']) . 'px; right: ' . esc_attr($offset_tablet['right']) . 'px; }';
        echo '}';

        echo '@media (max-width: 767px) {';
        echo '.andw-floating-tools { bottom: ' . esc_attr($offset_mobile['bottom']) . 'px; right: ' . esc_attr($offset_mobile['right']) . 'px; }';
        echo '}';

        // Anchor sheet settings
        echo ':root {';
        echo '--andw-sheet-max-width: ' . esc_attr($sheet_settings['sheetMaxWidth']) . 'px;';
        echo '--andw-max-height-vh: ' . esc_attr($sheet_settings['maxHeightVh']) . 'vh;';
        echo '--andw-gap-right: ' . esc_attr($sheet_settings['gapRight']) . 'px;';
        echo '--andw-gap-left: ' . esc_attr($sheet_settings['gapLeft']) . 'px;';
        echo '--andw-anchor-offset-y: ' . esc_attr($sheet_settings['anchorOffsetY']) . 'px;';
        echo '}';

        echo '</style>';
    }

    private function get_sheet_settings() {
        $block_attributes = $this->get_block_attributes();

        $sheet_max_width = isset($block_attributes['sheetMaxWidth']) && $block_attributes['sheetMaxWidth'] > 0 ?
            $block_attributes['sheetMaxWidth'] :
            (isset($this->options['sheet_max_width']) ? $this->options['sheet_max_width'] : 480);

        $max_height_vh = isset($block_attributes['maxHeightVh']) && $block_attributes['maxHeightVh'] > 0 ?
            $block_attributes['maxHeightVh'] :
            (isset($this->options['max_height_vh']) ? $this->options['max_height_vh'] : 80);

        $gap_right = isset($block_attributes['gapRight']) && $block_attributes['gapRight'] > 0 ?
            $block_attributes['gapRight'] :
            (isset($this->options['gap_right']) ? $this->options['gap_right'] : 12);

        $gap_left = isset($block_attributes['gapLeft']) && $block_attributes['gapLeft'] > 0 ?
            $block_attributes['gapLeft'] :
            (isset($this->options['gap_left']) ? $this->options['gap_left'] : 16);

        $anchor_offset_y = isset($block_attributes['anchorOffsetY']) && $block_attributes['anchorOffsetY'] > 0 ?
            $block_attributes['anchorOffsetY'] :
            (isset($this->options['anchor_offset_y']) ? $this->options['anchor_offset_y'] : 8);

        return array(
            'sheetMaxWidth' => $sheet_max_width,
            'maxHeightVh' => $max_height_vh,
            'gapRight' => $gap_right,
            'gapLeft' => $gap_left,
            'anchorOffsetY' => $anchor_offset_y,
        );
    }

    private function get_offset($device) {
        $key = 'offset_' . $device;
        return isset($this->options[$key]) ? $this->options[$key] : array('bottom' => 16, 'right' => 16);
    }

    public function localize_toc_data($handle, $object_name, $l10n) {
        if ($handle === 'andw-floating-tools-app' && $object_name === 'andwFloatingTools') {
            $l10n['tocOffset'] = $this->toc_instance->get_toc_scroll_offset();
            $l10n['hasToc'] = $this->toc_instance->has_toc();
            $l10n['tocDisplayMode'] = $this->get_toc_display_mode();
            $l10n['sheetSettings'] = $this->get_sheet_settings();

            return $l10n;
        }

        return $l10n;
    }

}

