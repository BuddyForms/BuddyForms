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
    global $buddyforms;


    parse_str($_POST['data'], $formdata);

    $args = buddyforms_process_post($formdata);


    switch ($buddyforms['buddyforms'][$formdata['form_slug']]['after_submit']) {
        case 'display_post':
            buddyforms_after_save_post_redirect(get_permalink( $args['post_id'] ));
            break;
        case 'display_message':
            $permalink = get_permalink( $buddyforms['buddyforms'][$args['form_slug']]['attached_page'] );

            $display_message = $buddyforms['buddyforms'][$formdata['form_slug']]['after_submit_message_text'];
            $display_message = str_ireplace('[form_singular_name]', $buddyforms['buddyforms'][$args['form_slug']]['singular_name'], $display_message);
            $display_message = str_ireplace('[post_title]', get_the_title($args['post_id']), $display_message);
            $display_message = str_ireplace('[post_link]', '<a title="Display Post" href="'. get_permalink( $args['post_id'] ) .'"">' . __( 'Display Post', 'buddyforms' ) .'</a>', $display_message);
            $display_message = str_ireplace('[edit_link]', '<a title="Edit Post" href="'. $permalink.'edit/'.$args['form_slug'].'/'. $args['post_id'] .'"">' . __( 'Continue Editing', 'buddyforms' ) .'</a>', $display_message);
            echo $display_message;
            break;
        default:
            echo buddyforms_form_html( $args );

    }

    die();

}
add_action('wp_ajax_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post');
add_action('wp_ajax_nopriv_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post');


function buddyforms_after_save_post_redirect($url){
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';
    echo $string;
}


