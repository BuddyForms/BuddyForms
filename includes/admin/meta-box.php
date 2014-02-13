<?php
/**
 * Created by PhpStorm.
 * User: svenl77
 * Date: 03.02.14
 * Time: 22:53
 */

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_add_custom_box() {
    global $buddyforms;

/*    echo '<pre>',
    print_r($buddyforms);
    echo '</pre>',*/


    $screens = array( 'post', 'page' );

    foreach ( $screens as $screen ) {

        add_meta_box(
            'buddyforms_sectionid',
            __( 'Attach BuddyForm', 'buddyforms' ),
            'buddyforms_inner_custom_box',
            $screen,
            'side', 'high'
        );
    }
}
add_action( 'add_meta_boxes', 'buddyforms_add_custom_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function buddyforms_inner_custom_box( $post ) {
    global $buddyforms;
    $buddyforms_options = $buddyforms; //get_option('buddyforms_options');
    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'buddyforms_inner_custom_box', 'buddyforms_inner_custom_box_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    $value = get_post_meta( $post->ID, '_bf_form_slug', true );

    echo '<label for="_bf_form_slug">';
    _e( "Select a Form", 'buddyforms' );
    echo '</label> ';

    echo '<select id="_bf_form_slug" name="_bf_form_slug" >';
    echo '<option value="none">none</option>';
    if(isset($buddyforms_options['buddyforms'])){
        foreach( $buddyforms_options['buddyforms'] as $key => $buddyform) {
            echo  '<option value="' .$buddyform['slug']. '"' . selected(esc_attr( $value ),$buddyform['slug'] ) . '>'.$buddyform['name'].'</option>';
        }
    }
    echo '</select>';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function buddyforms_save_postdata( $post_id ) {

    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['buddyforms_inner_custom_box_nonce'] ) )
        return $post_id;

    $nonce = $_POST['buddyforms_inner_custom_box_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'buddyforms_inner_custom_box' ) )
        return $post_id;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // Check the user's permissions.
    if ( 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) )
            return $post_id;

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }

    /* OK, its safe for us to save the data now. */

    // Sanitize user input.
    $mydata = sanitize_text_field( $_POST['_bf_form_slug'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_bf_form_slug', $mydata );
}
add_action( 'save_post', 'buddyforms_save_postdata' );