<?php

function bf_form_elements($form, $args){

    extract($args);

    if (!isset($customfields))
        return;

    foreach ($customfields as $field_id => $customfield) :

        if(isset($customfield['slug']))
            $slug = sanitize_title($customfield['slug']);

        if(empty($slug))
            $slug = sanitize_title($customfield['name']);

        if($slug != '') :

            if (isset($_POST[$slug] )) {
                $customfield_val = $_POST[$slug];
            } else {
                $customfield_val = get_post_meta($post_id, $slug, true);
            }
//            if(isset($customfield['field_is_array'])){
//                $slug = $slug . $customfield['field_is_array'];
//                $customfield_val = $customfield_val[$customfield['field_is_array']];
//            }

            $name = '';
            if(isset($customfield['name']))
                $name           = stripcslashes($customfield['name']);
            $description = '';
            if(isset($customfield['description']))
                $description    = stripcslashes($customfield['description']);


            if(isset($customfield['type'])){
                switch( $customfield['type'] ) {
                    case 'Number':

                        $element_attr = isset($customfield['required']) ? array('required' => true, 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description) : array( 'id' => str_replace("-", "", $slug),'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description);
                        $form->addElement(new Element_Number($name, $slug, $element_attr));

                        break;
                    case 'HTML':

                        $form->addElement(new Element_HTML($customfield['html']));

                        break;
                    case 'Date':
                        // $customfield_val = get_post_meta($post_id, '_sale_price_dates_from', true);
                        // $customfield_val = date_i18n('Y-m-d', (int)$customfield_val);
                        // $element_attr = isset($customfield['required']) ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input bf_datetime', 'shortDesc' => isset($description) ? $description : '') : array('value' => $customfield_val, 'class' => 'settings-input bf_price_date', 'shortDesc' => isset($description) ? $description : '');
                        // $form->addElement(new Element_Textbox('Sale Price Date From', '_sale_price_dates_from', $element_attr));

                        $element_attr = isset($customfield['required']) ? array('required' => true, 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description) : array( 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description);
                        $form->addElement(new Element_Date($name, $slug, $element_attr));
                        break;
                    case 'Title':

                        if (isset($_POST['editpost_title'])) {
                            $post_title = stripslashes($_POST['editpost_title']);
                        } else {
                            $post_title = $the_post->post_title;
                        }
                        if( isset($customfield['hidden']) ) {
                            $form->addElement(new Element_Hidden('editpost_title', $post_title ));
                        } else {
                            $form->addElement(new Element_Textbox($name, "editpost_title", array("required" => 1, 'id' => 'editpost_title', 'value' => $post_title, 'shortDesc' => $description)));
                        }

                        break;
                    case 'Content':
                        add_filter( 'tiny_mce_before_init', 'my_tinymce_setup_function' );
                        $editpost_content_val = false;
                        if (isset($_POST['editpost_content'])) {
                            $editpost_content_val = stripslashes($_POST['editpost_content']);
                        } else {
                            if (!empty($the_post->post_content))
                                $editpost_content_val = $the_post->post_content;
                        }

                        ob_start();
                        $settings = array(
                            'wpautop'       => true,
                            'media_buttons' => isset($customfield['post_content_options']) ? in_array('media_buttons', $customfield['post_content_options']) ? false : true : true,
                            'tinymce'       => isset($customfield['post_content_options']) ? in_array('tinymce', $customfield['post_content_options']) ? false : true : true,
                            'quicktags'     => isset($customfield['post_content_options']) ? in_array('quicktags', $customfield['post_content_options']) ? false : true : true,
                            'textarea_rows' => 18,
                            'textarea_name' => 'editpost_content',
                            'editor_class'  => 'textInMce',
                        );

                        if (isset($post_id)) {
                            wp_editor($editpost_content_val, 'editpost_content', $settings);
                        } else {
                            $content = false;
                            $post = 0;
                            wp_editor($content, 'editpost_content', $settings);
                        }
                        $wp_editor = ob_get_contents();
                        ob_clean();

                        $required = '';
                        if(isset($customfield['required'])){
                            $wp_editor = str_replace( '<textarea', '<textarea required="required"', $wp_editor );
                            $required = '<span class="required">* </span>';
                        }


                        echo '<div id="editpost_content_val" style="display: none">' . $editpost_content_val . '</div>';

                        if( isset($customfield['hidden']) ) {
                            $form->addElement(new Element_Hidden('editpost_content', $editpost_content_val ));
                        } else {
                            $wp_editor = '<div class="bf_field_group bf_form_content"><label for="editpost_content">' . $required . $name . ':</label><div class="bf_inputs">' . $wp_editor . '</div><span class="help-inline">'.$description.'</span></div>';
                            $form->addElement(new Element_HTML( $wp_editor ));
                        }

                         break;
                    case 'Mail' :
                        $element_attr = isset($customfield['required']) ? array('required' => true,  'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description) : array( 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description);
                        $form->addElement(new Element_Email($name, $slug, $element_attr));
                        break;

                    case 'Radiobutton' :
                        $element_attr = isset($customfield['required']) ? array('required' => true,  'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description) : array( 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description);
                        if (is_array($customfield['value'])) {
                            $form->addElement(new Element_Radio($name, $slug, $customfield['value'], $element_attr));
                        }
                        break;

                    case 'Checkbox' :

                        $element_attr = isset($customfield['required']) ? array('required' => true, 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description) : array( 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description);
                        if (isset($customfield['value']) && is_array($customfield['value'])) {
                            $form->addElement(new Element_Checkbox($name, $slug, $customfield['value'], $element_attr));
                        }
                        break;

                    case 'Dropdown' :
                        $element_attr = isset($customfield['required']) ? array('required' => true, 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description) : array( 'id' => str_replace("-", "", $slug),'value' => $customfield_val, 'class' => 'settings-input bf-select2', 'shortDesc' => $description);
                        if (isset($customfield['value']) && is_array($customfield['value'])) {
                            $element = new Element_Select($name, $slug, $customfield['value'], $element_attr);

                            if (isset($customfield['multiple']) && is_array($customfield['multiple']))
                                $element->setAttribute('multiple', 'multiple');

                            bf_add_element($form, $element);
                        }
                        break;

                    case 'Comments' :

                        if(isset($the_post))
                            $customfield_val = $the_post->comment_status;

                        $element_attr = isset($customfield['required']) ? array('required' => true, 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' => $description) : array( 'id' => str_replace("-", "", $slug),'value' => $customfield_val, 'class' => 'settings-input');
                        $form->addElement(new Element_Select($name, 'comment_status', array('open', 'closed'), $element_attr));
                        break;

                    case 'Status' :
                        global $buddyforms;

                        if(isset($customfield['post_status']) && is_array($customfield['post_status'])){
                            if (in_array('pending', $customfield['post_status']))
                                $post_status['pending'] = 'Pending Review';

                            if (in_array('publish', $customfield['post_status']))
                                $post_status['publish'] = 'Published';

                            if (in_array('draft', $customfield['post_status']))
                                $post_status['draft'] = 'Draft';


                            if (in_array('future', $customfield['post_status']) && empty($customfield_val) || in_array('future', $customfield['post_status']) && get_post_status($post_id) == 'future')
                                $post_status['future'] = 'Scheduled';

                            if (in_array('private', $customfield['post_status']))
                                $post_status['private'] = 'Privately Published';

                            if (in_array('private', $customfield['post_status']))
                                $post_status['trash'] = 'Trash';

                            $customfield_val = $the_post->post_status;

                            if(isset($_POST['status']))
                                $customfield_val = $_POST['status'];

                            $element_attr = array('value' => $customfield_val, 'class' => 'settings-input');
                            $form->addElement(new Element_Select($name, 'status', $post_status, $element_attr));

                            if (isset($_POST[$slug])) {
                                $schedule_val = $_POST['schedule'];
                            } else {
                                $schedule_val = get_post_meta($post_id, 'schedule', true);
                            }

                            $element_attr = array('value' => $schedule_val, 'class' => 'settings-input, bf_datetime');

                            $form->addElement(new Element_HTML('<div class="bf_datetime_wrap">'));
                            $form->addElement(new Element_Textbox('Schedule Time', 'schedule', $element_attr));
                            $form->addElement(new Element_HTML('</div>'));
                        }
                        break;

                    case 'Textarea' :
                        $element_attr = isset($customfield['required']) ? array('required' => true, 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $description) : array( 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $description);
                        $form->addElement( new Element_Textarea($name, $slug, $element_attr));
                        break;

                    case 'Hidden' :
                        $form->addElement( new Element_Hidden($name, $customfield['value']));
                        break;

                    case 'Text' :
                        $element_attr = isset($customfield['required']) ? array('required' => true, 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $description) : array( 'id' => str_replace("-", "", $slug),'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $description);
                        $form->addElement( new Element_Textbox($name, $slug, $element_attr));
                        break;

                    case 'Link' :
                        $element_attr = isset($customfield['required']) ? array('required' => true, 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $description) : array( 'id' => str_replace("-", "", $slug), 'value' => $customfield_val, 'class' => 'settings-input', 'shortDesc' =>  $description);
                        $form->addElement( new Element_Url($name, $slug, $element_attr));
                        break;
                    case 'Featured_Image':

                        $attachment_ids = $customfield_val;
                        $attachments = array_filter( explode( ',', $attachment_ids ) );

                        $str = '<div id="bf_files_container_'.$slug.'" class="bf_files_container"><ul class="bf_files">';

                        if ( $attachments ) {
                            foreach ( $attachments as $attachment_id ) {

                                $attachment_metadat = get_post( $attachment_id );

//                                echo '<pre>';
//                                print_r($attachment_metadat);
//                                echo '</pre>';

                                $str .= '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">

                                    <div class="bf_attachment_li">
                                    <div class="bf_attachment_img">
                                    '. wp_get_attachment_image( $attachment_id,  array(64,64), true) . '
                                    </div><div class="bf_attachment_meta">
                                    <p><b>' . __('Name: ', 'buddyforms') .'</b>'. $attachment_metadat->post_name.'<p>
                                    <p><b>' . __('Type: ', 'buddyforms') .'</b>'. $attachment_metadat->post_mime_type.'<p>

                                    <p>
                                    <a href="#" class="delete tips" data-slug="'.$slug.'" data-tip="' . __( 'Delete image', 'buddyforms' ) . '">' . __( 'Delete', 'buddyforms' ) . '</a>
                                    <a href="'.wp_get_attachment_url($attachment_id).'" target="_blank" class="view" data-tip="' . __( 'View', 'buddyforms' ) . '">' . __( 'View', 'buddyforms' ) . '</a>
                                    </p>
                                    </div></div>

                                </li>';
                            }
                        }

                        $str .= '</ul>';

                        $str .= '<span class="bf_add_files hide-if-no-js">';
                        $str .= '<a href="#" data-slug="'.$slug.'" data-type="image/jpeg,image/gif,image/png,image/bmp,image/tiff,image/x-icon" data-multiple="false" data-choose="' . __( 'Add ', 'buddyforms' ) . $name.'" data-update="' . __( 'Add ', 'buddyforms' ) . $name.'" data-delete="' . __( 'Delete ', 'buddyforms' ) . $name.'" data-text="' . __( 'Delete', 'buddyforms' ) . '">' . __( 'Add ', 'buddyforms' ) . $name.'</a>';
                        $str .= '</span>';

                        $str .= '</div><span class="help-inline">';
                        $str .= $description;
                        $str .= '</span>';


                        $form->addElement(new Element_HTML( '
                        <div class="bf_field_group">
                            <label for="_'.$slug.'">'));

                        if(isset($customfield['required']))
                            $form->addElement(new Element_HTML( '<span class="required">* </span>' ));

                        $form->addElement(new Element_HTML( $name.'</label>'));
                        $form->addElement(new Element_HTML( '<div class="bf_inputs">
                            '.$str.'
                            </div>
                        '));
                        $form->addElement(new Element_Hidden('featured_image', $customfield_val , array('id' => $slug)));
                        $form->addElement(new Element_HTML( '</div>' ));

                        break;
                    case 'File':

                        $attachment_ids = $customfield_val;

                        $str = '<div id="bf_files_container_'.$slug.'" class="bf_files_container"><ul class="bf_files">';


                        $attachments = array_filter( explode( ',', $attachment_ids ) );

                        if ( $attachments ) {
                            foreach ( $attachments as $attachment_id ) {

                                $attachment_metadat = get_post( $attachment_id );

//                                echo '<pre>';
//                                print_r($attachment_metadat);
//                                echo '</pre>';

                                $str .= '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">

                                    <div class="bf_attachment_li">
                                    <div class="bf_attachment_img">
                                    '. wp_get_attachment_image( $attachment_id,  array(64,64), true) . '
                                    </div><div class="bf_attachment_meta">
                                    <p><b>' . __('Name: ', 'buddyforms') .'</b>'. $attachment_metadat->post_title.'<p>
                                    <p><b>' . __('Type: ', 'buddyforms') .'</b>'. $attachment_metadat->post_mime_type.'<p>

                                    <p>
                                    <a href="#" class="delete tips" data-slug="'.$slug.'" data-tip="' . __( 'Delete image', 'buddyforms' ) . '">' . __( 'Delete', 'buddyforms' ) . '</a>
                                    <a href="'.wp_get_attachment_url($attachment_id).'" target="_blank" class="view" data-tip="' . __( 'View', 'buddyforms' ) . '">' . __( 'View', 'buddyforms' ) . '</a>
                                    </p>
                                    </div></div>

                                </li>';
                            }
                        }

                        $str .= '</ul>';

                        $str .= '<span class="bf_add_files hide-if-no-js">';

                        $data_type = '';
                        if(isset($customfield['validation_data_type']))
                            $data_type = 'data-type="' . $customfield['validation_data_type'] . '"';

                        $data_multiple = 'data-multiple="false"';
                        if(isset($customfield['validation_multiple']))
                            $data_multiple = 'data-multiple="true"';

                        $str .= '<a href="#" data-slug="'.$slug.'" '.$data_multiple.' '.$data_type.' data-choose="' . __( 'Add ', 'buddyforms' ) . $name.'" data-update="' . __( 'Add ', 'buddyforms' ) . $name.'" data-delete="' . __( 'Delete ', 'buddyforms' ) . $name.'" data-text="' . __( 'Delete', 'buddyforms' ) . '">' . __( 'Attache File', 'buddyforms' ) . '</a>';
                        $str .= '</span>';

                        $str .= '</div><span class="help-inline">';
                        $str .= $description;
                        $str .= '</span>';

                        $form->addElement(new Element_HTML( '
                        <div class="bf_field_group">
                            <label for="_'.$slug.'">'));

                        if(isset($customfield['required']))
                            $form->addElement(new Element_HTML( '<span class="required">* </span>' ));

                        $form->addElement(new Element_HTML( $name.'</label>'));
                        $form->addElement(new Element_HTML( '<div class="bf_inputs">
                            '.$str.'
                            </div>
                        '));
                        $form->addElement(new Element_Hidden($slug, $customfield_val , array('id' => $slug)));
                        $form->addElement(new Element_HTML( '</div>' ));

                        break;
                    case 'Taxonomy' :

                        $args = array(
                            'hide_empty'        => 0,
                            'id'                => $field_id,
                            'child_of'          => 0,
                            'echo'              => FALSE,
                            'selected'          => false,
                            'hierarchical'      => 1,
                            'name'              => $slug . '[]',
                            'class'             => 'postform bf-select2',
                            'depth'             => 0,
                            'tab_index'         => 0,
                            'taxonomy'          => $customfield['taxonomy'],
                            'hide_if_empty'     => FALSE,
                            'orderby'           => 'SLUG',
                            'order'             => $customfield['taxonomy_order'],
                        );

                        if(isset($customfield['show_option_none']) && !isset($customfield['multiple']))
                            $args = array_merge( $args, Array( 'show_option_none' => 'Nothing Selected' ) );

                        if(isset($customfield['multiple']))
                            $args = array_merge( $args, Array( 'multiple' => $customfield['multiple'] ) );

                        $dropdown = wp_dropdown_categories($args);

                        if (isset($customfield['multiple']) && is_array( $customfield['multiple'] ))
                            $dropdown = str_replace('id=', 'multiple="multiple" id=', $dropdown);

                        if (isset($customfield['required']) && is_array( $customfield['required'] ))
                            $dropdown = str_replace('id=', 'required id=', $dropdown);


                        $the_post_terms = get_the_terms( $post_id, $customfield['taxonomy'] );

                        if (is_array($the_post_terms)) {
                            foreach ($the_post_terms as $key => $post_term) {
                                $dropdown = str_replace(' value="' . $post_term->term_id . '"', ' value="' . $post_term->term_id . '" selected="selected"', $dropdown);
                            }
                        } else {
                            if(isset($customfield['taxonomy_default'])){
                                foreach($customfield['taxonomy_default'] as $key => $tax){
                                    $dropdown = str_replace(' value="' . $customfield['taxonomy_default'][$key] . '"', ' value="' . $tax . '" selected="selected"', $dropdown);
                                }
                            }
                        }

                        $required = '';
                        if(isset($customfield['required']) && is_array( $customfield['required'] )){
                            $required = '<span class="required">* </span>';
                        }
                        $dropdown = '<div class="bf_field_group">
                        <label for="editpost-element-' . $field_id . '">
                            '.$required.$name . ':
                        </label>
                        <div class="bf_inputs">' . $dropdown . ' </div>
                        <span class="help-inline">' . $description . '</span>
                    </div>';

                        if( isset($customfield['hidden']) ){
                            if(isset($customfield['taxonomy_default'])){
                                foreach( $customfield['taxonomy_default'] as $key => $tax){
                                    $form->addElement( new Element_Hidden($slug.'['.$key.']',$tax));
                                }
                            }
                        } else {
                            $form->addElement( new Element_HTML($dropdown));

                            if(isset($customfield['creat_new_tax']) ){
                                $form->addElement( new Element_Textbox(__('Create a new ', 'buddyforms') . $customfield['taxonomy'].':', $slug.'_creat_new_tax', array('class' => 'settings-input')));
                            }
                        }

                        break;

                    default:

                        $form_args = Array(
                            'field_id'          => $field_id,
                            'post_id'           => $post_id,
                            'post_parent'       => $post_parent,
                            'form_slug'         => $form_slug,
                            'customfield'       => $customfield,
                            'customfield_val'   => $customfield_val
                        );

                        // hook to add your form element
                        apply_filters('buddyforms_create_edit_form_display_element',$form, $form_args);

                        break;

                }
            }

        endif;
    endforeach;

}

function my_tinymce_setup_function( $initArray ) {
    $initArray['setup'] = 'function(ed){
      ed.onChange.add(function(ed, l) {
        tinyMCE.triggerSave();
	    jQuery("#editpost_content").valid();
      });
    }';
    return $initArray;
}