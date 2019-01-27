// License: GPLv2+

var el = wp.element.createElement,
    Fragment = wp.element.Fragment,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.components.ServerSideRender,
    TextControl = wp.components.TextControl,
    SelectControl = wp.components.SelectControl,
	InspectorControls = wp.editor.InspectorControls;

/*
 * Here's where we register the block in JavaScript.
 *
 * It's not yet possible to register a block entirely without JavaScript, but
 * that is something I'd love to see happen. This is a barebones example
 * of registering the block, and giving the basic ability to edit the block
 * attributes. (In this case, there's only one attribute, 'form_slug'.)
 */
registerBlockType( 'buddyforms/bf-embed-form', {
	title: 'Embed a BuddyForm',
	icon: 'welcome-widgets-menus',
	category: 'buddyforms',

	/*
	 * In most other blocks, you'd see an 'attributes' property being defined here.
	 * We've defined attributes in the PHP, that information is automatically sent
	 * to the block editor, so we don't need to redefine it here.
	 */

	edit: function( props ) {
        // console.log(buddyforms_forms);

        var forms = [
            { value: 'no', label: 'Select a Form' },
        ];
        for (var key in buddyforms_forms) {
            // console.log(key +' - '+buddyforms_forms[key]);
            forms.push({ value: key, label: buddyforms_forms[key] });
        }

        //console.log(forms);

		return [




			/*
			 * The ServerSideRender element uses the REST API to automatically call
			 * buddyforms_block_render_form() in your PHP code whenever it needs to get an updated
			 * view of the block.
			 */
			el( ServerSideRender, {
				block: 'buddyforms/bf-embed-form',
				attributes: props.attributes,
			} ),
			/*
			 * InspectorControls lets you add controls to the Block sidebar. In this case,
			 * we're adding a TextControl, which lets us edit the 'form_slug' attribute (which
			 * we defined in the PHP). The onChange property is a little bit of magic to tell
			 * the block editor to update the value of our 'form_slug' property, and to re-render
			 * the block.
			 */

			el( InspectorControls, {},
                // el( TextControl, {
                //     label: 'Form Slug',
                //     value: props.attributes.form_slug,
                //     onChange: ( value ) => { props.setAttributes( { form_slug: value } ); },
                // } ),
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


registerBlockType( 'buddyforms/bf-list-submissions', {
    title: 'List Submissions',
    icon: 'welcome-widgets-menus',
    category: 'buddyforms',
    header: "My Panel",

    /*
     * In most other blocks, you'd see an 'attributes' property being defined here.
     * We've defined attributes in the PHP, that information is automatically sent
     * to the block editor, so we don't need to redefine it here.
     */



    edit: function( props ) {

        // var bf_form_slug = props.attributes.bf_form_slug;
        // var bf_permissions = props.attributes.bf_permissions;
        // var bf_author = props.attributes.bf_author;

        console.log(props.attributes);

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




            /*
             * The ServerSideRender element uses the REST API to automatically call
             * buddyforms_block_render_form() in your PHP code whenever it needs to get an updated
             * view of the block.
             */
            el( ServerSideRender, {
                block: 'buddyforms/bf-list-submissions',
                attributes: props.attributes,
            } ),
            /*
             * InspectorControls lets you add controls to the Block sidebar. In this case,
             * we're adding a TextControl, which lets us edit the 'form_slug' attribute (which
             * we defined in the PHP). The onChange property is a little bit of magic to tell
             * the block editor to update the value of our 'form_slug' property, and to re-render
             * the block.
             *
             *
             *
             * query_option
             * user_logged_in_only
             * meta_key
             * meta_value
             *
             *
             */



            //


            el( InspectorControls, {},
                el( SelectControl, {
                    label: 'Please Select a form',
                    value: props.attributes.bf_form_slug,
                    options: forms,
                    onChange: ( value ) => { props.setAttributes( { bf_form_slug: value } ); },
                } ),
                el( SelectControl, {
                    label: 'Permissions',
                    value: props.attributes.bf_rights,
                    options: permission,
                    onChange: ( value ) => { props.setAttributes( { bf_rights: value } ); },
                } ),
                el( SelectControl, {
                    label: 'Author',
                    value: props.attributes.bf_author,
                    options: permission,
                    onChange: ( value ) => { props.setAttributes( { bf_author: value } ); },
                } ),
                // el( TextControl, {
                //     label: 'Author',
                //     value: props.attributes.bf_author,
                //     onChange: ( value ) => { props.setAttributes( { bf_author: value } ); },
                // } ),
            )
        ];
    },

    // We're going to be rendering in PHP, so save() can just return null.
    save: function() {
        return null;
    },
} );
