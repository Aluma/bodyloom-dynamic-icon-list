=== Bodyloom Dynamic Icon List ===
Contributors: Jimmy Thanki
Tags: icon list, acf, metabox, pods, elementor
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create static and dynamic icon lists for Elementor, the Block Editor, and shortcodes.

== Description ==

Bodyloom Dynamic Icon List displays static list items or dynamic icon-list data from ACF Pro, Meta Box, and Pods repeater-style fields. It includes an Elementor widget, a dynamic block, and a shortcode so the same content pattern can be used across page builders and native WordPress content.

Features include:

* Static list items in Elementor.
* Dynamic repeater field output from ACF Pro, Meta Box, or Pods.
* Editor field pickers for discovered repeater and subfield paths.
* Manual field-path fallbacks for compatibility, nested paths, and undiscovered field layouts.
* Layout, marker, spacing, color, typography, and link controls.
* Gutenberg block and shortcode rendering for non-Elementor layouts.
* Cache-aware dynamic Elementor rendering.
* Defensive provider handling so missing optional plugins do not fatal the site.

The plugin does not connect to external services or load remote JavaScript or CSS.

== Installation ==

1. Upload `bodyloom-dynamic-icon-list` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. Add the Bodyloom Icon List widget, the Bodyloom Icon List block, or the `[bodyloom_icon_list]` shortcode.

== Usage ==

= Elementor =

Use the "Bodyloom Icon List" widget. Choose Static for manually managed list items, or Dynamic to use repeater data from ACF Pro, Meta Box, or Pods.

= Block Editor =

Use the "Bodyloom Icon List" block and configure the source, repeater field, text field, value field, and optional link field in the block sidebar.

= Shortcode =

Use a shortcode such as:
`[bodyloom_icon_list data_type="dynamic" dynamic_source="acf" acf_repeater_field_name="my_repeater" dynamic_text_sub_field="text" dynamic_value_sub_field="value" dynamic_link_sub_field="link"]`

== Frequently Asked Questions ==

= Does this require ACF Pro, Meta Box, or Pods? =

No. Static Elementor lists work without those plugins. Dynamic repeater output requires the matching field plugin to be active and configured.

= What happens if a dynamic field plugin is missing? =

The widget, block, or shortcode returns an empty result instead of causing a fatal error.

= Can I still type field paths manually? =

Yes. Field pickers are provided where possible, but manual field-path fields remain available for compatibility, nested paths, and fallback use.

== Screenshots ==

1. Elementor elements panel: `assets/screenshots/elementor-elements-panel-bodyloom-icon-list-and-toggles-plugins.png`
2. Icon list content controls: `assets/screenshots/edit-bodyloom-icon-list-content-edit-panel-1.png`
3. Additional icon list content controls: `assets/screenshots/edit-bodyloom-icon-list-content-edit-panel-2.png`
4. Rendered frontend example: `assets/screenshots/bodyloom-dynamic-icon-list-rendered.jpg`

== Changelog ==

= 1.1.1 =
* Field paths may be given as either ACF field names or ACF field keys; keys are resolved to names before lookup.
* Fixed paths that cross a seamless ACF clone field, which contributes no stored level of its own.
* Fixed nested field paths whose parent is a repeater rather than a group, and paths deeper than two segments.
* Field paths saved by version 1.0.x (bare repeater name and bare sub-field names) continue to resolve.

= 1.1.0 =
* Added editor field pickers with manual field-path fallbacks.
* Added provider support for ACF Pro, Meta Box, and Pods repeater data.
* Marked dynamic Elementor output as dynamic for cache compatibility.
* Improved block editor metadata and release packaging readiness.
* Hardened rendering and metadata for WordPress.org submission.
* Updated screenshot references and documentation for the v1.1.0 feature set.

= 1.0.0 =
* Initial release.
