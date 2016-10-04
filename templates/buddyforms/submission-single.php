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

			<div class="postbox-submissions postbox">
				<h3 class="hndle"><span>Entry</span></h3>
				<div class="inside">
					<script>
						jQuery(document).ready(function () {
							jQuery("#metabox_<?php echo $form_slug ?> :input").attr("disabled", true);
							jQuery('#metabox_<?php echo $form_slug ?>').prop('readonly', true);
							jQuery('#metabox_<?php echo $form_slug ?>').find('input, textarea, button, select').attr('disabled','disabled');
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
						"view"   => new View_Metabox(),
						'class'  => 'standard-form',
					) );

					$fields = $buddyforms[$form_slug]['form_fields'];


					$args = array(
						'post_type'    => $buddyforms[$form_slug]['post_type'],
						'customfields' => $fields,
						'post_id'      => $post_id,
						'form_slug'    => $form_slug,
					);

					// if the form has custom field to save as post meta data they get displayed here
					bf_form_elements( $form, $args );

					$form->render();

					?>

				</div>
			</div>
		</div>

		<div class="bf-submission-single-meta-wrap bf-row">

			<div id="bf-submissions-entry-actions" class="bf-submission-metabox bf-col-50">
				<div class="inside">
					<h3>Entry Actions</h3>
					<p><span id="timestamp-<?php echo $post_id; ?>">Submitted on: <b><?php echo get_the_date('l, F j, Y', $post_id ); ?></b></span></p>
					<p><span class="dashicons dashicons-format-aside wp-media-buttons-icon"></span><a href="#" onclick="window.print();return false;">&nbsp;Print</a></p>
				</div>
			</div>

			<div id="bf-submissions-entry-details" class="bf-submission-metabox bf-col-50">
				<div class="inside">
					<h3>Entry Details</h3>
					<p><span class="dashicons dashicons-id wp-media-buttons-icon"></span>&nbsp;Entry ID: <b><?php echo $post_id; ?></b></p>
				</div>
			</div>

		</div>

		<p class="bf-alignright"><a href="#" class="bf-close-submissions-modal button btn btn-primary" data-id="<?php the_ID() ?>"><i class=" fa fa-times-circle"></i>&nbsp;&nbsp;Close</a></p>



	</div>
</div>
