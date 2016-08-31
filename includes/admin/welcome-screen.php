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

		<?php // include( 'bf-admin-header.php' ); ?>

		<style>
			/* Welcome Page CSS */

			.about-wrap.buddyforms-welcome .feature-section .lead {
			    max-width: none;
			    margin: 25px 0;
			}
			.about-wrap.buddyforms-welcome .feature-section h1 {
			    max-width: none;
			    margin: 40px 0 25px;
			}


		</style>


		<div class="wrap about-wrap buddyforms-welcome">

				<h1>Welcome to BuddyForms&nbsp;1.6</h1>

				<p class="about-text">Great New Features Are Waiting For You!</p>
				<!-- <div class="wp-badge">Version 1.6</div> -->

				<h2 class="nav-tab-wrapper wp-clearfix">
					<a href="about.php" class="nav-tab nav-tab-active">What’s New</a>
					<a href="https://themekraft.com/buddyforms/#extensions" target="_new" title="Browse BuddyForms Add-ons" class="nav-tab">BuddyForms Add-ons</a>
				</h2>


				<div class="feature-section two-col" style="margin: 30px 0; overflow: auto;">

					<div class="xcol col-big">
						<h1>A Revolutionary Form Wizard</h1>
						<p class="lead">
						<b>Never feel lost again.</b> Setting up your custom forms will be a breeze.
						</p>
					</div>

					<div class="xcol col-small">
						<div class="imgframe">
							<img class="nopad" style="margin: 10px 0; padding: 5px; background: #fff; border: 1px solid #ddd;" src="https://1l1jrk1lr1oc1721v72mdabi-wpengine.netdna-ssl.com/wp-content/themes/themekraft-2017/includes/img/buddyforms-form-wizard-screenshot-small.jpg" alt="BuddyForms Form Wizard Screenshot">
						</div>
					</div>

					<hr>

					<div class="buddyforms_template">
						<h5>Welcome to the new BuddyForms Version 1.6</h5>
						<p>1. Getting Started Documentation mit link</p>
						<p>2. Create Form Wizard / Ad New</p>
						<p>3. Latest Blog Posts?</p>
						<p>4. Changelog?</p>
						<p>5 Whats up in this version ;)</p>
					</div>


				</div>

		</div>



	</div>
<?php
}

add_action( 'admin_head', 'buddyforms_welcome_screen_remove_menus' );

function buddyforms_welcome_screen_remove_menus() {
	remove_submenu_page( 'index.php', 'welcome-screen-about' );
}
