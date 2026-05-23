(function (blocks, element, components, blockEditor, i18n) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var ServerSideRender = components.ServerSideRender || wp.serverSideRender;
    var useEffect = element.useEffect;
    var useState = element.useState;
    var __ = i18n.__;

    registerBlockType('bodyloom/dynamic-icon-list', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var fieldState = useState({
                repeaters: [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-icon-list'), value: '' }],
                leafFields: [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-icon-list'), value: '' }]
            });
            var fieldOptions = fieldState[0];
            var setFieldOptions = fieldState[1];

            useEffect(function () {
                if (!window.wp || !window.wp.apiFetch) {
                    return;
                }

                window.wp.apiFetch({ path: '/bodyloom-dynamic-icon-list/v1/fields' }).then(function (response) {
                    var repeaters = [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-icon-list'), value: '' }];
                    var leafFields = [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-icon-list'), value: '' }];
                    var sources = response && response.sources ? response.sources : {};

                    Object.keys(sources).forEach(function (source) {
                        var sourceData = sources[source];
                        Object.keys(sourceData.repeaters || {}).forEach(function (path) {
                            repeaters.push({
                                label: sourceData.label + ': ' + sourceData.repeaters[path].label,
                                value: source + ':' + path
                            });
                        });
                        Object.keys(sourceData.leaf_fields || {}).forEach(function (path) {
                            leafFields.push({
                                label: sourceData.label + ': ' + sourceData.leaf_fields[path].label,
                                value: path
                            });
                        });
                    });

                    setFieldOptions({
                        repeaters: repeaters,
                        leafFields: leafFields
                    });
                }).catch(function () {
                    setFieldOptions({
                        repeaters: [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-icon-list'), value: '' }],
                        leafFields: [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-icon-list'), value: '' }]
                    });
                });
            }, []);

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Settings', 'bodyloom-dynamic-icon-list'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'bodyloom-dynamic-icon-list'),
                            value: attributes.title,
                            onChange: function (val) { setAttributes({ title: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Data Type', 'bodyloom-dynamic-icon-list'),
                            value: attributes.data_type,
                            options: [
                                { label: __('Static', 'bodyloom-dynamic-icon-list'), value: 'static' },
                                { label: __('Dynamic', 'bodyloom-dynamic-icon-list'), value: 'dynamic' }
                            ],
                            onChange: function (val) { setAttributes({ data_type: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Dynamic Source', 'bodyloom-dynamic-icon-list'),
                            value: attributes.dynamic_source,
                            options: [
                                { label: __('ACF', 'bodyloom-dynamic-icon-list'), value: 'acf' },
                                { label: __('Pods', 'bodyloom-dynamic-icon-list'), value: 'pods' },
                                { label: __('Meta Box', 'bodyloom-dynamic-icon-list'), value: 'metabox' }
                            ],
                            onChange: function (val) { setAttributes({ dynamic_source: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Repeater Field', 'bodyloom-dynamic-icon-list'),
                            value: attributes.acf_repeater_field_name,
                            options: fieldOptions.repeaters,
                            onChange: function (val) { setAttributes({ acf_repeater_field_name: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Repeater Field Path', 'bodyloom-dynamic-icon-list'),
                            value: attributes.acf_repeater_field_name_manual,
                            onChange: function (val) { setAttributes({ acf_repeater_field_name_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Text Sub-Field', 'bodyloom-dynamic-icon-list'),
                            value: attributes.dynamic_text_sub_field,
                            options: fieldOptions.leafFields,
                            onChange: function (val) { setAttributes({ dynamic_text_sub_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Text Sub-Field', 'bodyloom-dynamic-icon-list'),
                            value: attributes.dynamic_text_sub_field_manual,
                            onChange: function (val) { setAttributes({ dynamic_text_sub_field_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Value Sub-Field', 'bodyloom-dynamic-icon-list'),
                            value: attributes.dynamic_value_sub_field,
                            options: fieldOptions.leafFields,
                            onChange: function (val) { setAttributes({ dynamic_value_sub_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Value Sub-Field', 'bodyloom-dynamic-icon-list'),
                            value: attributes.dynamic_value_sub_field_manual,
                            onChange: function (val) { setAttributes({ dynamic_value_sub_field_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Link Sub-Field', 'bodyloom-dynamic-icon-list'),
                            value: attributes.dynamic_link_sub_field,
                            options: fieldOptions.leafFields,
                            onChange: function (val) { setAttributes({ dynamic_link_sub_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Link Sub-Field', 'bodyloom-dynamic-icon-list'),
                            value: attributes.dynamic_link_sub_field_manual,
                            onChange: function (val) { setAttributes({ dynamic_link_sub_field_manual: val }); }
                        })
                    )
                ),
                el('div', { className: props.className },
                    el(ServerSideRender, {
                        block: 'bodyloom/dynamic-icon-list',
                        attributes: attributes
                    })
                )
            ];
        },
        save: function () {
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor,
    window.wp.i18n
);
