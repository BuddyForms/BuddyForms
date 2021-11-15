<?php
//
// Add the Settings Page to the BuddyForms Menu
//
function buddyforms_welcome_screen_menu() {
	add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Welcome', 'buddyforms' ), __( 'Welcome', 'buddyforms' ), 'manage_options', 'buddyforms_welcome_screen', 'buddyforms_welcome_screen_content', 1 );
}

add_action( 'admin_menu', 'buddyforms_welcome_screen_menu', 9999 );

function buddyforms_welcome_screen_content() {
	?>
    <div id="bf_admin_wrap" class="wrap">

        <div class="wrap about-wrap buddyforms-welcome">
            <h1><?php _e( 'Welcome to BuddyForms', 'buddyforms' ) ?> <?php echo BUDDYFORMS_VERSION ?></h1>
        </div>
        <div class="welcome-screen-separator"></div>
        <div class="wrapper">
            <div class="bf-welcome-accordion active">
                <div class="bf-welcome-accordion_tab active">
                    Introduction
                    <div class="bf-welcome-accordion_arrow">
                        <img src="https://i.imgur.com/PJRz0Fc.png" alt="arrow">
                    </div>
                </div>
                <div class="bf-welcome-accordion_content">
                <iframe width="100%" height="315" src="https://www.youtube.com/embed/Gt8dcLZPR9A" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
            
            <div class="bf-welcome-accordion">
                <div class="bf-welcome-accordion_tab">
                    Templates
                    <div class="bf-welcome-accordion_arrow">
                        <img src="https://i.imgur.com/PJRz0Fc.png" alt="arrow">
                    </div>
                </div>
                <div class="bf-welcome-accordion_content bf-welcome-accordion-templates">
                    <?php 
                        $templates = buddyforms_form_builder_templates( $is_wizard = false );
                        echo $templates;
                    ?>
              
                </div>
            </div>

            <div class="bf-welcome-accordion">
                <div class="bf-welcome-accordion_tab">
                    Shortcodes
                    <div class="bf-welcome-accordion_arrow">
                        <img src="https://i.imgur.com/PJRz0Fc.png" alt="arrow">
                    </div>
                </div>
                <div class="bf-welcome-accordion_content">
                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">Display a Form</p>
                        <p>Use this shortcode if you wanna show a form on frontend. Don't forget to change YOUR-FORM-SLUG to your own form slug.</p>
                        <p class="bf-shortcode-doc">[bf form_slug="YOUR-FORM-SLUG"]</p>
                    </div>
                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">Display Submissions</p>
                        <p>Use this shortcode if you wannan show a list of entries belongs to a Form. Don't forget to change YOUR-FORM-SLUG to your own form slug. The attribute "list_posts_style" is optional and its possible values ​​are "table" or "list" (default). </p>
                        <p class="bf-shortcode-doc">[bf_posts_list form_slug="YOUR-FORM-SLUG" list_posts_style=""]</p>
                    </div>
                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">Link to Form</p>
                        <p>This shortcode will create a link to the form for creating or editing submissions. Don't forget to change YOUR-FORM-SLUG to your own form slug. The attribute "label" is optional (default value is "Add New").</p>
                        <p class="bf-shortcode-doc">[bf_link_to_form form_slug="YOUR-FORM-SLUG" label=""]</p>
                    </div>
                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">Link to User Posts</p>
                        <p>For logged in users you can use the following shortcode to display their submissions. Don't forget to change YOUR-FORM-SLUG to your own form slug. The attribute "label" is optional (default value is "View").</p>
                        <p class="bf-shortcode-doc">[bf_link_to_user_posts form_slug="YOUR-FORM-SLUG" label=""]</p>
                    </div>
                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">User Posts List</p>
                        <p>For logged in users you can use the following shortcode to display a the list of posts. Don't forget to change YOUR-FORM-SLUG to your own form slug.</p>
                        <p class="bf-shortcode-doc">[bf_user_posts_list form_slug="YOUR-FORM-SLUG"]</p>
                    </div>
                </div>
            </div>

            <div class="bf-welcome-accordion">
                <div class="bf-welcome-accordion_tab">
                    Gutenberg Support
                    <div class="bf-welcome-accordion_arrow">
                        <img src="https://i.imgur.com/PJRz0Fc.png" alt="arrow">
                    </div>
                </div>
                <div class="bf-welcome-accordion_content">
                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">Embed Forms</p>
                        <p>Embed any BuddyForms Form as Gutenberg Block. Just select the form you like to embed in the block sidebar.</p>
                        <img style="width:650px;" src="<?php echo BUDDYFORMS_ASSETS; ?>admin/img/welcome-screen/gutenberg-form.gif" alt="">
                    </div>

                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">List Submissions</p>
                        <p>You can list form submissions form any form and post type. Filter post lists by author or only display posts from the logged in user. Use the options in the Block sidebar.</p>
                        <img style="width:650px;" src="<?php echo BUDDYFORMS_ASSETS; ?>admin/img/welcome-screen/gutenberg-list-submissions.gif" alt="">
                    </div>

                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">Embed Navigation</p>
                        <p>Link to form endpoints or user posts lists for every post form with an attached page to create and edit submissions. You can select the attached page under the "Edit Submissions" tab in the Form Builder.</p>
                        <img style="width:650px;" src="<?php echo BUDDYFORMS_ASSETS; ?>admin/img/welcome-screen/gutenberg-add-navigation.gif" alt="">
                    </div>

                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">Login/ Logout Form</p>
                        <p>Display a login form or a logout button if the user is logged in.</p>
                        <img style="width:650px;" src="<?php echo BUDDYFORMS_ASSETS; ?>admin/img/welcome-screen/gutenberg-login-form.gif" alt="">
                    </div>
                    
                </div>
            </div>

            <div class="bf-welcome-accordion">
                <div class="bf-welcome-accordion_tab">
                    More Info
                    <div class="bf-welcome-accordion_arrow">
                        <img src="https://i.imgur.com/PJRz0Fc.png" alt="arrow">
                    </div>
                </div>
                <div class="bf-welcome-accordion_content">
                    <div class="bf-welcome-accordion_item">
                        <p class="item_title">Documentation</p>
                        <p>Our goal is to help you, that's why if you have any questions or concerns, on our website you can find all the information related to BuddyForms.</p>
                        <a class="documentation_link" href="https://docs.buddyforms.com/" target="_blank">Visit Now!</a>
                    </div>
                    
                </div>
            </div>

        </div>

    </div>
	<?php
}
