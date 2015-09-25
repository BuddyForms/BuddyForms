<?php

/**
 * View form fields
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_display_form_element($args){

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);

    if (isset($_POST['post_args']))
        $post_args = explode('/', $_POST['post_args']);

    if (isset($post_args[0]))
        $field_type = $post_args[0];

    if (isset($post_args[1]))
        $form_slug = $post_args[1];

    if (isset($post_args[2]))
        $field_unique = $post_args[2];

    if (isset($field_unique) && $field_unique == 'unique') {
        if (isset($buddyform['form_fields'])) {

            foreach ($buddyform['form_fields'] as $key => $form_field) {
                if ($form_field['type'] == $field_type)
                    return 'unique';
            }

        }
    }

    if (is_array($args))
        extract($args);

    if (!isset($field_id))
        $field_id = $mod5 = substr(md5(time() * rand()), 0, 10);


    $customfield = $buddyform['form_fields'][$field_id];
    $form_fields = Array();

    $required = isset($customfield['required']) ? $customfield['required'] : 'false';
    $form_fields['validation']['required']   = new Element_Checkbox('<b>' . __('Required', 'buddyforms') .'</b>', "buddyforms_options[form_fields][" . $field_id . "][required]", array('required' => '<b>' . __('Make this field a required field', 'buddyforms') . '</b>'), array('value' => $required, 'id' => "buddyforms_options[form_fields][" . $field_id . "][required]"));

    $name = isset($customfield['name']) ? stripcslashes($customfield['name']) : '';
    $form_fields['general']['name']        = new Element_Textbox('<b>' . __('Name', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array('value' => $name, 'required' => 1));

    $field_slug = isset($customfield['slug']) ? sanitize_title($customfield['slug']) : sanitize_title($customfield['name']);
    $form_fields['general']['slug']        = new Element_Textbox('<b>' . __('Slug', 'buddyforms') . '</b> <small>(optional)</small>', "buddyforms_options[form_fields][" . $field_id . "][slug]", array('shortDesc' => __('_name will create a hidden post meta field', 'buddyforms'), 'value' => $field_slug, 'required' => 1));

    $description = isset($customfield['description']) ? stripslashes($customfield['description']) : '';
    $form_fields['general']['description'] = new Element_Textbox('<b>' . __('Description', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][description]", array('value' => $description));
    $form_fields['general']['type']        = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][type]", $field_type);

    $validation_error_message = isset($customfield['validation_error_message']) ? stripcslashes($customfield['validation_error_message']) : __('This field is required.', 'buddyforms');
    $form_fields['validation']['validation_error_message']    = new Element_Textbox('<b>' . __('Validation Error Message', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_error_message]", array('value' => $validation_error_message));

    $custom_class = isset($customfield['custom_class']) ? stripcslashes($customfield['custom_class']) : '';
    $form_fields['advanced']['custom_class']    = new Element_Textbox('<b>' . __('Add custom class to the form element', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][custom_class]", array('value' => $custom_class));

    switch ($field_type) {

        case 'Text':
            $validation_minlength = isset($customfield['avalidation_minlengtha']) ? stripcslashes($customfield['validation_minlength']) : 0;
            $form_fields['validation']['validation_minlength']    = new Element_Number('<b>' . __('Validation Min Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array('value' => $validation_minlength));

            $validation_maxlength = isset($customfield['validation_maxlength']) ? stripcslashes($customfield['validation_maxlength']) : 0;
            $form_fields['validation']['validation_maxlength']    = new Element_Number('<b>' . __('Validation Max Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array('value' => $validation_maxlength));
            break;
        case 'Textarea':
            $validation_minlength = isset($customfield['validation_minlength']) ? stripcslashes($customfield['validation_minlength']) : 0;
            $form_fields['validation']['validation_minlength']    = new Element_Number('<b>' . __('Validation Min Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array('value' => $validation_minlength));

            $validation_maxlength = isset($customfield['validation_maxlength']) ? stripcslashes($customfield['validation_maxlength']) : 0;
            $form_fields['validation']['validation_maxlength']    = new Element_Number('<b>' . __('Validation Max Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array('value' => $validation_maxlength));
            break;

        case 'Number':
            $validation_min = isset($customfield['validation_min']) ? stripcslashes($customfield['validation_min']) : 0;
            $form_fields['validation']['validation_min']    = new Element_Number('<b>' . __('Validation Min Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_min]", array('value' => $validation_min));

            $validation_max = isset($customfield['validation_max']) ? stripcslashes($customfield['validation_max']) : 0;
            $form_fields['validation']['validation_max']    = new Element_Number('<b>' . __('Validation Max Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_max]", array('value' => $validation_max));
        break;
        case 'Dropdown':
            $multiple = isset($customfield['multiple']) ? $customfield['multiple'] : 'false';
            $form_fields['general']['multiple'] = new Element_Checkbox('<b>' . __('Multiple Selection', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple]", array('multiple' => '<b>' . __('Multiple', 'buddyforms') . '</b>'), array('value' => $multiple));

            $field_args = Array(
                'form_slug' => $form_slug,
                'field_id' => $field_id,
                'buddyforms_options' => $buddyform
            );
            $form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
            break;
        case 'Radiobutton':
            $field_args = Array(
                'form_slug' => $form_slug,
                'field_id' => $field_id,
                'buddyforms_options' => $buddyform
            );
            $form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
            break;
        case 'Checkbox':
            $field_args = Array(
                'form_slug' => $form_slug,
                'field_id' => $field_id,
                'buddyforms_options' => $buddyform
            );
            $form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
            break;
        case 'Taxonomy':
            $taxonomies = buddyforms_taxonomies($form_slug);
            $taxonomy = isset($customfield['taxonomy']) ? $customfield['taxonomy'] : false;
            $form_fields['general']['taxonomy']        = new Element_Select('<b>' . __('Taxonomy', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][taxonomy]", $taxonomies, array('value' => $taxonomy));

            $taxonomy_order = isset($customfield['taxonomy_order']) ? $customfield['taxonomy_order'] : 'false';
            $form_fields['general']['taxonomy_order']  = new Element_Select('<b>' . __('Taxonomy Order', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][taxonomy_order]", array('ASC', 'DESC'), array('value' => $taxonomy_order));

            $taxonomy_default = isset($customfield['taxonomy_default']) ? $customfield['taxonomy_default'] : 'false';

            if ($taxonomy) {

                $wp_dropdown_categories_args = array(
                    'hide_empty' => 0,
                    'child_of' => 0,
                    'echo' => FALSE,
                    'selected' => false,
                    'hierarchical' => 1,
                    'name' => "buddyforms_options[form_fields][" . $field_id . "][taxonomy_default][]",
                    'class' => 'postform bf-select2',
                    'depth' => 0,
                    'tab_index' => 0,
                    'taxonomy' => $taxonomy,
                    'hide_if_empty' => FALSE,
                    'orderby' => 'SLUG',
                    'order' => $taxonomy_order,
                );


                $dropdown = wp_dropdown_categories($wp_dropdown_categories_args);

                $dropdown = str_replace('id=', 'multiple="multiple" id=', $dropdown);

                if (is_array($taxonomy_default)) {
                    foreach ($taxonomy_default as $key => $post_term) {
                        $dropdown = str_replace(' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $dropdown);
                    }
                } else {
                    $dropdown = str_replace(' value="' . $taxonomy_default . '"', ' value="' . $taxonomy_default . '" selected="selected"', $dropdown);
                }

                $dropdown = '<div class="bf_field_group">
                                <div class="buddyforms_field_label"><b>Taxonomy Default</b></div>
                                <div class="bf_inputs">' . $dropdown . ' </div>

                            </div>';

                $form_fields['advanced']['taxonomy_default'] = new Element_HTML($dropdown);

            }

            $multiple = isset($customfield['multiple']) ? $customfield['multiple'] : 'false';
            $form_fields['advanced']['multiple']            = new Element_Checkbox('<b>' . __('Multiple Selection', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple]", array('multiple' => '<b>' . __('Multiple', 'buddyforms') . '</b>'), array('value' => $multiple));

            $show_option_none = isset($customfield['show_option_none']) ? stripcslashes($customfield['show_option_none']) : 'false';
            $form_fields['advanced']['show_option_none']    = new Element_Checkbox('<b>' . __('Display Select an Option', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][show_option_none]", array('show_select_option' => '<b>' . __("Show 'Select an Option'", 'buddyforms') . '</b>'), array('value' => $show_option_none));

            $creat_new_tax = isset($customfield['creat_new_tax']) ? stripcslashes($customfield['creat_new_tax']) : 'false';
            $form_fields['advanced']['creat_new_tax']       = new Element_Checkbox('<b>' . __('New Taxonomy Item', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][creat_new_tax]", array('user_can_create_new' => '<b>' . __('User can create new', 'buddyforms') . '</b>'), array('value' => $creat_new_tax));

            $hidden = isset($customfield['hidden']) ? $customfield['hidden'] : false;
            $form_fields['advanced']['hidden']              = new Element_Checkbox('<b>' . __('Hidden', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden]", array('hidden' => '<b>' . __('Make this field Hidden', 'buddyforms') . '</b>'), array('value' => $hidden));
            break;
        case 'Hidden':
            unset($form_fields);
            $form_fields['general']['name']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][name]", $field_slug);
            $form_fields['general']['slug']    = new Element_Textbox('<b>' . __('Slug', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][slug]", array('required' => true, 'value' => $field_slug, 'required' => 1));
            $form_fields['general']['type']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][type]", $field_type);

            $value = isset($customfield['value']) ? $customfield['value'] : '';
            $form_fields['general']['value']   = new Element_Textbox('<b>' . __('Value:', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][value]", array('value' => $value));
            break;
        case 'Comments':
            unset($form_fields);
            $required = isset($customfield['required']) ? stripcslashes($customfield['required']) : 'false';
            $form_fields['general']['required']   = new Element_Checkbox('<b>' . __('Required', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][required]", array('required' => '<b>' . __('Required', 'buddyforms') . '</b>'), array('value' => $required, 'id' => "buddyforms_options[form_fields][" . $field_id . "][required]"));

            $name = isset($customfield['name']) ? stripcslashes($customfield['name']) : 'Comments';
            $form_fields['general']['name']    = new Element_Textbox('<b>' . __('Name', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array('value' => $name, 'required' => 1));
            $form_fields['general']['slug']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][slug]", 'comments');
            $form_fields['general']['type']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][type]", $field_type);
            $form_fields['general']['html']    = new Element_HTML(__("There are no settings needed so far. You can change the global comment settings in the form control section. If the 'comments' element is added to the form, the user has the possibility to overwrite the global settings and open/close 'comments' for their own post.", 'buddyforms'));
            break;
        case 'Title':
            unset($form_fields['general']['required']);
            $name = isset($customfield['name']) ? stripcslashes($customfield['name']) : 'Title';
            $form_fields['general']['name']    = new Element_Textbox('<b>' . __('Name', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array('value' => $name, 'required' => 1 ));
            $form_fields['general']['slug']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][slug]", 'editpost_title');
            $form_fields['general']['type']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][type]", $field_type);

            $hidden = isset($customfield['hidden']) ? stripcslashes($customfield['hidden']) : false;
            $form_fields['advanced']['hidden']  = new Element_Checkbox('<b>' . __('Hidden?', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden]", array('hidden' => '<b>' . __('Make this field Hidden', 'buddyforms') . '</b>'), array('value' => $hidden));

            $validation_minlength = isset($customfield['validation_minlength']) ? stripcslashes($customfield['validation_minlength']) : 0;
            $form_fields['validation']['validation_minlength']  = new Element_Number('<b>' . __('Validation Min Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array('value' => $validation_minlength));

            $validation_maxlength = isset($customfield['validation_maxlength']) ? stripcslashes($customfield['validation_maxlength']) : '';
            $form_fields['validation']['validation_maxlength']  = new Element_Number('<b>' . __('Validation Max Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array('value' => $validation_maxlength));
            break;
        case 'Content':
            $name = isset($customfield['name']) ? stripcslashes($customfield['name']) : 'Content';
            $form_fields['general']['name']    = new Element_Textbox('<b>' . __('Name', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array('value' => $name, 'required' => 1));

            $post_content_options = isset($customfield['post_content_options']) ? stripcslashes($customfield['post_content_options']) : 'false';
            $post_content_options_array = array( 'media_buttons' => 'media_buttons', 'tinymce' => 'tinymce', 'quicktags' => 'quicktags');
            $form_fields['advanced']['content_opt_a']   = new Element_Checkbox('<b>' . __('Turn off wp editor features', 'buddyforms') . '</b><br><br>', "buddyforms_options[form_fields][" . $field_id . "][post_content_options]", $post_content_options_array, array('value' => $post_content_options));

            $form_fields['general']['slug']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][slug]", 'editpost_content');
            $form_fields['general']['type']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][type]", $field_type);

            $hidden = isset($customfield['hidden']) ? $customfield['hidden'] : false;
            $form_fields['advanced']['hidden']  = new Element_Checkbox('<b>' . __('Hidden?', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hidden]", array('hidden' => '<b>' . __('Make this field Hidden', 'buddyforms') . '</b>'), array('value' => $hidden));

            $validation_minlength = isset($customfield['validation_minlength']) ? stripcslashes($customfield['validation_minlength']) : 0;
            $form_fields['validation']['validation_minlength']    = new Element_Number('<b>' . __('Validation Min Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_minlength]", array('value' => $validation_minlength));

            $validation_maxlength = isset($customfield['validation_maxlength']) ? stripcslashes($customfield['validation_maxlength']) : 0;
            $form_fields['validation']['validation_maxlength']    = new Element_Number('<b>' . __('Validation Max Length', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_maxlength]", array('value' => $validation_maxlength));
            break;
        case 'Status':
            unset($form_fields);
            $required = isset($customfield['required']) ? stripcslashes($customfield['required']) : 'false';
            $form_fields['general']['required']   = new Element_Checkbox('<b>' . __('Required', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][required]", array('required' => '<b>' . __('Required', 'buddyforms') . '</b>'), array('value' => $required, 'id' => "buddyforms_options[form_fields][" . $field_id . "][required]"));

            $name = isset($customfield['name']) ? stripcslashes($customfield['name']) : 'Status';
            $form_fields['general']['name']        = new Element_Textbox('<b>' . __('Name', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array('value' => $name, 'required' => 1));
            $form_fields['general']['slug']        = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][slug]", 'post_status');

            $post_status = isset($customfield['post_status']) ? stripcslashes($customfield['post_status']) : 'post_status';
            $form_fields['general']['post_status'] = new Element_Checkbox('<b>' . __('Select the post status you want to make available in the frontend form', 'buddyforms') . '</b><br><br>', "buddyforms_options[form_fields][" . $field_id . "][post_status]", bf_get_post_status_array(), array('value' => $post_status, 'id' => "buddyforms_options[form_fields][" . $field_id . "][post_status]", 'shortDesc' => __("You can change the global post status settings in the form control section. If the 'status' element is added to the form, the user has the possibility to overwrite the global settings and change the 'status' for their own post.", 'buddyforms')));
            $form_fields['general']['type']        = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][type]", $field_type);
            break;
        case 'Featured_Image':
        case 'Featured-Image':
        case 'FeaturedImage':
            unset($form_fields);
            $required = isset($customfield['required']) ? stripcslashes($customfield['required']) : 'false';
            $form_fields['general']['required']   = new Element_Checkbox('<b>' . __('Required', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][required]", array('required' => '<b>' . __('Required', 'buddyforms') . '</b>'), array('value' => $required, 'id' => "buddyforms_options[form_fields][" . $field_id . "][required]"));

            $name = isset($customfield['name']) ? stripcslashes($customfield['name']) : 'FeaturedImage';
            $form_fields['general']['name']        = new Element_Textbox('<b>' . __('Name', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array('value' => $name, 'required' => 1));
            $form_fields['general']['slug']        = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][slug]", 'featured_image');

            $description = isset($customfield['description']) ? stripcslashes($customfield['description']) : '';
            $form_fields['general']['description'] = new Element_Textbox('<b>' . __('Description:', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][description]", array('value' => $description));
            $form_fields['general']['type']        = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][type]", $field_type);
            break;
        case 'File':

            $validation_multiple = isset($customfield['validation_multiple']) ? stripcslashes($customfield['validation_multiple']) : 0;
            $form_fields['advanced']['validation_multiple']    = new Element_Checkbox('<b>' . __('Only one file or multiple?', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][validation_multiple]", array('multiple' => '<b>' . __('Allow multiple file upload', 'buddyforms') . '</b>'), array('value' => $validation_multiple));

            $allowed_mime_types = get_allowed_mime_types();

            $data_types = isset($customfield['data_types']) ? $customfield['data_types'] : '';
            $form_fields['advanced']['data_types']    = new Element_Checkbox('<b>' . __('Select allowed file Types', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][data_types]",$allowed_mime_types , array('value' => $data_types));
            break;
        case 'HTML':
            unset($form_fields);
            $html = isset($customfield['html']) ? stripcslashes($customfield['html']) : '';
            $form_fields['general']['description'] = new Element_Textarea('<b>' . __('HTML:', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][html]", array('value' => $html));
            $form_fields['general']['name']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][name]", 'HTML');
            $form_fields['general']['slug']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][slug]", 'html');
            $form_fields['general']['type']    = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][type]", $field_type);
            break;
        default:
            $form_fields = apply_filters('buddyforms_form_element_add_field', $form_fields, $form_slug, $field_type, $field_id);
            break;
    }

    $form_fields = apply_filters('buddyforms_formbuilder_fields_options', $form_fields, $form_slug, $field_id);

    ob_start(); ?>
    <li id="field_<?php echo $field_id ?>" class="list_item <?php echo $field_id . ' ' . $field_type ?>">
        <div class="accordion_fields">
            <div class="accordion-group postbox">
                <div class="accordion-heading-options">
                    <table class="wp-list-table widefat fixed posts">
                        <tbody>
                            <tr>
                                <td class="field_order ui-sortable-handle">
                                    <span class="circle"><?php echo $customfield['order'] ?></span>
                                </td>
                                <td class="field_label">
                                    <strong>
                                        <a class="bf_edit_field row-title accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text" href="#accordion_<?php echo $form_slug; ?>_<?php echo $field_type . '_' . $field_id; ?>" title="Edit this Field" href="javascript:;"><?php echo $customfield['name'] ?></a>
                                    </strong>

                                </td>
                                <td class="field_name"><?php echo $customfield['slug'] ?></td>
                                <td class="field_type"><?php echo $customfield['type'] ?></td>
                                <td class="field_delete">
                                    <span><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text" href="#accordion_<?php echo $form_slug; ?>_<?php echo $field_type . '_' . $field_id; ?>" title="Edit this Field" href="javascript:;">Edit</a> | </span>
                                    <span><a class="bf_delete_field" id="<?php echo $field_id ?>" title="Delete this Field" href="#">Delete</a></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="accordion_<?php echo $form_slug; ?>_<?php echo $field_type . '_' . $field_id; ?>" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <script>
                            jQuery('#bf_field_group<?php echo $form_slug . '-' . $field_id ?> a').click(function (e) {
                                e.preventDefault();
                                jQuery(this).tab('show');
                            })
                        </script>
                        <div class="tabs-<?php echo $form_slug . '-' . $field_id ?>">
                            <ul id="bf_field_group<?php echo $form_slug . '-' . $field_id ?>" class="nav nav-tabs">
                                <li class="active"><a href="#General<?php echo $form_slug . '-' . $field_id ?>" data-toggle="tab">General</a></li>
                                <?php if (isset($form_fields['validation'])) { ?>
                                    <li><a href="#Validation<?php echo $form_slug . '-' . $field_id ?>" data-toggle="tab">Validation</a></li>
                                <?php } ?>
                                <?php if (isset($form_fields['advanced'])) { ?>
                                    <li><a href="#Advanced<?php echo $form_slug . '-' . $field_id ?>" data-toggle="tab">Advanced</a></li>
                                <?php } ?>
                                <?php if (isset($form_fields['addons'])) { ?>
                                    <li><a href="#AddOns<?php echo $form_slug . '-' . $field_id ?>" data-toggle="tab">AddOns</a></li>
                                <?php } ?>
                            </ul>
                            <div id="bf_field_groupContent<?php echo $form_slug . '-' . $field_id ?>" class="tab-content">
                                <div class="tab-pane fade in active" id="General<?php echo $form_slug . '-' . $field_id ?>">
                                    <div class="buddyforms_accordion_general">
                                        <?php buddyforms_display_field_group_table($form_fields['general']) ?>
                                    </div>
                                </div>
                                <?php if (isset($form_fields['validation'])) { ?>
                                    <div class="tab-pane fade" id="Validation<?php echo $form_slug . '-' . $field_id ?>">
                                        <div class="buddyforms_accordion_general">
                                            <?php buddyforms_display_field_group_table($form_fields['validation']) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (isset($form_fields['advanced'])) { ?>
                                    <div class="tab-pane fade" id="Advanced<?php echo $form_slug . '-' . $field_id ?>">
                                        <div class="buddyforms_accordion_general">
                                            <?php buddyforms_display_field_group_table($form_fields['advanced']) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (isset($form_fields['addons'])) { ?>
                                    <div class="tab-pane fade" id="AddOns<?php echo $form_slug . '-' . $field_id ?>">
                                        <div class="buddyforms_accordion_general">
                                            <?php buddyforms_display_field_group_table($form_fields['addons']) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
    <?php
    $field_html = ob_get_contents();
    ob_end_clean();

    if (is_array($args)) {
        return $field_html;
    } else {
        echo $field_html;
        die();
    }


}

add_action('wp_ajax_buddyforms_display_form_element', 'buddyforms_display_form_element');
function buddyforms_form_element_multiple($form_fields, $args)
{

    extract($args);

    $form_fields['general']['html_1'] = new Element_HTML('
	<div class="element_field">
	<b>' . __('Values', 'buddyforms') . '</b>
		 <ul id="' . $form_slug . '_field_' . $field_id . '" class="element_field_sortable">');
    if (isset($buddyform['form_fields'][$field_id]['value'])) {
        $count = 1;
        foreach ($buddyform['form_fields'][$field_id]['value'] as $key => $value) {
            $form_fields['general']['html_li_start_' . $key] = new Element_HTML('<li class="field_item field_item_' . $field_id . '_' . $count . '">');
            $form_fields['general']['html_value_' . $key] = new Element_Textbox('<b>' . __("Entry ", 'buddyforms') . $key . '</b>', "buddyforms_options[form_fields][" . $field_id . "][value][]", array('value' => $value));
            $form_fields['general']['html_li_end_' . $key] = new Element_HTML('<a href="#" id="' . $field_id . '_' . $count . '" class="delete_input" title="delete me">X</a> - <a href="#" id="' . $field_id . '" title="drag and move me!">' . __('move', 'buddyforms') . '</a></li>');
            $count++;
        }
    }
    $form_fields['general']['html_2'] = new Element_HTML('
	    </ul>
     </div>
     <a href="' . $form_slug . '/' . $field_id . '" class="button add_input">+</a>
    ');

    return $form_fields;
}


function buddyforms_display_field_group_table($form_fields){
    ?>
    <table class="form-table">
        <tbody>
        <?php
        if (isset($form_fields)) {
            foreach ($form_fields as $key => $field) { ?>
                <tr id="row_form_title">
                    <th scope="row">
                        <label for="form_title"><?php echo $field->getLabel() ?></label>
                    </th>
                    <td>
                        <?php echo $field->render() ?>
                        <p class="description"><?php echo $field->getShortDesc() ?></p>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
    <?php
}