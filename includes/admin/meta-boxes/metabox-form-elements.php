<?php

function bf_edit_form_screen(){
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);


    $form_setup = array();

    $slug = $post->post_name;

    $form_setup[] = new Element_HTML('<div id="buddyforms_forms_builder_' . $slug . '" class="buddyforms_forms_builder">');


    $sortArray = array();

//    if (!empty($buddyform['form_fields'])) {
//        foreach ($buddyform['form_fields'] as $key => $array) {
//            $sortArray[$key] = $array['order'];
//        }
//        array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $buddyform['form_fields']);
//    }

    $form_setup[] = new Element_HTML('
        <div class="fields_header">
            <table class="wp-list-table widefat fixed posts">
                <thead>
                    <tr>
                        <th class="field_order">Field Order</th>
                        <th class="field_label">Field Label</th>
                        <th class="field_name">Field Slug</th>
                        <th class="field_type">Field Type</th>
                        <th class="field_type">Action</th>
                    </tr>
                </thead>
            </table>
         </div>
    ');

    $form_setup[] = new Element_HTML('<ul id="sortable_' . $slug . '" class="sortable sortable_' . $slug . '">');

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
                            //'field_position' => $customfield['order'],
                            'field_id' => $field_id,
                            'form_slug' => $slug,
                            'post_type' => $buddyform['post_type'],
                            'field_type' => $customfield['type']
                        );
                        $form_setup[] = new Element_HTML(buddyforms_view_form_fields($args));

                    }

                }
            }


            $form_setup[] = new Element_HTML('</ul>');
        $form_setup[] = new Element_HTML('</div>');


    foreach($form_setup as $key => $field){
        echo $field->getLabel();
        echo $field->getShortDesc();
        echo $field->render();
    }
}