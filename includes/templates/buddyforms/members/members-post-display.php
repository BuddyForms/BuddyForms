<div id="item-body">

<?php 
	global $current_user, $the_lp_query, $bp;
	
	
	if ($bp->displayed_user->id == $current_user->ID){
		$args = array( 
			'post_type' => $bp->current_component,
			'post_status' => array('publish', 'pending', 'draft'),
			'posts_per_page' => 10,
			'author' => get_current_user_id() );
	} else {
		$args = array( 
			'post_type' => $bp->current_component,
			'post_status' => array('publish'),
			'posts_per_page' => 10,
			'author' => $bp->displayed_user->id );
	}
	$the_lp_query = new WP_Query( $args );

	buddyforms_locate_template('buddyforms/members/members-post-loop.php');
	
	// Support for wp_pagenavi
	if(function_exists('wp_pagenavi')){
		wp_pagenavi( array( 'query' => $the_lp_query) );	
	}

?>              

</div><!-- #item-body -->