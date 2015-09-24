<?php

function bf_metabox_sidebar()
{
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);


    $form_setup = array();

    $slug = $post->post_name;

//    $form_setup[] = new Element_HTML('
//    <a style="margin-bottom: 5px;" class="button" href="' . get_admin_url() . 'admin.php?page=bf_mail_notification&form_slug=' . $slug . '"> ' . __('Mail Notification', 'buddyforms') . '</a>
//    <a style="margin-bottom: 5px;" class="button" href="' . get_admin_url() . 'admin.php?page=bf_manage_form_roles_and_capabilities&form_slug=' . $slug . '"> ' . __('Roles and Capabilities', 'buddyforms') . '</a>
//    ');



    $form_setup[] = new Element_HTML('

        <h5>' . __('Classic Fields', 'buddyforms') . '</h5>
        <p><a href="Text/' . $slug . '" class="action">' . __('Text', 'buddyforms') . '</a></p>
        <p><a href="Textarea/' . $slug . '" class="action">' . __('Textarea', 'buddyforms') . '</a></p>
        <p><a href="Link/' . $slug . '" class="action">' . __('Link', 'buddyforms') . '</a></p>
        <p><a href="Mail/' . $slug . '" class="action">' . __('Mail', 'buddyforms') . '</a></p>
        <p><a href="Dropdown/' . $slug . '" class="action">' . __('Dropdown', 'buddyforms') . '</a></p>
        <p><a href="Radiobutton/' . $slug . '" class="action">' . __('Radiobutton', 'buddyforms') . '</a></p>
        <p><a href="Checkbox/' . $slug . '" class="action">' . __('Checkbox', 'buddyforms') . '</a></p>
        <h5>Post Fields</h5>
        <p><a href="Content/' . $slug . '/unique" class="action">' . __('Content', 'buddyforms') . '</a></p>
        <p><a href="Taxonomy/' . $slug . '" class="action">' . __('Taxonomy', 'buddyforms') . '</a></p>
        <p><a href="Comments/' . $slug . '/unique" class="action">' . __('Comments', 'buddyforms') . '</a></p>
        <p><a href="Status/' . $slug . '/unique" class="action">' . __('Post Status', 'buddyforms') . '</a></p>
        <p><a href="Featured_Image/' . $slug . '/unique" class="action">' . __('Featured Image', 'buddyforms') . '</a></p>

        <h5>Extras</h5>
        <p><a href="File/' . $slug . '" class="action">' . __('File', 'buddyforms') . '</a></p>
        <p><a href="Hidden/' . $slug . '" class="action">' . __('Hidden', 'buddyforms') . '</a></p>
        <p><a href="Number/' . $slug . '" class="action">' . __('Number', 'buddyforms') . '</a></p>
        <p><a href="HTML/' . $slug . '" class="action">' . __('HTML', 'buddyforms') . '</a></p>
        <p><a href="Date/' . $slug . '" class="action">' . __('Date', 'buddyforms') . '</a></p>

    ');

    foreach($form_setup as $key => $field){
        echo '<div class="buddyforms_field_label">' . $field->getLabel() . '</div>';
        echo '<p class="buddyforms_field_description">' . $field->getShortDesc() . '</p>';
        echo '<div class="buddyforms_form_field">' . $field->render() . '</div>';
    }
}