<?php
if (!defined('ABSPATH')) {
    exit;
}

class Of_Floating_Tools_Render {
    private $options;
    private $toc_instance;

    public function __construct() {
        $this->options = get_option('of_floating_tools_options', array());
        $this->toc_instance = Of_Floating_Tools_TOC::get_instance();

        add_action('wp_footer', array($this, 'render_floating_tools'));
        add_action('wp_localize_script', array($this, 'localize_toc_data'), 10, 3);
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

        return true;
    }

    private function get_enabled_buttons() {
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['enabled'])) {
            return of_sanitize_enabled_buttons($block_attributes['enabled']);
        }

        return isset($this->options['enabled_buttons']) ? $this->options['enabled_buttons'] : array();
    }

    private function get_button_order() {
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['order'])) {
            return of_sanitize_button_order($block_attributes['order']);
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
            'of-floating-tools',
            'of-preset-' . esc_attr($preset_id),
            'of-layout-' . esc_attr($layout_desktop),
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
            return of_sanitize_layout_desktop($block_attributes['layoutDesktop']);
        }

        return isset($this->options['layout_desktop']) ? $this->options['layout_desktop'] : 'stack-vertical-right-center';
    }

    private function render_button($button_type) {
        $button_config = $this->get_button_config($button_type);

        if (!$button_config) {
            return;
        }

        $classes = array(
            'of-floating-button',
            'of-button-' . esc_attr($button_type),
        );

        if ($button_type === 'top') {
            $classes[] = 'of-scroll-trigger';
        }

        $attributes = array(
            'class' => implode(' ', $classes),
            'type' => 'button',
            'aria-label' => $button_config['aria_label'],
        );

        if ($button_type === 'toc') {
            $attributes['data-toc-toggle'] = 'true';
            $attributes['aria-expanded'] = 'false';
            $attributes['aria-controls'] = 'of-toc-drawer';
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
            echo '<span class="of-button-label">' . esc_html($button_config['label']) . '</span>';
        }

        echo '</button>';
    }

    private function get_button_config($button_type) {
        $block_attributes = $this->get_block_attributes();

        switch ($button_type) {
            case 'top':
                return array(
                    'aria_label' => __('ページトップへ', 'andw-floating-tools'),
                    'label' => '',
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
                if (!$this->toc_instance->has_toc()) {
                    return false;
                }

                return array(
                    'aria_label' => __('目次', 'andw-floating-tools'),
                    'label' => '',
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

        $url_with_utm = of_add_utm_to_url($url, $utm_params);

        return esc_url($url_with_utm);
    }

    private function get_button_icon($button_type) {
        $icons = array(
            'top' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3l-8 8h5v10h6V11h5l-8-8z"/></svg>',
            'apply' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 11H7v9a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2v2H9v2zm0-4V5a2 2 0 012-2h2a2 2 0 012 2v2h3a1 1 0 011 1v1H6V8a1 1 0 011-1h2z"/></svg>',
            'contact' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4.7l-8 5.334L4 8.7V6.297l8 5.333 8-5.333V8.7z"/></svg>',
            'toc' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 9h14V7H3v2zm0 4h14v-2H3v2zm0 4h14v-2H3v2zm16 0h2v-2h-2v2zm0-10v2h2V7h-2zm0 6h2v-2h-2v2z"/></svg>',
        );

        return isset($icons[$button_type]) ? $icons[$button_type] : '';
    }

    private function render_toc_drawer() {
        $enabled_buttons = $this->get_enabled_buttons();

        if (!in_array('toc', $enabled_buttons, true) || !$this->toc_instance->has_toc()) {
            return;
        }

        echo '<div id="of-toc-drawer" class="of-toc-drawer" role="dialog" aria-modal="true" aria-labelledby="of-toc-title" aria-hidden="true">';
        echo '<div class="of-toc-backdrop" data-toc-close="true"></div>';
        echo '<div class="of-toc-content">';
        echo '<div class="of-toc-header">';
        echo '<button type="button" class="of-toc-close" data-toc-close="true" aria-label="' . esc_attr__('目次を閉じる', 'andw-floating-tools') . '">';
        echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
        echo '</button>';
        echo '</div>';
        echo '<div class="of-toc-body">';
        echo $this->toc_instance->render_toc_html();
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private function render_custom_css() {
        $offset_desktop = $this->get_offset('desktop');
        $offset_mobile = $this->get_offset('mobile');
        $offset_tablet = $this->get_offset('tablet');
        $z_index = isset($this->options['z_index']) ? $this->options['z_index'] : 999;

        echo '<style id="of-floating-tools-inline-css">';
        echo '.of-floating-tools { z-index: ' . esc_attr($z_index) . '; }';

        echo '@media (min-width: 1024px) {';
        echo '.of-floating-tools { bottom: ' . esc_attr($offset_desktop['bottom']) . 'px; right: ' . esc_attr($offset_desktop['right']) . 'px; }';
        echo '}';

        echo '@media (min-width: 768px) and (max-width: 1023px) {';
        echo '.of-floating-tools { bottom: ' . esc_attr($offset_tablet['bottom']) . 'px; right: ' . esc_attr($offset_tablet['right']) . 'px; }';
        echo '}';

        echo '@media (max-width: 767px) {';
        echo '.of-floating-tools { bottom: ' . esc_attr($offset_mobile['bottom']) . 'px; right: ' . esc_attr($offset_mobile['right']) . 'px; }';
        echo '}';

        echo '</style>';
    }

    private function get_offset($device) {
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['offset' . ucfirst($device)])) {
            return of_sanitize_offset($block_attributes['offset' . ucfirst($device)]);
        }

        $key = 'offset_' . $device;
        return isset($this->options[$key]) ? $this->options[$key] : array('bottom' => 16, 'right' => 16);
    }

    public function localize_toc_data($handle, $object_name, $l10n) {
        if ($handle === 'of-floating-tools-app' && $object_name === 'ofFloatingTools') {
            $l10n['tocOffset'] = $this->toc_instance->get_toc_scroll_offset();
            $l10n['hasToc'] = $this->toc_instance->has_toc();

            return $l10n;
        }

        return $l10n;
    }
}