<?php
// Shortcode to add the form everywhere easily ;) the form is located in form.php
add_shortcode('buddyforms_form', 'buddyforms_create_edit_form_shortcode');

function buddyforms_create_edit_form_shortcode($args){

    extract(shortcode_atts(array(
        'post_type' => '',
        'the_post' => 0,
        'post_id' => '',
        'revision_id' => false,
        'form_slug' => '',
    ), $args));

    ob_start();
    buddyforms_create_edit_form($args);
    $create_edit_form = ob_get_contents();
    ob_clean();

    return $create_edit_form;
}

function bf_get_url_var($name){
    $strURL = $_SERVER['REQUEST_URI'];
    $arrVals = explode("/",$strURL);
    $found = 0;
    foreach ($arrVals as $index => $value)
    {
        if($value == $name) $found = $index;
    }
    $place = $found + 1;
    return ($found == 0) ? 1 : $arrVals[$place];
}

/**
 * Shortcode to display author posts of a specific post type
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_shortcode('buddyforms_the_loop', 'buddyforms_the_loop');
function buddyforms_the_loop($args){
	global $the_lp_query, $buddyforms, $form_slug, $paged;

    extract(shortcode_atts(array(
        'post_type' => '',
        'form_slug' => '',
        'post_parent' => 0
    ), $args));

	if(!isset($buddyforms['buddyforms'][$form_slug]['post_type']))
		return;

	if(empty($post_type))
		$post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

    $user_id = get_current_user_id();
    $post_status = array('publish', 'pending', 'draft');

    if (!$user_id)
        $post_status = array('publish');

    $paged = bf_get_url_var('page');

	$query_args = array(
		'post_type'         => $post_type,
        'post_parent'       => $post_parent,
		'form_slug'         => $form_slug,
		'post_status'       => $post_status,
		'posts_per_page'    => 10,
		'author'            => $user_id,
        'paged'             => $paged
	);

    $query_args =  apply_filters('bf_post_to_display_args',$query_args);

	$the_lp_query = new WP_Query( $query_args );

	$form_slug = $the_lp_query->query_vars['form_slug'];

	buddyforms_locate_template('buddyforms/the-loop.php');

	// Support for wp_pagenavi
	if(function_exists('wp_pagenavi')){
		wp_pagenavi( array( 'query' => $the_lp_query) );	
	}
    wp_reset_postdata();
}

/**
 * Shortcode to display author posts of a specific post type
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_shortcode('buddyforms_list_all', 'buddyforms_list_all');



function buddyforms_list_all($args){
    global $the_lp_query, $buddyforms, $form_slug, $paged;

    extract(shortcode_atts(array(
        'form_slug' => ''
    ), $args));

    $post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

    if(!$post_type)
        return;

    $paged = bf_get_url_var('page');

    $query_args = array(
        'post_type'         => $post_type,
        'form_slug'         => $form_slug,
        'post_status'       => array('publish'),
        'posts_per_page'    => 10,
        'paged'             => $paged
    );

    $query_args =  apply_filters('bf_post_to_display_args',$query_args);

    $the_lp_query = new WP_Query( $query_args );

    $form_slug = $the_lp_query->query_vars['form_slug'];
    ob_start();
        buddyforms_locate_template('buddyforms/the-loop.php');

        // Support for wp_pagenavi
        if(function_exists('wp_pagenavi')){
            wp_pagenavi( array( 'query' => $the_lp_query) );
        }
        $theloop = ob_get_clean();
    wp_reset_postdata();
    return $theloop;
}

//
// BuddyForms Schortcode Buttons
//

add_shortcode('buddyforms_nav', 'buddyforms_nav');

function buddyforms_nav($args){

    extract(shortcode_atts(array(
        'form_slug'     => '',
        'separator'     => ' | '
    ), $args));

    $tmp = buddyforms_button_view_posts($args);
    $tmp .= $separator;
    $tmp .= buddyforms_button_add_new($args);

    return $tmp;
}

add_shortcode('buddyforms_button_view_posts', 'buddyforms_button_view_posts');
function buddyforms_button_view_posts($args){
    global $buddyforms;

    extract(shortcode_atts(array(
        'form_slug' => '',
        'label'    => 'View',
    ), $args));

    $button = '<a class="button" href="/'.get_post( $buddyforms['buddyforms'][$form_slug]['attached_page'] )->post_name.'/view/'.$form_slug.'/"> '.__($label, 'buddyforms').' </a>';

    return apply_filters('buddyforms_button_view_posts',$button,$args);

}

add_shortcode('buddyforms_button_add_new', 'buddyforms_button_add_new');
function buddyforms_button_add_new($args){
    global $buddyforms;

    extract(shortcode_atts(array(
        'form_slug' => '',
        'label'     => 'Add New',
    ), $args));


    $button = '<a class="button" href="/'.get_post( $buddyforms['buddyforms'][$form_slug]['attached_page'] )->post_name.'/create/'.$form_slug.'/"> '.__($label, 'buddyforms').'</a>';

    return apply_filters('buddyforms_button_add_new',$button,$args);

}

//add_shortcode('buddyforms_ajax_nav', 'buddyforms_ajax_nav');
function buddyforms_ajax_nav($args){

    extract(shortcode_atts(array(
        'form_slug' => ''
    ), $args));


    $tmp = '<a class="button bf_view_form" href="'.$form_slug.'"> '.__('View', 'buddyforms').' </a>';
    $tmp .= '<a class="button bf_add_new_form" href="'.$form_slug.'"> '.__('Add New', 'buddyforms').' </a>';

    $tmp .= '<div class="bf_blub"></div>';


    return $tmp;

}

//add_action('wp_ajax_buddyforms_list_all_ajax', 'buddyforms_list_all_ajax');
//add_action('wp_ajax_nopriv_buddyforms_list_all_ajax', 'buddyforms_list_all_ajax');
function buddyforms_list_all_ajax(){

    if(isset($_POST['form_slug'])) {
        $form_slug = $_POST['form_slug'];

        $args = array(
            'form_slug' => $form_slug
        );
        echo buddyforms_list_all($args);

    }
    die();
}