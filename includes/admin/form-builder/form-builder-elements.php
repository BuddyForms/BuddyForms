<?php

/**
 * View form fields
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_view_form_fields($args){
    global $buddyforms, $field_position;

    $buddyforms_options = $buddyforms;

    if (!isset($_POST))
        return;

    if (isset($_POST['post_args']))
        $post_args = explode('/', $_POST['post_args']);

    if (isset($post_args[0]))
        $field_type = $post_args[0];

    if (isset($post_args[1]))
        $form_slug = $post_args[1];

    if (isset($post_args[2]))
        $field_unique = $post_args[2];


    if (isset($field_unique) && $field_unique == 'unique') {
        if (isset($buddyforms['buddyforms'][$form_slug]['form_fields'])) {

            foreach ($buddyforms['buddyforms'][$form_slug]['form_fields'] as $key => $form_field) {
                if ($form_field['type'] == $field_type)
                    return 'unique';
            }

        }
    }

    if (isset($_POST['numItems']))
        $numItems = $_POST['numItems'];

    if (is_array($args))
        extract($args);


    if (!isset($field_id))
        $field_id = $mod5 = substr(md5(time() * rand()), 0, 10);

    if (isset($field_position) == '')
        $field_position = $numItems;

    $form_fields = Array();

    $required = 'false';
    if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['required']))
        $required = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['required'];
    $form_fields['right']['required']   = new Element_Checkbox('', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][required]", array('required' => '<b>' . __('Required', 'buddyforms') . '</b>'), array('value' => $required));

    $name = '';
    if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['name']))
        $name = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['name'];
    $form_fields['left']['name']        = new Element_Textbox('<b>' . __('Name', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][name]", array('value' => $name));

    if (empty($slug))
        $slug = sanitize_title($name);
    $form_fields['left']['slug']        = new Element_Textbox('<b>' . __('Slug', 'buddyforms') . '</b> <small>(optional)</small>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][slug]", array('shortDesc' => __('_name will create a hidden post meta field', 'buddyforms'), 'value' => $slug));

    $description = '';
    if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['description']))
        $description = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['description'];

    $form_fields['left']['description'] = new Element_Textbox('<b>' . __('Description', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][description]", array('value' => $description));
    $form_fields['left']['type']        = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][type]", $field_type);
    $form_fields['left']['order']       = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][order]", $field_position, array('id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order'));

    switch ($field_type) {

        case 'Link':
            $target = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['target']))
                $target = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['target'];

            $form_fields['left']['target'] = new Element_Select('<b>' . __('Target', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][target]", array('_self', '_blank'), array('value' => $target));
            break;
        case 'Dropdown':

            $multiple = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['multiple']))
                $multiple = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['multiple'];
            $form_fields['left']['multiple'] = new Element_Checkbox('', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][multiple]", array('multiple' => '<b>' . __('Multiple', 'buddyforms') . '</b>'), array('value' => $multiple));

            $field_args = Array(
                'form_slug' => $form_slug,
                'field_id' => $field_id,
                'buddyforms_options' => $buddyforms_options
            );
            $form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
            break;
        case 'Radiobutton':
            $field_args = Array(
                'form_slug' => $form_slug,
                'field_id' => $field_id,
                'buddyforms_options' => $buddyforms_options
            );
            $form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
            break;
        case 'Checkbox':
            $field_args = Array(
                'form_slug' => $form_slug,
                'field_id' => $field_id,
                'buddyforms_options' => $buddyforms_options
            );
            $form_fields = buddyforms_form_element_multiple($form_fields, $field_args);
            break;
        case 'Taxonomy':
            $taxonomies = buddyforms_taxonomies($form_slug);

            $taxonomy = false;
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['taxonomy']))
                $taxonomy = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['taxonomy'];
            $form_fields['left']['taxonomy']        = new Element_Select('<b>' . __('Taxonomy', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][taxonomy]", $taxonomies, array('value' => $taxonomy));

            $taxonomy_order = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['taxonomy_order']))
                $taxonomy_order = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['taxonomy_order'];
            $form_fields['left']['taxonomy_order']  = new Element_Select('<b>' . __('Taxonomy Order', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][taxonomy_order]", array('ASC', 'DESC'), array('value' => $taxonomy_order));

            $taxonomy_default = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['taxonomy_default']))
                $taxonomy_default = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['taxonomy_default'];

            if ($taxonomy) {

                $wp_dropdown_categories_args = array(
                    'hide_empty' => 0,
                    'child_of' => 0,
                    'echo' => FALSE,
                    'selected' => false,
                    'hierarchical' => 1,
                    'name' => "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][taxonomy_default][]",
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


                $form_fields['left']['taxonomy_default'] = new Element_HTML($dropdown);

            }

            $multiple = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['multiple']))
                $multiple = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['multiple'];
            $form_fields['left']['multiple']            = new Element_Checkbox('', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][multiple]", array('multiple' => '<b>' . __('Multiple', 'buddyforms') . '</b>'), array('value' => $multiple));

            $show_option_none = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['show_option_none']))
                $show_option_none = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['show_option_none'];
            $form_fields['left']['show_option_none']    = new Element_Checkbox('', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][show_option_none]", array('show_select_option' => '<b>' . __("Show 'Select an Option'", 'buddyforms') . '</b>'), array('value' => $show_option_none));

            $creat_new_tax = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['creat_new_tax']))
                $creat_new_tax = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['creat_new_tax'];
            $form_fields['left']['creat_new_tax']       = new Element_Checkbox('', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][creat_new_tax]", array('user_can_create_new' => '<b>' . __('User can create new', 'buddyforms') . '</b>'), array('value' => $creat_new_tax));
            break;
        case 'Hidden':
            unset($form_fields);

            $slug = '';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['slug']))
                $slug = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['slug'];
            $form_fields['left']['name']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][name]", $slug);
            $form_fields['left']['slug']    = new Element_Textbox('<b>' . __('Slug', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][slug]", array('required' => true, 'value' => $slug));
            $form_fields['left']['type']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][type]", $field_type);
            $form_fields['left']['order']   = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][order]", $field_position, array('id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order'));

            $value = '';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value']))
                $value = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value'];
            $form_fields['left']['value']   = new Element_Textbox(__('Value:', 'buddyforms'), "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][value]", array('value' => $value));
            break;
        case 'Comments':
            unset($form_fields);
            $form_fields['left']['name']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][name]", 'Comments');
            $form_fields['left']['type']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][type]", $field_type);
            $form_fields['left']['order']   = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][order]", $field_position, array('id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order'));
            $form_fields['left']['html']    = new Element_HTML(__("There are no settings needed so far. You can change the global comment settings in the form control section. If the 'comments' element is added to the form, the user has the possibility to overwrite the global settings and open/close 'comments' for their own post.", 'buddyforms'));

        case 'Title':
            unset($form_fields);
            $form_fields['left']['name']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][name]", 'Title');
            $form_fields['left']['slug']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][slug]", 'editpost_title');
            $form_fields['left']['type']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][type]", $field_type);
            $form_fields['left']['order']   = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][order]", $field_position, array('id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order'));
            $form_fields['left']['html']    = new Element_HTML(__("There are no settings needed so far. The Title is required and can not be removed. You can change the order of the title via drag and drop.", 'buddyforms'));

            break;
        case 'Content':
            unset($form_fields);

            $post_content_options = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['post_content_options']))
                $post_content_options = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['post_content_options'];

            $post_content_options_array = array( 'media_buttons' => 'media_buttons', 'tinymce' => 'tinymce', 'quicktags' => 'quicktags');

            $form_fields['left']['content_opt_a']   = new Element_Checkbox('<br><b>' . __('Turn off wp editor features', 'buddyforms') . '</b><br><br>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][post_content_options]", $post_content_options_array, array('value' => $post_content_options, 'required' => true));
            $form_fields['left']['name']            = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][name]", 'Content');
            $form_fields['left']['slug']            = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][slug]", 'editpost_content');
            $form_fields['left']['type']            = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][type]", $field_type);
            $form_fields['left']['order']           = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][order]", $field_position, array('id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order'));

            break;
        case 'Status':
            unset($form_fields);

            $form_fields['left']['html']        = new Element_HTML(__("You can change the global post status settings in the form control section. If the 'status' element is added to the form, the user has the possibility to overwrite the global settings and change the 'status' for their own post.", 'buddyforms'));

            $post_status = 'false';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['post_status']))
                $post_status = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['post_status'];

            $form_fields['left']['post_status'] = new Element_Checkbox('<br><b>' . __('Select the post status you want to make available in the frontend form', 'buddyforms') . '</b><br><br>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][post_status]", bf_get_post_status_array(), array('value' => $post_status, 'required' => true));

            $form_fields['left']['name']        = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][name]", 'Status');
            $form_fields['left']['type']        = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][type]", $field_type);
            $form_fields['left']['order']       = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][order]", $field_position, array('id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order'));

            break;
        case 'Featured Image':

            unset($form_fields);
            $required = 'false';

            $form_fields['left']['html']        = new Element_HTML(__('With the Featured Image Form Element you can add a featured image upload to your form', 'buddyforms') . '<br><br>');

            $description = '';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['description']))
                $description = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['description'];

            $form_fields['left']['description'] = new Element_Textbox('<b>' . __('Description:', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][description]", array('value' => $description));

            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['required']))
                $required = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['required'];
            $form_fields['left']['required']    = new Element_Checkbox('', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][required]", array('required' => '<b>' . __('Required', 'buddyforms') . '</b>'), array('value' => $required));

            $form_fields['left']['name']        = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][name]", 'FeaturedImage');
            $form_fields['left']['type']        = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][type]", $field_type);
            $form_fields['left']['order']       = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][order]", $field_position, array('id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order'));

            break;
        case 'HTML':
            unset($form_fields);

            $html = '';
            if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['html']))
                $html = $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['html'];

            $form_fields['left']['description'] = new Element_Textarea('<b>' . __('HTML:', 'buddyforms') . '</b>', "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][html]", array('value' => $html));


            $form_fields['left']['name']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][name]", 'HTML');
            $form_fields['left']['slug']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][slug]", 'html');
            $form_fields['left']['type']    = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][type]", $field_type);
            $form_fields['left']['order']   = new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][order]", $field_position, array('id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order'));
            break;
        default:
            $form_fields = apply_filters('buddyforms_form_element_add_field', $form_fields, $form_slug, $field_type, $field_id);
            break;

    }

    $form_fields = apply_filters('buddyforms_formbuilder_fields_options', $form_fields, $form_slug, $field_id);

    ob_start(); ?>
    <li id="buddyforms/<?php echo $form_slug ?>/form_fields/<?php echo $field_id ?>/order"
        class="list_item <?php echo $field_id . ' ' . $field_type ?>">
        <div class="accordion_fields">
            <div class="accordion-group postbox">
                <div class="accordion-heading">

                        <div class="accordion-heading-options">
                            <a class="delete" id="<?php echo $field_id ?>"
                               href="buddyforms/<?php echo $form_slug ?>/form_fields/<?php echo $field_id ?>/order">
                                <i class="icon-remove-sign" style="margin-top:0px;"></i>
                            </a>
                        </div>

                    <p class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text"
                       href="#accordion_<?php echo $form_slug; ?>_<?php echo $field_type . '_' . $field_id; ?>">
                        <b><?php echo $field_type; ?></b>
                        <i><?php  if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['name']))
                                echo $buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['name']; ?></i>
                    </p>

                </div>

                <div id="accordion_<?php echo $form_slug; ?>_<?php echo $field_type . '_' . $field_id; ?>" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="buddyforms_accordion_full">
                            <?php
                            if (isset($form_fields['full'])) {
                                foreach ($form_fields['full'] as $key => $value) {
                                    echo '<div class="buddyforms_field_label">' . $form_fields['full'][$key]->getLabel() . '</div>';
                                    echo '<div class="buddyforms_form_field">' . $form_fields['full'][$key]->render() . '</div>';
                                }
                            }
                            ?>
                        </div>
                        <div class="buddyforms_accordion_right">
                            <?php
                            if (isset($form_fields['right'])) {
                                foreach ($form_fields['right'] as $key => $value) {
                                    echo '<div class="buddyforms_field_label">' . $form_fields['right'][$key]->getLabel() . '</div>';
                                    echo '<div class="buddyforms_form_field">' . $form_fields['right'][$key]->render() . '</div>';
                                }
                            }
                            ?>
                        </div>
                        <div class="buddyforms_accordion_left">
                            <?php
                            if (isset($form_fields['left'])) {
                                foreach ($form_fields['left'] as $key => $value) {
                                    if (substr($key, 0, 4) == 'html') {
                                        echo $form_fields['left'][$key]->getLabel();
                                        echo $form_fields['left'][$key]->render();
                                    } else {
                                        echo '<div class="buddyforms_field_label">' . $form_fields['left'][$key]->getLabel() . '</div>';
                                        echo '<div class="buddyforms_form_field">' . $form_fields['left'][$key]->render() . '</div>';
                                    }

                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div>
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

add_action('wp_ajax_buddyforms_view_form_fields', 'buddyforms_view_form_fields');
add_action('wp_ajax_nopriv_buddyforms_view_form_fields', 'buddyforms_view_form_fields');


function buddyforms_form_element_multiple($form_fields, $args)
{

    extract($args);

    $form_fields['left']['html_1'] = new Element_HTML('
	<div class="element_field">
	<b>' . __('Values', 'buddyforms') . '</b>
		 <ul id="' . $form_slug . '_field_' . $field_id . '" class="element_field_sortable">');
    if (isset($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value'])) {
        $count = 1;
        foreach ($buddyforms_options['buddyforms'][$form_slug]['form_fields'][$field_id]['value'] as $key => $value) {
            $form_fields['left']['html_li_start_' . $key] = new Element_HTML('<li class="field_item field_item_' . $field_id . '_' . $count . '">');
            $form_fields['left']['html_value_' . $key] = new Element_Textbox(__("Entry ", 'buddyforms') . $key, "buddyforms_options[buddyforms][" . $form_slug . "][form_fields][" . $field_id . "][value][]", array('value' => $value));
            $form_fields['left']['html_li_end_' . $key] = new Element_HTML('<a href="#" id="' . $field_id . '_' . $count . '" class="delete_input" title="delete me">X</a> - <a href="#" id="' . $field_id . '" title="drag and move me!">' . __('move', 'buddyforms') . '</a></li>');
            $count++;
        }
    }
    $form_fields['left']['html_2'] = new Element_HTML('
	    </ul>
     </div>
     <a href="' . $form_slug . '/' . $field_id . '" class="button add_input">+</a>
    ');

    return $form_fields;
}