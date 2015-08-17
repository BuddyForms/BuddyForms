<?php

/**
 * Adds a form shortcode for the create and edit screen
 * @var $args = posttype, the_post, post_id
 *
 * @package buddyforms
 * @since 0.1-beta
 */

function buddyforms_create_edit_form( $args ) {
    global $current_user, $buddyforms, $wp_query, $bp;

    do_action('buddyforms_create_edit_form_loader');

    // hook for plugins to overwrite the $args.
    $args = apply_filters('buddyforms_create_edit_form_args',$args);

    extract(shortcode_atts(array(
        'post_type' 	=> '',
        'the_post'		=> 0,
        'post_id'		=> 0,
        'post_parent'   => 0,
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

        $post_id = 0;
        if(isset($wp_query->query_vars['bf_post_id']))
            $post_id = $wp_query->query_vars['bf_post_id'];

        $post_parent = 0;
        if(isset($wp_query->query_vars['bf_parent_post_id']))
            $post_parent = $wp_query->query_vars['bf_parent_post_id'];

        $revision_id = 0;
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
//    echo '<pre>';
//    print_r($wp_query->query_vars);
//    echo '</pre>';

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

    if($the_post->post_parent)
        $post_parent = $the_post->post_parent;

    $args = array(
        'post_type' 	=> $post_type,
        'the_post'		=> $the_post,
        'post_parent'   => $post_parent,
        'customfields'  => $customfields,
        'post_id'		=> $post_id,
        'form_slug' 	=> $form_slug,
        'form_notice'   => $form_notice,
    );

    // If the form is submitted we will get in action
    if( isset( $_POST['submitted'] ) ){
        $args = buddyforms_process_post($args);
        if(isset($buddyforms['buddyforms'][$form_slug]['after_submit'])){
            switch ($buddyforms['buddyforms'][$form_slug]['after_submit']) {
                case 'display_post':
                    $args['form_notice'] = buddyforms_after_save_post_redirect(get_permalink( $args['post_id'] ));
                    break;
                case 'display_message':
                    $permalink = get_permalink( $buddyforms['buddyforms'][$args['form_slug']]['attached_page'] );

                    $display_message = $buddyforms['buddyforms'][$_POST['form_slug']]['after_submit_message_text'];
                    $display_message = str_ireplace('[form_singular_name]', $buddyforms['buddyforms'][$args['form_slug']]['singular_name'], $display_message);
                    $display_message = str_ireplace('[post_title]', get_the_title($args['post_id']), $display_message);
                    $display_message = str_ireplace('[post_link]', '<a title="Display Post" href="'. get_permalink( $args['post_id'] ) .'"">' . __( 'Display Post', 'buddyforms' ) .'</a>', $display_message);
                    $display_message = str_ireplace('[edit_link]', '<a title="Edit Post" href="'. $permalink.'edit/'.$args['form_slug'].'/'. $args['post_id'] .'"">' . __( 'Continue Editing', 'buddyforms' ) .'</a>', $display_message);

                    $args['form_notice'] = $display_message;
                    break;
            }
        }

        if( ! isset($_POST['submitted']) || $buddyforms['buddyforms'][$form_slug]['after_submit'] == 'display_form'){
            echo buddyforms_form_html( $args );
        } else {
            echo $args['form_notice'];
        }

    } else {
        echo buddyforms_form_html( $args );
    }

}