// License: GPLv2+


var el = wp.element.createElement,
    Fragment = wp.element.Fragment,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.components.ServerSideRender,
    TextControl = wp.components.TextControl,
    SelectControl = wp.components.SelectControl,
    ToggleControl = wp.components.ToggleControl,
	InspectorControls = wp.editor.InspectorControls;

//
// Embed a form
//
registerBlockType( 'buddyforms/bf-embed-form', {
	title: 'Embed a BuddyForm',
	icon: 'welcome-widgets-menus',
	category: 'buddyforms',

	edit: function( props ) {

        var forms = [
            { value: 'no', label: 'Select a Form' },
        ];
        for (var key in buddyforms_forms) {
            forms.push({ value: key, label: buddyforms_forms[key] });
        }

		return [

			el( ServerSideRender, {
				block: 'buddyforms/bf-embed-form',
				attributes: props.attributes,
			} ),

			el( InspectorControls, {},
                el( SelectControl, {
                    label: 'Please Select a form',
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: ( value ) => { props.setAttributes( { bf_form_slug: value } ); },
                } ),
			)
		];
	},

	// We're going to be rendering in PHP, so save() can just return null.
	save: function() {
		return null;
	},
} );

//
// Embed a form
//
registerBlockType( 'buddyforms/bf-navigation', {
    title: 'Links to Forms and Post Lists',
    icon: 'welcome-widgets-menus',
    category: 'buddyforms',

    edit: function( props ) {

        var forms = [
            { value: 'no', label: 'Select a Form' },
        ];
        for (var key in buddyforms_forms) {
            forms.push({ value: key, label: buddyforms_forms[key] });
        }

        return [

            el( ServerSideRender, {
                block: 'buddyforms/bf-embed-form',
                attributes: props.attributes,
            } ),

            el( InspectorControls, {},
                el( SelectControl, {
                    label: 'Please Select a form',
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: ( value ) => { props.setAttributes( { bf_form_slug: value } ); },
                } ),
            )
        ];
    },

    // We're going to be rendering in PHP, so save() can just return null.
    save: function() {
        return null;
    },
} );



//
// Display Submissions
//
registerBlockType( 'buddyforms/bf-list-submissions', {
    title: 'List Submissions',
    icon: 'welcome-widgets-menus',
    category: 'buddyforms',

    edit: function( props ) {
        var className = props.className;
        // var bf_form_slug = props.attributes.bf_form_slug;
        // var bf_permissions = props.attributes.bf_permissions;
        // var bf_author = props.attributes.bf_author;

        console.log(props.attributes);
        // Generate Forms array
        var bf_list_posts_style_options = [
            { value: 'list', label: 'List' },
            { value: 'table', label: 'Table' },
        ];

        // Generate Forms array
        var forms = [
            { value: 'no', label: 'Select a Form' },
        ];
        for (var key in buddyforms_forms) {
            // console.log(key +' - '+buddyforms_forms[key]);
            forms.push({ value: key, label: buddyforms_forms[key] });
        }

        // Generate Permissions array
        var permission = [
            { value: 'public', label: 'Public (Unregistered Users)' },
            { value: 'logged_in_user', label: 'Private (Logged in user only) ' },
        ];
        for (var key in buddyforms_roles) {
            // console.log(key +' - '+buddyforms_roles[key]);
            permission.push({ value: key, label: buddyforms_roles[key] });
        }

        return [

            el( ServerSideRender, {
                block: 'buddyforms/bf-list-submissions',
                attributes: props.attributes,
            } ),

            el( InspectorControls, {},
                el( 'p', {}, '' ),
                el( SelectControl, {
                    label: 'Please Select a form',
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: ( value ) => { props.setAttributes( { bf_form_slug: value } ); },
                } ),
                el( 'p', {}, '' ),
                el( 'b', {}, 'Restrict Access to this Block' ),
                el( SelectControl, {
                    label: 'Permissions',
                    value: props.attributes.bf_rights,
                    options: permission,
                    onChange: ( value ) => { props.setAttributes( { bf_rights: value } ); },
                } ),
                el( 'p', {}, '' ),
                el( 'b', {}, 'Filter Posts' ),
                el( SelectControl, {
                    className: props.className,
                    label: 'by Author',
                    value: props.attributes.bf_author,
                    options: permission,
                    onChange: ( value ) => { props.setAttributes( { bf_author: value } ); },
                } ),
                el( TextControl, {
                    label: 'by Meta Key',
                    value: props.attributes.bf_meta_key,
                    onChange: ( value ) => { props.setAttributes( { bf_meta_key: value } ); },
                } ),
                el( TextControl, {
                    label: 'Meta Value',
                    value: props.attributes.bf_meta_key,
                    onChange: ( value ) => { props.setAttributes( { bf_meta_key: value } ); },
                } ),
                el( TextControl, {
                    label: 'Posts peer page',
                    value: props.attributes.bf_meta_key,
                    onChange: ( value ) => { props.setAttributes( { bf_meta_key: value } ); },
                } ),
                el( TextControl, {
                    label: 'With Pagination',
                    value: props.attributes.bf_meta_key,
                    onChange: ( value ) => { props.setAttributes( { bf_meta_key: value } ); },
                } ),
                el( 'p', {}, '' ),
                el( 'b', {}, 'Template' ),
                el( SelectControl, {
                    label: 'List or Table',
                    value: props.attributes.bf_list_posts_style,
                    options: bf_list_posts_style_options,
                    onChange: ( value ) => { props.setAttributes( { bf_list_posts_style: value } ); },
                } ),

            )
        ];
    },

    // We're going to be rendering in PHP, so save() can just return null.
    save: function() {
        return null;
    },
} );
