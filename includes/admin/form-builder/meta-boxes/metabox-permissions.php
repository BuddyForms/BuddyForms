<?php
function buddyforms_permissions_unregistered_screen() {
	global $post, $buddyform;

	$form_slug = $post->post_name;

	$form_setup = array();

	if(!$buddyform){
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}

	echo '<h4>Unregistered User</h4><br>';

	$public_submit = '';
	if ( isset( $buddyform['public_submit'] ) ) {
		$public_submit = $buddyform['public_submit'];
	}
	$form_setup[] = new Element_Checkbox( '<b>' . __( 'Public Submittable', 'buddyforms' ) . '</b>', "buddyforms_options[public_submit]", array( 'public_submit' => __( 'This Form is accessible for unregistered users', 'buddyforms' ) ), array( 'value' => $public_submit, 'shortDesc' => 'Please make sure you use the reCAPTCHA form element if this option is enabled.' ) );

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

function buddyforms_permissions_screen() {
	global $post, $buddyform;

	$form_slug = $post->post_name;

	$form_setup = array();

	if(!$buddyform){
		$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );
	}

	echo '<br><br><h4>Logged in User</h4><br>';

	echo '<div class="bf-roles-main-desc">
			<div class="">
			<p>' . __( 'Control who can create, edit and delete content that is created from this form for each user role. If you want to create additional custom user roles, we recommend the Members plugin.', 'buddyforms' ) . '</p>
			<p><b>' . __( 'Check/Uncheck capabilities to allow/disallow users to create, edit and/or delete posts of this form', 'buddyforms' ) . '</b></p><p><a href="#" class="bf_check_all">' . __( 'Check all', 'buddyforms' ) . '</a></p></div></div>';


//		$form_setup[] = new Element_HTML();


		foreach ( get_editable_roles() as $role_name => $role_info ):

			$default_roles[ 'buddyforms_' . $form_slug . '_create' ] = '';
			$default_roles[ 'buddyforms_' . $form_slug . '_edit' ]   = '';
			$default_roles[ 'buddyforms_' . $form_slug . '_delete' ] = '';

			$form_user_role = array();

			foreach ( $role_info['capabilities'] as $capability => $_ ):

				$capability_array = explode( '_', $capability );

				if ( $capability_array[0] == 'buddyforms' ) {

					if ( $capability_array[1] == $form_slug ) {

						$form_user_role[ $capability ] = $capability;
						$default_roles[ $capability ]  = '';

					}
				}


			endforeach;


			if($role_name == 'administrator'){

				foreach($default_roles as $role_n_a => $role_a)
					$form_user_role[$role_n_a] = $role_n_a;


				$form_setup[] = new Element_Checkbox( '<b>' . $role_name . '</b>', 'buddyforms_roles[' . $form_slug . '][' . $role_name . ']', $default_roles, array(
					'value'     => $form_user_role,
					'inline'    => true,
					'style'     => 'margin-right: 60px;',
					'shortDesc' => 'Admin rights can not get changed'
				) );

			} else {
				$form_setup[] = new Element_Checkbox( '<b>' . $role_name . '</b>', 'buddyforms_roles[' . $form_slug . '][' . $role_name . ']', $default_roles, array(
					'value'  => $form_user_role,
					'inline' => true,
					'style'  => 'margin-right: 60px;'
				) );

			}

		endforeach;
			?>
			<div class="fields_header">
				<table class="wp-list-table widefat posts striped bf_permissions">
					<thead>
						<tr>
							<th class="field_label">Role</th>
							<th class="field_name">Create - Edit - Delete</th>
						</tr>
					</thead>
					<tbody id="the-list">
					<?php
					if ( isset( $form_setup ) ) {
						foreach ( $form_setup as $key => $field ) {

							$type  = $field->getAttribute( 'type' );

							if($type == 'html'){
								echo '<tr id="table_row_' . $field_id . '_' . $key . '" class="' . $class . '"><td colspan="2">';
								$field->render();
								echo '</td></tr>';
							} else { ?>
								<tr id="row_form_title">
									<th scope="row">
										<label for="role_role"><?php echo $field->getLabel() ?></label>
									</th>
									<td>
										<?php echo $field->render() ?>
										<p class="description"><?php echo $field->getShortDesc() ?></p>
									</td>
								</tr>
							<?php }
						}
					}
					?>
					</tbody>
				</table>
			</div>
			<?php

}

function buddyforms_form_setup_nav_li_permission(){ ?>
	<li><a class="permission"
		href="#permission"
		data-toggle="tab"><?php _e( 'Permission', 'buddyforms' ); ?></a>
	</li><?php
}
add_action('buddyforms_form_setup_nav_li_last', 'buddyforms_form_setup_nav_li_permission');

function buddyforms_form_setup_tab_pane_permission(){ ?>
	<div class="tab-pane fade in" id="permission">
		<div class="buddyforms_accordion_permission">
			<?php buddyforms_permissions_unregistered_screen() ?>
			<?php buddyforms_permissions_screen() ?>
		</div>
	</div><?php
}
add_action('buddyforms_form_setup_tab_pane_last', 'buddyforms_form_setup_tab_pane_permission');