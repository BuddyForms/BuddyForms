<style>
	.item .item-status { 
padding: 3px 5px; 
border-radius: 3px; 
-moz-border-radius: 3px; 
-webkit-border-radius: 3px; 
font-weight: bold; 
color: white; 
background: #555555; 
}
li.publish .item .item-status { background: green; } 
li.pending .item .item-status { background: orange; }
li.draft .item .item-status { background: red; }
</style>
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