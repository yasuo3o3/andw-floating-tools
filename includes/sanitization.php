<?php
if (!defined('ABSPATH')) {
    exit;
}

function andw_sanitize_button_order($value) {
    if (!is_array($value)) {
        return array('top', 'apply', 'contact', 'toc');
    }

    $allowed_buttons = array('top', 'apply', 'contact', 'toc');
    $sanitized = array();

    foreach ($value as $button) {
        if (in_array($button, $allowed_buttons, true)) {
            $sanitized[] = $button;
        }
    }

    return empty($sanitized) ? $allowed_buttons : $sanitized;
}

function andw_sanitize_enabled_buttons($value) {
    if (!is_array($value)) {
        return array('top', 'toc', 'apply', 'contact');
    }

    $allowed_buttons = array('top', 'apply', 'contact', 'toc');
    $sanitized = array();

    foreach ($value as $button) {
        if (in_array($button, $allowed_buttons, true)) {
            $sanitized[] = $button;
        }
    }

    return $sanitized;
}

function andw_sanitize_layout_desktop($value) {
    $allowed_layouts = array('stack-vertical-right-center', 'bottom-right-inline');
    return in_array($value, $allowed_layouts, true) ? $value : 'stack-vertical-right-center';
}

function andw_sanitize_offset($value) {
    if (!is_array($value)) {
        return array('bottom' => 16, 'right' => 16);
    }

    $sanitized = array();
    $sanitized['bottom'] = isset($value['bottom']) ? absint($value['bottom']) : 16;
    $sanitized['right'] = isset($value['right']) ? absint($value['right']) : 16;

    $sanitized['bottom'] = min(max($sanitized['bottom'], 0), 999);
    $sanitized['right'] = min(max($sanitized['right'], 0), 999);

    return $sanitized;
}

function andw_sanitize_toc_depth($value) {
    $depth = absint($value);
    return in_array($depth, array(2, 3, 4), true) ? $depth : 2;
}

function andw_sanitize_toc_offset($value) {
    $offset = absint($value);
    return min(max($offset, 0), 999);
}

function andw_sanitize_url($value) {
    return esc_url_raw(trim($value));
}

function andw_sanitize_text($value) {
    return sanitize_text_field($value);
}

function andw_sanitize_target($value) {
    $allowed_targets = array('_self', '_blank');
    return in_array($value, $allowed_targets, true) ? $value : '_blank';
}

function andw_sanitize_preset_id($value) {
    $allowed_presets = array('default', 'light', 'dark', 'round-small', 'round-medium', 'round-large', 'square-small', 'square-medium', 'square-large');
    return in_array($value, $allowed_presets, true) ? $value : 'default';
}

function andw_sanitize_z_index($value) {
    $z_index = absint($value);
    return min(max($z_index, 1), 9999);
}

function andw_sanitize_utm_key($value) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', sanitize_text_field($value));
}

function andw_sanitize_toc_display_mode($value) {
    $allowed_modes = array('anchor-sheet', 'drawer', 'anchor-panel');
    return in_array($value, $allowed_modes, true) ? $value : 'anchor-sheet';
}

function andw_sanitize_sheet_max_width($value) {
    $width = absint($value);
    return min(max($width, 200), 800);
}

function andw_sanitize_max_height_vh($value) {
    $vh = absint($value);
    return min(max($vh, 20), 100);
}

function andw_sanitize_gap($value) {
    $gap = absint($value);
    return min(max($gap, 0), 100);
}

function andw_sanitize_anchor_offset_y($value) {
    $offset = absint($value);
    return min(max($offset, 0), 50);
}

function andw_sanitize_initial_state($value) {
    $allowed_states = array('peek', 'closed');
    return in_array($value, $allowed_states, true) ? $value : 'closed';
}

function andw_sanitize_svg_path($value) {
    // SVGコンテンツの基本的なサニタイゼーション
    if (empty($value)) {
        return '';
    }

    // 危険なスクリプトタグやイベントハンドラーを除去
    $value = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $value);
    $value = preg_replace('/on\w+\s*=\s*["\'].*?["\']/i', '', $value);
    $value = preg_replace('/javascript\s*:/i', '', $value);

    // 許可するSVGタグのみを通す（svg要素も含める）
    $allowed_tags = '<svg><path><circle><rect><line><polygon><polyline><ellipse><g><defs><use><clipPath><mask>';
    $value = strip_tags($value, $allowed_tags);

    return trim($value);
}

function andw_add_utm_to_url($url, $utm_params) {
    if (empty($url) || empty($utm_params)) {
        return $url;
    }

    $parsed_url = parse_url($url);

    if ($parsed_url === false) {
        return $url;
    }

    $query_params = array();
    if (isset($parsed_url['query'])) {
        parse_str($parsed_url['query'], $query_params);
    }

    $has_utm = false;
    foreach ($query_params as $key => $value) {
        if (strpos($key, 'utm_') === 0) {
            $has_utm = true;
            break;
        }
    }

    if ($has_utm) {
        return $url;
    }

    foreach ($utm_params as $key => $value) {
        if (!empty($value)) {
            $query_params['utm_' . $key] = $value;
        }
    }

    $new_query = http_build_query($query_params);

    $result = '';
    if (isset($parsed_url['scheme'])) {
        $result .= $parsed_url['scheme'] . '://';
    }
    if (isset($parsed_url['host'])) {
        $result .= $parsed_url['host'];
    }
    if (isset($parsed_url['port'])) {
        $result .= ':' . $parsed_url['port'];
    }
    if (isset($parsed_url['path'])) {
        $result .= $parsed_url['path'];
    }
    if (!empty($new_query)) {
        $result .= '?' . $new_query;
    }
    if (isset($parsed_url['fragment'])) {
        $result .= '#' . $parsed_url['fragment'];
    }

    return $result;
}