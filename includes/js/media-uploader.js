(function($) {

    $(document).ready( function() {
        var file_frame; // variable for the wp.media file_frame

        // attach a click event (or whatever you want) to some element on your page
        jQuery(document).on( 'click', '#frontend-button', function( event ) {
            event.preventDefault();

            // if the file_frame has already been created, just reuse it
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            file_frame = wp.media.frames.file_frame = wp.media({
                title: $( this ).data( 'uploader_title' ),
                button: {
                    text: $( this ).data( 'uploader_button_text' ),
                },
                multiple: false // set this to true for multiple file selection
            });

            file_frame.on( 'select', function() {
                attachment = file_frame.state().get('selection').first().toJSON();
                // do something with the file here

                $( '#featured-image').val(attachment.id);
                $( '#frontend-image' ).attr('src', attachment.url);
            });

            file_frame.open();
        });
    });

    jQuery(document).on( 'click', '.bf_add_files a', function( event ) {

        var $el = $(this);
        // Product gallery file uploads
        var product_gallery_frame;

        var $image_gallery_ids  = $('#'+$el.data('slug'));
        var bf_files     = $('#bf_files_container ul.bf_files');

        var attachment_ids = $image_gallery_ids.val();

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( product_gallery_frame ) {
            product_gallery_frame.open();
            return;
        }

        // Create the media frame.
        product_gallery_frame = wp.media.frames.product_gallery = wp.media({
            // Set the title of the modal.
            title: $el.data('choose'),
            button: {
                text: $el.data('update'),
            },
            states : [
                new wp.media.controller.Library({
                    title: $el.data('choose'),
                    filterable :	'all',
                    multiple: true,
                })
            ]
        });

        // When an image is selected, run a callback.
        product_gallery_frame.on( 'select', function() {

            var selection = product_gallery_frame.state().get('selection');

            selection.map( function( attachment ) {

                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids   = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

                    if(attachment.type == 'image'){
                        attachment_image = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    } else {
                        attachment_image = attachment.icon;
                        attachment_url = attachment.url;
                    }

                    bf_files.append('\
                            <li class="image" data-attachment_id="' + attachment.id + '">\
                            <div class="bf_attachment_li">\
                            <div class="bf_attachment_img">\
                            <img style="height:64px" src="' + attachment_image + '" />\
                            </div><div class="bf_attachment_meta">\
                            <p><b>Name: </b>' + attachment.name + '<p>\
                            <p>\
                            <a href="#" class="delete tips" data-slug="' + $el.data('slug') + '" data-tip="' + $el.data('tip') +  '">' + $el.data('text') +  '</a>\
                            <a href="' + attachment_url + '" target="_blank" class="view" data-tip="' + +  '">' + +  '</a>\
                            </p>\
                            </div></div>\
                            </li>');
                }

            });

            $image_gallery_ids.val( attachment_ids );
        });

        // Finally, open the modal.
        product_gallery_frame.open();
    });

    // Remove images
    jQuery(document).on( 'click', '#bf_files_container a.delete', function( event ) {

        var $el = $(this);

        var $image_gallery_ids  = $('#'+$el.data('slug'));
        var bf_files     = $('#bf_files_container ul.bf_files');

        var attachment_ids = $image_gallery_ids.val();

        $(this).closest('li.image').remove();

        var attachment_ids = '';

        $('#bf_files_container ul li.image').css('cursor','default').each(function() {
            var attachment_id = jQuery(this).attr( 'data-attachment_id' );
            attachment_ids = attachment_ids + attachment_id + ',';
        });

        $image_gallery_ids.val( attachment_ids );

        // remove any lingering tooltips
        $( '#tiptip_holder' ).removeAttr( 'style' );
        $( '#tiptip_arrow' ).removeAttr( 'style' );

        return false;
    });

})(jQuery);