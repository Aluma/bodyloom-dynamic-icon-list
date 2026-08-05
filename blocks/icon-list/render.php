<?php
/**
 * Render callback for the Vybose Repeater Icon List block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

use Vybose\RepeaterIconList\Provider_Factory;
use Vybose\RepeaterIconList\Renderer;

if (!defined('ABSPATH')) {
    exit;
}

// Map block style attributes to plugin settings format if needed
$vybose_settings = $attributes;

// Ensure defaults
$vybose_settings['data_type'] = isset($vybose_settings['data_type']) ? $vybose_settings['data_type'] : 'static';

// Get items from provider
$vybose_provider = Provider_Factory::get_provider($vybose_settings);
$vybose_items = $vybose_provider->get_items($vybose_settings);

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Renderer escapes all dynamic values during construction.
echo Renderer::render($vybose_settings, $vybose_items);
