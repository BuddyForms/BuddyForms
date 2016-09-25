<?php

function buddyforms_form_setup_nav_li_registration(){ ?>
	<li class="registrations_nav"><a
		href="#registration"
		data-toggle="tab"><?php _e( 'Registration', 'buddyforms' ); ?></a>
	</li><?php
}
add_action('buddyforms_form_setup_nav_li_last', 'buddyforms_form_setup_nav_li_registration', 50);

function buddyforms_form_setup_tab_pane_registration(){ ?>
	<div class="tab-pane fade in" id="registration">
		<div class="buddyforms_accordion_registration">
			<?php  buddyforms_registration_screen() ?>
		</div>
	</div><?php
}
add_action('buddyforms_form_setup_tab_pane_last', 'buddyforms_form_setup_tab_pane_registration');

function buddyforms_registration_screen(){
	global $post, $buddyform;

	$form_slug = $post->post_name;

	$form_setup = array();

	if(!$buddyform){
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}

	echo '<h4>' . __('Registration Options', 'buddyforms') . '</h4><br>';

	$generate_password = '';
	if ( isset( $buddyform['registration']['generate_password'] ) ) {
		$generate_password = $buddyform['registration']['generate_password'];
	}
	$form_setup[] = new Element_Checkbox( '<b>' . __( 'Generate Password', 'buddyforms' ) . '</b>', "buddyforms_options[registration][generate_password]", array( 'yes' => __( 'Auto generate the password.', 'buddyforms' ) ), array( 'value' => $generate_password, 'shortDesc' => 'If generate password is enabled the password field is not required and can be removed from the form. How ever if the password field exist and a passowrd was entered the password from the password field is used instad of the auto generated password.' ) );

	// Get all Pages
	$pages = get_pages( array(
		'sort_order'  => 'asc',
		'sort_column' => 'post_title',
		'parent'      => 0,
		'post_type'   => 'page',
		'post_status' => 'publish'
	) );

	// Generate teh Pages Array
	$all_pages = Array();
	$all_pages['none'] = 'Select a Page';
	foreach ( $pages as $page ) {
		$all_pages[ $page->ID ] = $page->post_title;
	}

	$activation_page = isset($buddyform['registration']['activation_page']) ? $buddyform['registration']['activation_page'] : 'none';

	// Activation Page
	$form_setup[] = new Element_Select( '<b>' . __( "Activation Page", 'buddyforms' ) . '</b>', "buddyforms_options[registration][activation_page]", $all_pages, array(
		'value'     => $activation_page,
		'shortDesc' => __('Select the Page from where the user lands if he press the activation link in the activation mail. You can create a personal page or add a other form.', 'buddyforms'),
		'class'     => '',
	) );

	$auto_loggin = '';
	if ( isset( $buddyform['registration']['auto_loggin'] ) ) {
		$generate_password = $buddyform['registration']['auto_loggin'];
	}
	$form_setup[] = new Element_Checkbox( '<b>' . __( 'auto_loggin', 'buddyforms' ) . '</b>', "buddyforms_options[registration][auto_loggin]", array( 'yes' => __( 'Auto loggin the user.', 'buddyforms' ) ), array( 'value' => $generate_password, 'shortDesc' => 'Make sure you have the recharter form element...' ) );

	?>
	<div class="fields_header">
		<table class="wp-list-table widefat posts striped">
			<tbody id="the-list">
			<?php
			if ( isset( $form_setup ) ) {
				foreach ( $form_setup as $key => $field ) { ?>
					<tr id="row_form_title">
						<th scope="row">
							<label for="role_role"><?php echo $field->getLabel() ?></label>
						</th>
						<td>
							<?php echo $field->render() ?>
							<p class="description"><?php echo $field->getShortDesc() ?></p>
						</td>
					</tr>
					<?php
				}
			}
			?>
			</tbody>
		</table>
	</div>

	<?php


}