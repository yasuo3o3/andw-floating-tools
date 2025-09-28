<?php
if (!defined('ABSPATH')) {
    exit;
}

class Andw_Floating_Tools_TOC {
    private static $instance = null;
    private $toc_data = array();

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp', array($this, 'init_toc'));
    }

    public function init_toc() {
        if (!is_singular()) {
            return;
        }

        add_filter('the_content', array($this, 'process_content'), 10);
    }

    public function process_content($content) {
        if (!$this->should_process_content()) {
            return $content;
        }

        $depth = $this->get_toc_depth();

        $headings = $this->extract_headings($content, $depth);

        if (empty($headings)) {
            return $content;
        }

        $content = $this->add_ids_to_headings($content, $headings);
        $this->toc_data = $this->build_toc_structure($headings);

        return $content;
    }

    private function should_process_content() {
        global $post;

        if (!$post || is_admin() || wp_doing_ajax()) {
            return false;
        }

        // ブロック設定優先・メイン設定フォールバックで目次の有効性をチェック
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['enabled'])) {
            // ブロック設定がある場合：ブロック設定の目次有効性をチェック
            return in_array('toc', $block_attributes['enabled'], true);
        }

        // ブロック設定がない場合：メイン設定の目次有効性をチェック
        $options = get_option('andw_floating_tools_options', array());
        $enabled_buttons = isset($options['enabled_buttons']) ? $options['enabled_buttons'] : array();

        return in_array('toc', $enabled_buttons, true);
    }

    private function get_toc_depth() {
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['tocDepth'])) {
            return andw_sanitize_toc_depth($block_attributes['tocDepth']);
        }

        $options = get_option('andw_floating_tools_options', array());
        return isset($options['toc_default_depth']) ? andw_sanitize_toc_depth($options['toc_default_depth']) : 2;
    }

    public function get_toc_scroll_offset() {
        $block_attributes = $this->get_block_attributes();

        if (isset($block_attributes['tocOffset'])) {
            return andw_sanitize_toc_offset($block_attributes['tocOffset']);
        }

        $options = get_option('andw_floating_tools_options', array());
        return isset($options['toc_scroll_offset']) ? andw_sanitize_toc_offset($options['toc_scroll_offset']) : 72;
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

    private function extract_headings($content, $depth) {
        $headings = array();

        $tag_pattern = $this->build_heading_pattern($depth);

        if (preg_match_all($tag_pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $level = intval($match[1]);
                $existing_id = !empty($match[2]) ? $match[3] : '';
                $text = wp_strip_all_tags($match[4]);
                $text = trim($text);

                if (!empty($text)) {
                    $headings[] = array(
                        'level' => $level,
                        'text' => $text,
                        'existing_id' => $existing_id,
                        'full_match' => $match[0],
                    );
                }
            }
        }

        return $headings;
    }

    private function build_heading_pattern($depth) {
        $levels = array();
        for ($i = 2; $i <= $depth; $i++) {
            $levels[] = $i;
        }

        $level_pattern = implode('|', $levels);
        return '/<h(' . $level_pattern . ')(\s+id=["\']([^"\']*)["\'])?[^>]*>(.*?)<\/h[' . $level_pattern . ']>/i';
    }

    private function add_ids_to_headings($content, &$headings) {
        $used_ids = array();

        foreach ($headings as &$heading) {
            if (!empty($heading['existing_id'])) {
                $id = $heading['existing_id'];
            } else {
                $id = $this->generate_id_from_text($heading['text']);
                $id = $this->ensure_unique_id($id, $used_ids);
            }

            $heading['id'] = $id;
            $used_ids[] = $id;

            if (empty($heading['existing_id'])) {
                $new_heading = preg_replace(
                    '/(<h' . $heading['level'] . ')([^>]*)(>)/i',
                    '$1$2 id="' . esc_attr($id) . '"$3',
                    $heading['full_match']
                );

                $content = str_replace($heading['full_match'], $new_heading, $content);
            }
        }

        return $content;
    }

    private function generate_id_from_text($text) {
        $text = remove_accents($text);

        $text = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $text);

        $text = preg_replace('/\s+/', '-', trim($text));

        $text = strtolower($text);

        $text = trim($text, '-');

        if (empty($text)) {
            $text = 'heading';
        }

        return $text;
    }

    private function ensure_unique_id($id, $used_ids) {
        $original_id = $id;
        $counter = 2;

        while (in_array($id, $used_ids, true)) {
            $id = $original_id . '-' . $counter;
            $counter++;
        }

        return $id;
    }

    private function build_toc_structure($headings) {
        if (empty($headings)) {
            return array();
        }

        $toc_items = array();
        $stack = array();

        foreach ($headings as $heading) {
            $item = array(
                'id' => $heading['id'],
                'text' => $heading['text'],
                'level' => $heading['level'],
                'children' => array(),
            );

            while (!empty($stack) && end($stack)['level'] >= $heading['level']) {
                array_pop($stack);
            }

            if (empty($stack)) {
                $toc_items[] = $item;
            } else {
                $parent = &$stack[count($stack) - 1];
                $parent['children'][] = $item;
            }

            $stack[] = &$item;
            unset($item);
        }

        return $toc_items;
    }

    public function get_toc_data() {
        return $this->toc_data;
    }

    public function render_toc_html() {
        if (empty($this->toc_data)) {
            // 見出しがない場合は適切なメッセージを表示
            $html = '<nav class="andw-toc-nav andw-toc-empty" aria-label="' . esc_attr__('目次', 'andw-floating-tools') . '">';
            $html .= '<div class="andw-toc-empty-message">';
            $html .= esc_html__('このページには見出しがありません', 'andw-floating-tools');
            $html .= '</div>';
            $html .= '</nav>';
            return $html;
        }

        $html = '<nav class="andw-toc-nav" aria-label="' . esc_attr__('目次', 'andw-floating-tools') . '">';
        $html .= $this->render_toc_list($this->toc_data);
        $html .= '</nav>';

        return $html;
    }

    private function render_toc_list($items, $level = 1) {
        if (empty($items)) {
            return '';
        }

        $nested_class = $level > 1 ? ' andw-toc-nested' : '';
        $html = '<ol class="andw-toc-list andw-toc-level-' . esc_attr($level) . $nested_class . '">';

        foreach ($items as $item) {
            $level_class = 'andw-toc-level-' . $item['level'];
            $html .= '<li class="andw-toc-item ' . esc_attr($level_class) . '">';
            $html .= '<a href="#' . esc_attr($item['id']) . '" class="andw-toc-link ' . esc_attr($level_class) . '">';
            $html .= esc_html($item['text']);
            $html .= '</a>';

            if (!empty($item['children'])) {
                $html .= $this->render_toc_list($item['children'], $level + 1);
            }

            $html .= '</li>';
        }

        $html .= '</ol>';

        return $html;
    }

    public function has_toc() {
        return !empty($this->toc_data);
    }
}