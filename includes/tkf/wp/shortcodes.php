<?php
/*
 * Tabs
 */
function tk_sc_tabs( $atts ){
	extract( shortcode_atts(array(
		'posts' => ''
	), $atts ) );
	
	$args = array(
		'include' => $posts
	);
	
	$myposts = get_pages( $args );

	foreach( $myposts as $post ): setup_postdata( $post );
	
		$elements[] = array( 'id' => $post->post_name, 'title' => $post->post_title, 'content' => do_shortcode( $post->post_content ) );
	
	endforeach; wp_reset_postdata();
	
	$tabs = tk_tabs( 'clean_tabs', $elements );
	
	return $tabs;	
}
add_shortcode( 'tk_tabs', 'tk_sc_tabs' );

?>