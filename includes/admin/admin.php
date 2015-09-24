<?php

/**
 * Create "BuddyForms Options" nav menu
 *
 * @package buddyforms
 * @since 0.1-beta
 */
add_action('admin_menu', 'buddyforms_create_menu');
function buddyforms_create_menu(){

    if (!session_id()) ;
    @session_start();

    add_menu_page('BuddyForms', 'BuddyForms', 'manage_options', 'buddyforms_options_page', 'buddyforms_options_content');


    add_submenu_page('edit.php?post_type=buddyforms', __('Add New', 'buddyforms'), __('Add New', 'buddyforms'), 'manage_options', 'create-new-form', 'bf_import_export_screen');
    add_submenu_page('edit.php?post_type=buddyforms', __('Add-ons', 'buddyforms'), __('Add-ons', 'buddyforms'), 'manage_options', 'bf_add_ons', 'bf_add_ons_screen');
    add_submenu_page('edit.php?post_type=buddyforms', __('Mail Notification', 'buddyforms'), __('Mail Notification', 'buddyforms'), 'manage_options', 'bf_mail_notification', 'bf_mail_notification_screen');
    add_submenu_page('edit.php?post_type=buddyforms', __('Manage User Roles', 'buddyforms'), __('Manage User Roles', 'buddyforms'), 'manage_options', 'bf_manage_form_roles_and_capabilities', 'bf_manage_form_roles_and_capabilities_screen');

}

add_action('admin_head', 'buddyforms_remove_submenu_page', 999);

function buddyforms_remove_submenu_page(){
    remove_submenu_page( 'edit.php?post_type=buddyforms', 'bf_mail_notification' );
    remove_submenu_page( 'edit.php?post_type=buddyforms', 'bf_manage_form_roles_and_capabilities' );
}

/**
 * Display the settings page
 *
 * @package buddyforms
 * @since 0.2-beta
 */
function buddyforms_options_content() {
    global $buddyforms, $bf_mod5;


    echo '<pre>';
    print_r($buddyforms);
    echo '</pre>';

    $bf_mod5 = substr(md5(time() * rand()), 0, 10);

    // Check that the user is allowed to update options
    if (!current_user_can('manage_options'))
        wp_die(__('You do not have sufficient permissions to access this page.', 'buddyforms'));

    ?><div id="bf_admin_wrap" class="wrap"><?php

        include('admin-credits.php');

        if(isset($_POST['bf_delete']) && $_POST['bf_delete'] == 'Apply'){

            if ( isset( $_POST['bf_bulkactions'] ) && $_POST['bf_bulkactions'] == 'delete' && isset($_POST['bf_bulkactions_slugs']) ){

                foreach ($_POST['bf_bulkactions_slugs'] as $key => $form_slug){

                    if( isset($buddyforms[$form_slug]))
                        unset($buddyforms[$form_slug]);

                }
                $update_option = update_option("buddyforms_options", $buddyforms);

                if ($update_option)
                    echo "<div id=\"settings_updated\" class=\"updated\"> <p><strong>" . __('Settings saved', 'buddyforms') . ".</strong></p></div>";
            }
        }
        ?>
        <div id="post-body">
            <div id="post-body-content">
                <?php buddyforms_settings_page(); ?>
            </div>
        </div>

    </div>

<?php
}