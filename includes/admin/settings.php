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
		include( BUDDYFORMS_INCLUDES_PATH . '/admin/bf-admin-header.php' );
		?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">

				<div id="postbox-container-1" class="postbox-container">
					<?php buddyforms_settings_page_sidebar(); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php buddyforms_settings_page_tabs_content(); ?>
				</div>
			</div>
		</div>

	</div> <!-- .wrap -->
	<?php
}

//
// Settings Tabs Navigation
//
/**
 * @param string $current
 */
function buddyforms_admin_tabs( $current = 'homepage' ) {
	$tabs = array( 'general' => 'General Settings', 'import' => 'Import Forms' );

	$tabs = apply_filters( 'buddyforms_admin_tabs', $tabs );

	$links = array();

	echo '<h2 class="nav-tab-wrapper" style="padding-bottom: 0;">';
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
}

add_action( 'admin_init', 'buddyforms_register_option' );

/**
 * @param $new
 * @return mixed
 */
function buddyforms_posttypes_default_sanitize( $new ) {
	return $new;
}

function buddyforms_settings_page_tabs_content(){
	global $pagenow, $buddyforms; ?>
	<div id="poststuff">

		<?php

		// Display the Update Message
		if ( isset($_GET['updated']) && 'true' == esc_attr( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p>BuddyForms...</p></div>';
		}

		if ( isset ( $_GET['tab'] ) ) {
			buddyforms_admin_tabs( $_GET['tab'] );
		} else {
			buddyforms_admin_tabs( 'homepage' );
		}

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
				default:
					do_action( 'buddyforms_settings_page_tab', $tab );
					break;
			}
		}
		?>
	</div> <!-- #poststuff -->
<?php
}

function buddyforms_settings_page_sidebar(){
	buddyforms_go_pro('Awesome Premium Features', '', array(
		'Priority Support',
		'More Post Types',
		'More Form Elements',
		'Admin Metabox Support'
	));
}
