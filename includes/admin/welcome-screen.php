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

			.about-wrap.buddyforms-welcome .lead {
			    max-width: none;
			    margin: 20px 0;
			}
			.about-wrap.buddyforms-welcome .feature-section h1 {
			    max-width: none;
			    margin: 40px 0 20px;
			}
			.about-wrap.buddyforms-welcome h2 {
				max-width: none;
				margin: 40px 0 20px;
				text-align: left;
			}
			.about-wrap.buddyforms-welcome .about-text {
		    min-height: 40px;
			}
			.bfw-row {
				overflow: auto;
				clear: both;
			}
			.bfwell {
				margin: 40px 0 0 0;
				background: #e5e5e5;
				overflow: auto;
				border: 1px solid #ccc;
				padding: 20px 10px;
			}
			.bfw-col {
				display: block;
				float: left;
				width: 100%;
				overflow: auto;
				padding: 10px;
				box-sizing: border-box;
			}
			.bfw-col-40 {
				width: 40%;
			}
			.bfw-col-50 {
				width: 50%;
			}
			.bfw-col-60 {
				width: 60%;
			}
			.bfw-well {
				padding: 20px;
				background: #fafafa;
				border: 1px solid rgba(0,0,0,0.1);
			}
			.about-wrap .bfw-title {
				margin-top: 0;
			}
		</style>


		<div class="wrap about-wrap buddyforms-welcome">

				<h1>Welcome to BuddyForms&nbsp;1.6</h1>

				<p class="about-text">Great New Features Are Waiting For You!</p>
				<!-- <div class="wp-badge">Version 1.6</div> -->

				<div class="bfw-row bfwell">
					<div class="bfw-col bfw-col-40">
						<div class="well">
							<h3 class="bfw-title">Your First Time?</h3>
							<a class="button button-primary" href="#" title="" target="new">Getting Started</a>
						</div>
					</div>
					<div class="bfw-col bfw-col-60">
						<div class="well">
							<h3 class="bfw-title">How To Create New Forms</h3>
							<a class="button xbutton-primary" href="#" title="" target="new">Contact Form</a>
							<a class="button xbutton-primary" href="#" title="" target="new">Registration Form</a>
							<a class="button xbutton-primary" href="#" title="" target="new">Post Form</a>
						</div>
					</div>
				</div>

				<br />

				<h2 class="nav-tab-wrapper wp-clearfix">
					<a href="about.php" class="nav-tab nav-tab-active">What’s New</a>
					<a href="https://themekraft.com/buddyforms/#extensions" target="_new" title="Browse BuddyForms Add-ons" class="nav-tab">BuddyForms Add-ons</a>
				</h2>


				<div class="feature-section two-col" style="margin: 30px 0; overflow: auto;">

					<div class="xcol col-big">
						<h2>A Revolutionary Form Wizard</h2>
						<p class="lead">
						<b>Never feel lost again.</b> Setting up your custom forms will be a breeze.
						</p>
					</div>

					<div class="xcol col-small">
						<div class="imgframe">
							<img class="nopad" style="margin: 10px 0; padding: 5px; background: #fff; border: 1px solid #ddd;" src="https://1l1jrk1lr1oc1721v72mdabi-wpengine.netdna-ssl.com/wp-content/themes/themekraft-2017/includes/img/buddyforms-form-wizard-screenshot-small.jpg" alt="BuddyForms Form Wizard Screenshot">
						</div>
					</div>

				</div>

				<hr>


				<div class="feature-section two-col" style="margin: 30px 0; overflow: auto;">

					<div class="xcol col-big">
						<h2>All Form Types</h2>
						<p class="lead">
						Contact Forms. Signup Forms. Post Forms.
						</p>
					</div>

					<div class="xcol col-small">
						<div class="imgframe">
							<img class="nopad" style="width: 800px; height: auto; max-width: 100%; margin: 10px 0; padding: 5px; background: #fff; border: 1px solid #ddd;" src="https://1l1jrk1lr1oc1721v72mdabi-wpengine.netdna-ssl.com/wp-content/themes/themekraft-2017/includes/img/buddyforms-formbuilder-screenshot-01.jpg" alt="BuddyForms Form Wizard Screenshot">
						</div>
					</div>

				</div>

				<hr>

				<div style="margin: 30px 0; overflow: auto;">

					<h2>Latest Blogpost</h2>
					<p class="lead">Read all about this new BuddyForms version:</p>
					<p style="lead"><a href="" target="_new" class="button button-primary">Read Blogpost</a></p>

				</div>


		</div>



	</div>
<?php
}

add_action( 'admin_head', 'buddyforms_welcome_screen_remove_menus' );

function buddyforms_welcome_screen_remove_menus() {
	remove_submenu_page( 'index.php', 'welcome-screen-about' );
}
