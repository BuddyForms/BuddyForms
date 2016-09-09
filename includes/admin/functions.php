<?php

function custom_menu_page_removing() {

	remove_submenu_page( 'edit.php', 'edit.php?post_type=buddyforms&page=buddyforms-contact' );
	remove_menu_page('edit.php?post_type=buddyforms&page=buddyforms_contact_us');
}
add_action( 'admin_menu', 'custom_menu_page_removing', 999999 );

?>