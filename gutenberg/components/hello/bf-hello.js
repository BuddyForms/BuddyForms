const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText } = wp.editor;

registerBlockType( 'buddyforms/hello', {
    title: __('Hello World (Step 3)','buddyforms'),

    icon: 'universal-access-alt',

    category: 'layout',

    attributes: {
        content: {
            type: 'string',
            source: 'html',
            selector: 'p',
        },
    },

    edit( { attributes, className, setAttributes } ) {
        const { content } = attributes;

        function onChangeContent( newContent ) {
            setAttributes( { content: newContent } );
        }

        return (
            <RichText
                tagName="p"
                className={ className }
                onChange={ onChangeContent }
                value={ content }
            />
        );
    },

    save( { attributes } ) {
        const { content } = attributes;

        return (
            <RichText.Content
                tagName="p"
                value={ content }
            />
        );
    },
} );