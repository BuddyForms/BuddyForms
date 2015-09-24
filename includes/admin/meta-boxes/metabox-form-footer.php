<?php
function buddyforms_metabox_form_footer(){

    global $post;

    if($post->post_type != 'buddyforms')
        return;


    //$form_setup[] = new Element_HTML('</div>');

//    foreach($form_setup as $key => $field){
//        echo '<div class="buddyforms_field_label">' . $field->getLabel() . '</div>';
//        echo '<p class="buddyforms_field_description">' . $field->getShortDesc() . '</p>';
//        echo '<div class="buddyforms_form_field">' . $field->render() . '</div>';
//    }


    ?>

<?php


}
add_action( 'edit_form_after_title', 'buddyforms_metabox_form_footer' );