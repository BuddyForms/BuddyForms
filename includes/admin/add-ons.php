<?php
/**
 * Created by PhpStorm.
 * User: svenl77
 * Date: 25.03.14
 * Time: 14:44
 */

function bf_add_ons_screen(){

    // Check that the user is allowed to update options
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    } ?>

    <div id="bf_admin_wrap" class="wrap">

    <?php include('admin-credits.php'); ?>

        <div id="post-body">
            <div id="post-body-content">
                <h2><?php _e('BuddyForms Add-ons', 'buddyforms'); ?></h2>
                <p><?php _e('Extend the BuddyForms functionality with add-ons. Find new add-ons and manage existing ones.', 'buddyforms'); ?></p>
                <?php
                do_action('bf-add-ons-options');
                // Form starts
                $form = new Form("buddyforms_add_ons");
                $form->configure(array(
                    "prevent" => array("bootstrap", "jQuery"),
                    "action" => $_SERVER['REQUEST_URI'],
                    "view" => new View_Inline
                ));

                $form = apply_filters('buddyforms_general_settings', $form);

                $form->addElement(new Element_Hidden("addons-submit", "addons-submit"));
                $form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => __('Save', 'buddyforms'), 'class' => 'button-primary', 'style' => 'float: right;')));

                $form->render(); ?>
            </div>
        </div>

    </div>

<?php
}