<?php

function bf_edit_form_screen(){
    global $buddyforms, $wpdb;

    $_GET['post'] = 'asdasd';

    if(!isset($_GET['post']))
        return;
    $form_slug = $_GET['post'];
    $form_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$form_slug' and post_type = 'buddyforms'");




//    echo '<pre>';
//    print_r($buddyforms);
//    echo '</pre>';

    // Get all post types
    $args = array(
        //'public' => true,
        'show_ui' => true
    );
    $output = 'names'; // names or objects, note: names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types = get_post_types($args, $output, $operator);
    $post_types_none['none'] = 'none';
    $post_types = array_merge($post_types_none, $post_types);

    // Form starts
    $form = new Form("buddyforms_form");
    $form->configure(array(
        "prevent" => array("bootstrap", "jQuery"),
        "action" => $_SERVER['REQUEST_URI'],
        "view" => new View_Inline
    ));


    if (isset($buddyforms['buddyforms'])) {
        $buddyform = $buddyforms['buddyforms'][$_GET['post']];

            $slug = $buddyform['slug'];
            $slug = sanitize_title($slug);
            if (empty($slug)) {
                $slug = $bf_mod5;
            }
            $buddyform['slug'] = $slug;

            if (empty($buddyform['name']))
                $buddyform['name'] = $slug;

            if (empty($buddyform['singular_name']))
                $buddyform['singular_name'] = $slug;



            $form->addElement(new Element_HTML('<div id="buddyforms_forms_builder_' . $buddyform['slug'] . '" class="buddyforms_forms_builder">'));





        // Content Starts

//        $form->addElement(new Element_HTML('
//            <div id="poststuff">
//
//
//
//                    <div id="post-body-content">'));




//            $form->addElement(new Element_HTML('
//
//            <h3>' . __('Form Settings for', 'buddyforms') . ' "' . stripslashes($buddyform['name']) . '"'));
//
//            $viwe_form_permalink = get_permalink($buddyform['attached_page']);
//
//            $form->addElement(new Element_HTML('<small style="float:right; padding:15px;"><a href="' . $viwe_form_permalink . 'view/' . $buddyform['slug'] . '/" target="_new">' . __('View Form Posts', 'buddyforms') . '</a> - '));
//            $form->addElement(new Element_HTML(' <a href="' . $viwe_form_permalink . 'create/' . $buddyform['slug'] . '/" target="_new">' . __('View Form', 'buddyforms') . '</a></small></h3>'));
//
//            if (empty($buddyform['name']) || empty($buddyform['singular_name']) || empty($buddyform['slug']) || $buddyform['post_type'] == 'none' || $buddyform['attached_page'] == '')
//                $form->addElement(new Element_HTML('<div class="bf-error"><h4>' . __('This form is broken please check your required settings under Form Control and save the form') . '</h4></div>'));
//
//            $form->addElement(new Element_HTML('<p class="loading-animation-order alert alert-success">' . __('Save new order', 'buddyforms') . ' <i class="icon-ok"></i></p>'));
//            $form->addElement(new Element_HTML('<div class="loading-animation-new alert alert-success">' . __('Load new element', 'buddyforms') . ' <i class="icon-ok"></i></div>'));
//            $form->addElement(new Element_HTML('<div class="hidden loading-animation-save alert alert-success">' . __('Saving the Form', 'buddyforms') . ' <i class="icon-ok"></i></div>'));
//            $form->addElement(new Element_HTML('<div class="hidden loading-animation-error alert alert-error">' . __('Something went wrong, please try again', 'buddyforms') . ' <i class="icon-ok"></i></div>'));

            $sortArray = array();

            if (!empty($buddyform['form_fields'])) {
                foreach ($buddyform['form_fields'] as $key => $array) {
                    $sortArray[$key] = $array['order'];
                }
                array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $buddyform['form_fields']);
            }


//            $form->addElement(new Element_HTML('
//            <div class="accordion-group postbox">
//                <div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_' . $buddyform['slug'] . '" href="#accordion_' . $buddyform['slug'] . '_status"><b>' . __('Form Control', 'buddyforms') . '</b></p></div>
//                <div id="accordion_' . $buddyform['slug'] . '_status" class="accordion-body collapse">
//                    <div class="accordion-inner bf-main-settings">'));
//            $form->addElement(new Element_Textbox('<b>' . __("Name", 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][name]", array('value' => stripslashes($buddyform['name']), 'required' => 1)));
//            $form->addElement(new Element_Textbox('<b>' . __("Singular Name", 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][singular_name]", array('value' => stripslashes($buddyform['singular_name']), 'required' => 1)));
//            $form->addElement(new Element_Textbox('<b>' . __("Slug", 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][slug]", array('shortDesc' => __('If you change the slug you need to reset the roles and capabilities', 'buddyforms'), 'value' => $buddyform['slug'], 'required' => 1)));
//
//            $form->addElement(new Element_HTML('<br><hr /><br />'));
//
//            $form->addElement(new Element_HTML('<div class="post_form_' . $buddyform['slug'] . ' form_type_settings" >'));
//            $form->addElement(new Element_HTML('<div class="buddyforms_accordion_right">'));
//
//            $form->addElement(new Element_HTML('<div class="innerblock revision">'));
//
//            $revision = 'false';
//            if (isset($buddyform['revision']))
//                $revision = $buddyform['revision'];
//            $form->addElement(new Element_Checkbox('', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][revision]", array('Revision' => "<b>" . __('Revision', 'buddyforms') . "</b>"), array('shortDesc' => __('Enable frontend revision control.', 'buddyforms'), 'value' => $revision)));
//
//            $admin_bar = 'false';
//            if (isset($buddyform['admin_bar']))
//                $admin_bar = $buddyform['admin_bar'];
//
//            $form->addElement(new Element_Checkbox('<br>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][admin_bar]", array('Admin Bar' => "<b>" . __('Add to Admin Bar', 'buddyforms') . "</b>"), array('value' => $admin_bar)));
//
//            $edit_link = 'all';
//            if (isset($buddyform['edit_link']))
//                $edit_link = $buddyform['edit_link'];
//
//            $form->addElement(new Element_Radio('<b>' . __("Overwrite Frontend 'Edit Post' Link", 'buddyforms') . '</b><br><span class="help-inline">' . __('The link to the backend will be changed', 'buddyforms') . "<br>" . __('to use the frontend editing.', 'buddyforms') . '</span>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][edit_link]", array('none' => 'None', 'all' => __("All Edit Links", 'buddyforms'), 'my-posts-list' => __("Only in My Posts List", 'buddyforms')), array('value' => $edit_link)));
//
//
//            $form->addElement(new Element_HTML('</div>'));
//            $form->addElement(new Element_HTML('</div>'));
//            $form->addElement(new Element_HTML('<div class="buddyforms_accordion_left">'));
//
//
//            $status = 'false';
//            if (isset($buddyform['status']))
//                $status = $buddyform['status'];
//
//            $form->addElement(new Element_Select('<b>' . __("Status", 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][status]", array('publish', 'pending', 'draft'), array('value' => $status)));
//
//            $comment_status = 'false';
//            if (isset($buddyform['comment_status']))
//                $comment_status = $buddyform['comment_status'];
//
//            $form->addElement(new Element_Select('<b>' . __("Comment Status", 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][comment_status]", array('open', 'closed'), array('value' => $comment_status)));
//
//            $post_type = 'false';
//            if (isset($buddyform['post_type']))
//                $post_type = $buddyform['post_type'];
//
//            $form->addElement(new Element_Select('<b>' . __("Post Type", 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][post_type]", $post_types, array('value' => $post_type, 'required' => 1)));
//
//            $attached_page = 'false';
//            if (isset($buddyform['attached_page']))
//                $attached_page = $buddyform['attached_page'];
//
//            $args = array(
//                'depth' => 1,
//                'id' => $key,
//                'echo' => FALSE,
//                'sort_column' => 'post_title',
//                'show_option_none' => __('none', 'buddyforms'),
//                'name' => "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][attached_page]",
//                'class' => 'postform',
//                'selected' => $attached_page
//            );
//            $form->addElement(new Element_HTML("<br><br><p><span class='required'>* </span><b>" . __('Attach Page to this Form', 'buddyforms') . "</b></p>"));
//            $form->addElement(new Element_HTML(wp_dropdown_pages($args)));
//
//            $form->addElement(new Element_HTML(' <a href="' . admin_url(add_query_arg(array('post_type' => 'page'), 'post-new.php')) . '" class="button">' . __('Create New', 'buddyforms') . '</a>'));
//
//            $form->addElement(new Element_HTML("<p><span class='help-inline' >" . __('Needs to be a parent page') . "</span></p>"));
//
//            $form->addElement(new Element_HTML('<div><hr>'));
//
//
//            $after_submit = isset($buddyform['after_submit']) ? $buddyform['after_submit'] : 'display_form';
//            $form->addElement(new Element_Radio('<b>' . __("After Submission", 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][after_submit]", array('display_form' => 'Display the Form and Message and continue editing', 'display_post' => 'Display the Post', 'display_message' => 'Just display a Message'), array('value' => $after_submit, 'id' => 'after_submit_hidden' . $buddyform['slug'], 'class' => 'after_submit_hidden')));
//
//
//            $after_submit_message_text = isset($buddyform['after_submit_message_text']) ? $buddyform['after_submit_message_text'] : 'The [form_singular_name] [post_title] has been successfully updated!<br>1. [post_link]<br>2. [edit_link]';
//            $form->addElement(new Element_Textarea('<br><b>' . __('Add your Message Text', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][after_submit_message_text]", array('rows' => 3, 'style' => "width:100%", 'value' => $after_submit_message_text, 'shortDesc' => __('<p>
//                                        <small>You can use special shortcodes to add dynamic content:<br>
//                                            [form_singular_name] = Singular Name<br>
//                                            [post_title] = The Post Title<br>
//                                            [post_link] = The Post Permalink<br>
//                                            [edit_link] = Link to the Post Edit Form</small><br>
//
//                                    </p>', 'buddyforms'))));
//
//
//            $bf_ajax = false;
//            if (isset($buddyform['bf_ajax']))
//                $bf_ajax = $buddyform['bf_ajax'];
//            $form->addElement(new Element_Checkbox('<br><b>AJAX</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][bf_ajax]", array('bf_ajax' => __('Disable ajax form submission.', 'buddyforms')), array('shortDesc' => __('', 'buddyforms'), 'value' => $bf_ajax)));
//
//            $form->addElement(new Element_HTML('</div>'));
//
//            $form->addElement(new Element_HTML('</div>'));
//
//            $form->addElement(new Element_HTML('</div>'));
//
//            $form->addElement(new Element_HTML('
//                    </div>
//                </div>
//            </div>'));


//            $form->addElement(new Element_HTML('
//            <br>
//            <h4>' . __('Form Builder', 'buddyforms') . '</h4>
//            ' . __('Add additional form elements from the right box "Form Elements". Change the order via drag and drop.', 'buddyforms') . '
//            '));

            $form->addElement(new Element_HTML('
            <div class="fields_header">
                <table class="wp-list-table widefat fixed posts">
                    <thead>
                        <tr>
                            <th class="field_order">Field Order</th>
                            <th class="field_label">Field Label</th>
                            <th class="field_name">Field Slug</th>
                            <th class="field_type">Field Type</th>
                        </tr>
                    </thead>
                </table>
             </div>
            '));

            $form->addElement(new Element_HTML('<ul id="sortable_' . $buddyform['slug'] . '" class="sortable sortable_' . $buddyform['slug'] . '">'));


            if (isset($buddyform['form_fields'])) {

                foreach ($buddyform['form_fields'] as $field_id => $customfield) {

                    if (isset($customfield['slug']))
                        $slug = sanitize_title($customfield['slug']);

                    if (empty($slug))
                        $slug = sanitize_title($customfield['name']);

                    if (empty($buddyform['singular_name']))
                        $buddyform['singular_name'] = $key;

                    if (empty($slug))
                        $slug = $key;

                    if ($slug != '' && isset($customfield['name'])) {
                        $args = Array(
                            'slug' => $slug,
                            'field_position' => $customfield['order'],
                            'field_id' => $field_id,
                            'form_slug' => $buddyform['slug'],
                            'post_type' => $buddyform['post_type'],
                            'field_type' => $customfield['type']
                        );
                        $form->addElement(new Element_HTML(buddyforms_view_form_fields($args)));




                    }

                }
            }


            $form->addElement(new Element_HTML('</ul>'));
        $form->addElement(new Element_HTML('</div>'));
    }


//    $form->addElement(new Element_HTML('</div></div>'));





    $form->render();
}