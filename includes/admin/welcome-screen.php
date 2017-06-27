<?php
//
// Add the Settings Page to the BuddyForms Menu
//
function buddyforms_welcome_screen_menu() {
	add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Info', 'buddyforms' ), __( 'Info', 'buddyforms' ), 'manage_options', 'buddyforms_welcome_screen', 'buddyforms_welcome_screen_content' );
}

add_action( 'admin_menu', 'buddyforms_welcome_screen_menu', 9999 );

function buddyforms_welcome_screen_content() {
	?>
    <div id="bf_admin_wrap" class="wrap">

		<?php // include( 'admin-header.php' ); ?>

        <style>
            /* Welcome Page CSS */

            .about-wrap.buddyforms-welcome {
                margin-top: 40px;
            }

            .about-wrap.buddyforms-welcome .lead {
                max-width: none;
                margin: 20px 0;
            }

            .about-wrap.buddyforms-welcome .feature-section h1 {
                max-width: none;
                margin: 40px 0 20px;
                font-weight: 300;
            }

            .about-wrap.buddyforms-welcome h2 {
                max-width: none;
                margin: 40px 0 20px;
                text-align: left;
            }

            .about-wrap.buddyforms-welcome .about-text {
                min-height: 40px;
                margin-top: 20px;
                font-size: 23px;
                color: #32373c;
                margin-bottom: 30px;
                font-weight: 300;
            }

            .bfw-section {
                margin: 70px 0;
                overflow: auto;
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
                border: 1px solid rgba(0, 0, 0, 0.1);
            }

            .about-wrap.buddyforms-welcome .bfw-title {
                margin-top: 0;
                font-weight: 300;
            }
        </style>


        <div class="wrap about-wrap buddyforms-welcome">

            <h1>Welcome to BuddyForms <?php echo BUDDYFORMS_VERSION ?></h1>

            <p class="about-text">Enjoy Groundbreaking New Features!</p>

            <h2 class="nav-tab-wrapper wp-clearfix">
                <a href="about.php" class="nav-tab nav-tab-active">What’s New</a>
                <a href="edit.php?post_type=buddyforms&page=buddyforms-addons" target="_new"
                   title="Browse BuddyForms Add-ons"
                   class="nav-tab">BuddyForms Add-ons</a>
            </h2>


            <div class="feature-section two-col" style="margin: 30px 0; overflow: auto;">

                <div class="xcol col-big">
                    <h2>A new Form Designer for Beautiful Forms</h2>
                    <p class="lead">
                        Create Individual Forms to Match your Website Layout and Design!
                    </p>
                </div>

                <div class="xcol col-small">
                    <div class="imgframe">
                        <img class="nopad"
                             style="margin: 10px 0; padding: 5px; background: #fff; border: 1px solid #ddd;"
                             src="<?php echo BUDDYFORMS_PLUGIN_URL . '/assets/admin/img/welcome-screen/form-designer.png' ?>"
                             alt="BuddyForms Form Wizard Screenshot">
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
                        <img class="nopad"
                             style="width: 800px; height: auto; max-width: 100%; margin: 10px 0; padding: 5px; background: #fff; border: 1px solid #ddd;"
                             src="<?php echo BUDDYFORMS_PLUGIN_URL . '/assets/admin/img/welcome-screen/form-templates.png' ?>"
                             alt="BuddyForms Form Wizard Screenshot">
                    </div>
                </div>

            </div>


            <!-- Blogpost & Changelog -->
            <div class="bfw-section bfw-news" style="margin-top: 30px;">
                <div class="bfw-col bfw-col-50">
                    <h2 class="bfw-title">Latest Blogpost</h2>
                    <p class="lead">Read all about this new BuddyForms version Tips and Tricks:</p>
                    <a href="https://themekraft.com/buddyforms-news/" target="_new" class="button button-primary">Read
                        Blogpost</a>
                </div>
                <div class="bfw-col bfw-col-50">
                    <h2 class="bfw-title">Changelog</h2>
                    <p class="lead">Check out the changelog for version 2.0</p>
                    <a href="https://wordpress.org/plugins/buddyforms/changelog/" target="_new"
                       class="button button-primary">View Changelog</a></p>
                </div>
            </div>


            <hr style="margin: 70px 0;">


            <!-- Getting Started -->
            <div class="bfw-section bfw-getting-started">
                <div class="bfw-col bfw-col-50">
                    <div class="well">
                        <h3 class="bfw-title">First Time Here?</h3>
                        <a class="button xbutton-primary" href="http://docs.buddyforms.com/category/122-form-creation"
                           title="" target="new">Getting Started</a>
                    </div>
                </div>
                <div class="bfw-col bfw-col-50">
                    <div class="well">
                        <h3 class="bfw-title">How To Create New Forms</h3>
                        <a class="button xbutton-primary"
                           href="http://docs.buddyforms.com/article/383-creating-a-contact-form-with-the-form-wizard"
                           title="" target="new">Contact Form</a>
                        <a class="button xbutton-primary"
                           href="http://docs.buddyforms.com/article/385-creating-a-registration-form-with-the-form-wizard"
                           title="" target="new">Registration Form</a>
                        <a class="button xbutton-primary"
                           href="http://docs.buddyforms.com/article/384-creating-a-post-form-with-the-form-wizard"
                           title="" target="new">Post Form</a>
                    </div>
                </div>
            </div>


        </div>


    </div>
	<?php
}
