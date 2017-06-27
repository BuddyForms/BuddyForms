<?php
/**
 * Submission Single View
 *
 * This template can be overridden by copying it to yourtheme/buddyforms/submission-single.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
?>

<script>
    jQuery(document).ready(function () {
        jQuery(".bf_submit_form<?php echo $post_id; ?> :input").attr("disabled", true);
        jQuery(".bf_submit_form<?php echo $post_id; ?>").show();
        jQuery(".bf_submit_form<?php echo $post_id; ?> span.bf_add_files").remove();
    });
</script>

<div id="bf-submission-<?php echo $post_id; ?>" class="bf-submission-single bf_submit_form<?php echo $post_id; ?>">
    <div class="inner-wrap">
        <p><a href="#" class="bf-close-submissions-modal button btn btn-primary" data-id="<?php the_ID() ?>"><i
                        class="dashicons dashicons-arrow-left-alt2"></i>&nbsp;Back</a></p>

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

    <div class="bf-submission-single-meta-wrap bf-row">

        <div id="bf-submissions-entry-actions" class="bf-submission-metabox bf-col-50">
            <div class="inner-wrap">
                <h3>Entry Actions</h3>
                <p><span
                            id="timestamp-<?php echo $post_id; ?>">Submitted on: <b><?php echo get_the_date( 'l, F j, Y', $post_id ); ?></b></span>
                </p>
                <p><span class="dashicons dashicons-format-aside wp-media-buttons-icon"></span><a href="#"
                                                                                                  onclick="window.print();return false;">
                        &nbsp;Print</a></p>
            </div>
        </div>

        <div id="bf-submissions-entry-details" class="bf-submission-metabox bf-col-50">
            <div class="inner-wrap">
                <h3>Entry Details</h3>
                <p><span class="dashicons dashicons-id wp-media-buttons-icon"></span>&nbsp;Entry ID:
                    <b><?php echo $post_id; ?></b></p>
            </div>
        </div>

    </div>

    <p><a href="#" class="bf-close-submissions-modal button btn btn-primary" data-id="<?php the_ID() ?>"><i
                    class="dashicons dashicons-arrow-left-alt2"></i>&nbsp;Back To All Submissions</a></p>

</div>
