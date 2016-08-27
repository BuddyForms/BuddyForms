<?php

add_action( 'admin_init', 'buddyforms_welcome_screen_do_activation_redirect' );
function buddyforms_welcome_screen_do_activation_redirect() {
// Bail if no activation redirect
if ( ! get_transient( '_buddyforms_welcome_screen_activation_redirect' ) ) {
return;
}

// Delete the redirect transient
delete_transient( '_buddyforms_welcome_screen_activation_redirect' );

// Bail if activating from network, or bulk
if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
return;
}

// Redirect to bbPress about page
wp_safe_redirect( add_query_arg( array( 'page' => 'welcome-screen-about' ), admin_url( 'index.php' ) ) );

}

add_action('admin_menu', 'buddyforms_welcome_screen_pages');

function buddyforms_welcome_screen_pages() {
add_dashboard_page(
'Welcome To Welcome Screen',
'Welcome To Welcome Screen',
'read',
'welcome-screen-about',
'buddyforms_welcome_screen_content'
);
}

function buddyforms_welcome_screen_content() {
?>
	<div id="bf_admin_wrap" class="wrap">

		<?php  include( 'bf-admin-header.php' ); ?>

		<p>Konrad ????? ;)</p>

		<div class="buddyforms_template">
			<h5>Welcome to the new BuddyForms Version 1.6</h5>
			<p>1. Getting Started Documentation mit link</p>
			<p>2. Create Form Wizard / Ad New</p>
			<p>3. Latest Blog Posts?</p>
			<p>4. Changelog?</p>
			<p>5 Whats up in this version ;)</p>
		</div>

	</div>
<?php
}

add_action( 'admin_head', 'buddyforms_welcome_screen_remove_menus' );

function buddyforms_welcome_screen_remove_menus() {
	remove_submenu_page( 'index.php', 'welcome-screen-about' );
}