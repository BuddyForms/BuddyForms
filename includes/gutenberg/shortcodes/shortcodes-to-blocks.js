var el = wp.element.createElement,
    Fragment = wp.element.Fragment,
    registerBlockType = wp.blocks.registerBlockType,
    ServerSideRender = wp.components.ServerSideRender,
    TextControl = wp.components.TextControl,
    SelectControl = wp.components.SelectControl,
    CheckboxControl = wp.components.CheckboxControl,
    ToggleControl = wp.components.ToggleControl,
    InspectorControls = wp.editor.InspectorControls;

const iconBuddyForms = el('svg', {width: 24, height: 24},
    el('path', {d: "M9.247 0.323c6.45-1.52 12.91 2.476 14.43 8.925s-2.476 12.91-8.925 14.43c-6.45 1.52-12.91-2.476-14.43-8.925s2.476-12.91 8.925-14.43zM9.033 14.121c-0.445-0.604-0.939-1.014-1.656-1.269-0.636 0.196-1.18 0.176-1.8-0.066-1.857 0.507-2.828 2.484-2.886 4.229 1.413 0.025 2.825 0.050 4.237 0.076M5.007 11.447c0.662 0.864 1.901 1.029 2.766 0.366s1.030-1.9 0.367-2.766c-0.662-0.864-1.901-1.029-2.766-0.366s-1.029 1.9-0.367 2.766zM7.476 18.878l7.256-0.376c-0.096-1.701-1.066-3.6-2.87-4.103-0.621 0.241-1.165 0.259-1.8 0.059-1.816 0.635-2.65 2.675-2.585 4.419zM9.399 13.162c0.72 0.817 1.968 0.894 2.784 0.173s0.894-1.968 0.173-2.784c-0.72-0.817-1.968-0.894-2.784-0.173s-0.894 1.968-0.173 2.784zM14.007 9.588h6.794v-1.109h-6.794v1.109zM14.007 11.645h6.794v-1.109h-6.794v1.109zM14.007 7.532h6.794v-1.109h-6.794v1.109zM9.033 14.121c-0.192 0.118-0.374 0.251-0.544 0.399-0.205 0.177-0.393 0.375-0.564 0.585-0.175 0.216-0.331 0.447-0.468 0.688-0.136 0.243-0.255 0.495-0.353 0.757-0.068 0.177-0.126 0.358-0.176 0.541"})
);

const {__} = wp.i18n;


//
// Embed a form
//
registerBlockType('buddyforms/bf-embed-form', {
    title: __('BuddyForm Form', 'buddyforms'),
    icon: iconBuddyForms,
    category: 'buddyforms',

    edit: function (props) {

        var forms = [
            {value: 'no', label: 'Select a Form'},
        ];
        for (var key in buddyforms_forms) {
            forms.push({value: key, label: buddyforms_forms[key]});
        }

        return [

            el(ServerSideRender, {
                block: 'buddyforms/bf-embed-form',
                attributes: props.attributes,
            }),

            el(InspectorControls, {},
                el('p', {}, ''),
                el(SelectControl, {
                    label: 'Please Select a form',
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: (value) => {
                        props.setAttributes({bf_form_slug: value});
                    },
                }),
                el('p', {}, ''),
                el('a', {
                    href: buddyforms_create_new_form_url,
                    target: 'new'
                }, 'Create a new Form'),
            )
        ];
    },

    save: function () {
        return null;
    },
});


//
// Embed a reset password form
//
registerBlockType('buddyforms/bf-password-reset-form', {
    title: 'Password Reset Form',
    icon: 'admin-network',
    category: 'buddyforms',

    edit: function (props) {

        var forms = [
            {value: 'no', label: 'Select a Form'},
        ];
        for (var key in buddyforms_forms) {
            forms.push({value: key, label: buddyforms_forms[key]});
        }

        return [

            el(ServerSideRender, {
                block: 'buddyforms/bf-password-reset-form',
                attributes: props.attributes,
            }),

            el(InspectorControls, {},
                el('p', {}, ''),
                el(TextControl, {
                    label: 'Redirect URL',
                    value: props.attributes.bf_redirect_url,
                    onChange: (value) => {
                        props.setAttributes({bf_redirect_url: value});
                    },
                }),
            )
        ];
    },

    save: function () {
        return null;
    },
});


//
// Embed a login form
//
registerBlockType('buddyforms/bf-embed-login-form', {
    title: 'Login/logout Form',
    icon: 'lock',
    category: 'buddyforms',

    edit: function (props) {

        var forms = [
            {value: 'no', label: 'Select a Registration Form'},
        ];
        for (var key in buddyforms_registration_forms) {
            forms.push({value: key, label: buddyforms_registration_forms[key]});
        }

        return [

            el(ServerSideRender, {
                block: 'buddyforms/bf-embed-login-form',
                attributes: props.attributes,
            }),

            el(InspectorControls, {},
                el('p', {}, ''),
                el(SelectControl, {
                    label: 'Select a Registration Form',
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: (value) => {
                        props.setAttributes({bf_form_slug: value});
                    },
                }),
                el(TextControl, {
                    label: 'Redirect URL',
                    value: props.attributes.bf_redirect_url,
                    onChange: (value) => {
                        props.setAttributes({bf_redirect_url: value});
                    },
                }),
                el(TextControl, {
                    label: 'Title',
                    value: props.attributes.bf_title,
                    onChange: (value) => {
                        props.setAttributes({bf_title: value});
                    },
                }),
            )
        ];
    },

    save: function () {
        return null;
    },
});


//
// Embed a Navigation
//
registerBlockType('buddyforms/bf-navigation', {
    title: 'BuddyForms Navigation',
    icon: 'menu',
    category: 'buddyforms',

    edit: function (props) {

        var bf_nav_style = [
            {value: 'buddyforms_nav', label: 'View - Add New'},
            {value: 'buddyforms_button_view_posts', label: 'View Posts'},
            {value: 'buddyforms_button_add_new', label: 'Add New'},
        ];


        var forms = [
            {value: 'no', label: 'Select a Form'},
        ];
        for (var key in buddyforms_post_forms) {
            forms.push({value: key, label: buddyforms_post_forms[key]});
        }

        return [

            el(ServerSideRender, {
                block: 'buddyforms/bf-navigation',
                attributes: props.attributes,
            }),

            el(InspectorControls, {},
                el('p', {}, ''),
                el(SelectControl, {
                    label: 'Please Select a form',
                    'description': 'sadsadas',
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: (value) => {
                        props.setAttributes({bf_form_slug: value});
                    },
                }),
                el(SelectControl, {
                    label: 'Navigation Style',
                    value: props.attributes.bf_nav_style,
                    options: bf_nav_style,
                    onChange: (value) => {
                        props.setAttributes({bf_nav_style: value});
                    },
                }),
                el(TextControl, {
                    label: 'Label Add',
                    value: props.attributes.bf_label_add,
                    onChange: (value) => {
                        props.setAttributes({bf_label_add: value});
                    },
                }),
                el(TextControl, {
                    label: 'Label View',
                    value: props.attributes.bf_label_view,
                    onChange: (value) => {
                        props.setAttributes({bf_label_view: value});
                    },
                }),
                el(TextControl, {
                    label: 'Separator',
                    value: props.attributes.bf_nav_separator,
                    onChange: (value) => {
                        props.setAttributes({bf_nav_separator: value});
                    },
                }),
            )
        ];
    },

    save: function () {
        return null;
    },
});


//
// Display Submissions
//
registerBlockType('buddyforms/bf-list-submissions', {
    title: 'List Submissions',
    icon: 'list-view',
    category: 'buddyforms',

    edit: function (props) {

        //
        // Generate the Select options arrays
        //
        var bf_by_author = [
            {value: 'logged_in_user', label: 'Logged in Author Posts'},
            {value: 'all_users', label: 'All Author Posts'},
            {value: 'author_ids', label: 'Author ID\'S'},
        ];

        var bf_by_form = [
            {value: 'form', label: 'Form Submissions'},
            {value: 'all', label: 'Form selected Post Type'},
        ];

        var bf_list_posts_style_options = [
            {value: 'list', label: 'List'},
            {value: 'table', label: 'Table'},
        ];

        var forms = [
            {value: 'no', label: 'Select a Form'},
        ];
        for (var key in buddyforms_forms) {
            forms.push({value: key, label: buddyforms_forms[key]});
        }

        var permission = [
            {value: 'public', label: 'Public (Unregistered Users)'},
            {value: 'private', label: 'Private (Logged in user only) '},
        ];
        for (var key in buddyforms_roles) {
            permission.push({value: key, label: buddyforms_roles[key]});
        }

        return [

            el(ServerSideRender, {
                block: 'buddyforms/bf-list-submissions',
                attributes: props.attributes,
            }),

            el(InspectorControls, {},
                el('p', {}, ''),
                el(SelectControl, {
                    label: 'Please Select a form',
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: (value) => {
                        props.setAttributes({bf_form_slug: value});
                    },
                }),
                el('p', {}, ''),
                el('b', {}, 'Restrict Access to this Block'),
                el(SelectControl, {
                    label: 'Permissions',
                    value: props.attributes.bf_rights,
                    options: permission,
                    onChange: (value) => {
                        props.setAttributes({bf_rights: value});
                    },
                }),
                el('p', {}, ''),
                el('b', {}, 'Filter Posts'),
                el(SelectControl, {
                    label: 'by Author',
                    value: props.attributes.bf_by_author,
                    options: bf_by_author,
                    onChange: (value) => {
                        props.setAttributes({bf_by_author: value});
                    },
                }),
                el(TextControl, {
                    label: 'Author ID\'s',
                    value: props.attributes.bf_author_ids,
                    onChange: (value) => {
                        props.setAttributes({bf_author_ids: value});
                    },
                }),
                el(SelectControl, {
                    label: 'by Form',
                    value: props.attributes.bf_by_form,
                    options: bf_by_form,
                    onChange: (value) => {
                        props.setAttributes({bf_by_form: value});
                    },
                }),
                el(TextControl, {
                    label: 'Posts peer page',
                    value: props.attributes.bf_posts_per_page,
                    onChange: (value) => {
                        props.setAttributes({bf_posts_per_page: value});
                    },
                }),
                el('p', {}, ''),
                el('b', {}, 'Template'),
                el(SelectControl, {
                    label: 'List or Table',
                    value: props.attributes.bf_list_posts_style,
                    options: bf_list_posts_style_options,
                    onChange: (value) => {
                        props.setAttributes({bf_list_posts_style: value});
                    },
                }),
            )
        ];
    },

    save: function () {
        return null;
    },
});
