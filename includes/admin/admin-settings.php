<?php

//
// Add the Settings Page to the BuddyForms Menu
//
function buddyforms_settings_menu() {

	add_submenu_page( 'edit.php?post_type=buddyforms', __( 'BuddyForms Settings', 'buddyforms' ), __( 'Settings', 'buddyforms' ), 'manage_options', 'buddyforms_settings', 'buddyforms_settings_page' );

}
add_action( 'admin_menu', 'buddyforms_settings_menu' );

//
// Settings Page Content
//
function buddyforms_settings_page() {
	global $pagenow, $buddyforms; ?>

	<div class="wrap">

		<?php

		// Display the BuddyForms Header
		include( BUDDYFORMS_INCLUDES_PATH . '/admin/admin-credits.php' );

		// Display the Update Message
		if ( 'true' == esc_attr( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p>BuddyForms...</p></div>';
		}

		if ( isset ( $_GET['tab'] ) ) {
			buddyforms_admin_tabs( $_GET['tab'] );
		} else {
			buddyforms_admin_tabs( 'homepage' );
		}
		?>

		<div id="poststuff">

			<?php

			if ( $pagenow == 'edit.php' && $_GET['page'] == 'buddyforms_settings' ) {

				if ( isset ( $_GET['tab'] ) ) {
					$tab = $_GET['tab'];
				} else {
					$tab = 'general';
				}

				switch ( $tab ) {
					case 'general' :
						$buddyforms_posttypes_default = get_option( 'buddyforms_posttypes_default' ); ?>
						<div class="metabox-holder">
							<div class="postbox">
								<h3><span><?php _e( 'Post Types Default Form', 'buddyforms' ); ?></span></h3>
								<div class="inside">
								<p>Select a default form for every post type.</p>

								<p>This will make sure that posts created before BuddyForms will have a form associated. <br>
									If you select none the post edit link will point to the admin for posts not created with
									BuddyForms</p>

								<form method="post" action="options.php">

									<?php settings_fields( 'buddyforms_posttypes_default' ); ?>

									<table class="form-table">
										<tbody>
										<?php
										if ( isset( $buddyforms ) && is_array( $buddyforms ) ) {
											$post_types_forms = Array();
											foreach ( $buddyforms as $key => $buddyform ) {

												if(isset($buddyform['post_type']) && $buddyform['post_type'] != 'bf_submissions' && post_type_exists($buddyform['post_type'])){
													$post_types_forms[ $buddyform['post_type'] ][ $key ] = $buddyform;
												}

											}

											foreach ( $post_types_forms as $post_type => $post_types_form ) : ?>
												<tr valign="top">
													<th scope="row" valign="top">
														<?php
														$post_type_object = get_post_type_object( $post_type );
														echo $post_type_object->labels->name; ?>
													</th>
													<td>
														<select name="buddyforms_posttypes_default[<?php echo $post_type ?>]"
														        class="regular-radio">
															<option value="none">None</option>
															<?php foreach ( $post_types_form as $form_key => $form ) {

																$default = '';
																if(isset($buddyforms_posttypes_default[ $post_type ])){
																	$default = $buddyforms_posttypes_default[ $post_type ];
																}
																?>
																<option <?php echo selected( $default, $form_key, true ) ?>
																	value="<?php echo $form_key ?>"><?php echo $form['name'] ?></option>
															<?php } ?>
														</select>
													</td>
												</tr>
											<?php endforeach;
										} else {
											echo '<h3>You need to create at least one form to select a post type default.</h3>';
										} ?>
										</tbody>
									</table>
									<?php submit_button(); ?>

								</form>
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .metabox-holder -->
						<?php
						break;
					case 'import' : ?>
							<div class="metabox-holder">
								<div class="postbox">
									<h3><span><?php _e( 'Import Forms', 'buddyforms' ); ?></span></h3>
									<div class="inside">
										<p><?php _e( 'Import the form from a .json file. This file can be obtained by exporting the form from the list view.' ); ?></p>
										<form method="post" enctype="multipart/form-data">
											<p>
												<input type="file" name="import_file"/>
											</p>
											<p>
												<input type="hidden" name="buddyforms_action" value="import_settings" />
												<?php wp_nonce_field( 'buddyforms_import_nonce', 'buddyforms_import_nonce' ); ?>
												<?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
											</p>
										</form>
									</div><!-- .inside -->
								</div><!-- .postbox -->
							</div><!-- .metabox-holder -->
						<?php
						break;
					case 'recaptcha' :
						$recaptcha = get_option( 'buddyforms_recaptcha' ); ?>

						<div class="metabox-holder">
							<div class="postbox">
								<h3><span><?php _e( 'Google reCaptcha Options', 'buddyforms' ); ?></span></h3>
								<div class="inside">
									<form method="post" action="options.php">
										<?php settings_fields("header_section");
										do_settings_sections("recaptcha-options");
										submit_button(); ?>
									</form>
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .metabox-holder -->
						<?php
						break;
					default:
						do_action( 'buddyforms_settings_page_tab', $tab );
						break;
				}
			}
			?>

		</div> <!-- #poststuff -->

	</div> <!-- .wrap -->

	<?php
}

//
// Settings Tabs Navigation
//
function buddyforms_admin_tabs( $current = 'homepage' ) {
	$tabs = array( 'general' => 'General Settings', 'recaptcha' => 'reCaptcha', 'import' => 'Import Forms' );

	$tabs = apply_filters( 'buddyforms_admin_tabs', $tabs );

	$links = array();

	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab$class' href='edit.php?post_type=buddyforms&page=buddyforms_settings&tab=$tab'>$name</a>";

	}
	echo '</h2>';
}

//
// Register Settings Options
//
function buddyforms_register_option() {
	register_setting( 'buddyforms_posttypes_default', 'buddyforms_posttypes_default', 'buddyforms_posttypes_default_sanitize' );
	register_setting( 'buddyforms_edd_license', 'buddyforms_edd_license_key', 'buddyforms_edd_sanitize_license' );
	register_setting( 'buddyforms_google_recaptcha', 'buddyforms_google_recaptcha' );
}

add_action( 'admin_init', 'buddyforms_register_option' );

function buddyforms_posttypes_default_sanitize( $new ) {
	return $new;
}

function buddyforms_edd_sanitize_license( $new ) {
	$old = get_option( 'buddyforms_edd_license_key' );
	if ( $old && $old != $new ) {
		delete_option( 'buddyforms_edd_license_status' ); // new license has been entered, so must reactivate
	}

	return $new;
}




//
// EDD Settings Start
//




/************************************
 * this illustrates how to activate
 * a license key
 *************************************/

function buddyforms_edd_activate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_license_activate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'buddyforms_edd_nonce', 'buddyforms_edd_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'buddyforms_edd_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( BUDDYFORMS_EDD_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( BUDDYFORMS_STORE_URL, array( 'timeout'   => 15,
		                                                         'sslverify' => false,
		                                                         'body'      => $api_params
		) );


		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'buddyforms_edd_license_status', $license_data->license );

	}
}

add_action( 'admin_init', 'buddyforms_edd_activate_license' );

//
// GOOGLE reCaptcha
//

function buddyforms_display_recaptcha_options() {
	add_settings_section("header_section", "", "buddyforms_display_recaptcha_content", "recaptcha-options");

	add_settings_field("captcha_site_key", __("Site Key"), "buddyforms_display_captcha_site_key_element", "recaptcha-options", "header_section");
	add_settings_field("captcha_secret_key", __("Secret Key"), "buddyforms_display_captcha_secret_key_element", "recaptcha-options", "header_section");

	register_setting("header_section", "captcha_site_key");
	register_setting("header_section", "captcha_secret_key");
}

function buddyforms_display_recaptcha_content() {
	echo __('<p>You need to <a target="_blank" href="https://www.google.com/recaptcha/admin" rel="external">register you domain</a> and get keys to make this plugin work.</p>');
	echo __("Enter the key details below");
}

function buddyforms_display_captcha_site_key_element() { ?>
	<input type="text" name="captcha_site_key" id="captcha_site_key" value="<?php echo get_option('captcha_site_key'); ?>" />
<?php }

function buddyforms_display_captcha_secret_key_element() { ?>
	<input type="text" name="captcha_secret_key" id="captcha_secret_key" value="<?php echo get_option('captcha_secret_key'); ?>" />
<?php }
add_action("admin_init", "buddyforms_display_recaptcha_options");


//
// Import form from a json file
//
function buddyforms_process_settings_import() {
	if( empty( $_POST['buddyforms_action'] ) || 'import_settings' != $_POST['buddyforms_action'] )
		return;
	if( ! wp_verify_nonce( $_POST['buddyforms_import_nonce'], 'buddyforms_import_nonce' ) )
		return;
	if( ! current_user_can( 'manage_options' ) )
		return;
	$extension = end( explode( '.', $_FILES['import_file']['name'] ) );
	if( $extension != 'json' ) {
		wp_die( __( 'Please upload a valid .json file' ) );
	}
	$import_file = $_FILES['import_file']['tmp_name'];
	if( empty( $import_file ) ) {
		wp_die( __( 'Please upload a file to import' ) );
	}
	// Retrieve the settings from the file and convert the json object to an array.
	$buddyform = json_decode( file_get_contents( $import_file ), true );


	// Create post object
	$new_form = array(
		'post_title'    => $buddyform['name'],
		'post_status'   => 'publish',
		'post_type'     => 'buddyforms'
	);

	// Insert the post into the database
	$new_form_id = wp_insert_post( $new_form );

	// Update post meta
	update_post_meta( $new_form_id, '_buddyforms_options', $buddyform );

	// Regenerate the global $buddyforms.
	// The global$buddyforms is sored in the option table and provides all fors and form fields
	buddyforms_regenerate_global_options();

	// Rewrite the page roles and flash permalink if needed
	buddyforms_attached_page_rewrite_rules( true );

	wp_safe_redirect( admin_url( 'post.php?post='.$new_form_id.'&action=edit' ) ); exit;
}
add_action( 'admin_init', 'buddyforms_process_settings_import' );
?>