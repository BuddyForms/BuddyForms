<?php
/**
 * Ajax call back function to add a form element
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_add_form(){
    global $buddyforms;

    if(!is_array($buddyforms))
        $buddyforms = Array();

    if(empty($_POST['create_new_form_name']))
        return;
    if(empty($_POST['create_new_form_singular_name']))
        return;
    if(empty($_POST['create_new_form_attached_page']) && empty($_POST['create_new_page']))
        return;
    if(empty($_POST['create_new_form_post_type']))
        return;

    if(!empty($_POST['create_new_page'])){
        // Create post object
        $mew_post = array(
            'post_title'    => wp_strip_all_tags( $_POST['create_new_page'] ),
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'page'
        );

        // Insert the post into the database
        $_POST['create_new_form_attached_page'] = wp_insert_post( $mew_post );
    }

    $create_new_form_slug = sanitize_title($_POST['create_new_form_name']);
    $mod5 = substr(md5(time() * rand()), 0, 10);

    if(isset($buddyforms['buddyforms']) && array_key_exists($create_new_form_slug, $buddyforms['buddyforms']))
        $create_new_form_slug = $create_new_form_slug.'-'.$mod5;

    $options = Array(
        'slug'              => $create_new_form_slug,
        'name'              => $_POST['create_new_form_name'],
        'singular_name'     => $_POST['create_new_form_singular_name'],
        'attached_page'     => $_POST['create_new_form_attached_page'],
        'post_type'         => $_POST['create_new_form_post_type'],
    );

    if(!empty($_POST['create_new_form_status']))
        $options = array_merge($options, Array('status' => $_POST['create_new_form_status']));

    if(!empty($_POST['create_new_form_comment_status']))
        $options = array_merge($options, Array('comment_status' => $_POST['create_new_form_comment_status']));

    $field_id = $mod5 = substr(md5(time() * rand()), 0, 10);

    $options['form_fields'][$field_id]['name']          = 'Title';
    $options['form_fields'][$field_id]['slug']          = 'editpost_title';
    $options['form_fields'][$field_id]['type']          = 'Title';
    $options['form_fields'][$field_id]['order']         = '1';

    $field_id = $mod5 = substr(md5(time() * rand()), 0, 10);

    $options['form_fields'][$field_id]['name']          = 'Content';
    $options['form_fields'][$field_id]['slug']          = 'editpost_content';
    $options['form_fields'][$field_id]['type']          = 'Content';
    $options['form_fields'][$field_id]['order']         = '2';

    $buddyforms['buddyforms'][$create_new_form_slug] = $options;

    $update_option = update_option("buddyforms_options", $buddyforms);

    if($update_option){
        buddyforms_attached_page_rewrite_rules(TRUE);
        echo sanitize_title($_POST['create_new_form_name']);
    } else {
        echo 'Error Saving the Form';
    }

    die();

}
add_action( 'wp_ajax_buddyforms_add_form', 'buddyforms_add_form' );

function buddyforms_save_options(){
    if(empty($_POST['buddyforms_options']))
        return;

    parse_str($_POST['buddyforms_options'], $formdata);
//    echo '<pre>';
//    print_r($formdata['buddyforms_options']);
//    echo '</pre>';

    $update_option = update_option('buddyforms_options', $formdata['buddyforms_options']);

    buddyforms_attached_page_rewrite_rules(TRUE);

    die();
}
add_action( 'wp_ajax_buddyforms_save_options', 'buddyforms_save_options' );
/**
 * Ajax call back function to delete a form element
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_delete_form(){

    $buddyforms_options = get_option('buddyforms_options');
    unset( $buddyforms_options['buddyforms'][$_POST['dele_form_slug']] );

    buddyforms_attached_page_rewrite_rules(TRUE);
    update_option("buddyforms_options", $buddyforms_options);
    die();
}
add_action('wp_ajax_buddyforms_delete_form', 'buddyforms_delete_form');
//add_action('wp_ajax_nopriv_buddyforms_delete_form', 'buddyforms_delete_form');

/**
 * Ajax call back function to save the new form elements order
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_save_item_order() {
    global $wpdb;

    $buddyforms_options = get_option('buddyforms_options');
    $order = explode(',', $_POST['order']);
    $counter = 0;

    foreach ($order as $item_id) {
        $item_id = explode('/', $item_id);
        $buddyforms_options[$item_id[0]][$item_id[1]][$item_id[2]][$item_id[3]][$item_id[4]] = $counter;
        $counter++;
    }

    update_option("buddyforms_options", $buddyforms_options);
    die();
}
add_action('wp_ajax_buddyforms_save_item_order', 'buddyforms_save_item_order');

/**
 * Ajax call back function to delete a form element
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_item_delete(){
    $post_args = explode('/', $_POST['post_args']);

    $buddyforms_options = get_option('buddyforms_options');

    unset( $buddyforms_options[$post_args[0]][$post_args[1]]['form_fields'][$post_args[3]] );

    update_option("buddyforms_options", $buddyforms_options);
    die();
}
add_action('wp_ajax_buddyforms_item_delete', 'buddyforms_item_delete');
//add_action('wp_ajax_nopriv_buddyforms_item_delete', 'buddyforms_item_delete');

/**
 * Get all taxonomies
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_taxonomies($form_slug){
    global $buddyforms;

    $post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

    $taxonomies=get_object_taxonomies($post_type);

    return $taxonomies;
}