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

    echo buddyforms_process_post($formdata);

    //buddyforms_after_save_post_redirect(get_permalink( $formdata['post_id'] ));


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


