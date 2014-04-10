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

        <div class="credits">
            <p>
                <a class="buddyforms" href="http://buddyforms.com" title="BuddyForms" target="_blank"><img src="<?php echo plugins_url( 'img/buddyforms-s.png' , __FILE__ ); ?>" title="BuddyForms" /></a>
                - &nbsp; <?php _e( 'User Front End Posting and Form Builder for WordPress.', 'buddyforms' ); ?>

            </p>
        </div>

        <h1 style="line-height: 58px; margin-top: 20px;"><div style="font-size: 52px; margin-top: -2px; float: left; margin-right: 15px;" class="tk-icon-buddyforms"></div> BuddyForms <span class="version">1.0</span>
            <a href="<?php echo get_admin_url(); ?>admin.php?page=create-new-form" class="add-new-h2">Add New Form</a>
        </h1>

        <div id="bf_support_nav" class="button-nav">
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/en-us/categories/200022561-BuddyForms" title="BuddyForms Documentation" target="_new"><i class="icon-list-alt"></i> Documentation</a>
            <a onClick="script: Zenbox.show(); return false;" class="btn btn-small" href="#" title="Write us. Bugs. Ideas. Whatever."><i class="icon-comment"></i> Submit a support ticket</a>
            <!--            &nbsp; &nbsp;-->
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/communities/public/topics/200001402-BuddyForms-Ideas" title="Add and vote for ideas in our Ideas Forums!" target="_new"><i class="icon-plus-sign"></i> Submit your ideas</a>
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/communities/public/topics/200001402-BuddyForms-Ideas" title="Learn, share and discuss with other users in our free community forums!" target="_new"><i class="icon-circle-arrow-right"></i> Visit community forums</a>
        </div>

        <hr />

        <div id="post-body">
            <div id="post-body-content">
                <h2>BuddyForms Add-ons</h2>
                <p>Extend the BuddyForms functionality with add-ons. Find new add-ons and manage existing ones.</p>
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
                $form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'Save', 'class' => 'button-primary', 'style' => 'float: right;')));

                $form->render(); ?>
            </div>
        </div>

    </div>

<?php
}