<?php
// Shortcode to add the form everywhere easely ;-)
add_shortcode('buddyforms_edit_form', 'buddyforms_edit_form');
function buddyforms_edit_form(){
	return 'Boom';
}

add_shortcode('buddyforms_the_loop', 'buddyforms_the_loop');
function buddyforms_the_loop($args){?>
<div id="item-body">
<style>
	
	.test{
	float:left;
}
.item-status { 
	padding: 3px 5px; 
	border-radius: 3px; 
	-moz-border-radius: 3px; 
	-webkit-border-radius: 3px; 
	font-weight: bold; 
	color: white; 
	background: #555555; 
	font-size: 11px;
	float: left;
	margin-right: 5px;
	text-shadow: -1px -1px 0px #222;
}
.publish .item-status { background: green; } 
.pending .item-status { background: orange; }
.draft .item-status { background: red; }
</style>
	<?php 
	global $current_user, $the_lp_query, $bp, $buddyforms;
	
	extract(shortcode_atts(array(
		'post_type' => '',
		'form_slug' => ''
	), $args));
	
	if(empty($post_type))
		$post_type = $buddyforms['buddyforms'][$form_slug]['post_type'];

	if (get_the_author_meta('ID') == get_current_user_id()){	
		$args = array( 
			'post_type' => $post_type,
			'form_slug' => $form_slug,
			'post_status' => array('publish', 'pending', 'draft'),
			'posts_per_page' => 10,
			'author' => get_current_user_id() );
	}
	
	$the_lp_query = new WP_Query( $args );
	
	buddyforms_locate_template('buddyforms/the-loop.php');
	
	// Support for wp_pagenavi
	if(function_exists('wp_pagenavi')){
		wp_pagenavi( array( 'query' => $the_lp_query) );	
	}
	
	?>              

</div><!-- #item-body -->
<?php
}

?>
