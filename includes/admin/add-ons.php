<?php
/**
 * Created by PhpStorm.
 * User: svenl77
 * Date: 25.03.14
 * Time: 14:44
 */

/**
 * Create "BuddyForms Options" nav menu
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_create_addons_menu() {

	add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Add-ons', 'buddyforms' ), __( 'Add-ons', 'buddyforms' ), 'manage_options', 'buddyforms-addons', 'buddyforms_add_ons_screen' );
//	add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Upgrade', 'buddyforms' ), __( 'Upgrade', 'buddyforms' ), 'manage_options', 'bbuddyforms-pricing', 'buddyforms_add_ons_screen' );

}

add_action( 'admin_menu', 'buddyforms_create_addons_menu', 99999999 );

function buddyforms_add_ons_screen() {

	// Check that the user is allowed to update options
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'buddyforms' ) );
	} ?>

    <div id="bf_admin_wrap" class="wrap">
		<?php include( 'admin-header.php' ); ?>
    </div>
	<?php
}