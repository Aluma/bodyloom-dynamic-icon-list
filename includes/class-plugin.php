<?php

namespace Vybose\RepeaterIconList;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Plugin
{

	private static $instance = null;

	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct()
	{
		add_action('init', [$this, 'init']);

		add_action('elementor/widgets/register', [$this, 'register_elementor_widgets']);

		add_action('elementor/controls/register', [$this, 'register_controls']);

		add_action('rest_api_init', [$this, 'register_rest_routes']);
	}

	public function init()
	{
		// Register Style
		wp_register_style(
			'vybose-repeater-icon-list',
			VYBOSE_REPEATER_ICON_LIST_URL . 'assets/css/vybose-repeater-icon-list.css',
			[],
			VYBOSE_REPEATER_ICON_LIST_VERSION
		);

		// Register Shortcode
		add_shortcode('vybose_repeater_icon_list', [new Shortcode(), 'render']);

		// Register Block
		register_block_type(VYBOSE_REPEATER_ICON_LIST_PATH . 'blocks/icon-list');
	}

	public function register_controls($controls_manager)
	{
		require_once VYBOSE_REPEATER_ICON_LIST_PATH . 'includes/controls/class-choose-text-control.php';
		$controls_manager->register(new \Vybose\RepeaterIconList\Controls\Choose_Text_Control());
	}

	public function register_elementor_widgets($widgets_manager)
	{
		$widget_file = VYBOSE_REPEATER_ICON_LIST_PATH . 'widgets/elementor/class-icon-list-widget.php';
		if (file_exists($widget_file)) {
			require_once $widget_file;
			$widgets_manager->register(new \Vybose\RepeaterIconList\Widgets\Elementor\Icon_List_Widget());

			// Resolves documents saved before the 2.0.0 rename. Absent from the
			// WordPress.org package, where no legacy data exists.
			$legacy_alias = VYBOSE_REPEATER_ICON_LIST_PATH . 'includes/compat/class-legacy-widget-alias.php';

			if (file_exists($legacy_alias)) {
				require_once $legacy_alias;
				$widgets_manager->register(new \Vybose\RepeaterIconList\Compat\Legacy_Widget_Alias());
			}
		}
	}

	public function register_rest_routes()
	{
		register_rest_route(
			'vybose-repeater-icon-list/v1',
			'/fields',
			[
				'methods' => 'GET',
				'callback' => [$this, 'get_field_discovery'],
				'permission_callback' => function (\WP_REST_Request $request) {
					// Field schemas are only exposed to users who can edit the
					// post type actually being asked about. A blanket edit_posts
					// check would let any contributor enumerate every post type.
					$post_type = sanitize_key((string) $request->get_param('post_type')) ?: 'post';
					$post_type_object = get_post_type_object($post_type);

					if (!$post_type_object || empty($post_type_object->cap->edit_posts)) {
						return false;
					}

					return current_user_can($post_type_object->cap->edit_posts);
				},
				'args' => [
					'post_type' => [
						'type' => 'string',
						'sanitize_callback' => 'sanitize_key',
					],
					'refresh' => [
						'type' => 'boolean',
						'default' => false,
					],
				],
			]
		);
	}

	public function get_field_discovery($request)
	{
		$post_type = $request->get_param('post_type') ?: 'post';
		$refresh = (bool) $request->get_param('refresh');

		return rest_ensure_response(Field_Discovery::get_rest_data($post_type, $refresh));
	}
}
