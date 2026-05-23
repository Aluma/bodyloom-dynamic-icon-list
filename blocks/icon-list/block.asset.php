<?php

if (!defined('ABSPATH')) {
    exit;
}

return [
    'dependencies' => [
        'wp-api-fetch',
        'wp-block-editor',
        'wp-blocks',
        'wp-components',
        'wp-element',
        'wp-i18n',
        'wp-server-side-render',
    ],
    'version' => defined('BODYLOOM_DYNAMIC_ICON_LIST_VERSION') ? BODYLOOM_DYNAMIC_ICON_LIST_VERSION : '1.1.0',
];
