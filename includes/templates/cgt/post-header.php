<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<li><a href="<?php cgt_group_extension_link(); ?>"><?php _e( 'View', 'bp-cgt' ) ?></a> </li>
		<?php if ( get_current_user_id() == get_the_author_ID() ) { ?>
			<li><a href="<?php echo BP_DOCS_EDIT_SLUG.'/' ?>"><?php _e( 'Edit', 'bp-cgt' ) ?></a> </li>
			<li><a href="<?php echo BP_DOCS_DELETE_SLUG.'/' ?> "><?php _e( 'Delete', 'bp-cgt' ) ?></a> </li>
		<?php }?>
	</ul>
</div><!-- .item-list-tabs -->

<?php


function cgt_group_extension_link(){
	global $bp;
	echo bp_group_permalink().$bp->current_action.'/';
}?>