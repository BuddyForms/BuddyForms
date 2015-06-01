<?php

function buddyforms_form_html( $args ){
    global  $buddyforms;

    extract(shortcode_atts(array(
        'post_type' 	=> '',
        'the_post'		=> 0,
        'customfields'  => false,
        'post_id'		=> false,
        'revision_id' 	=> false,
        'redirect_to'   => $_SERVER['REQUEST_URI'],
        'form_slug' 	=> '',
        'form_notice'   => ''
    ), $args));


//    echo '<pre>';
//    print_r($args);
//    echo '</pre>';


    session_id('buddyforms-create-edit-form');

    $form_html = '<div class="the_buddyforms_form">';


    if ( !is_user_logged_in() ) :
        $wp_login_form = '<h3>' . __('You need to be logged in to use this Form', 'buddyforms') . '</h3>';
        $wp_login_form .= apply_filters( 'buddyforms_wp_login_form', wp_login_form(array('echo' => false)) );
        return $wp_login_form;
    endif;


    $user_can_edit = false;
    if( empty($post_id) && current_user_can('buddyforms_' . $form_slug . '_create')) {
        $user_can_edit = true;
    } elseif( !empty($post_id) && current_user_can('buddyforms_' . $form_slug . '_edit')){
        $user_can_edit = true;
    }

    if ( $user_can_edit == false ){
        $error_message = __('You do not have the required user role to use this form', 'buddyforms');
        return '<div class="error alert">'.$error_message.'</div>';
    }

    $form_html .= '<div class="form_wrapper">';

    // Create the form object
    $form = new Form("editpost");

    // Set the form attribute
    $form->configure(array(
        "prevent" => array("bootstrap", "jQuery", "focus"),
        "action" => $redirect_to,
        "view" => new View_Vertical,
        'class' => 'standard-form',
    ));

    $form->addElement(new Element_HTML(do_action('template_notices')));
    $form->addElement(new Element_HTML(wp_nonce_field('buddyforms_form_nonce', '_wpnonce', true, false)));

    $form->addElement(new Element_Hidden("redirect_to"  , $redirect_to));

    $form->addElement(new Element_Hidden("post_id"      , $post_id));
    $form->addElement(new Element_Hidden("revision_id"  , $revision_id));

    $form->addElement(new Element_Hidden("form_slug"    , $form_slug));
    $form->addElement(new Element_Hidden("post_type"    , $post_type));


    if (isset($form_notice))
        $form->addElement(new Element_HTML($form_notice));

    // if the form have custom field to save as post meta data they get displayed here
    bf_form_elements($form, $args);

    $form->addElement(new Element_Hidden("submitted", 'true', array('value' => 'true', 'id' => "submitted")));

    $form_button = apply_filters('buddyforms_create_edit_form_button',new Element_Button(__('Submit', 'buddyforms'), 'submit', array('class' => 'bf-submit', 'name' => 'submitted')));

    if($form_button)
        $form->addElement($form_button);

    $form = apply_filters( 'bf_form_before_render', $form, $args);

    // thats it! render the form!
    ob_start();
    $form->render();
    $form_html .= ob_get_contents();
    ob_clean();

    $form_html .= '<div class="bf_modal"></div></div>';

    if (isset($buddyforms['buddyforms'][$form_slug]['revision']) && $post_id != 0) {
        ob_start();
        buddyforms_wp_list_post_revisions($post_id);
        $form_html .= ob_get_contents();
        ob_clean();
    }
    $form_html .= '</div>';

    return $form_html;
}