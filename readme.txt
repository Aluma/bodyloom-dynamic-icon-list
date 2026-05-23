=== Bodyloom Dynamic Icon List ===
Contributors: Jimmy Thanki
Tags: icon list, acf, metabox, pods, elementor
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create static and dynamic icon lists from Elementor, blocks, shortcodes, and custom field repeaters.

== Description ==

Bodyloom Dynamic Icon List displays static list items or dynamic list data from ACF Pro, Meta Box, and Pods repeater-style fields. It includes an Elementor widget, a dynamic block, and a shortcode.

Features include:

* Static list items in Elementor.
* Dynamic repeater field output from ACF Pro, Meta Box, or Pods.
* Editor field pickers with manual field-path fallbacks.
* Layout, marker, spacing, color, typography, and link controls.
* Gutenberg block and shortcode rendering for non-Elementor layouts.

The plugin does not connect to external services or load remote JavaScript or CSS.

== Installation ==

1. Upload `bodyloom-dynamic-icon-list` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. Add the Bodyloom Icon List widget, the Bodyloom Icon List block, or the `[bodyloom_icon_list]` shortcode.

== Usage ==

= Elementor =

Use the "Bodyloom Icon List" widget. Choose Static for manually managed list items, or Dynamic to select a repeater field from ACF Pro, Meta Box, or Pods.

= Block Editor =

Use the "Bodyloom Icon List" block and configure the field source in the block sidebar.

= Shortcode =

Use a shortcode such as:

`[bodyloom_icon_list data_type="dynamic" acf_repeater_field_name="my_repeater" dynamic_text_sub_field="text" dynamic_value_sub_field="value" dynamic_link_sub_field="link"]`

== Frequently Asked Questions ==

= Does this require ACF Pro, Meta Box, or Pods? =

No. Static Elementor lists work without those plugins. Dynamic repeater output requires the matching field plugin to be active and configured.

= What happens if a dynamic field plugin is missing? =

The widget, block, or shortcode returns an empty result instead of causing a fatal error.

= Can I still type field paths manually? =

Yes. Field pickers are provided where possible, but manual field-path fields remain available for compatibility and fallback use.

== Screenshots ==

1. Elementor elements panel.
2. Icon list content controls.
3. Icon list style controls.
4. Additional icon list style controls.
5. Rendered frontend example.

== Changelog ==

= 1.1.0 =
* Added editor field pickers with manual field-path fallbacks.
* Improved provider selection for ACF Pro, Meta Box, and Pods.
* Marked dynamic Elementor output as dynamic for cache compatibility.
* Improved block editor metadata and release packaging readiness.
* Hardened rendering and metadata for WordPress.org submission.

= 1.0.0 =
* Initial release.
