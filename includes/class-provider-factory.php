<?php

namespace Bodyloom\DynamicIconList;

use Bodyloom\DynamicIconList\Interfaces\Provider;
use Bodyloom\DynamicIconList\Providers\Static_Provider;
use Bodyloom\DynamicIconList\Providers\Acf_Provider;
use Bodyloom\DynamicIconList\Providers\Pods_Provider;
use Bodyloom\DynamicIconList\Providers\Metabox_Provider;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Provider_Factory
{

    public static function get_provider($settings): Provider
    {
        $type = isset($settings['data_type']) ? $settings['data_type'] : 'static';

        if ('static' === $type) {
            return new Static_Provider();
        }

        $source = isset($settings['dynamic_source']) ? $settings['dynamic_source'] : 'acf';
        $field_path = isset($settings['acf_repeater_field_name']) ? $settings['acf_repeater_field_name'] : '';
        $parsed = self::parse_source_path($field_path);

        if (!empty($parsed['source'])) {
            $source = $parsed['source'];
        }

        switch ($source) {
            case 'pods':
                return new Pods_Provider();
            case 'metabox':
                return new Metabox_Provider();
            case 'acf':
            default:
                return new Acf_Provider();
        }
    }

    public static function parse_source_path($path)
    {
        $path = is_string($path) ? trim($path) : '';

        if (preg_match('/^(acf|pods|metabox):(.+)$/', $path, $matches)) {
            return [
                'source' => $matches[1],
                'path' => $matches[2],
            ];
        }

        return [
            'source' => '',
            'path' => $path,
        ];
    }

    public static function get_field_path($settings, $key)
    {
        $parsed = self::parse_source_path($settings[$key] ?? '');

        return $parsed['path'];
    }

    public static function get_nested_value($data, $path, $root_path = '')
    {
        $path = is_string($path) ? trim($path) : '';
        $root_path = self::parse_source_path($root_path)['path'];

        if ($root_path && 0 === strpos($path, $root_path . '/')) {
            $path = substr($path, strlen($root_path) + 1);
        }

        if ('' === $path) {
            return '';
        }

        $value = $data;

        foreach (explode('/', $path) as $part) {
            if (is_array($value) && array_key_exists($part, $value)) {
                $value = $value[$part];
            } else {
                return '';
            }
        }

        return $value;
    }
}
