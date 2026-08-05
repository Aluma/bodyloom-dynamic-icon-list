<?php
/**
 * Vybose Icon-List Plugin v1
 **/
namespace Vybose\RepeaterIconList\Widgets\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;
use Vybose\RepeaterIconList\Field_Discovery;
use Vybose\RepeaterIconList\Provider_Factory;


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


/**
 * Addon icon list widget.
 *
 * Addon widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.2.0
 */
class Icon_List_Widget extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.2.0
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'vybose-repeater-icon-list';
    }

    /**
     * Get widget title.
     *
     * Retrieve widget title.
     *
     * @since 1.2.0
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Vybose Icon List', 'vybose-repeater-icon-list');
    }

    /**
     * Get widget icon.
     *
     * Retrieve widget icon.
     *
     * @since 1.2.0
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eicon-bullet-list';
    }

    /**
     * Get widget unique keywords.
     *
     * Retrieve the list of unique keywords the widget belongs to.
     *
     * @since 1.2.0
     *
     * @return array Widget unique keywords.
     */
    public function get_unique_keywords()
    {
        return [
            'icon list',
            'icon',
            'list',
        ];
    }

    /**
     * Specifying caching of the widget by default.
     *
     * @since 1.14.0
     */
    protected function is_dynamic_content(): bool
    {
        return true;
    }

    /**
     * Get style dependencies.
     *
     * Retrieve the list of style dependencies the widget requires.
     *
     * @since 1.16.0
     *
     * @return array Widget style dependencies.
     */
    public function get_style_depends(): array
    {
        return [
            'vybose-repeater-icon-list',
        ];
    }



    /**
     * Get HTML wrapper class.
     *
     * Retrieve the widget container class.
     *
     * Can be used to override the container class for specific widgets.
     *
     * @since 1.2.0
     *
     * @return string Widget container class.
     */
    protected function get_html_wrapper_class()
    {
        $parent_classes = explode(' ', parent::get_html_wrapper_class());
        $widget_class = 'vybose-widget-icon-list';

        if (!in_array($widget_class, $parent_classes, true)) {
            $parent_classes[] = $widget_class;
        }

        return implode(' ', $parent_classes);
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.2.0
     */
    protected function register_controls()
    {
        $this->register_list_content_section();

        $this->register_list_style_section();

        $this->register_item_style_section();

        $this->register_value_style_section();

        $this->register_icon_style_section();

        $this->register_title_style_section();
    }

    /**
     * Register widget list content section.
     *
     * Adds icon list widget `list content` settings section controls.
     *
     * @since 1.2.0
     */
    protected function register_list_content_section()
    {
        $this->start_controls_section(
            'section_list',
            ['label' => __('Icon List', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'data_type',
            [
                'label' => __('Data Type', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => 'choose_text',
                'options' => [
                    'static' => [
                        'title' => __('Static', 'vybose-repeater-icon-list'),
                    ],
                    'dynamic' => [
                        'title' => __('Dynamic', 'vybose-repeater-icon-list'),
                    ],
                ],
                'default' => 'static',
                'toggle' => false,
                'render_type' => 'template',
            ]
        );

        $this->register_list_static_content();

        $this->register_list_dynamic_content();

        $this->add_control(
            'items_heading',
            [
                'label' => __('Items', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'item_layout',
            [
                'label' => __('Layout', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => 'choose_text',
                'options' => [
                    'row' => [
                        'title' => __('Row', 'vybose-repeater-icon-list'),
                    ],
                    'column' => [
                        'title' => __('Column', 'vybose-repeater-icon-list'),
                    ],
                ],
                'default' => 'row',
                'toggle' => false,
                'prefix_class' => 'vybose-widget-layout-',
                'render_type' => 'template',
            ]
        );

        $this->add_responsive_control(
            'items_align',
            [
                'label' => __('Alignment', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-right',
                    ],
                    'stretch' => [
                        'title' => __('Stretch', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-stretch',
                    ],
                ],
                'default' => 'stretch',
                'toggle' => false,
                'prefix_class' => 'vybose-widget%s-align-',
                'condition' => ['item_layout' => 'row'],
                'render_type' => 'template',
            ]
        );

        $this->add_responsive_control(
            'items_align_column',
            [
                'label' => __('Alignment', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => false,
                'prefix_class' => 'vybose-widget%s-align-column-',
                'condition' => ['item_layout' => 'column'],
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'item_direction',
            [
                'label' => __('Direction', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => 'choose_text',
                'options' => [
                    'default' => [
                        'title' => __('Default', 'vybose-repeater-icon-list'),
                    ],
                    'reverse' => [
                        'title' => __('Reverse', 'vybose-repeater-icon-list'),
                    ],
                ],
                'default' => 'default',
                'toggle' => false,
                'prefix_class' => 'vybose-widget-direction-',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'value_heading',
            [
                'label' => __('Value', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'value_position',
            [
                'label' => __('Position', 'vybose-repeater-icon-list'),
                'type' => 'choose_text',
                'options' => [
                    'bottom' => [
                        'title' => __('Bottom', 'vybose-repeater-icon-list'),
                    ],
                    'inline' => [
                        'title' => __('Inline', 'vybose-repeater-icon-list'),
                    ],
                ],
                'default' => 'bottom',
                'label_block' => false,
                'toggle' => false,

                'prefix_class' => 'vybose-value-position-',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'marker_heading',
            [
                'label' => __('Marker', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'global_marker',
            [
                'label' => __('Type', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => 'choose_text',
                'options' => [
                    'icon' => [
                        'title' => __('Icon', 'vybose-repeater-icon-list'),
                    ],
                    'numeric' => [
                        'title' => __('Numeric', 'vybose-repeater-icon-list'),
                    ],
                ],
                'default' => 'icon',
                'toggle' => false,
                'prefix_class' => 'vybose-widget-marker-element-',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'global_icon',
            [
                'label' => __('Global Icon', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-check',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'condition' => ['global_marker' => 'icon'],
            ]
        );

        $this->add_control(
            'marker_view',
            [
                'label' => __('View', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => 'choose_text',
                'options' => [
                    'default' => [
                        'title' => __('Default', 'vybose-repeater-icon-list'),
                    ],
                    'stacked' => [
                        'title' => __('Stacked', 'vybose-repeater-icon-list'),
                    ],
                    'framed' => [
                        'title' => __('Framed', 'vybose-repeater-icon-list'),
                    ],
                ],
                'default' => 'default',
                'prefix_class' => 'vybose-widget-marker-view-',
                'toggle' => false,
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'marker_shape',
            [
                'label' => __('Shape', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => 'choose_text',
                'options' => [
                    'circle' => [
                        'title' => __('Circle', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-circle-o',
                    ],
                    'square' => [
                        'title' => __('Square', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-square-o',
                    ],
                ],
                'default' => 'circle',
                'condition' => ['marker_view!' => 'default'],
                'prefix_class' => 'vybose-widget-marker-shape-',
                'toggle' => false,
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'link_click',
            [
                'label' => __('Apply Link To:', 'vybose-repeater-icon-list'),
                'label_block' => true,
                'type' => 'choose_text',
                'options' => [
                    'text' => [
                        'title' => __('Text', 'vybose-repeater-icon-list'),
                    ],
                    'value' => [
                        'title' => __('Value', 'vybose-repeater-icon-list'),
                    ],
                    'full_width' => [
                        'title' => __('Full Width', 'vybose-repeater-icon-list'),
                    ],
                ],
                'default' => 'text',
                'toggle' => false,

                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_heading',
            [
                'label' => __('Title', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'vybose-repeater-icon-list'),
                'label_block' => true,
                'show_label' => false,
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => __('HTML Tag', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
                'condition' => ['title!' => ''],
                'render_type' => 'template',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register widget list static content section.
     *
     * Adds icon list widget static content settings controls.
     *
     * @since 1.2.0
     */
    protected function register_list_static_content()
    {
        $repeater = new Repeater();

        $repeater->add_control(
            'text',
            [
                'label' => __('Text', 'vybose-repeater-icon-list'),
                'label_block' => true,
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
            ]
        );

        $repeater->add_control(
            'value',
            [
                'label' => __('Value', 'vybose-repeater-icon-list'),
                'label_block' => true,
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => __('Link', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::URL,
                'dynamic' => ['active' => true],
            ]
        );

        $repeater->add_control(
            'icon_type',
            [
                'label' => __('Icon Type', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => 'choose_text',
                'options' => [
                    'global' => [
                        'title' => __('Global', 'vybose-repeater-icon-list'),
                    ],
                    'custom' => [
                        'title' => __('Custom', 'vybose-repeater-icon-list'),
                    ],
                ],
                'default' => 'global',
                'toggle' => false,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => __('Custom Icon', 'vybose-repeater-icon-list'),
                'label_block' => false,
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'condition' => ['icon_type' => 'custom'],
            ]
        );

        $repeater->add_control(
            'text_nowrap',
            [
                'label' => __('Prevent Text Wrapping', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SWITCHER,
                'selectors_dictionary' => [
                    'yes' => 'nowrap',
                    '' => 'normal',
                ],
                'default' => '',
                'render_type' => 'ui',
                'description' => __('Display text in a single line without wrapping.', 'vybose-repeater-icon-list'),
                'selectors' => [
                    '{{WRAPPER}} .vybose-widget-icon-list-item-text-inner {{CURRENT_ITEM}}' => '--vybose-text-nowrap: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'icon_list',
            [
                'label' => __('Items', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '<span class="vybose-repeat-item-num"></span>. {{{ text }}} {{{ elementor.helpers.renderIcon( this, icon, {}, "i", "panel" ) }}}',
                'condition' => ['data_type' => 'static'],
            ]
        );
    }

    /**
     * Register widget list dynamic content section.
     *
     * Adds icon list widget dynamic content settings controls.
     *
     * @since 1.2.0
     */
    protected function register_list_dynamic_content()
    {
        $this->add_control(
            'dynamic_source',
            array(
                'label' => __('Dynamic Source', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'acf' => __('ACF', 'vybose-repeater-icon-list'),
                    'pods' => __('Pods', 'vybose-repeater-icon-list'),
                    'metabox' => __('Meta Box', 'vybose-repeater-icon-list'),
                ),
                'default' => 'acf',
                'condition' => array('data_type' => 'dynamic'),
            )
        );

        $this->add_control(
            'acf_repeater_field_name',
            array(
                'label' => __('Repeater Field Path', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT2,
                'options' => Field_Discovery::get_repeater_options(get_post_type() ?: 'post'),
                'description' => __('Choose a discovered field or use the manual field path fallback below.', 'vybose-repeater-icon-list'),
                'label_block' => true,
                'condition' => array('data_type' => 'dynamic'),
            )
        );

        $this->add_control(
            'acf_repeater_field_name_manual',
            array(
                'label' => __('Manual Repeater Field Path', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::TEXT,
                'description' => __('Used when no discovered field is selected. Supports nested paths such as parent/child.', 'vybose-repeater-icon-list'),
                'label_block' => true,
                'condition' => array('data_type' => 'dynamic'),
            )
        );

        $this->add_control(
            'dynamic_text_sub_field',
            array(
                'label' => __('Text Sub-field Key', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT2,
                'options' => Field_Discovery::get_leaf_field_options(get_post_type() ?: 'post'),
                'label_block' => true,
                'condition' => array('data_type' => 'dynamic'),
            )
        );

        $this->add_control(
            'dynamic_text_sub_field_manual',
            array(
                'label' => __('Manual Text Sub-field Key', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'condition' => array('data_type' => 'dynamic'),
            )
        );

        $this->add_control(
            'dynamic_value_sub_field',
            array(
                'label' => __('Value Sub-field Key', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT2,
                'options' => Field_Discovery::get_leaf_field_options(get_post_type() ?: 'post'),
                'label_block' => true,
                'condition' => array('data_type' => 'dynamic'),
            )
        );

        $this->add_control(
            'dynamic_value_sub_field_manual',
            array(
                'label' => __('Manual Value Sub-field Key', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'condition' => array('data_type' => 'dynamic'),
            )
        );

        $this->add_control(
            'dynamic_link_sub_field',
            array(
                'label' => __('Link Sub-field Key', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT2,
                'options' => Field_Discovery::get_leaf_field_options(get_post_type() ?: 'post'),
                'label_block' => true,
                'condition' => array('data_type' => 'dynamic'),
            )
        );

        $this->add_control(
            'dynamic_link_sub_field_manual',
            array(
                'label' => __('Manual Link Sub-field Key', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'condition' => array('data_type' => 'dynamic'),
            )
        );
    }

    /**
     * Register widget list style section.
     *
     * Adds icon list widget `list style` settings section controls.
     *
     * @since 1.2.0
     */
    protected function register_list_style_section()
    {
        $this->start_controls_section(
            'section_list_style',
            [
                'label' => __('List', 'vybose-repeater-icon-list'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'space_between',
            [
                'label' => __('Space Between', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => ['max' => 50],
                    'em' => ['max' => 5],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-items-gap: calc({{SIZE}}{{UNIT}}/2)',
                ],
            ]
        );

        $this->add_control(
            'columns_heading',
            [
                'label' => __('Columns', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Count', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-columns-count: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'columns_gap',
            [
                'label' => __('Gap', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => ['max' => 100],
                    'em' => ['max' => 5],
                    '%' => ['max' => 30],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-columns-gap: {{SIZE}}{{UNIT}}',
                ],
                'condition' => ['columns!' => ''],
            ]
        );

        $this->add_control(
            'columns_rule_style',
            [
                'label' => __('Separator Style', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => __('None', 'vybose-repeater-icon-list'),
                    'solid' => __('Solid', 'vybose-repeater-icon-list'),
                    'double' => __('Double', 'vybose-repeater-icon-list'),
                    'dotted' => __('Dotted', 'vybose-repeater-icon-list'),
                    'dashed' => __('Dashed', 'vybose-repeater-icon-list'),
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-columns-rule-style: {{VALUE}}',
                ],
                'condition' => ['columns!' => ''],
            ]
        );

        $this->add_responsive_control(
            'columns_rule_weight',
            [
                'label' => __('Separator Weight', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-columns-rule-weight: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'columns!' => '',
                    'columns_rule_style!' => '',
                ],
            ]
        );

        $this->add_control(
            'columns_rule_color',
            [
                'label' => __('Separator Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-columns-rule-color: {{VALUE}}',
                ],
                'condition' => [
                    'columns!' => '',
                    'columns_rule_style!' => '',
                ],
            ]
        );

        $this->add_control(
            'divider',
            [
                'label' => __('Divider', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('Off', 'vybose-repeater-icon-list'),
                'label_on' => __('On', 'vybose-repeater-icon-list'),
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .vybose-widget-icon-list-item:not(:last-child):after' => 'content: ""',
                ],
            ]
        );

        $this->add_control(
            'divider_style',
            [
                'label' => __('Style', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'solid' => __('Solid', 'vybose-repeater-icon-list'),
                    'double' => __('Double', 'vybose-repeater-icon-list'),
                    'dotted' => __('Dotted', 'vybose-repeater-icon-list'),
                    'dashed' => __('Dashed', 'vybose-repeater-icon-list'),
                ],
                'default' => 'solid',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-items-divider-style: {{VALUE}}',
                ],
                'condition' => ['divider' => 'yes'],
            ]
        );

        $this->add_responsive_control(
            'divider_weight',
            [
                'label' => __('Weight', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-items-divider-weight: {{SIZE}}{{UNIT}}',
                ],
                'condition' => ['divider' => 'yes'],
            ]
        );

        $this->add_responsive_control(
            'divider_width',
            [
                'label' => __('Width', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%', 'px'],
                'default' => ['unit' => '%'],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-items-divider-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => ['divider' => 'yes'],
            ]
        );

        $this->add_control(
            'divider_color',
            [
                'label' => __('Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-items-divider-color: {{VALUE}}',
                ],
                'condition' => ['divider' => 'yes'],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register widget item style section.
     *
     * Adds icon list widget `item style` settings section controls.
     *
     * @since 1.2.0
     * @since 1.5.1 Fixed text shadow in list item.
     */
    protected function register_item_style_section()
    {
        $this->start_controls_section(
            'section_item_style',
            [
                'label' => __('Item', 'vybose-repeater-icon-list'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_typography',
                'selector' => '{{WRAPPER}} .vybose-widget-icon-list-item, {{WRAPPER}} .vybose-widget-icon-list-item > a',
            ]
        );

        $this->start_controls_tabs('item_colors');

        $this->start_controls_tab(
            'item_normal',
            ['label' => __('Normal', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'item_color',
            [
                'label' => __('Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_link_color',
            [
                'label' => __('Link Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-link-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'item_hover',
            ['label' => __('Hover', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'item_hover_color',
            [
                'label' => __('Hover Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-hover-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_link_hover_color',
            [
                'label' => __('Link Hover Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-link-hover-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'text_indent',
            [
                'label' => __('Indent', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => ['max' => 50],
                    'em' => ['max' => 5],
                ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-text-indent: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .vybose-widget-icon-list-item-text',
            ]
        );

        $this->add_control(
            'text_vertical_align',
            [
                'label' => __('Vertical Alignment', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Top', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __('Bottom', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-vertical-align: {{VALUE}};',
                ],
                'condition' => ['item_layout' => 'row'],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register widget value style section.
     *
     * Adds icon list widget `value style` settings section controls.
     *
     * @since 1.2.0
     */
    protected function register_value_style_section()
    {
        $this->start_controls_section(
            'section_value_style',
            [
                'label' => __('Value', 'vybose-repeater-icon-list'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'value_typography',
                'selector' => '{{WRAPPER}} .vybose-widget-icon-list-item-value, {{WRAPPER}} .vybose-widget-icon-list-item-value > a',
            ]
        );

        $this->start_controls_tabs('value_colors');

        $this->start_controls_tab(
            'value_normal',
            ['label' => __('Normal', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'value_color',
            [
                'label' => __('Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-value-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_link_color',
            [
                'label' => __('Link Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-value-link-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'value_hover',
            ['label' => __('Hover', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'value_hover_color',
            [
                'label' => __('Hover Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-value-hover-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_link_hover_color',
            [
                'label' => __('Link Hover Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-value-link-hover-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'value_indent',
            [
                'label' => __('Indent', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => ['max' => 200],
                    'em' => ['max' => 10],
                ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-value-indent: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'item_layout',
                            'operator' => '=',
                            'value' => 'row',
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'item_layout',
                                    'operator' => '=',
                                    'value' => 'column',
                                ],
                                [
                                    'name' => 'value_position',
                                    'operator' => '!==',
                                    'value' => 'inline',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'value_gap',
            [
                'label' => __('Gap', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => ['max' => 50],
                    'em' => ['max' => 5],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-value-gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ['item_layout' => 'column'],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register widget icon style section.
     *
     * Adds icon list widget `icon style` settings section controls.
     *
     * @since 1.2.0
     */
    protected function register_icon_style_section()
    {
        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => __('Marker', 'vybose-repeater-icon-list'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'number_type',
            [
                'label' => __('Number Type', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'decimal' => __('Decimal', 'vybose-repeater-icon-list'),
                    'decimal-leading-zero' => __('Decimal Leading Zero', 'vybose-repeater-icon-list'),
                    'upper-latin' => __('Uppercase Latin', 'vybose-repeater-icon-list'),
                    'lower-latin' => __('Lowercase Latin', 'vybose-repeater-icon-list'),
                    'upper-roman' => __('Uppercase Roman', 'vybose-repeater-icon-list'),
                    'lower-roman' => __('Lowercase Roman', 'vybose-repeater-icon-list'),
                    'lower-greek' => __('Greek', 'vybose-repeater-icon-list'),
                ],
                'default' => 'decimal',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-counter-type: {{VALUE}};',
                ],
                'condition' => ['global_marker' => 'numeric'],
            ]
        );

        $this->add_control(
            'number_prefix',
            [
                'label' => __('Number Prefix', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::TEXT,
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-counter-prefix: \'{{VALUE}}\';',
                ],
                'condition' => ['global_marker' => 'numeric'],
            ]
        );

        $this->add_control(
            'number_suffix',
            [
                'label' => __('Number Suffix', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::TEXT,
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-counter-suffix: \'{{VALUE}}\';',
                ],
                'condition' => ['global_marker' => 'numeric'],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'number_typography',
                'exclude' => ['line_height'], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
                'selector' => '{{WRAPPER}} .vybose-widget-icon-list-item-icon > span:before',
                'condition' => ['global_marker' => 'numeric'],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __('Size', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => ['min' => 7],
                    'em' => ['min' => 0.5],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ['global_marker' => 'icon'],
            ]
        );

        $this->add_control(
            'icon_vertical_align',
            [
                'label' => __('Vertical Alignment', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Top', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __('Bottom', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .vybose-widget-icon-list-item-icon' => 'align-self: {{VALUE}};',
                ],
            ]
        );

        $this->start_controls_tabs('icon_colors');

        $this->start_controls_tab(
            'icon_normal',
            ['label' => __('Normal', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Primary Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_secondary_color',
            [
                'label' => __('Secondary Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-secondary-color: {{VALUE}};',
                ],
                'condition' => ['marker_view!' => 'default'],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_box_shadow',
                'selector' => '{{WRAPPER}} .vybose-widget-icon-list-item .vybose-widget-icon-list-item-icon > span',
                'condition' => ['marker_view!' => 'default'],
            ]
        );

        $this->add_control(
            'icon_rotate',
            [
                'label' => __('Rotate', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'default' => ['unit' => 'deg'],
                'range' => [
                    'deg' => [
                        'min' => 0,
                        'max' => 360,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-rotate: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_hover',
            ['label' => __('Hover', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label' => __('Primary Hover', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-hover-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_secondary_color',
            [
                'label' => __('Secondary Hover', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-hover-secondary-color: {{VALUE}};',
                ],
                'condition' => ['marker_view!' => 'default'],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_hover_box_shadow',
                'selector' => '{{WRAPPER}} .vybose-widget-icon-list-item:hover .vybose-widget-icon-list-item-icon > span',
                'condition' => ['marker_view!' => 'default'],
            ]
        );

        $this->add_control(
            'icon_rotate_hover',
            [
                'label' => __('Rotate', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'default' => ['unit' => 'deg'],
                'range' => [
                    'deg' => [
                        'min' => 0,
                        'max' => 360,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-rotate-hover: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'icon_wrapper_size',
            [
                'label' => __('Wrapper Size', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-wrapper: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ['marker_view!' => 'default'],
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label' => __('Padding', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 3,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-padding: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ['marker_view!' => 'default'],
            ]
        );

        $this->add_responsive_control(
            'icon_border_width',
            [
                'label' => __('Border Width', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 3,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ['marker_view' => 'framed'],
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius',
            [
                'label' => __('Border Radius', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => ['marker_view!' => 'default'],
            ]
        );

        $this->add_responsive_control(
            'icon_self_align',
            [
                'label' => __('Alignment', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-item-icon-alignment: {{VALUE}};',
                ],
                'condition' => ['global_marker' => 'icon'],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register widget title style section.
     *
     * Adds icon list widget `title style` settings section controls.
     *
     * @since 1.2.0
     * @since 1.3.0 Added `Alignment` control for title.
     */
    protected function register_title_style_section()
    {
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __('Title', 'vybose-repeater-icon-list'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => ['title!' => ''],
            ]
        );

        $this->add_responsive_control(
            'title_align',
            [
                'label' => __('Alignment', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'vybose-repeater-icon-list'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .vybose-widget-icon-list-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .vybose-widget-icon-list-title',
            ]
        );

        $this->start_controls_tabs('title_colors');

        $this->start_controls_tab(
            'title_normal',
            ['label' => __('Normal', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-title-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'title_hover',
            ['label' => __('Hover', 'vybose-repeater-icon-list')]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => __('Hover Color', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-title-hover-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'selector' => '{{WRAPPER}} .vybose-widget-icon-list-title',
            ]
        );

        $this->add_responsive_control(
            'title_gap',
            [
                'label' => __('Gap', 'vybose-repeater-icon-list'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => ['max' => 200],
                    'em' => ['max' => 10],
                    '%' => ['max' => 100],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--vybose-icon-list-title-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.2.0
     * @since 1.14.1 Fixed applying a reference to a value if that value is empty.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $base_class = 'vybose-widget-icon-list';
        $item_class = "{$base_class}-item";

        $this->add_render_attribute('icon_list', 'class', "{$base_class}-items");
        $this->add_render_attribute('list_item_text_wrap', 'class', "{$item_class}-text-wrap");

        if (!empty($settings['title'])) {
            $this->add_render_attribute('list_title', 'class', "{$base_class}-title");

            $tag = $settings['title_tag'];

            echo '<' . Utils::validate_html_tag($tag) . ' ' . $this->get_render_attribute_string('list_title') . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                esc_html($settings['title']) .
                '</' . Utils::validate_html_tag($tag) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $provider = Provider_Factory::get_provider($settings);
        $list_data = $provider->get_items($settings);

        $link_type = $settings['link_click'];
        $is_dynamic = 'dynamic' === $settings['data_type'];
        ?>
        <ul <?php echo $this->get_render_attribute_string('icon_list'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
            <?php
            foreach ($list_data as $index => $item) {
                $repeater_item_setting_key = $this->get_repeater_setting_key('item', 'icon_list', $index);

                $this->add_render_attribute($repeater_item_setting_key, 'class', $item_class);

                $repeater_text_setting_key = $this->get_repeater_setting_key('text', 'icon_list', $index);
                $repeater_inner_key = $this->get_repeater_setting_key('inner_wrap', 'icon_list', $index);

                $this->add_render_attribute($repeater_inner_key, 'class', [
                    "{$item_class}-text-inner",
                    "elementor-repeater-item-{$item['_id']}",
                ]);

                $this->add_render_attribute($repeater_text_setting_key, 'class', "{$item_class}-text");

                if (!$is_dynamic) {
                    $this->add_inline_editing_attributes($repeater_text_setting_key);
                }

                list($has_icon, $icon) = $this->get_list_item_icon($item);

                if ($has_icon) {
                    $repeater_icon_setting_key = $this->get_repeater_setting_key('icon', 'icon_list', $index);

                    $this->add_render_attribute($repeater_icon_setting_key, 'class', "{$item_class}-icon");
                    $this->add_render_attribute($repeater_item_setting_key, 'class', 'active-icon-item');
                }

                if (!empty($item['value'])) {
                    $repeater_value_setting_key = $this->get_repeater_setting_key('value', 'icon_list', $index);

                    $this->add_render_attribute($repeater_value_setting_key, 'class', "{$item_class}-value");

                    if (!$is_dynamic) {
                        $this->add_inline_editing_attributes($repeater_value_setting_key);
                    }
                }

                $has_item_link = !empty($item['link']['url']);

                if ($has_item_link) {
                    $link_key = "link_{$index}";

                    $this->add_link_attributes($link_key, $item['link']);

                    switch ($link_type) {
                        case 'full_width':
                            $this->add_render_attribute($repeater_item_setting_key, 'class', 'active-link-item');

                            break;
                        case 'text':
                            if (!empty($item['text'])) {
                                $this->add_render_attribute($repeater_text_setting_key, 'class', 'active-link-item');
                            }

                            break;
                        case 'value':
                            if (!empty($item['value'])) {
                                $this->add_render_attribute($repeater_value_setting_key, 'class', 'active-link-item');
                            }

                            break;
                    }
                }
                ?>
                <li <?php echo $this->get_render_attribute_string($repeater_item_setting_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                    <?php
                    if ($has_item_link && 'full_width' === $link_type) {
                        echo '<a ' . $this->get_render_attribute_string($link_key) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                    <span <?php echo $this->get_render_attribute_string('list_item_text_wrap'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                        <?php if ($has_icon) { ?>
                            <span <?php echo $this->get_render_attribute_string($repeater_icon_setting_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                                <span>
                                    <?php if ($icon) { ?>
                                        <?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
                                    <?php } ?>
                                </span>
                            </span>
                        <?php } ?>
                        <span <?php echo $this->get_render_attribute_string($repeater_inner_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                            <span <?php echo $this->get_render_attribute_string($repeater_text_setting_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                                <?php
                                if ($has_item_link && 'text' === $link_type) {
                                    echo '<a ' . $this->get_render_attribute_string($link_key) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        wp_kses_post($item['text']) .
                                        '</a>';
                                } else {
                                    echo wp_kses_post($item['text']);
                                }
                                ?>
                            </span>
                            <?php
                            if ('row' === $settings['item_layout'] || ('column' === $settings['item_layout'] && 'inline' === $settings['value_position'])) {
                                $this->get_item_value($item, $index);
                            }
                            ?>
                        </span>
                    </span>
                    <?php
                    // Render Value outside only for Column layout with Bottom position
                    if ('column' === $settings['item_layout'] && 'inline' !== $settings['value_position']) {
                        $this->get_item_value($item, $index);
                    }

                    if ($has_item_link && 'full_width' === $link_type) {
                        echo '</a>';
                    }
                    ?>
                </li>
            <?php } ?>
        </ul>
        <?php
    }

    /**
     * Get item value.
     *
     * Retrieves icon item value.
     *
     * @since 1.3.3
     */
    protected function get_item_value($item, $index)
    {
        $settings = $this->get_settings_for_display();

        if (!empty($item['value'])) {
            $repeater_value_setting_key = $this->get_repeater_setting_key('value', 'icon_list', $index);

            $this->add_render_attribute($repeater_value_setting_key, 'class', "vybose-widget-icon-list-item-value");

            if ('dynamic' !== $settings['data_type']) {
                $this->add_inline_editing_attributes($repeater_value_setting_key);
            }
        }

        if (!empty($item['value'])) {
            echo '<span ' . $this->get_render_attribute_string($repeater_value_setting_key) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            $has_item_link = !empty($item['link']['url']);
            $link_type = $settings['link_click'];
            $link_key = "link_{$index}";

            if ($has_item_link && 'value' === $link_type) {
                echo '<a ' . $this->get_render_attribute_string($link_key) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    wp_kses_post($item['value']) .
                    '</a>';
            } else {
                echo wp_kses_post($item['value']);
            }

            echo '</span>';
        }
    }


    /**
     * Get list item icon.
     *
     * Retrieves list item icon.
     *
     * @since 1.2.0
     *
     * @param array $item Widget list item.
     *
     * @return array Widget list item icon array.
     */
    protected function get_list_item_icon($item)
    {
        $settings = $this->get_settings_for_display();

        if ('numeric' === $settings['global_marker']) {
            return [true, false];
        }

        if (isset($item['icon_type']) && 'custom' === $item['icon_type']) {
            $icon = (!empty($item['icon']['value'])) ? $item['icon'] : ['value' => ''];
        } else {
            $icon = $settings['global_icon'];
        }

        $has_icon = isset($icon['value']) && !empty($icon['value']);

        return [$has_icon, $icon];
    }

    /**
     * Get fields config for WPML.
     *
     * @since 1.3.3
     *
     * @return array Fields config.
     */
    public static function get_wpml_fields()
    {
        return [
            [
                'field' => 'title',
                'type' => esc_html__('Title', 'vybose-repeater-icon-list'),
                'editor_type' => 'LINE',
            ],
            [
                'field' => 'dynamic_text',
                'type' => esc_html__('Dynamic Text', 'vybose-repeater-icon-list'),
                'editor_type' => 'LINE',
            ],
            [
                'field' => 'dynamic_value',
                'type' => esc_html__('Dynamic Value', 'vybose-repeater-icon-list'),
                'editor_type' => 'LINE',
            ],
            'dynamic_link' => [
                'field' => 'url',
                'type' => esc_html__('Dynamic Link', 'vybose-repeater-icon-list'),
                'editor_type' => 'LINK',
            ],
            [
                'field' => 'number_prefix',
                'type' => esc_html__('Number Prefix', 'vybose-repeater-icon-list'),
                'editor_type' => 'LINE',
            ],
            [
                'field' => 'number_suffix',
                'type' => esc_html__('Number Suffix', 'vybose-repeater-icon-list'),
                'editor_type' => 'LINE',
            ],
        ];
    }

    /**
     * Get fields_in_item config for WPML.
     *
     * @since 1.3.3
     *
     * @return array Fields in item config.
     */
    public static function get_wpml_fields_in_item()
    {
        return [
            'icon_list' => [
                [
                    'field' => 'text',
                    'type' => esc_html__('Text', 'vybose-repeater-icon-list'),
                    'editor_type' => 'LINE',
                ],
                [
                    'field' => 'value',
                    'type' => esc_html__('Value', 'vybose-repeater-icon-list'),
                    'editor_type' => 'LINE',
                ],
                'link' => [
                    'field' => 'url',
                    'type' => esc_html__('Link', 'vybose-repeater-icon-list'),
                    'editor_type' => 'LINK',
                ],
            ],
        ];
    }
}
