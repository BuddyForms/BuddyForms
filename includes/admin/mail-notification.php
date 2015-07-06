<?php
function bf_mail_notification_screen() { ?>

    <div class="wrap">

        <?php include('admin-credits.php');

        if (isset($_GET['form_slug'])) {

            global $buddyforms;

            $form_slug = $_GET['form_slug'];

            if (isset($_POST['buddyforms_options'])) {

                foreach ($_POST['buddyforms_options']['buddyforms'][$form_slug]['mail_notification'] as $key => $value) {
                    $buddyforms['buddyforms'][$form_slug]['mail_notification'][$key] = $value;
                }

                $update_option = update_option("buddyforms_options", $buddyforms);

                if ($update_option)
                    echo "<div id='settings_updated' class='updated'> <p><strong>" . __('Settings saved', 'buddyforms') . ".</strong></p></div>";
            }

            $form = new Form("buddyforms_notification_trigger");
            $form->configure(array(
                "prevent" => array("bootstrap", "jQuery"),
                "action" => $_SERVER['REQUEST_URI'],
                "view" => new View_Inline
            ));

            $form->addElement(new Element_HTML('
            <div id="poststuff">
                <div id="post-body" class="bf-mail-columns metabox-holder columns-2">


                    <div id="post-body-content">
                        <div class="bf-half-col bf-left" >
                            <div class="bf-col-content"> '));
                                $form->addElement(new Element_HTML('<h2>' . __(' Mail Notification Settings for "', 'buddyforms') . $buddyforms['buddyforms'][$form_slug]['name'] . '"</h2>'));
                                $form->addElement(new Element_HTML(__('Every form can have different mail notification depends on the post status change. You can create a mail notification for each individual post status. Use the select box and choose the post status you want to create mail notifications for.', 'buddyforms') . '<br>'));

                                $form->addElement(new Element_HTML('<br><br><br><div class="trigger-select">'));
                                $form->addElement(new Element_Select('<b>' . __("Create new Mail Notification", 'buddyforms') . '</b>', "buddyforms_notification_trigger", bf_get_post_status_array(), array('class' => 'buddyforms_notification_trigger', 'shortDesc' => '<a class="button-primary btn btn-primary" href="#" id="btnAdd">' . __('Create Trigger', 'buddyforms') . '</a>')));
                                $form->addElement(new Element_HTML('</div>'));

                                $form->addElement(new Element_HTML('<br>
                                <div class="help-trigger">
                                    <b>' . __( 'Post Status', 'buddyforms') . '</b>

                                    <ul>
                                        <li><b>publish</b> <small>' . __('(post or page is visible in the frontend)' , 'buddyforms') . '</small></li>
                                        <li><b>pending</b> <small>' . __('(post or page is in review process)'    , 'buddyforms') . '</small></li>
                                        <li><b>draft</b> <small>' .   __('(post or page is not visible in the frontend for public)'   , 'buddyforms') . '</small></li>
                                        <li><b>future</b> <small>' .  __('(post or page is scheduled to publish in the future)'    , 'buddyforms') . '</small></li>
                                        <li><b>private</b> <small>' . __('(not visible to users who are not logged in)'   , 'buddyforms') . '</small></li>
                                        <li><b>trash</b> <small>' .   __('(post is in trash)', 'buddyforms') . '</small></li>
                                    </ul>

                                </div>'));

                            $form->addElement(new Element_HTML('
                            </div>
                        </div>

                        <div id="postbox-container-1" class="postbox-container">
                            <div class="accordion_sidebar" id="accordion_save">
                                <div class="accordion-group postbox">
                                    <div class="accordion-heading"><h5 class="accordion-toggle"><b>' . __('Form Builder', 'buddyforms') . '</b></h5></div>
                                    <b>
                                        <div id="accordion_save" class="accordion-body">
                                            <div class="accordion-inner">
                                               <a class="button" href="' . get_admin_url() . 'admin.php?page=buddyforms_options_page#subcon' . $form_slug . '">' . __('Jump into the Form Builder', 'buddyforms') . '</a>
                                            </div>
                                        </div>
                                    </b>
                                </div>
                            </div>
                        </div>
            '));
            $form->render();

            echo '
            <div class="bf-half-col bf-right">
                <div class="bf-col-content">';

                    if (isset($buddyforms['buddyforms'][$form_slug]['mail_notification'])) {
                        echo '<ul class="nav nav-tabs" id="tabs">';
                        $index = 1;
                        foreach ($buddyforms['buddyforms'][$form_slug]['mail_notification'] as $key => $value) {

                            if ($index == 1) {
                                echo '<li class="active" ><a data-toggle="tab" href="#tab' . $index . '">' . $key . '</a></li>';
                            } else {
                                echo '<li><a data-toggle="tab" href="#tab' . $index . '">' . $key . '</a></li>';
                            }


                            $index++;
                        }
                        echo '</ul> <div class="tab-content" style="background-color: #fff; margin-top: -20px; padding: 20px;">';
                        $index = 1;
                        foreach ($buddyforms['buddyforms'][$form_slug]['mail_notification'] as $key => $value) {


                            ob_start();
                                buddyforms_new_notification_trigger_form($form_slug, $buddyforms['buddyforms'][$form_slug]['mail_notification'][$key]['mail_trigger']);
                                $trigger_form = ob_get_contents();
                            ob_clean();


                            if ($index == 1) {
                                echo '<div class="tab-pane active" id="tab' . $index . '">' . $trigger_form . '</div>';
                            } else {
                                echo '<div class="tab-pane" id="tab' . $index . '">' . $trigger_form . '</div>';
                            }


                            $index++;
                        }
                        echo '</div>';
                    } else {
                        echo '<h2>' . __('No Mail Notification found', 'buddyforms') . '</h2><div id="mailcontainer"></div>';
                    }

                } else {

                    _e('no form selected', 'buddyforms');

                } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php
}


function buddyforms_new_notification_trigger_form($form_slug, $trigger, $href = FALSE)
{
    global $buddyforms;

    session_start();
    $form = new Form("buddyforms_mail_notifications");

    if (!isset($href))
        $href = $_SERVER['REQUEST_URI'];


    $form->configure(array(
        "prevent" => array("bootstrap", "jQuery"),
        "action" => $href,
        "view" => new View_Inline
    ));

    $shortDesc = "
    <br>
    <h4>User Shortcodes</h4>
    <ul>
        <li><p><b>[user_login] </b>Username</p></li>
        <li><p><b>[user_nicename] </b>Username Sanitized</p><p><small> user_nicename is url sanitized version of user_login. In general, if you don't use any special characters in your login, then your nicename will always be the same as login. But if you enter email address in the login field during registration, then you will see the difference.
            For instance, if your login is user@example.com then you will have userexample-com nicename and it will be used in author's urls (like author's archive, post permalink, etc).
        </small></p></li>
        <li><p><b>[user_email]</b> user email</p></li>
        <li><p><b>[first_name]</b> user first name</p></li>
        <li><p><b>[last_name] </b> user last name</p></li>
    </ul>
    <h4>Published Post Shortcodes</h4>
    <ul>
        <li><p><b>[published_post_link_html]</b> the published post link in html</p></li>
        <li><p><b>[published_post_link_plain]</b> the published post link in plain</p></li>
        <li><p><b>[published_post_title]</b> the published post title</p></li>
    </ul>
    <h4>Site Shortcodes</h4>
    <ul>
        <li><p><b>[site_name]</b> the site name </p></li>
        <li><p><b>[site_url]</b> the site url</p></li>
        <li><p><b>[site_url_html]</b> the site url in html</p></li>
    </ul>
        ";


    $form->addElement(new Element_Hidden("buddyforms_options[buddyforms][" . $form_slug . "][mail_notification][" . $trigger . "][mail_trigger]", $trigger));


    $form->addElement(new Element_Textbox(__("Name", 'buddyforms'), "buddyforms_options[buddyforms][" . $form_slug . "][mail_notification][" . $trigger . "][mail_from_name]", array('value' => $buddyforms['buddyforms'][$form_slug]['mail_notification'][$trigger]['mail_from_name'], 'required' => 1, 'shortDesc' => 'the senders name')));
    $form->addElement(new Element_HTML('<br><br>'));
    $form->addElement(new Element_Email(__("Email", 'buddyforms'), "buddyforms_options[buddyforms][" . $form_slug . "][mail_notification][" . $trigger . "][mail_from]", array('value' => $buddyforms['buddyforms'][$form_slug]['mail_notification'][$trigger]['mail_from'], 'required' => 1,  'shortDesc' => 'the senders email')));
    $form->addElement(new Element_HTML('<br><br>'));

    $form->addElement(new Element_Checkbox(__('Sent mail to', 'buddyforms'), "buddyforms_options[buddyforms][" . $form_slug . "][mail_notification][" . $trigger . "][mail_to]", array('author' => 'The Post Author', 'admin' => 'Admin E-mail Address from Settings/General'), array('value' => $buddyforms['buddyforms'][$form_slug]['mail_notification'][$trigger]['mail_to'], 'inline' => 1)));
    $form->addElement(new Element_HTML('<br><br>'));
    $form->addElement(new Element_Textbox(__("Add mail to addresses separated with ','", 'buddyforms'), "buddyforms_options[buddyforms][" . $form_slug . "][mail_notification][" . $trigger . "][mail_to_address]", array("class" => "bf-mail-field", 'value' => $buddyforms['buddyforms'][$form_slug]['mail_notification'][$trigger]['mail_to_address'])));
    $form->addElement(new Element_HTML('<br><br>'));
    $form->addElement(new Element_Textbox(__("Subject", 'buddyforms'), "buddyforms_options[buddyforms][" . $form_slug . "][mail_notification][" . $trigger . "][mail_subject]", array("class" => "bf-mail-field", 'value' => $buddyforms['buddyforms'][$form_slug]['mail_notification'][$trigger]['mail_subject'], 'required' => 1)));
    $form->addElement(new Element_HTML('<br><br>'));

    ob_start();
    $settings = array('wpautop' => true, 'media_buttons' => false, 'wpautop' => true, 'tinymce' => true, 'quicktags' => true, 'textarea_rows' => 18);

    wp_editor($buddyforms['buddyforms'][$form_slug]['mail_notification'][$trigger]['mail_body'], "buddyforms_options[buddyforms][" . $form_slug . "][mail_notification][" . $trigger . "][mail_body]", $settings);

    $wp_editor = ob_get_contents();
    ob_clean();

    $wp_editor = '<div class="bf_field_group bf_form_content"><label>' . __('Content', 'buddyforms') . ':</label><div class="bf_inputs">' . $wp_editor . '</div></div>';
    $form->addElement(new Element_HTML($wp_editor));
    $form->addElement(new Element_HTML('<br><br>'));
    $form->addElement(new Element_Button());
    $form->addElement(new Element_HTML($shortDesc));
    $form->addElement(new Element_HTML('<br><br><br><br>'));


    $form->render();

}


function buddyforms_new_mail_notification()
{

    global $buddyforms;

    $form_slug = $_POST['form_slug'];
    $trigger = $_POST['trigger'];
    $href = $_POST['href'];

    if (isset($trigger, $buddyforms['buddyforms'][$form_slug]['mail_notification'][$trigger]))
        return false;

    buddyforms_new_notification_trigger_form($form_slug, $trigger, $href);
    die();
}

add_action('wp_ajax_buddyforms_new_mail_notification', 'buddyforms_new_mail_notification');
