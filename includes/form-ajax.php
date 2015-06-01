<?php

function buddyforms_delete_attachment(){

    $delete_attachment_id = $_POST['delete_attachment_id'];
    $delete_attachment_href = $_POST['delete_attachment_href'];

    $delete_attachment_attr = explode('/',$delete_attachment_href);

    wp_delete_attachment( $delete_attachment_id );

    delete_post_meta($delete_attachment_attr[0], $delete_attachment_attr[1]);

    echo $_POST['delete_attachment_id'];

    die();

}
add_action('wp_ajax_buddyforms_delete_attachment', 'buddyforms_delete_attachment');
add_action('wp_ajax_nopriv_buddyforms_delete_attachment', 'buddyforms_delete_attachment');

function buddyforms_ajax_edit_post(){

    parse_str($_POST['data'], $formdata);

    echo buddyforms_ajax_create_edit_form($formdata);

    //  buddyforms_after_save_post_redirect(get_permalink( $post_id ));


    die();

}
add_action('wp_ajax_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post');
add_action('wp_ajax_nopriv_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post');


function buddyforms_ajax_create_edit_form( $formdata ) {
    global $current_user, $buddyforms;

    $hasError = false;

    get_currentuserinfo();

    extract(shortcode_atts(array(
        'post_type' 	=> '',
        'the_post'		=> 0,
        'post_id'		=> 0,
        'revision_id' 	=> false,
        'form_slug' 	=> 0,
    ), $formdata));

//    echo '<pre>';
//    print_r($formdata);
//    echo '</pre>';

    if(!empty($revision_id)) {
        $the_post = get_post( $revision_id );
    } else {
        $post_id = apply_filters('bf_create_edit_form_post_id', $post_id);
        $the_post = get_post($post_id, 'OBJECT');
    }

    // If post_id == 0 a new post is created
    if($post_id == 0){
        require_once(ABSPATH . 'wp-admin/includes/admin.php');
        $the_post = get_default_post_to_edit($post_type);
    }

    if(isset($buddyforms['buddyforms'][$form_slug]['form_fields']))
        $customfields = $buddyforms['buddyforms'][$form_slug]['form_fields'];


    $comment_status = $buddyforms['buddyforms'][$form_slug]['comment_status'];
    if(isset($formdata['comment_status']))
        $comment_status = $formdata['comment_status'];

    $post_excerpt = '';
    if(isset($formdata['post_excerpt']))
        $post_excerpt = $formdata['post_excerpt'];

    $action			= 'save';

    $post_status	= $buddyforms['buddyforms'][$form_slug]['status'];
    if($post_id != 0){
        $action = 'update';
        $post_status = get_post_status( $post_id );
    }
    if(isset($formdata['status']))
        $post_status = $formdata['status'];

    $args = Array(
        'post_id'		    => $post_id,
        'action'			=> $action,
        'form_slug'			=> $form_slug,
        'post_type' 		=> $post_type,
        'post_excerpt'		=> $post_excerpt,
        'post_author' 		=> $current_user->ID,
        'post_status' 		=> $post_status,
        'post_parent' 		=> 0,
        'comment_status'	=> $comment_status,
    );

    $post_id = bf_post_control($args);

    if($post_id != 0){

        // Check if the post has post meta / custom fields
        if(isset($customfields))
            bf_update_post_meta($post_id, $customfields);

        $hasError = bf_set_post_thumbnail($post_id);

        bf_media_handle_upload($post_id);

        // Save the Form slug as post meta
        update_post_meta($post_id, "_bf_form_slug", $form_slug);

    } else {
        $hasError = true;
    }


    // Display the message
    if( empty( $hasError ) ) :

        if(isset( $formdata['post_id'] ) && ! empty( $formdata['post_id'] )){
            $info_message = __('The ', 'buddyforms') . $buddyforms['buddyforms'][$form_slug]['singular_name']. __(' has been successfully updated', 'buddyforms'). '<a href="'.get_permalink($post_id).'" target="_blank"> View '.$buddyforms['buddyforms'][$form_slug]['singular_name'].'</a>';
            $form_notice = '<div class="info alert">'.$info_message.'</div>';
        } else {
            $info_message = __('The ', 'buddyforms') . $buddyforms['buddyforms'][$form_slug]['singular_name']. __(' has been successfully created', 'buddyforms'). '<a href="'.get_permalink($post_id).'" target="_blank"> View '.$buddyforms['buddyforms'][$form_slug]['singular_name'].'</a>';
            $form_notice = '<div class="info alert">'.$info_message.'</div>';
        }

    else:

        $error_message = __('Error! There was a problem submitting the post ;-(', 'buddyforms');
        $form_notice = '<div class="error alert">'.$error_message.'</div>';

        if(!empty($fileError))
            $form_notice = '<div class="error alert">'.$fileError.'</div>';

    endif;

    do_action('buddyforms_after_save_post', $post_id);

    $args = array(
        'post_type' 	=> $post_type,
        'the_post'		=> $the_post,
        'customfields'  => $customfields,
        'post_id'		=> $post_id,
        'revision_id' 	=> $revision_id,
        'redirect_to'   => $formdata['redirect_to'],
        'form_slug' 	=> $form_slug,
        'form_notice'   => $form_notice,
    );


    $form_html = buddyforms_form_html( $args );
    return $form_html;

}

function buddyforms_after_save_post_redirect($url){
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';
    echo $string;
}


