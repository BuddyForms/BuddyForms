<?php

add_action('wp_ajax_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post');
add_action('wp_ajax_nopriv_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post');
function buddyforms_ajax_edit_post(){
    $post_id = $_POST['post_id'];
    $form_slug = get_post_meta($post_id, '_bf_form_slug', true);

    $args = Array(
        'post_id'   => $post_id,
        'form_slug' => $form_slug
    );
    echo buddyforms_create_edit_form( $args );
    die();

}

add_action('wp_ajax_buddyforms_ajax_process_edit_post', 'buddyforms_ajax_process_edit_post');
add_action('wp_ajax_nopriv_buddyforms_ajax_process_edit_post', 'buddyforms_ajax_process_edit_post');
function buddyforms_ajax_process_edit_post(){
    global $buddyforms;

    if( isset($_POST['data'])){
        parse_str($_POST['data'], $formdata);
        $_POST = $formdata;
    }

    $args = buddyforms_process_post($formdata);


    switch ($buddyforms['buddyforms'][$_POST['form_slug']]['after_submit']) {
        case 'display_post':
            $json = Array(
                'form_notice' => buddyforms_after_save_post_redirect(get_permalink( $args['post_id'] )),
            );
            echo json_encode($json);
            break;
        case 'display_message':
            $permalink = get_permalink( $buddyforms['buddyforms'][$args['form_slug']]['attached_page'] );

            $display_message = $buddyforms['buddyforms'][$_POST['form_slug']]['after_submit_message_text'];
            $display_message = str_ireplace('[form_singular_name]', $buddyforms['buddyforms'][$args['form_slug']]['singular_name'], $display_message);
            $display_message = str_ireplace('[post_title]', get_the_title($args['post_id']), $display_message);
            $display_message = str_ireplace('[post_link]', '<a title="Display Post" href="'. get_permalink( $args['post_id'] ) .'"">' . __( 'Display Post', 'buddyforms' ) .'</a>', $display_message);
            $display_message = str_ireplace('[edit_link]', '<a title="Edit Post" href="'. $permalink.'edit/'.$args['form_slug'].'/'. $args['post_id'] .'"">' . __( 'Continue Editing', 'buddyforms' ) .'</a>', $display_message);
            $json = Array(
                'form_remove' => 'true',
                'form_notice' => $display_message,
            );
            echo json_encode($json);
            break;
        default:
             $json = Array(
                'post_id'     => $args['post_id'],
                'revision_id' => $args['revision_id'],
                'post_parent' => $args['post_parent'],
                'form_notice' => $args['form_notice'],
            );
            echo json_encode($json);
            break;
    }

    die();

}

add_action('wp_ajax_buddyforms_ajax_delete_post', 'buddyforms_ajax_delete_post');
add_action('wp_ajax_nopriv_buddyforms_ajax_delete_post', 'buddyforms_ajax_delete_post');
function buddyforms_ajax_delete_post(){
    global $current_user;
    get_currentuserinfo();

    $post_id    = $_POST['post_id'];
    $the_post	= get_post( $post_id );

    $form_slug = get_post_meta($post_id, '_bf_form_slug', true);
    if(!$form_slug){
        _e('You are not allowed to delete this entry! What are you doing here?', 'buddyforms');
        return;
    }

    // Check if the user is author of the post
    $user_can_delete = false;
    if ($the_post->post_author == $current_user->ID){
        $user_can_delete = true;
    }
    $user_can_delete = apply_filters( 'buddyforms_user_can_delete', $user_can_delete );
    if ( $user_can_delete == false ){
        _e('You are not allowed to delete this entry! What are you doing here?', 'buddyforms');
        return;
    }

    // check if the user has the roles roles and capabilities
    $user_can_delete = false;

    if( current_user_can('buddyforms_' . $form_slug . '_delete')){
        $user_can_delete = true;
    }
    $user_can_delete = apply_filters( 'buddyforms_user_can_delete', $user_can_delete );
    if ( $user_can_delete == false ){
        _e('You do not have the required user role to use this form', 'buddyforms');
        return;
    }

    do_action('buddyforms_delete_post',$post_id);

    wp_delete_post( $post_id );

    echo $post_id;
    die();
}

add_action('wp_ajax_buddyforms_delete_attachment', 'buddyforms_delete_attachment');
add_action('wp_ajax_nopriv_buddyforms_delete_attachment', 'buddyforms_delete_attachment');
function buddyforms_delete_attachment(){

    $delete_attachment_id = $_POST['delete_attachment_id'];
    $delete_attachment_href = $_POST['delete_attachment_href'];

    $delete_attachment_attr = explode('/',$delete_attachment_href);

    wp_delete_attachment( $delete_attachment_id );

    delete_post_meta($delete_attachment_attr[0], $delete_attachment_attr[1]);

    echo $_POST['delete_attachment_id'];

    die();

}

function buddyforms_after_save_post_redirect($url){
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';
    return $string;
}


