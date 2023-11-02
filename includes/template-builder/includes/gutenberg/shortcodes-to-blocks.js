


//
// Embed a form
//
registerBlockType('buddyformshooks/bf-insert-form-field-value', {
    title: __('BuddyForm Form Field Value', 'buddyforms'),
    icon: iconBuddyForms,
    category: 'buddyforms',
    attributes: {
        post_type: {
            type: 'string',
        },
        customfields: {
            type: 'object',
        },
        post_id: {
            type: 'string',
        },
        bf_form_slug: {
            type: 'string',
        },
        bf_form_field: {
            type: 'string',
        },
    },
    edit: function (props) {

        var forms = [
            {value: 'no', label: __('Select a Form', 'buddyforms')},
        ];
        for (var key in buddyforms_forms) {
            forms.push({value: key, label: buddyforms_forms[key]});
        }

        var fields = [
            {value: 'no', label: __('Select a Field', 'buddyforms')},
        ];

        for (var key in buddyforms_forms_fields[props.attributes.bf_form_slug]) {
            fields.push({value: buddyforms_forms_fields[props.attributes.bf_form_slug][key], label: buddyforms_forms_fields[props.attributes.bf_form_slug][key]});
        }

        return [

            el(ServerSideRender, {
                block: 'buddyformshooks/bf-insert-form-field-value',
                attributes: props.attributes,
            }),

            el(InspectorControls, {},
                el('p', {}, ''),
                el(SelectControl, {
                    label: __('Please Select a form', 'buddyforms'),
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: (value) => {
                        props.setAttributes({bf_form_slug: value});
                    },
                }),
                el(SelectControl, {
                    label: __('Please Select a Field', 'buddyforms'),
                    value: props.attributes.bf_form_field,
                    options: fields,
                    onChange: (value) => {
                        props.setAttributes({bf_form_field: value});
                    },
                }),
                el('p', {}, ''),
                el('a', {
                    href: buddyforms_create_new_form_url,
                    target: 'new'
                }, __('Create a new Form', 'buddyforms')),
            )
        ];
    },

    save: function () {
        return null;
    },
});
