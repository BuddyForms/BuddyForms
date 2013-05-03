
<div id="item-body">

<?php 
	global $the_lp_query, $bp;

	$the_lp_query = new WP_Query( array( 'post_type' => $bp->current_component, 'posts_per_page' => 10, 'author' => get_current_user_id() ) );

	cpt4bp_locate_template('cpt4bp/bp/members-post-loop.php');

	if(function_exists('wp_pagenavi')){
		wp_pagenavi( array( 'query' => $the_lp_query) );	
	}


 do_action( 'bp_after_postsonprofile_body' ) ?>                

</div><!-- #item-body -->