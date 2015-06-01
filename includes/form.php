<?php

/**
 * Adds a form shortcode for the create and edit screen
 * @var $args = posttype, the_post, post_id
 *
 * @package buddyforms
 * @since 0.1-beta
 */

function buddyforms_create_edit_form( $args = array() ) {
    global $current_user, $buddyforms, $wp_query;

    do_action('buddyforms_create_edit_form_loader');

    // hook for plugins to overwrite the $args.
    $args = apply_filters('buddyforms_create_edit_form_args',$args);

    extract(shortcode_atts(array(
        'post_type' 	=> '',
        'the_post'		=> 0,
        'post_id'		=> 0,
        'form_slug' 	=> false,
        'form_notice'   => '',
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
        $post_type = $the_post->post_type; //buddyforms??

    if( empty( $form_slug ) )
        $form_slug = apply_filters('buddyforms_the_form_to_use',$form_slug, $post_type);

    if(isset($buddyforms['buddyforms'][$form_slug]['form_fields']))
        $customfields = $buddyforms['buddyforms'][$form_slug]['form_fields'];


    $args = array(
        'post_type' 	=> $post_type,
        'the_post'		=> $the_post,
        'customfields'  => $customfields,
        'post_id'		=> $post_id,
        'form_slug' 	=> $form_slug,
        'form_notice'   => $form_notice,
    );

    // If the form is submitted we will get in action
    if( isset( $_POST['submitted'] ) )
        buddyforms_process_post($args);

    echo buddyforms_form_html( $args );

}