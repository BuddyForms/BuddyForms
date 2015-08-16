<?php
/**
 * Create the BuddyForms settings page
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_settings_page(){
    global $buddyforms, $bf_mod5;

    // Get all needed values
    BuddyForms::set_globals();

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

    $form->addElement(new Element_HTML('<div class="tab-content"><div class="subcontainer tab-pane fade in active" id="general-settings">'));

    $form->addElement(new Element_HTML('<div class="hero-unit-konrad">'));

    if (isset($buddyforms['buddyforms']) && count($buddyforms['buddyforms']) > 0) {

        $form->addElement(new Element_HTML('
        <div class="alignleft actions bulkactions">
            <select name="bf_bulkactions">
                <option value="-1" selected="selected">'.__('Bulk Actions','buddyforms').'</option>
                <option value="delete">'.__('Delete Permanently','buddyforms').'</option>
            </select>
            <button type="submit" class="button action" name="action" value="Apply">'.__('Apply','buddyforms').'</button>

        </div><br class="clear"><br>'));

        $form->addElement(new Element_HTML('
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                        <label class="screen-reader-text" for="cb-select-all-1">' . __('Select All', 'buddyforms') . ' </label>
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <th scope="col" id="name" class="manage-column column-comment sortable desc" style="width: 360px;">' . __('Name', 'buddyforms') . '</th>
                    <th scope="col" id="slug" class="manage-column column-description" style="">' . __('Slug', 'buddyforms') . '</th>
                    <th scope="col" id="attached-post-type" class="manage-column column-status" style="">' . __('Attached Post Type', 'buddyforms') . '</th>
                    <th scope="col" id="attached-page" class="manage-column column-status" style="">' . __('Attached Page', 'buddyforms') . '</th>

            </thead>'));
        foreach ($buddyforms['buddyforms'] as $key => $buddyform) {

            $slug = $buddyform['slug'];
            $slug = sanitize_title($slug);
            if(empty($slug)){
                $slug = $bf_mod5;
            }
            $buddyform['slug'] = $slug;

            if(empty($buddyform['name']))
                $buddyform['name'] = $slug;

            if(empty($buddyform['singular_name']))
                $buddyform['singular_name'] = $slug;

            $form->addElement(new Element_HTML(' <tr>
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="aid-' . $buddyform['slug'] . '">' . stripslashes($buddyform['name']) . '</label>
                        <input type="checkbox" name="bf_bulkactions_slugs[]" value="' . $buddyform['slug'] . '" id="aid-' . $buddyform['slug'] . '">


                    </th>
                    <td class="name column-name">

                    <div class="showhim">'.stripslashes($buddyform['name']).'<div class="showme"><a  href="#subcon'.$buddyform['slug'].'" data-toggle="tab">'.__('Form Builder','buddyforms').'</a> | <a href="'.get_admin_url().'admin.php?page=bf_mail_notification&form_slug='.$buddyform['slug'].'"> '.__('Mail Notification','buddyforms').'</a> | <a href="'.get_admin_url().'admin.php?page=bf_manage_form_roles_and_capabilities&form_slug='.$buddyform['slug'].'">'.__('Roles and Capabilities','buddyforms').'</a></div></div>
                    </td>'
            ));

            $form->addElement(new Element_HTML('<td class="slug column-slug"> '));
            $form->addElement(new Element_HTML(isset($buddyform['slug']) ? $buddyform['slug'] : '--'));
            $form->addElement(new Element_HTML('</td>'));

            $form->addElement(new Element_HTML('<td class="post_type column-post_type bf-error-text"> '));

            $post_type_html = $buddyform['post_type'];
            $post_type = isset($buddyform['post_type']) ? $buddyform['post_type'] : 'none';

            if(!post_type_exists($post_type))
                $post_type_html = '<p>Post Type ' . $post_type . ' not Exists</p>';

            if($post_type == 'none')
                $post_type_html = '<p>Post Type not Selected</p>';

            $form->addElement(new Element_HTML($post_type_html));
            $form->addElement(new Element_HTML('</td>'));

            $form->addElement(new Element_HTML('<td class="attached_page column-attached_page bf-error-text"> '));

            if( isset($buddyform['attached_page']) && empty($buddyform['attached_page']) ){
                $attached_page = '<p>No Page Attached</p>';
            } elseif(isset($buddyform['attached_page']) && $attached_page_title = get_the_title($buddyform['attached_page'])) {
                $attached_page = $attached_page_title;
            } else {
                $attached_page = '<p>Page not Exists</p>';
            }

            $form->addElement(new Element_HTML($attached_page));
            $form->addElement(new Element_HTML('</td>'));

        }
        $form->addElement(new Element_HTML('</table>'));
    } else {
        $form->addElement(new Element_HTML('<div class="bf-row"><div class="bf-half-col bf-left"><div class="bf-col-content bf_no_form"><h3 style="margin-top: 30px;">' . __('No Forms here so far...', 'buddyforms') . '</h3> <a href="' . get_admin_url() . 'admin.php?page=create-new-form" class="button-primary add-new-h3" style="font-size: 15px;">' . __('Create A New Form', 'buddyforms') . '</a></div></div></div>'));
    }

    $form->addElement(new Element_HTML('</div></div>'));

    if (isset($buddyforms['buddyforms'])) {
        foreach ($buddyforms['buddyforms'] as $key => $buddyform) {

            $slug = $buddyform['slug'];
            $slug = sanitize_title($slug);
            if(empty($slug)){
                $slug = $bf_mod5;
            }
            $buddyform['slug'] = $slug;

            if(empty($buddyform['name']))
                $buddyform['name'] = $slug;

            if(empty($buddyform['singular_name']))
                $buddyform['singular_name'] = $slug;

            $form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="subcon' . $buddyform['slug'] . '">'));

            $form->addElement(new Element_HTML('
                        <div class="accordion_sidebar" id="accordion_' . $buddyform['slug'] . '">
                            <div class="accordion-group postbox">
                                <div class="accordion-heading"><p class="accordion-toggle">' . __('Save Form Settings', 'buddyforms') . '</p></div>
                                <div id="accordion_' . $buddyform['slug'] . '_save" class="accordion-body">
                                    <div class="accordion-inner">'));

            $form->addElement(new Element_HTML('<a class="button" href="'.get_admin_url().'admin.php?page=buddyforms_options_page">'.__('Cancel', 'buddyforms').'</a>'));

            $form->addElement(new Element_Button('button', 'button', array('id' => $buddyform['slug'], 'class' => 'button dele_form', 'name' => 'dele_form', 'value' => __('Delete', 'buddyforms'))));


            $form->addElement(new Element_HTML('<button type="submit" class="button-primary" style="float: right" name="action" value="Save">Save</button>'));
            //$form->addElement(new Element_Button('submit', 'submit', array('action' => 'action', 'id' => 'submited', 'name' => 'form_save_action', 'value' => __('Save', 'buddyforms'), 'class' => 'button-primary', 'style' => 'float: right;')));

            $form->addElement(new Element_HTML('
                                    </div>
                                </div>
                            </div>'));

            $form->addElement(new Element_HTML('

                            <div class="accordion-group postbox">
                                <div class="accordion-heading"><p class="accordion-toggle">'.__('Mail Notification', 'buddyforms').'</p></div>
                                <div id="accordion_' . $buddyform['slug'] . '_save" class="accordion-body">
                                    <div class="accordion-inner">

                                    <a style="margin-bottom: 5px;" class="button" href="'.get_admin_url().'admin.php?page=bf_mail_notification&form_slug='.$buddyform['slug'].'"> '.__('Mail Notification Settings', 'buddyforms').'</a>
                                    <a style="margin-bottom: 5px;" class="button" href="'.get_admin_url().'admin.php?page=bf_manage_form_roles_and_capabilities&form_slug='.$buddyform['slug'].'"> '.__('Roles and Capabilities', 'buddyforms').'</a>
                                    '));

            $form->addElement(new Element_HTML('
                                    </div>
                                </div>
                            </div>'));

            $form->addElement(new Element_HTML('
                            <div class="accordion-group postbox">
                                <div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_' . $buddyform['slug'] . '" href="#accordion_' . $buddyform['slug'] . '_fields"> ' . __('Form Elements', 'buddyforms') . '</p></div>
                                <div id="accordion_' . $buddyform['slug'] . '_fields" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div>
                                            <h5>' . __('Classic Fields', 'buddyforms') . '</h5>
                                            <p><a href="Text/' . $buddyform['slug'] . '" class="action">' . __('Text', 'buddyforms') . '</a></p>
                                            <p><a href="Textarea/' . $buddyform['slug'] . '" class="action">' . __('Textarea', 'buddyforms') . '</a></p>
                                            <p><a href="Link/' . $buddyform['slug'] . '" class="action">' . __('Link', 'buddyforms') . '</a></p>
                                            <p><a href="Mail/' . $buddyform['slug'] . '" class="action">' . __('Mail', 'buddyforms') . '</a></p>
                                            <p><a href="Dropdown/' . $buddyform['slug'] . '" class="action">' . __('Dropdown', 'buddyforms') . '</a></p>
                                            <p><a href="Radiobutton/' . $buddyform['slug'] . '" class="action">' . __('Radiobutton', 'buddyforms') . '</a></p>
                                            <p><a href="Checkbox/' . $buddyform['slug'] . '" class="action">' . __('Checkbox', 'buddyforms') . '</a></p>
                                            <h5>Post Fields</h5>
                                            <p><a href="Content/' . $buddyform['slug'] . '/unique" class="action">' . __('Content', 'buddyforms') . '</a></p>
                                            <p><a href="Taxonomy/' . $buddyform['slug'] . '" class="action">' . __('Taxonomy', 'buddyforms') . '</a></p>
                                            <p><a href="Comments/' . $buddyform['slug'] . '/unique" class="action">' . __('Comments', 'buddyforms') . '</a></p>
                                            <p><a href="Status/' . $buddyform['slug'] . '/unique" class="action">' . __('Post Status', 'buddyforms') . '</a></p>
                                            <p><a href="Featured-Image/' . $buddyform['slug'] . '/unique" class="action">' . __('Featured Image', 'buddyforms') . '</a></p>

                                            <h5>Extras</h5>
                                            <p><a href="File/' . $buddyform['slug'] . '" class="action">' . __('File', 'buddyforms') . '</a></p>
                                            <p><a href="Hidden/' . $buddyform['slug'] . '" class="action">' . __('Hidden', 'buddyforms') . '</a></p>
                                            <p><a href="Number/' . $buddyform['slug'] . '" class="action">' . __('Number', 'buddyforms') . '</a></p>
                                            <p><a href="HTML/' . $buddyform['slug'] . '" class="action">' . __('HTML', 'buddyforms') . '</a></p>
                                            <p><a href="Date/' . $buddyform['slug'] . '" class="action">' . __('Date', 'buddyforms') . '</a></p>

                                            '));

            $form = apply_filters('buddyforms_add_form_element_to_sidebar', $form, $buddyform['slug']);

            $form->addElement(new Element_HTML('
                                        </div>
                                    </div>
                                </div>
                            </div>'));

            apply_filters('buddyforms_admin_settings_sidebar_metabox', $form, $buddyform['slug']);
            $form->addElement(new Element_HTML('</div>
                        <div id="buddyforms_forms_builder_' . $buddyform['slug'] . '" class="buddyforms_forms_builder">'));




            $form->addElement(new Element_HTML('
                            <div class="hero-unit">
                            <h3>' . __('Form Settings for', 'buddyforms') . ' "' . stripslashes($buddyform['name']) . '"</h3>'));

            if(empty($buddyform['name']) || empty($buddyform['singular_name']) || empty($buddyform['slug']) || $buddyform['post_type'] == 'none' || $buddyform['attached_page'] == '')
                $form->addElement(new Element_HTML('<div class="bf-error"><h4>'.__('This form is broken please check your required settings under Form Control and save the form').'</h4></div>'));

            $form->addElement(new Element_HTML('<p class="loading-animation-order alert alert-success">' . __('Save new order', 'buddyforms') . ' <i class="icon-ok"></i></p>'));
            $form->addElement(new Element_HTML('<div class="loading-animation-new alert alert-success">' . __('Load new element', 'buddyforms') . ' <i class="icon-ok"></i></div>
                        '));

            $sortArray = array();

            if (!empty($buddyform['form_fields'])) {
                foreach ($buddyform['form_fields'] as $key => $array) {
                    $sortArray[$key] = $array['order'];
                }
                array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $buddyform['form_fields']);
            }
            $form->addElement(new Element_HTML('
                            <div class="accordion-group postbox">
                                <div class="accordion-heading"><p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_' . $buddyform['slug'] . '" href="#accordion_' . $buddyform['slug'] . '_status"><b>' . __('Form Control', 'buddyforms') . '</b></p></div>
                                <div id="accordion_' . $buddyform['slug'] . '_status" class="accordion-body collapse">
                                    <div class="accordion-inner bf-main-settings">'));
            $form->addElement(new Element_Textbox('<b>'.__("Name", 'buddyforms').'</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][name]", array( 'value' => stripslashes($buddyform['name']),'required' => 1)));
            $form->addElement(new Element_Textbox('<b>'.__("Singular Name", 'buddyforms').'</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][singular_name]", array('value' => stripslashes($buddyform['singular_name']),'required' => 1)));
            $form->addElement(new Element_Textbox('<b>'.__("Slug", 'buddyforms').'</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][slug]", array('shortDesc' => __('If you change the slug you need to reset the roles and capabilities', 'buddyforms'), 'value' => $buddyform['slug'], 'required' => 1)));

            $form->addElement(new Element_HTML('<br><hr /><br />'));

            $form->addElement(new Element_HTML('<div class="post_form_' . $buddyform['slug'] . ' form_type_settings" >'));
            $form->addElement(new Element_HTML('<div class="buddyforms_accordion_right">'));

                $form->addElement(new Element_HTML('<div class="innerblock revision">'));

                $revision = 'false';
                if (isset($buddyform['revision']))
                    $revision = $buddyform['revision'];
                $form->addElement(new Element_Checkbox('' , "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][revision]", array('Revision' => "<b>" . __('Revision', 'buddyforms') . "</b>"), array( 'shortDesc' => __('Enable frontend revision control.', 'buddyforms'), 'value' => $revision)));

                $admin_bar = 'false';
                if (isset($buddyform['admin_bar']))
                    $admin_bar = $buddyform['admin_bar'];

                $form->addElement(new Element_Checkbox('<br>' , "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][admin_bar]", array('Admin Bar' => "<b>" . __('Add to Admin Bar', 'buddyforms') . "</b>"), array('value' => $admin_bar)));

                $edit_link = 'all';
                if (isset($buddyform['edit_link']))
                    $edit_link = $buddyform['edit_link'];

                $form->addElement(new Element_Radio( '<b>' . __("Overwrite Frontend 'Edit Post' Link", 'buddyforms') . '</b><br><span class="help-inline">'.__('The link to the backend will be changed', 'buddyforms') . "<br>" . __('to use the frontend editing.', 'buddyforms').'</span>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][edit_link]", array('none' => 'None', 'all' => __("All Edit Links", 'buddyforms' ), 'my-posts-list' => __("Only in My Posts List", 'buddyforms')), array('value' => $edit_link )));


                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('</div>'));
                $form->addElement(new Element_HTML('<div class="buddyforms_accordion_left">'));


                $status = 'false';
                if (isset($buddyform['status']))
                    $status = $buddyform['status'];

                $form->addElement(new Element_Select('<b>'.__("Status", 'buddyforms').'</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][status]", array('publish', 'pending', 'draft'), array('value' => $status)));

                $comment_status = 'false';
                if (isset($buddyform['comment_status']))
                    $comment_status = $buddyform['comment_status'];

                $form->addElement(new Element_Select('<b>'.__("Comment Status", 'buddyforms').'</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][comment_status]", array('open', 'closed'), array('value' => $comment_status)));

                $post_type = 'false';
                if (isset($buddyform['post_type']))
                    $post_type = $buddyform['post_type'];

                $form->addElement(new Element_Select('<b>'.__("Post Type", 'buddyforms').'</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][post_type]", $post_types, array('value' => $post_type, 'required' => 1)));

                $attached_page = 'false';
                if (isset($buddyform['attached_page']))
                    $attached_page = $buddyform['attached_page'];

                $args = array(
                    'depth'             => 1,
                    'id'                => $key,
                    'echo'              => FALSE,
                    'sort_column'       => 'post_title',
                    'show_option_none'  => __('none', 'buddyforms'),
                    'name'              => "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][attached_page]",
                    'class'             => 'postform',
                    'selected'          => $attached_page
                );
                $form->addElement( new Element_HTML("<br><br><p><span class='required'>* </span><b>" . __('Attach Page to this Form', 'buddyforms') . "</b></p>"));
                $form->addElement( new Element_HTML(wp_dropdown_pages($args)));

                $form->addElement(new Element_HTML(' <a href="' . admin_url(add_query_arg(array('post_type' => 'page'), 'post-new.php')) . '" class="button">' . __('Create New', 'buddyforms') . '</a>'));

                $form->addElement( new Element_HTML("<p><span class='help-inline' >".__('Needs to be a parent page')."</span></p>"));

                    $form->addElement(new Element_HTML('<div><hr>'));


            $after_submit = isset($buddyform['after_submit']) ? $buddyform['after_submit'] : 'display_form';
            $form->addElement(new Element_Radio('<b>'.__("After Submission", 'buddyforms').'</b>', "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][after_submit]", array('display_form' => 'Display the Form and continue editing', 'display_post' => 'Display the Post', 'display_message' => 'Just display a Message'), array('value' => $after_submit, 'id' => 'after_submit_hidden'.$buddyform['slug'], 'class' => 'after_submit_hidden' )));

            $after_submit_hidden_checked = ($after_submit == 'display_message')  ? '' : 'style="display: none;"';
            $form->addElement( new Element_HTML('<div ' . $after_submit_hidden_checked . ' class="after_submit_hidden'.$buddyform['slug'].'-2">'));

                $after_submit_message_text = isset($buddyform['after_submit_message_text']) ? $buddyform['after_submit_message_text'] : 'The [form_singular_name] [post_title] has been successfully updated!<br>1. [post_link]<br>2. [edit_link]';
                $form->addElement(new Element_Textarea('<br><p><b>'.__('Add your Message Text', 'buddyforms').'</b><br>
                                    You can use special shortcodes to add dynamic content:<br>
                                    [form_singular_name] = Singular Name<br>
                                    [post_title] = The Post Title<br>
                                    [post_link] = The Post Permalink<br>
                                    [edit_link] = Link to the Post Edit Form<br>
                                    <br>

                </p>' , "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][after_submit_message_text]", array( 'value' => $after_submit_message_text)));

            $form->addElement(new Element_HTML('</div>'));

            $bf_ajax = false;
            if (isset($buddyform['bf_ajax']))
                $bf_ajax = $buddyform['bf_ajax'];
            $form->addElement(new Element_Checkbox('<b>AJAX</b>' , "buddyforms_options[buddyforms][" . $buddyform['slug'] . "][bf_ajax]", array('bf_ajax' => "<b>" . __('Enable ajax form submission.', 'buddyforms') . "</b>"), array( 'shortDesc' => __('This feature is new. Please test your form if you enable ajax.', 'buddyforms'), 'value' => $bf_ajax)));

            $form->addElement(new Element_HTML('</div>'));

                $form->addElement(new Element_HTML('</div>'));

            $form->addElement(new Element_HTML('</div>'));

            $form->addElement(new Element_HTML('
                                    </div>
                                </div>
                            </div>'));

            $form->addElement(new Element_HTML('
                        <br>
                        <h4>' . __('Form Builder', 'buddyforms') . '</h4>
                        ' . __('Add additional form elements from the right box "Form Elements". Change the order via drag and drop.', 'buddyforms') . '
                        <ul id="sortable_' . $buddyform['slug'] . '" class="sortable sortable_' . $buddyform['slug'] . '">'));

            if (isset( $buddyform['form_fields'])) {

                foreach ( $buddyform['form_fields'] as $field_id => $customfield) {

                    if (isset($customfield['slug']))
                        $slug = sanitize_title($customfield['slug']);

                    if (empty($slug))
                        $slug = sanitize_title($customfield['name']);

                    if(empty($buddyform['singular_name']))
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
            $form->addElement(new Element_HTML('</ul></div></div></div>'));
        }

    }

    $form = apply_filters('buddyforms_before_admin_form_render', $form);

    $form->addElement(new Element_HTML('</div>'));

    $form->render();
}