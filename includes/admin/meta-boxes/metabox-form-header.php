<?php
function buddyforms_metabox_form_header(){

    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);
    $viwe_form_permalink = isset($buddyform['attached_page']) ? get_permalink($buddyform['attached_page']) : '';

echo   '

    <a href="'.$viwe_form_permalink . 'view/' . $post->post_name . '/" target="_new">'.__('View Form Posts', 'buddyforms').'</a> -
    <a href="'.$viwe_form_permalink . 'create/' . $post->post_name . '/" target="_new">'.__('View Form', 'buddyforms').'</a>
';

}

add_action( 'edit_form_top', 'buddyforms_metabox_form_header' );