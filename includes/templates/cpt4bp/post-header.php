<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<li><a href="<?php cpt4bp_group_extension_link(); ?>"><?php _e( 'View', 'cpt4bp' ) ?></a> </li>
		<?php if ( get_current_user_id() == get_the_author_ID() ) { ?>
			<li><a href="<?php echo BP_DOCS_EDIT_SLUG.'/' ?>"><?php _e( 'Edit', 'cpt4bp' ) ?></a> </li>
			<li><a href="<?php echo BP_DOCS_DELETE_SLUG.'/' ?> "><?php _e( 'Delete', 'cpt4bp' ) ?></a> </li>
		<?php }?>
	</ul>
</div><!-- .item-list-tabs -->