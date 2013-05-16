
<div id="item-body">

<?php 
	global $the_lp_query, $bp;

	$the_lp_query = new WP_Query( array( 'post_type' => $bp->current_component, 'posts_per_page' => 10, 'author' => get_current_user_id() ) );

	buddyforms_locate_template('buddyforms/members/members-post-loop.php');
	
	// Support for wp_pagenavi
	if(function_exists('wp_pagenavi')){
		wp_pagenavi( array( 'query' => $the_lp_query) );	
	}

?>              

</div><!-- #item-body -->