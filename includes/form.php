<?php

/**
 * Adds a form shortcode for the create and edit screen
 * @var $args = posttype, the_post, post_id
 *
 * @package buddyforms
 * @since 0.1-beta
 */

function buddyforms_create_edit_form( $args = array() ) {
    global $current_user, $buddyforms, $post_id, $wp_query, $form_slug, $form;

    session_id('buddyforms-create-edit-form');

    do_action('buddyforms_create_edit_form_loader');

    // hook for plugins to overwrite the $args.
    $args = apply_filters('buddyforms_create_edit_form_args',$args);

    extract(shortcode_atts(array(
        'post_type' 	=> '',
        'the_post'		=> 0,
        'post_id'		=> $post_id,
        'form_slug' 	=> $form_slug,
    ), $args));

    get_currentuserinfo();

    if(empty($post_type))
        $post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

    // if post edit screen is displayed in pages
    if(isset($wp_query->query_vars['bf_action'])){

        $form_slug = '';
        if(isset($wp_query->query_vars['bf_form_slug']))
            $form_slug = $wp_query->query_vars['bf_form_slug'];

        $post_id = '';
        if(isset($wp_query->query_vars['bf_post_id']))
            $post_id = $wp_query->query_vars['bf_post_id'];

        $revision_id = '';
        if(isset($wp_query->query_vars['bf_rev_id']))
            $revision_id = $wp_query->query_vars['bf_rev_id'];

        $post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

        if(!empty($revision_id)) {
            $the_post = get_post( $revision_id );
        } else {
            $post_id = apply_filters('bf_create_edit_form_post_id', $post_id);
            $the_post = get_post($post_id, 'OBJECT');
        }

        if($wp_query->query_vars['bf_action'] == 'edit'){

            $user_can_edit = false;
            if ($the_post->post_author == $current_user->ID){
                $user_can_edit = true;
            }
            $user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );

            if ( $user_can_edit == false ){
                $error_message = __('You are not allowed to edit this post. What are you doing here?', 'buddyforms');
                echo '<div class="error alert">'.$error_message.'</div>';
                return;
            }

        }

    }

    // if post edit screen is displayed
    if(!empty($post_id)) {

        if(!empty($revision_id)) {
            $the_post	= get_post( $revision_id );
        } else {
            $post_id = apply_filters('bf_create_edit_form_post_id', $post_id);
            $the_post	= get_post( $post_id );
        }

        $user_can_edit = false;
        if ($the_post->post_author == $current_user->ID){
            $user_can_edit = true;
        }
        $user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );

        if ( $user_can_edit == false ){
            $error_message = __('You are not allowed to edit this post. What are you doing here?', 'buddyforms');
            echo '<div class="error alert">'.$error_message.'</div>';
            return;
        }
    }

    // If post_id == 0 a new post is created
    if($post_id == 0){
        require_once(ABSPATH . 'wp-admin/includes/admin.php');
        $the_post = get_default_post_to_edit($post_type);
    }

    if( empty( $post_type ) )
        $post_type = $the_post->post_type;

    if( empty( $form_slug ) )
        $form_slug = apply_filters('buddyforms_the_form_to_use',$form_slug, $post_type);

    if(isset($buddyforms['buddyforms'][$form_slug]['form_fields']))
        $customfields = $buddyforms['buddyforms'][$form_slug]['form_fields'];


    // If the form is submitted we will get in action
    if( isset( $_POST['submitted'] ) ) {

//        $user_can_edit = false;
//        if ($the_post->post_author == $current_user->ID){
//            $user_can_edit = true;
//        }
//        $user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );
//        echo '3';
//        if ( $user_can_edit == false ){
//            $error_message = __('You are not allowed to edit this post. What are you doing here?', 'buddyforms');
//            echo '<div class="error alert">'.$error_message.'</div>';
//            return;
//        }

        $hasError = false;

        $comment_status = $buddyforms['buddyforms'][$form_slug]['comment_status'];
        if(isset($_POST['comment_status']))
            $comment_status = $_POST['comment_status'];

        $post_excerpt = '';
        if(isset($_POST['post_excerpt']))
            $post_excerpt = $_POST['post_excerpt'];

        $action			= 'save';
        $post_status	= $buddyforms['buddyforms'][$form_slug]['status'];

        if( isset( $_POST['post_id'] ) && ! empty( $_POST['post_id'] ) ){
            $action = 'update';
            $post_status = get_post_status( $_POST['post_id'] );
        }

        if(isset($_POST['status']))
            $post_status = $_POST['status'];

        $args = Array(
            'action'			=> $action,
            'form_slug'			=> $form_slug,
            'post_type' 		=> $post_type,
            'post_excerpt'		=> $post_excerpt,
            'post_author' 		=> $current_user->ID,
            'post_status' 		=> $post_status,
            'post_parent' 		=> 0,
            'comment_status'	=> $comment_status,
        );

        $post_id = bf_post_control($args);

        if($post_id){
            // Check if the post has post meta / custom fields
            if(isset($customfields))
                bf_update_post_meta($post_id, $customfields);

            $hasError = bf_set_post_thumbnail($post_id);

            bf_media_handle_upload($post_id);

            // Save the Form slug as post meta
            update_post_meta($post_id, "_bf_form_slug", $form_slug);

        } else {
            $hasError = true;
        }

        // Display the message
        if( empty( $hasError ) ) :

            if(isset( $_POST['post_id'] ) && ! empty( $_POST['post_id'] )){
                $info_message = __('The ', 'buddyforms') . $buddyforms['buddyforms'][$form_slug]['singular_name']. __(' has been successfully updated', 'buddyforms'). '<a href="'.get_permalink($post_id).'" target="_blank"> View '.$buddyforms['buddyforms'][$form_slug]['singular_name'].'</a>';
                $form_notice = '<div class="info alert">'.$info_message.'</div>';
            } else {
                $info_message = __('The ', 'buddyforms') . $buddyforms['buddyforms'][$form_slug]['singular_name']. __(' has been successfully created', 'buddyforms'). '<a href="'.get_permalink($post_id).'" target="_blank"> View '.$buddyforms['buddyforms'][$form_slug]['singular_name'].'</a>';
                $form_notice = '<div class="info alert">'.$info_message.'</div>';
            }

        else:

            $error_message = __('Error! There was a problem submitting the post ;-(', 'buddyforms');
            $form_notice = '<div class="error alert">'.$error_message.'</div>';

            if(!empty($fileError))
                $form_notice = '<div class="error alert">'.$fileError.'</div>';

        endif;

        do_action('buddyforms_after_save_post', $post_id);
    }

    $args = array(
        'post_type' 	=> $post_type,
        'the_post'		=> $the_post,
        'customfields'  => $customfields,
        'post_id'		=> $post_id,
        'form_slug' 	=> $form_slug,
    );


    echo buddyforms_form_html( $args );

}