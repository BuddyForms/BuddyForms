<?php
function bf_mail_notification_screen() { ?>

    <div class="wrap">

        <?php include('admin-credits.php');

        if (isset($_GET['form_slug'])) {

            global $buddyforms;

            $form_slug = $_GET['form_slug'];

            if (isset($_POST['buddyforms_options'])) {

                foreach ($_POST['buddyforms_options']['mail_notification'][$form_slug] as $key => $value) {
                    $buddyforms['mail_notification'][$form_slug][$key] = $value;
                }

                $update_option = update_option("buddyforms_options", $buddyforms);

                if ($update_option)
                    echo "<div id=\"settings_updated\" class=\"updated\"> <p><strong>" . __('Settings saved', 'buddyforms') . ".</strong></p></div>";
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
                                $form->addElement(new Element_HTML('Every form can have different mail notification depends on the post status change. You can create mail notification for each individual post status. Use the select box and shuse the post status you want to ceata mail notification trigger for.<br><br>'));
                                $form->addElement(new Element_Select(__("Create new Notification Trigger for", 'buddyforms'), "buddyforms_notification_trigger", bf_get_post_status_array(), array('class' => 'buddyforms_notification_trigger', 'shortDesc' => '<a class="button-primary btn btn-primary" href="#" id="btnAdd"> Create Trigger</a>')));
                                $form->addElement(new Element_HTML('<br>
                                <div class="help-trigger">
                                    <p><h4>The available post statuses are:</h4></p>
                                    <small>
                                    <ul>
                                        </li>publish – A published post or page.<li>
                                        </li>pending – A post pending review.<li>
                                        </li>draft – A post in draft status.<li>
                                        </li>future – A post scheduled to publish in the future.<li>
                                        </li>private – Not visible to users who are not logged in.<li>
                                        </li>trash – Post is in the trash.<li>
                                    </ul>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="postbox-container-1" class="postbox-container">
                        <div class="accordion_sidebar" id="accordion_save">
                            <div class="accordion-group postbox">
                                <div class="accordion-heading"><h5 class="accordion-toggle"><b>Form Builder</b></h5></div>
                                <b>
                                    <div id="accordion_save" class="accordion-body">
                                        <div class="accordion-inner">
                                           <a class="button-primary" href="' . get_admin_url() . 'admin.php?page=buddyforms_options_page#subcon' . $form_slug . '">Jump into the Form Builder</a>
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

                    if (isset($buddyforms['mail_notification'][$form_slug])) {
                        echo '<ul class="nav nav-tabs" id="tabs">';
                        $index = 1;
                        foreach ($buddyforms['mail_notification'][$form_slug] as $key => $value) {

                            if ($index == 1) {
                                echo '<li class="active" ><a data-toggle="tab" href="#tab' . $index . '">' . $key . '</a></li>';
                            } else {
                                echo '<li><a data-toggle="tab" href="#tab' . $index . '">' . $key . '</a></li>';
                            }


                            $index++;
                        }
                        echo '</ul> <div class="tab-content" style="background-color: #fff; margin-top: -20px; padding: 20px;">';
                        $index = 1;
                        foreach ($buddyforms['mail_notification'][$form_slug] as $key => $value) {


                            ob_start();
                            buddyforms_new_notification_trigger_form($form_slug, $buddyforms['mail_notification'][$form_slug][$key]['mail_trigger']);
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
                        echo '<h2>No Mail Notification Trigger Created so far</h2><div id="mailcontainer"></div>';
                    }

                } else {

                    echo 'no form selected';

                } ?>
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
        <li><p><b>[user_email]</b> User email</p></li>
        <li><p><b>[first_name]</b> User first name</p></li>
        <li><p><b>[last_name] </b> User last name</p></li>
    </ul>
    <h4>Published Post Shortcodes</h4>
    <ul>
        <li><p><b>[published_post_link_html]</b> The published post link in html</p></li>
        <li><p><b>[published_post_link_plain]</b> The published post link in plain</p></li>
        <li><p><b>[published_post_title]</b> The published post title</p></li>
    </ul>
    <h4>Site Shortcodes</h4>
    <ul>
        <li><p><b>[site_name]</b> The sitename </p></li>
        <li><p><b>[site_url]</b> The site url</p></li>
        <li><p><b>[site_url_html]</b> The site url in html</p></li>
    </ul>
        ";


    $form->addElement(new Element_Hidden("buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_trigger]", $trigger));


    $form->addElement(new Element_Textbox(__("Subject:", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_subject]", array('value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_subject'], 'required' => 1)));
    $form->addElement(new Element_Textbox(__("Email From Name:", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_from_name]", array('value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_from_name'], 'required' => 1)));
    $form->addElement(new Element_Email(__("Email From:", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_from]", array('value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_from'], 'required' => 1)));


    $form->addElement(new Element_Checkbox(__('Sent mail to:', 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_to]", array('author' => 'The Post Author', 'admin' => 'Admin E-mail Address from Settings/General'), array('value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_to'], 'inline' => 1)));
    $form->addElement(new Element_HTML('<br><br>'));
    $form->addElement(new Element_Textbox(__("Add mail to addresses separated with ',':", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_to_address]", array("class" => "span9", 'value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_to_address'], 'required' => 1)));


    ob_start();
    $settings = array('wpautop' => true, 'media_buttons' => false, 'wpautop' => true, 'tinymce' => true, 'quicktags' => true, 'textarea_rows' => 18);

    wp_editor($buddyforms['mail_notification'][$form_slug][$trigger]['mail_body'], 'buddyforms_options[mail_notification][' . $form_slug . '][' . $trigger . '][mail_body]', $settings);

    $wp_editor = ob_get_contents();
    ob_clean();

    $wp_editor = '<div class="bf_field_group bf_form_content"><label>' . __('Content', 'buddyforms') . ':</label><div class="bf_inputs">' . $wp_editor . '</div></div>';
    $form->addElement(new Element_HTML($wp_editor));
    $form->addElement(new Element_HTML('<br><br>'));
    $form->addElement(new Element_Button());
    //  $form->addElement(new Element_Textarea(__("Email Body:", 'buddyforms'), "buddyforms_options[mail_notification][" . $form_slug . "][" . $trigger . "][mail_body]", array("class" => "span9", 'value' => $buddyforms['mail_notification'][$form_slug][$trigger]['mail_body'], 'required' => 1, shortDesc => 'You can use Shortcodes to adjust your mail content with dynamic content. Place Shortcodes into the email body.')));
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

    if (isset($trigger, $buddyforms['mail_notification'][$form_slug][$trigger]))
        return false;

    buddyforms_new_notification_trigger_form($form_slug, $trigger, $href);
    die();
}

add_action('wp_ajax_buddyforms_new_mail_notification', 'buddyforms_new_mail_notification');
add_action('wp_ajax_nopriv_buddyforms_new_mail_notification', 'buddyforms_new_mail_notification');
?>