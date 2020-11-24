<div class="tk-buddyforms-editor-nav">
	<?php global $post;

	if ( get_post_type( $post ) != 'buddyforms' ) {
		return;
	}

	$buddyform               = get_post_meta( $post->ID, '_buddyforms_options', true );
	$attached_page_permalink = isset( $buddyform['attached_page'] ) ? get_permalink( $buddyform['attached_page'] ) : '';

	$base = home_url();

	$preview_page_id = get_option( 'buddyforms_preview_page', true );
	?>
	<div id="buddyforms-actions" class="misc-pub-section">
		<?php if ( isset( $post->post_name ) && $post->post_name != '' ) { ?>
			<div id="frontend-actions">
				<a class="button button-large bf_button_action" target="_blank"
				   href="<?php echo $base ?>/?page_id=<?php echo $preview_page_id ?>&preview=true&form_slug=<?php echo $post->post_name ?>"><span
						class="dashicons dashicons-visibility"></span> <?php _e( 'Preview Form', 'buddyforms' ) ?>
				</a>
			</div>
		<?php } ?>
		<?php if ( isset( $buddyform['attached_page'] ) && isset( $buddyform['post_type'] ) && $buddyform['attached_page'] != 'none' ) { ?>
			<div class="bf-tile actions">
				<div id="frontend-actions">
					<label for="button"><?php _e( 'Frontend', 'buddyforms' ) ?></label>
					<?php echo '<a class="button button-large bf_button_action" href="' . $attached_page_permalink . 'view/' . $post->post_name . '/" target="_new"><span class="dashicons dashicons-admin-page"></span> ' . __( 'Your Submissions', 'buddyforms' ) . '</a>
                <a class="button button-large bf_button_action" href="' . $attached_page_permalink . 'create/' . $post->post_name . '/" target="_new"><span class="dashicons dashicons-feedback"></span>    ' . __( 'The Form', 'buddyforms' ) . '</a>'; ?>
				</div>
			</div>
		<?php }
		if ( isset( $post->post_name ) && $post->post_name != '' ) { ?>
			<div class="bf-tile actions">
				<div id="admin-actions">
					<label for="button"><?php _e( 'Admin', 'buddyforms' ) ?></label>
					<?php echo '<a class="button button-large bf_button_action" href="edit.php?post_type=buddyforms&page=buddyforms_submissions&form_slug=' . $post->post_name . '"><span class="dashicons dashicons-email"></span> ' . __( 'Submissions', 'buddyforms' ) . '</a>'; ?>
				</div>
			</div>
		<?php } ?>

		<div class="clear"></div>
	</div>
</div>
