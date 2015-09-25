<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_add_meta_boxes() {
    global $post;


    if($post->post_type != 'buddyforms')
        return;

    add_meta_box('buddyforms_form_setup', __("Form Setup",'buddyforms') . '<br><small>' . __('Setup this form ', 'buddyforms'), 'buddyforms_metabox_form_setup', 'buddyforms', 'normal', 'high');
    add_meta_box('buddyforms_form_elements', __("Form Builder",'buddyforms') . '<br><small>' . __(' Add additional form elements from the right box "Form Elements". Change the order via drag and drop.', 'buddyforms') . '</small>', 'buddyforms_metabox_form_elements', 'buddyforms', 'normal', 'high');
    add_meta_box('buddyforms_form_mail', __("Mail Notification",'buddyforms') . '<br><small>' . __(' Add Mail Notification for any post status change".', 'buddyforms') . '</small>', 'bf_mail_notification_screen', 'buddyforms', 'normal', 'default');
    add_meta_box('buddyforms_form_roles', __("Roles and Capabilities",'buddyforms') . '<br><small>' . __('Manage Capabilities for every user user role ', 'buddyforms') . '</small>', 'bf_manage_form_roles_and_capabilities_screen', 'buddyforms', 'normal', 'default');
    add_meta_box('buddyforms_form_sidebar', __("Form Elements",'buddyforms'), 'buddyforms_metabox_sidebar', 'buddyforms', 'side', 'default');

}
add_action( 'add_meta_boxes', 'buddyforms_add_meta_boxes' );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_edit_form_save_meta_box_data($post_id){
    global $post;

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
        return;

    if(!isset($post->post_type) || $post->post_type != 'buddyforms')
        return;

    update_post_meta( $post_id, '_buddyforms_options', $_POST['buddyforms_options'] );

    $buddyform = $_POST['buddyforms_options'];

    if(isset($_POST['buddyforms_roles'])){

        foreach (get_editable_roles() as $role_name => $role_info):
            $role = get_role( $role_name );
            foreach ($role_info['capabilities'] as $capability => $_):

                $capability_array = explode('_', $capability);

                if($capability_array[0] == 'buddyforms'){
                    if($capability_array[1] == $buddyform['slug']){

                        $role->remove_cap( $capability );

                    }
                }

            endforeach;
        endforeach;

        foreach($_POST['buddyforms_roles'] as $form_role => $capabilities){
            foreach($capabilities as $key => $capability){
                $role = get_role( $key );
                foreach ($capability as $key => $cap) {
                    $role->add_cap( $cap );
                }
            }

        }

    }

}
add_action( 'save_post', 'buddyforms_edit_form_save_meta_box_data' );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_register_post_type(){

    // Create BuddyForms post type
    $labels = array(
        'name' => __('BuddyForms', 'buddyforms'),
        'singular_name' => __('BuddyForm', 'buddyforms'),
        'add_new' => __('Add New', 'buddyforms'),
        'add_new_item' => __('Add New Form', 'buddyforms'),
        'edit_item' => __('Edit Form', 'buddyforms'),
        'new_item' => __('New Form', 'buddyforms'),
        'view_item' => __('View Form', 'buddyforms'),
        'search_items' => __('Search BuddyForms', 'buddyforms'),
        'not_found' => __('No BuddyForm found', 'buddyforms'),
        'not_found_in_trash' => __('No Forms found in Trash', 'buddyforms'),
    );

    register_post_type('buddyforms', array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        '_builtin' => false,
        'capability_type' => 'page',
        'hierarchical' => true,
        'rewrite' => false,
        'supports' => array(
            'title'
        ),
        'show_in_menu' => true,
        'description' => 'MAl sehen was das soll',
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'menu_icon' => 'dashicons-feedback',
    ));

}
add_action( 'init', 'buddyforms_register_post_type' );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function codex_form_updated_messages( $messages ) {
    global $post, $post_ID;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);
    $viwe_form_permalink = isset($buddyform['attached_page']) ? get_permalink($buddyform['attached_page']) : '';

    $messages = array(
        0 => '', // Unused. Messages start at index 1.
        1 => sprintf( __('Form updated. <a href="%s">View Form</a>'), $viwe_form_permalink . 'create/' . $post->post_name  ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Form updated.'),
        /* translators: %s: date and time of the revision */
        5 => isset($_GET['revision']) ? sprintf( __('Form restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Form published. <a href="%s">View Form</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Form saved.'),
        8 => sprintf( __('Form submitted. <a target="_blank" href="%s">Preview Form</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Form scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Form</a>'),
            // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Form draft updated. <a target="_blank" href="%s">Preview Form</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );

    return $messages;
}
add_filter( 'post_updated_messages', 'codex_Form_updated_messages' );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function set_custom_edit_buddyforms_columns($columns) {
    unset($columns['date']);
    $columns['slug'] = __( 'Slug', 'buddyforms' );
    $columns['attached_post_type'] = __( 'Attached Post Type', 'buddyforms' );
    $columns['attached_page'] = __( 'Attached Page', 'buddyforms' );
    return $columns;
}
add_filter( 'manage_buddyforms_posts_columns', 'set_custom_edit_buddyforms_columns',10,1 );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function custom_buddyforms_column( $column, $post_id ) {

    $post =  get_post($post_id);
    $buddyform = get_post_meta($post_id, '_buddyforms_options', true);

    switch ( $column ) {
        case 'slug' :
            echo $post->post_name;
            break;
        case 'attached_post_type' :

            $post_type_html = isset($buddyform['post_type']) ? $buddyform['post_type'] : 'none';

            if(!post_type_exists($post_type_html))
                $post_type_html = '<p style="color: red;">' . __('Post Type not exists', 'buddyforms') . '</p>';

            if(!isset($buddyform['post_type']) || $buddyform['post_type'] == 'none')
                $post_type_html = '<p style="color: red;">' . __('No Post Type not Selected', 'buddyforms') . '</p>';

            echo $post_type_html;
            break;
        case 'attached_page' :
            if( isset($buddyform['attached_page']) && empty($buddyform['attached_page']) ){
                $attached_page = '<p style="color: red;">No Page Attached</p>';
            } elseif(isset($buddyform['attached_page']) && $attached_page_title = get_the_title($buddyform['attached_page'])) {
                $attached_page = $attached_page_title;
            } else {
                $attached_page = '<p style="color: red;">Page not Exists</p>';
            }

            echo $attached_page;

        break;
    }
}
add_action( 'manage_buddyforms_posts_custom_column' , 'custom_buddyforms_column', 10, 2 );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function hide_publishing_actions(){
    $my_post_type = 'buddyforms';
    global $post;
    if($post->post_type == $my_post_type){
        echo '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
    }
}
add_action('admin_head-post.php', 'hide_publishing_actions');
add_action('admin_head-post-new.php', 'hide_publishing_actions');

//add_action( 'load-edit.php', function(){
//
//    $screen = get_current_screen();
//
////    echo '<pre>';
////    print_r($screen);
////echo '</pre>';
//
//    // Only edit post screen:
//    if( 'edit-buddyforms' === $screen->id )
//    {
//        // Before:
//        add_action( 'all_admin_notices', function(){
//
//            include('admin-credits.php');
//
//        });
//
//        // After:
//        add_action( 'in_admin_footer', function(){
//            echo '<p>Goodbye from <strong>in_admin_footer</strong>!</p>';
//        });
//    }
//});
//

function add_menu_icons_styles(){ ?>

    <style>
        #adminmenu .menu-icon-buddyforms div.wp-menu-image:before {
            content: '\f328';
        }
    </style>

    <?php
}
add_action( 'admin_head', 'add_menu_icons_styles' );


function buddyforms_edit_form_top(){?>


                sdd


<?php
}
//add_action( 'edit_form_top', 'buddyforms_edit_form_top' );
