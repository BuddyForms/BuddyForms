<?php global $buddyforms, $form_slug, $post_id ?>
<script>
    jQuery(document).ready(function () {
        jQuery(".bf_submit_form<?php echo $post_id; ?> :input").attr("disabled", true);
        jQuery(".bf_submit_form<?php echo $post_id; ?>").show();
    });
</script>
<div id="poststuff" class="bf_submit_form<?php echo $post_id; ?>">
    <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">

            <div class="buddyforms-metabox postbox-submissions postbox">
                <h3 class="hndle"><span>Entry</span></h3>
                <div class="inside">
                    <script>
                        jQuery(document).ready(function () {
                            jQuery("#metabox_<?php echo $form_slug ?> :input").attr("disabled", true);
                            jQuery('#metabox_<?php echo $form_slug ?>').prop('readonly', true);
                            jQuery('#metabox_<?php echo $form_slug ?>').find('input, textarea, button, select').attr('disabled', 'disabled');
                        });
                    </script>
					<?php
					session_id( 'buddyforms-submissions-modal' . $post_id );

					// Create the form object
					$form = new Form( "submissions_" . $form_slug );

					// Set the form attribute
					$form->configure( array(
						//"prevent" => array("bootstrap", "jQuery", "focus"),
						//"action" => $redirect_to,
						"view"  => new View_Metabox(),
						'class' => 'standard-form',
					) );

					$fields = $buddyforms[ $form_slug ]['form_fields'];

					$args = array(
						'post_type'    => $buddyforms[ $form_slug ]['post_type'],
						'customfields' => $fields,
						'post_id'      => $post_id,
						'form_slug'    => $form_slug,
					);

					// if the form has custom field to save as post meta data they get displayed here
					buddyforms_form_elements( $form, $args );

					$form->render();

					?>
                </div>
            </div>


        </div>
        <div id="postbox-container-1" class="buddyforms-metabox postbox-container">
            <div id="submitdiv" class="buddyforms-metabox postbox">

                <h3 class="hndle"><span>Entry Actions</span></h3>
                <div class="inside">
                    <div class="submitbox">
                        <div id="minor-publishing-<?php echo $post_id; ?>" class="frm_remove_border">
                            <div class="misc-pub-section">
                                <div class="clear"></div>
                            </div>
                            <div id="misc-publishing-actions-<?php echo $post_id; ?>">

                                <div class="misc-pub-section curtime misc-pub-curtime">
										    <span id="timestamp-<?php echo $post_id; ?>">
										    Submitted: <b><?php echo get_the_date( 'l, F j, Y', $post_id ); ?></b>    </span>
                                </div>

                                <div class="misc-pub-section">
                                    <span class="dashicons dashicons-format-aside wp-media-buttons-icon"></span>&nbsp;<a
                                            href="#" onclick="window.print();return false;">Print</a>
                                </div>

                                <div class="misc-pub-section">
                                    <a href="?post_type=buddyforms&page=buddyforms_submissions&form_slug=<?php echo $form_slug ?>"
                                       class="button button-primary bf-close-submissions-modal"
                                       data-id="<?php the_ID() ?>">Close</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="buddyforms-metabox postbox frm_with_icons">
                <h3 class="hndle"><span>Entry Details</span></h3>
                <div class="inside">

                    <div class="misc-pub-section">
                        <span class="dashicons dashicons-id wp-media-buttons-icon"></span>
                        Entry ID:
                        <b><?php echo $post_id; ?></b>
                    </div>

                </div>
            </div>

			<?php
			if ( buddyforms_core_fs()->is__premium_only() ) {
				if ( buddyforms_core_fs()->is_plan( 'professional' ) ) {
					if ( is_admin() ) {
						$user_data = get_post_meta( $post_id, '_bf_user_data', true );

						if ( $user_data ) { ?>
                            <div class="buddyforms-metabox postbox">
                                <h3 class="hndle"><span>User Information</span></h3>
                                <div class="inside">

									<?php foreach ( $user_data as $uinfo => $uval ) { ?>
                                        <div class="misc-pub-section">
											<?php echo $uinfo ?>:
                                            <b><?php echo $uval ?></b>
                                        </div>
									<?php } ?>

                                </div>
                            </div>
						<?php } ?>
					<?php }
				}
			}
			if ( buddyforms_core_fs()->is_not_paying() ) { ?>
                <div class="buddyforms-metabox postbox">
                    <h3 class="hndle"><span><?php _e( 'Get all insights about your user' ); ?></span></h3>
                    <div class="inside">
						<?php
						buddyforms_go_pro( '', __( '', 'buddyforms' ), array(
							'IP Address',
							'Referer',
							'Browser',
							'Platform',
							'Reports',
							'User Agent',
						) );
						?>
                    </div>
                </div>
			<?php } ?>
        </div>
    </div>
</div>
