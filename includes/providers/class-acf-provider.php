<?php

namespace Vybose\RepeaterIconList\Providers;

use Vybose\RepeaterIconList\Interfaces\Provider;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Acf_Provider implements Provider
{

    public function get_items($settings)
    {
        if (!function_exists('get_field')) {
            return [];
        }

        $repeater_name = \Vybose\RepeaterIconList\Provider_Factory::get_field_path($settings, 'acf_repeater_field_name_manual');
        $repeater_name = $repeater_name ?: \Vybose\RepeaterIconList\Provider_Factory::get_field_path($settings, 'acf_repeater_field_name');

        if (empty($repeater_name)) {
            return [];
        }

        $repeater_name = $this->normalize_path($repeater_name);
        $rows = $this->resolve_rows(get_the_ID(), $repeater_name);

        if (empty($rows)) {
            return [];
        }

        $items = [];
        $text_key = !empty($settings['dynamic_text_sub_field_manual']) ? $settings['dynamic_text_sub_field_manual'] : ($settings['dynamic_text_sub_field'] ?? 'text');
        $value_key = !empty($settings['dynamic_value_sub_field_manual']) ? $settings['dynamic_value_sub_field_manual'] : ($settings['dynamic_value_sub_field'] ?? 'value');
        $link_key = !empty($settings['dynamic_link_sub_field_manual']) ? $settings['dynamic_link_sub_field_manual'] : ($settings['dynamic_link_sub_field'] ?? 'link');

        $text_key = $this->normalize_path($text_key);
        $value_key = $this->normalize_path($value_key);
        $link_key = $this->normalize_path($link_key);

        foreach ($rows as $row) {
            $text = \Vybose\RepeaterIconList\Provider_Factory::get_nested_value($row, $text_key, $repeater_name);
            $value = \Vybose\RepeaterIconList\Provider_Factory::get_nested_value($row, $value_key, $repeater_name);
            $link_raw = \Vybose\RepeaterIconList\Provider_Factory::get_nested_value($row, $link_key, $repeater_name);

            // Normalize Link
            $link = [
                'url' => '',
                'is_external' => '',
                'nofollow' => '',
                'custom_attributes' => '',
            ];

            if (is_array($link_raw) && isset($link_raw['url'])) {
                // ACF Link Field Type
                $link['url'] = $link_raw['url'];
                $link['is_external'] = isset($link_raw['target']) && '_blank' === $link_raw['target'] ? 'on' : '';
                $link['nofollow'] = ''; // ACF doesn't usually have this unless custom
            } elseif (is_string($link_raw)) {
                $link['url'] = $link_raw;
            }

            $items[] = [
                '_id' => uniqid(), // Elementor needs IDs sometimes
                'text' => $text,
                'value' => $value,
                'link' => $link,
                'icon_type' => 'global', // Dynamic items usually use the global icon unless we add an icon subfield
                'icon' => [], // Could add dynamic icon support later
                'text_nowrap' => '',
            ];
        }

        return $items;
    }

    /**
     * Resolve a slash-delimited field path to a flat list of repeater rows.
     *
     * Rows are always sourced from get_field(), which returns *formatted* values
     * keyed by sub-field NAME. The loop helpers (have_rows()/get_row()) expose the
     * *unformatted* value instead, whose rows are keyed by field KEY
     * ('field_abc123') -- see ACF Pro pro/fields/class-acf-field-repeater.php,
     * load_value(). Looking up a sub-field by name in that array always misses,
     * which silently renders every row blank.
     *
     * An intermediate segment may be either a group (a single associative array)
     * or a repeater (a list of rows); rows of a repeater ancestor are flattened.
     *
     * @param int    $post_id Post ID.
     * @param string $path    Field path, e.g. 'benefits' or 'hub_variant_cards/proxy_faqs'.
     * @return array List of rows keyed by sub-field name.
     */
    private function resolve_rows($post_id, $path)
    {
        $path = $this->normalize_path($path);
        $rows = $this->walk_rows($post_id, $path);

        // A seamless ACF clone shows up in a saved path but stores no level of
        // its own, so the full path resolves to nothing. The trailing segment is
        // the repeater's real name -- retry on that.
        if (empty($rows) && false !== strpos($path, '/')) {
            $segments = explode('/', $path);
            $rows = $this->walk_rows($post_id, end($segments));
        }

        return $rows;
    }

    /**
     * Translate ACF field-key segments ('field_abc123') into field names.
     *
     * Settings saved against earlier builds stored field keys, while get_field()
     * returns rows keyed by field name. Without this, key-based settings resolve
     * to nothing.
     *
     * @param string $path Field path.
     * @return string Path with every key segment replaced by its field name.
     */
    private function normalize_path($path)
    {
        $path = \Vybose\RepeaterIconList\Provider_Factory::parse_source_path(is_string($path) ? $path : '')['path'];

        if ('' === $path) {
            return '';
        }

        $segments = [];

        foreach (explode('/', $path) as $segment) {
            if (0 === strpos($segment, 'field_') && function_exists('acf_get_field')) {
                $field = acf_get_field($segment);

                if (!empty($field['name'])) {
                    $segment = $field['name'];
                }
            }

            $segments[] = $segment;
        }

        return implode('/', $segments);
    }

    private function walk_rows($post_id, $path)
    {
        $segments = explode('/', $path);
        $current = get_field(array_shift($segments), $post_id);

        foreach ($segments as $segment) {
            // A repeater ancestor yields a list of rows; a group yields a single array.
            $parents = (is_array($current) && isset($current[0])) ? $current : [$current];
            $next = [];

            foreach ($parents as $parent) {
                if (is_array($parent) && isset($parent[$segment]) && is_array($parent[$segment])) {
                    $next = array_merge($next, array_values($parent[$segment]));
                }
            }

            $current = $next;
        }

        if (!is_array($current)) {
            return [];
        }

        return array_values(array_filter($current, 'is_array'));
    }
}
