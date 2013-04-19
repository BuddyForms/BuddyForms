<?php 
if(isset($_POST['deletepost'])) {

	BP_CGT::delete_a_group($_POST['editpost_id'], $_POST['editpost_post_type']);
	
	wp_delete_post($_POST['editpost_id'],true);
	
	$deleted = true;	
}
if($deleted == true){
	echo _e('Post successful deleted','cgt');
} else { ?>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="delete_post" method="POST">

	<p><?php _e('Delete Post','cgt') ?></p>
	
	<input type="hidden" name="editpost_id" value="<?php echo get_the_ID(); ?>" />
	<input type="hidden" name="editpost_post_type" value="<?php echo get_post_type( get_the_ID() ) ?>" />
			
	<li class="buttons">
		<input type="hidden" name="deletepost" id="deletepost" value="true" />
		<button type="submit" id="deletepost" class="button"><?php _e('Delete','cgt'); ?></button>
	</li>
</form>

<?php } ?>