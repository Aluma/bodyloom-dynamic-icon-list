<?php
/**
 * Legacy widget alias — NOT distributed on WordPress.org.
 *
 * Documents saved before the 2.0.0 rename store the widget under its previous
 * name. Elementor resolves a saved element by that stored string, so without an
 * alias every pre-2.0.0 instance would render as "widget not found" while its
 * settings sat intact but unreachable in the database.
 *
 * This registers the old name as a hidden subclass of the current widget: saved
 * documents resolve and render exactly as before, but the legacy entry never
 * appears in the Elementor panel, so nothing new can be created against it.
 *
 * Excluded from the WordPress.org package by build-wordpress-org-packages.sh,
 * since a fresh install has no legacy data for it to rescue.
 *
 * @package Vybose\RepeaterIconList
 */

namespace Vybose\RepeaterIconList\Compat;

use Vybose\RepeaterIconList\Widgets\Elementor\Icon_List_Widget;

if (!defined('ABSPATH')) {
    exit;
}

class Legacy_Widget_Alias extends Icon_List_Widget
{
    /**
     * The pre-2.0.0 widget name as stored in _elementor_data.
     */
    public function get_name()
    {
        return 'bodyloom-dynamic-icon-list';
    }

    /**
     * Keep the alias out of the widget panel so it cannot be inserted anew.
     */
    public function show_in_panel()
    {
        return false;
    }

    /**
     * Swap only the widget-name class, preserving everything else the parent adds.
     *
     * Icon_List_Widget overrides this method to append its own base class, which
     * 59 CSS rules compound against. Returning a freshly built string here would
     * discard that class and kill every layout rule -- collapsing the text
     * wrapper to zero width. So delegate to the parent and rewrite one token.
     */
    public function get_html_wrapper_class()
    {
        $classes = explode(' ', parent::get_html_wrapper_class());
        $legacy = 'elementor-widget-' . $this->get_name();
        $canonical = 'elementor-widget-' . parent::get_name();

        foreach ($classes as $index => $class) {
            if ($class === $legacy) {
                $classes[$index] = $canonical;
            }
        }

        return implode(' ', $classes);
    }
}
