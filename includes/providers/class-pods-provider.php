<?php

namespace Vybose\RepeaterIconList\Providers;

use Vybose\RepeaterIconList\Interfaces\Provider;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Pods_Provider implements Provider
{

    public function get_items($settings)
    {
        if (!function_exists('pods')) {
            return [];
        }

        $repeater_name = \Vybose\RepeaterIconList\Provider_Factory::get_field_path($settings, 'acf_repeater_field_name_manual'); // Reusing the control name for simplicity
        $repeater_name = $repeater_name ?: \Vybose\RepeaterIconList\Provider_Factory::get_field_path($settings, 'acf_repeater_field_name');

        if (empty($repeater_name)) {
            return [];
        }

        $pod = pods(get_post_type(), get_the_ID());

        if (!$pod || !$pod->exists()) {
            return [];
        }

        $rows = $pod->field($repeater_name);

        if (!$rows || !is_array($rows)) {
            return [];
        }

        $items = [];
        $text_key = !empty($settings['dynamic_text_sub_field_manual']) ? $settings['dynamic_text_sub_field_manual'] : ($settings['dynamic_text_sub_field'] ?? 'text');
        $value_key = !empty($settings['dynamic_value_sub_field_manual']) ? $settings['dynamic_value_sub_field_manual'] : ($settings['dynamic_value_sub_field'] ?? 'value');
        $link_key = !empty($settings['dynamic_link_sub_field_manual']) ? $settings['dynamic_link_sub_field_manual'] : ($settings['dynamic_link_sub_field'] ?? 'link');

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
                $link['url'] = $link_raw['url'];
            } elseif (is_string($link_raw)) {
                $link['url'] = $link_raw;
            }

            $items[] = [
                '_id' => uniqid(),
                'text' => $text,
                'value' => $value,
                'link' => $link,
                'icon_type' => 'global',
                'icon' => [],
                'text_nowrap' => '',
            ];
        }

        return $items;
    }
}
