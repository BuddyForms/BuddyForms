<?php

function buddyforms_metabox_form_setup(){
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);

    // Get all post types
    $args = array(
        'show_ui' => true
    );
    $output = 'names'; // names or objects, note: names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types = get_post_types($args, $output, $operator);
    $post_types_none['none'] = 'none';
    $post_types = array_merge($post_types_none, $post_types);


    $form_setup = array();

    $name = get_the_title();
    $slug = $post->post_name;
    $singular_name = isset($buddyform['singular_name']) ? stripslashes($buddyform['singular_name']) : '';



    //$form_setup[] = new Element_HTML('<div class="subcontainer tab-pane fade in" id="subcon' . $slug . '">');





    $form_setup[] = new Element_HTML('<div id="bf_admin_wrap" class="bf-main-settings buddyforms_forms_builder">');

    $form_setup[] = new Element_Hidden('buddyforms_options[name]', $name);
    $form_setup[] = new Element_Hidden('buddyforms_options[slug]', $slug);
    $form_setup[] = new Element_Textbox( __("Singular Name", 'buddyforms'), "buddyforms_options[singular_name]", array('value' => $singular_name, 'required' => 1));

//    $form_setup[] = new Element_HTML('<div class="accordion-group postbox">
//    <div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_' . $slug . '" href="#accordion_' . $slug . '_status"><b>' . __('Form Settings', 'buddyforms') . '</b></p></div>
//        <div id="accordion_' . $slug . '_status" class="accordion-body collapse">
//            <div class="accordion-inner">');

            $form_setup[] = new Element_HTML('<div class="post_form_' . $slug . ' form_type_settings" >
            <div class="buddyforms_accordion_right"><div class="innerblock revision">');

            $revision = 'false';
            if (isset($buddyform['revision']))
                $revision = $buddyform['revision'];
            $form_setup[] = new Element_Checkbox('', "buddyforms_options[revision]", array('Revision' => "<b>" . __('Revision', 'buddyforms') . "</b>"), array('shortDesc' => __('Enable frontend revision control.', 'buddyforms'), 'value' => $revision));

            $admin_bar = 'false';
            if (isset($buddyform['admin_bar']))
                $admin_bar = $buddyform['admin_bar'];

            $form_setup[] = new Element_Checkbox('<br>', "buddyforms_options[admin_bar]", array('Admin Bar' => "<b>" . __('Add to Admin Bar', 'buddyforms') . "</b>"), array('value' => $admin_bar));

            $edit_link = 'all';
            if (isset($buddyform['edit_link']))
                $edit_link = $buddyform['edit_link'];

            $form_setup[] = new Element_Radio('<br><b>' . __("Overwrite Frontend 'Edit Post' Link", 'buddyforms') . '</b><br><span class="help-inline">' . __('The link to the backend will be changed', 'buddyforms') . "<br>" . __('to use the frontend editing.', 'buddyforms') . '</span>', "buddyforms_options[edit_link]", array('none' => 'None', 'all' => __("All Edit Links", 'buddyforms'), 'my-posts-list' => __("Only in My Posts List", 'buddyforms')), array('view' => 'vertical', 'value' => $edit_link));

            $form_setup[] = new Element_HTML('</div></div><div class="buddyforms_accordion_left">');


            $status = 'false';
            if (isset($buddyform['status']))
                $status = $buddyform['status'];

            $form_setup[] = new Element_Select('<b>' . __("Status", 'buddyforms') . '</b>', "buddyforms_options[status]", array('publish', 'pending', 'draft'), array('value' => $status));

            $comment_status = 'false';
            if (isset($buddyform['comment_status']))
                $comment_status = $buddyform['comment_status'];

            $form_setup[] = new Element_Select('<b>' . __("Comment Status", 'buddyforms') . '</b>', "buddyforms_options[comment_status]", array('open', 'closed'), array('value' => $comment_status));

            $post_type = 'false';
            if (isset($buddyform['post_type']))
                $post_type = $buddyform['post_type'];

            $form_setup[] = new Element_Select('<b>' . __("Post Type", 'buddyforms') . '</b>', "buddyforms_options[post_type]", $post_types, array('value' => $post_type, 'required' => 1));

            $attached_page = 'false';
            if (isset($buddyform['attached_page']))
                $attached_page = $buddyform['attached_page'];

            $args = array(
                'depth' => 1,
                'id' => $attached_page,
                'echo' => FALSE,
                'sort_column' => 'post_title',
                'show_option_none' => __('none', 'buddyforms'),
                'name' => "buddyforms_options[attached_page]",
                'class' => 'postform',
                'selected' => $attached_page
            );
            $form_setup[] = new Element_HTML("<br><br><p><span class='required'>* </span><b>" . __('Attach Page to this Form', 'buddyforms') . "</b></p>");
            $form_setup[] = new Element_HTML(wp_dropdown_pages($args));

            $form_setup[] = new Element_HTML(' <a href="' . admin_url(add_query_arg(array('post_type' => 'page'), 'post-new.php')) . '" class="button">' . __('Create New', 'buddyforms') . '</a>');

            $form_setup[] = new Element_HTML("<p><span class='help-inline' >" . __('Needs to be a parent page') . "</span></p>");

            $form_setup[] = new Element_HTML('<div><hr>');


            $after_submit = isset($buddyform['after_submit']) ? $buddyform['after_submit'] : 'display_form';
            $form_setup[] = new Element_Radio('<b>' . __("After Submission", 'buddyforms') . '</b>', "buddyforms_options[after_submit]", array('display_form' => 'Display the Form and Message and continue editing', 'display_post' => 'Display the Post', 'display_message' => 'Just display a Message'), array('value' => $after_submit, 'id' => 'after_submit_hidden' . $slug, 'class' => 'after_submit_hidden'));


            $after_submit_message_text = isset($buddyform['after_submit_message_text']) ? $buddyform['after_submit_message_text'] : 'The [form_singular_name] [post_title] has been successfully updated!<br>1. [post_link]<br>2. [edit_link]';
            $form_setup[] = new Element_Textarea('<br><b>' . __('Add your Message Text', 'buddyforms') . '</b>', "buddyforms_options[after_submit_message_text]", array('rows' => 3, 'style' => "width:100%", 'value' => $after_submit_message_text, 'shortDesc' => __('<p>
                                        <small>You can use special shortcodes to add dynamic content:<br>
                                            [form_singular_name] = Singular Name<br>
                                            [post_title] = The Post Title<br>
                                            [post_link] = The Post Permalink<br>
                                            [edit_link] = Link to the Post Edit Form</small><br>

                                    </p>', 'buddyforms')));


            $bf_ajax = false;
            if (isset($buddyform['bf_ajax']))
                $bf_ajax = $buddyform['bf_ajax'];
            $form_setup[] = new Element_Checkbox('<br><b>AJAX</b>', "buddyforms_options[bf_ajax]", array('bf_ajax' => __('Disable ajax form submission.', 'buddyforms')), array('shortDesc' => __('', 'buddyforms'), 'value' => $bf_ajax));

    $form_setup[] = new Element_HTML('</div></div></div></div>');?>

    <?php
    foreach($form_setup as $key => $field){
        echo '<div class="buddyforms_field_label">' . $field->getLabel() . '</div>';
        echo '<div class="buddyforms_field_description">' . $field->getShortDesc() . '</div>';
        echo '<div class="buddyforms_form_field">' . $field->render() . '</div>';
    }
    ?>

<?php

}

//add_action('edit_form_after_title','buddyforms_metabox_form_setup');