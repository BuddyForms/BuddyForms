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
        'form_slug' => ''
    ), $args));

	if(!isset($buddyforms['buddyforms'][$form_slug]['post_type']))
		return;

	if(empty($post_type))
		$post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

	if (!get_current_user_id())
		return;

    $paged = bf_get_url_var('page');

	$query_args = array(
		'post_type'         => $post_type,
		'form_slug'         => $form_slug,
		'post_status'       => array('publish', 'pending', 'draft'),
		'posts_per_page'    => 10,
        'post_parent'		=> 0,
		'author'            => get_current_user_id(),
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
?>