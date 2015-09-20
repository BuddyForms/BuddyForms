<?php
/**
 * Create the BuddyForms settings page
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_settings_page(){
    global $buddyforms, $bf_mod5;

//    echo '<pre>';
//    print_r($buddyforms);
//    echo '</pre>';


     if (isset($buddyforms['buddyforms']) && count($buddyforms['buddyforms']) > 0) {

        echo '
        <div class="alignleft actions bulkactions">
            <select name="bf_bulkactions">
                <option value="-1" selected="selected">'.__('Bulk Actions','buddyforms').'</option>
                <option value="delete">'.__('Delete Permanently','buddyforms').'</option>
            </select>
            <button type="submit" class="button bf_delete" name="bf_delete" value="Apply">'.__('Apply','buddyforms').'</button>

        </div><br class="clear"><br>';

        echo '
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                        <label class="screen-reader-text" for="cb-select-all-1">' . __('Select All', 'buddyforms') . ' </label>
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <th scope="col" id="name" class="manage-column column-comment sortable desc" style="width: 360px;">' . __('Name', 'buddyforms') . '</th>
                    <th scope="col" id="slug" class="manage-column column-description" style="">' . __('Slug', 'buddyforms') . '</th>
                    <th scope="col" id="attached-post-type" class="manage-column column-status" style="">' . __('Attached Post Type', 'buddyforms') . '</th>
                    <th scope="col" id="attached-page" class="manage-column column-status" style="">' . __('Attached Page', 'buddyforms') . '</th>

            </thead>';
        foreach ($buddyforms['buddyforms'] as $key => $buddyform) {

            $slug = $buddyform['slug'];
            $slug = sanitize_title($slug);
            if(empty($slug)){
                $slug = $bf_mod5;
            }
            $buddyform['slug'] = $slug;

            if(empty($buddyform['name']))
                $buddyform['name'] = $slug;

            if(empty($buddyform['singular_name']))
                $buddyform['singular_name'] = $slug;

            echo ' <tr>
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="aid-' . $buddyform['slug'] . '">' . stripslashes($buddyform['name']) . '</label>
                        <input type="checkbox" name="bf_bulkactions_slugs[]" value="' . $buddyform['slug'] . '" id="aid-' . $buddyform['slug'] . '">


                    </th>
                    <td class="name column-name">

                    <div class="showhim">'.stripslashes($buddyform['name']).'<div class="showme"> <a href="'.get_admin_url().'admin.php?page=bf_edit_form&form_slug='.$buddyform['slug'].'"> '.__('Edit Form','buddyforms').'</a> | <a href="'.get_admin_url().'admin.php?page=bf_mail_notification&form_slug='.$buddyform['slug'].'"> '.__('Mail Notification','buddyforms').'</a> | <a href="'.get_admin_url().'admin.php?page=bf_manage_form_roles_and_capabilities&form_slug='.$buddyform['slug'].'">'.__('Roles and Capabilities','buddyforms').'</a></div></div>
                    </td>';

            echo '<td class="slug column-slug"> ';
            echo isset($buddyform['slug']) ? $buddyform['slug'] : '--';
            echo '</td>';

            echo '<td class="post_type column-post_type bf-error-text"> ';

            $post_type_html = $buddyform['post_type'];
            $post_type = isset($buddyform['post_type']) ? $buddyform['post_type'] : 'none';

            if(!post_type_exists($post_type))
                $post_type_html = '<p>' . __('Post Type ', 'buddyforms') . $post_type . __(' not exists', 'buddyforms') . '</p>';

            if($post_type == 'none')
                $post_type_html = '<p>' . __('No Post Type not Selected', 'buddyforms') . '</p>';

            echo $post_type_html;
            echo '</td>';

            echo '<td class="attached_page column-attached_page bf-error-text"> ';

            if( isset($buddyform['attached_page']) && empty($buddyform['attached_page']) ){
                $attached_page = '<p>No Page Attached</p>';
            } elseif(isset($buddyform['attached_page']) && $attached_page_title = get_the_title($buddyform['attached_page'])) {
                $attached_page = $attached_page_title;
            } else {
                $attached_page = '<p>Page not Exists</p>';
            }

            echo $attached_page;
            echo '</td>';

        }
        echo '</table>';
    } else {
        echo '<div class="bf-row"><div class="bf-half-col bf-left"><div class="bf-col-content bf_no_form"><h3 style="margin-top: 30px;">' . __('No Forms here so far...', 'buddyforms') . '</h3> <a href="' . get_admin_url() . 'admin.php?page=create-new-form" class="button-primary add-new-h3" style="font-size: 15px;">' . __('Create A New Form', 'buddyforms') . '</a></div></div></div>';
    }

}