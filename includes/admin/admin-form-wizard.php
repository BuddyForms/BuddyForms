<?php

//
// Create a list of all available form builder templates
//
function buddyforms_form_builder_wizard_types(){

	$buddyforms_wizard_types['contact']['title'] = 'Contact Form';
	$buddyforms_wizard_types['contact']['desc'] = 'Setup a simple contact form.';

	$buddyforms_wizard_types['registration']['title'] = 'Registration Form';
	$buddyforms_wizard_types['registration']['desc'] = 'Setup a simple registration form.';

	$buddyforms_wizard_types['post']['title'] = 'Post Form';
	$buddyforms_wizard_types['post']['desc'] = 'Setup a simple post form.';

	$buddyforms_wizard_types = apply_filters('buddyforms_wizard_types', $buddyforms_wizard_types);

	ob_start();

	?>
	<div class="buddyforms_template">
		<h5>Choose a Form Type</h5>

		<?php foreach($buddyforms_wizard_types as $key => $template) { ?>
			<div class="bf-3-tile">
				<h4 class="bf-tile-title"><?php echo $template['title'] ?></h4>
				<p class="bf-tile-desc"><?php echo $template['desc'] ?></p>
				<button id="btn-compile-<?php echo $key ?>" data-template="<?php echo $key ?>" class="bf_form_template btn" onclick=""><span class="bf-plus">+</span> <?php echo $template['title'] ?></button>
			</div>
		<?php } ?>

	</div>

	<?php

	$tmp = ob_get_clean();

	echo $tmp;
	die();
}
add_action( 'wp_ajax_buddyforms_form_builder_wizard_types', 'buddyforms_form_builder_wizard_types' );