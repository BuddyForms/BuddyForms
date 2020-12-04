<?php
global $post, $buddyform, $buddyforms;

if ( $post->post_type != 'buddyforms' ) {
	return;
}

if ( ! $buddyform ) {
	$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );
}

$current_form_ID         = isset( $_REQUEST['bf_forms_selector'] ) ? $_REQUEST['bf_forms_selector'] : '';
$form_type               = isset( $buddyform['form_type'] ) ? $buddyform['form_type'] : 'contact';
$attached_page_permalink = isset( $buddyform['attached_page'] ) ? untrailingslashit(get_permalink( $buddyform['attached_page'] )) : '';
$base                    = home_url();
$preview_page_id         = get_option( 'buddyforms_preview_page', true );

$preview_btn_disabled     = empty( $post->post_name );
$preview_btn_cta          = '#';
$admin_submission_btn_cta = '#';
$front_submission_btn_cta = '#';
$front_form_btn_cta       = '#';
if ( ! $preview_btn_disabled ) {
	$preview_btn_cta          = sprintf( '%s/?page_id=%s&preview=true&form_slug=%s', $base, $preview_page_id, $post->post_name );
	$front_submission_btn_cta = sprintf( '%s/view/%s/', $attached_page_permalink, $post->post_name );
	$front_form_btn_cta       = sprintf( '%s/create/%s', $attached_page_permalink, $post->post_name );
	$admin_submission_btn_cta = sprintf( 'edit.php?post_type=buddyforms&page=buddyforms_submissions&form_slug=%s', $post->post_name );
}
?>

<div id="buddyforms_form_editor_header">
	<div class="tk-editor-header-info tk-select">
		<input type="text" name="post_title" placeholder="Add title" spellcheck="true" autocomplete="off" size="30">
		<select data-header="select" name="bf_forms_selector">
			<option value="this:form"></option>
			<?php foreach ( $buddyforms as $form_slug => $form ) : ?>
				<?php
				$form_item = get_page_by_path( $form_slug, 'OBJECT', 'buddyforms' );
				if ( empty( $form_item ) ) {
					continue;
				}
				?>
				<option value="<?php echo $form_item->ID ?>"><?php echo $form['name']; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="tk-editor-header-actions">
		<label class="tk-select">
			<span><?php _e( 'Form Type', 'buddyforms' ) ?>:</span>
			<select data-header="select" id="bf-form-type-select" style="margin-left: 0 !important;" name="buddyforms_options[form_type]">
				<optgroup label="<?php _e( 'Form Type', 'buddyforms' ) ?>">
					<option <?php selected( $form_type, 'contact' ) ?> value="contact"><?php _e( 'Contact Form', 'buddyforms' ) ?></option>
					<option <?php selected( $form_type, 'registration' ) ?> value="registration"><?php _e( 'Registration Form', 'buddyforms' ) ?></option>
					<option <?php selected( $form_type, 'post' ) ?> value="post"><?php _e( 'Post Form', 'buddyforms' ) ?></option>
				</optgroup>
			</select>
		</label>
		<?php if ( ! $preview_btn_disabled ): ?>
			<div class="tk-button tk-select">
				<a href="<?php echo esc_url( $front_form_btn_cta ); ?>" target="_blank"  class="tk-link">
					<span class="tk-icon">
						<i class="tk-fas tk-on-normal tk-fa-list-ul"></i>
						<i class="tk-fas tk-on-hover  tk-fa-external-link-square-alt"></i>
					</span>
					<span class="tk-on-normal"><?php _e( 'Frontend Form', 'buddyforms' ) ?></span>
					<span class="tk-on-hover"><?php _e( 'Open Frontend', 'buddyforms' ) ?></span>
				</a>
				<ul class="tk-menu">
					<a href="<?php echo esc_url( $front_form_btn_cta ); ?>" target="_blank"  class="tk-button is-fullwidth">
						<span class="tk-icon">
							<i class="tk-fas tk-fa-save"></i>
						</span>
						<span><?php _e( 'The Form', 'buddyforms' ) ?></span>
					</a>
					<a href="<?php echo esc_url( $front_submission_btn_cta ); ?>" target="_blank"  class="tk-button is-fullwidth">
						<span class="tk-icon">
							<i class="tk-fas tk-fa-save"></i>
						</span>
						<span><?php _e( 'Submissions', 'buddyforms' ) ?></span>
					</a>
				</ul>
			</div>
			<div class="tk-button">
				<a href="<?php echo esc_url( $admin_submission_btn_cta ); ?>" target="_blank" class="tk-link">
					<span class="tk-icon">
						<i class="tk-fas tk-on-normal tk-fa-list-ul"></i>
						<i class="tk-fas tk-on-hover  tk-fa-external-link-square-alt"></i>
					</span>
					<span><?php _e( 'Admin Submissions', 'buddyforms' ) ?></span>
				</a>
			</div>
			<div class="tk-button tk-is-info">
				<a href="<?php echo esc_url( $preview_btn_cta ); ?>" target="_blank" class="tk-link">
					<span class="tk-icon">
						<i class="tk-fas tk-on-normal tk-fa-eye"></i>
						<i class="tk-fas tk-on-hover  tk-fa-external-link-square-alt"></i>
					</span>
					<span><?php _e( 'Preview', 'buddyforms' ) ?></span>
				</a>
			</div>
		<?php endif; ?>
		<div class="tk-button tk-is-danger">
			<span class="tk-icon">
				<i class="tk-fas tk-fa-trash-alt"></i>
			</span>
		</div>
		<div class="tk-button tk-is-primary">
			<span class="tk-icon">
				<i class="tk-fas tk-fa-save"></i>
			</span>
			<span><?php _e( 'Update', 'buddyforms' ) ?></span>
		</div>
	</div>
</div>
