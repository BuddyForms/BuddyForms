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


        <?php
        $addon_args = array();

        $addon_args['buddyforms-members'] = array(
            'plugin_name'   => 'BuddyForms Members',
            'plugin_url'    => 'http://buddyforms.com',
            'plugin_image'  => 'http://themekraft.com/wp-content/uploads/2013/09/buddyforms-members-thumb-250x1703-250x170.png',
            'plugin_desc'   => 'This is the BuddyForms Members Extension. Integrate your BuddyForms Forms into your BuddyPress Members Profile.'
        );

        $addon_args['buddyforms-attach-posts-to-groups-extension'] = array(
            'plugin_name'   => 'BuddyForms Attach Posts to Groups Extension',
            'plugin_url'    => 'http://buddyforms.com',
            'plugin_image'  => 'http://themekraft.com/wp-content/uploads/2013/09/buddyforms-groups-thumb-250x1703-250x170.png',
            'plugin_desc'   => 'With this plugin, you’ll be able to automatically create a new BuddyPress group for pre-assigned BuddyForms post submissions and attach that group to the post. User-submitted posts with BuddyForms then become a BuddyPress group with all the included functionality and endless possibilities.'
        );
        $addon_args['buddyforms-posts-to-posts-integration'] = array(
            'plugin_name'   => 'BuddyForms Posts 2 Posts',
            'plugin_url'    => 'http://themekraft.com/',
            'plugin_image'  => 'http://ps.w.org/buddyforms-posts-to-posts-integration/assets/banner-772x250.png?rev=917118',
            'plugin_desc'   => 'With BuddyForms Posts 2 Posts Integration you can create complex connections and post relationships across your site. From posts to pages or to users all the Posts 2 Posts Plugin functionality is in your BuddyForms Form Builder available.'
        );
        $addon_args['buddyforms-hook-fields'] = array(
            'plugin_name'   => 'BuddyForms Hook Fields',
            'plugin_url'    => 'http://themekraft.com/',
            'plugin_desc'   => 'With this plugin you will get new options added to your Form Builder "Fields" to select where you want to display the field. This makes it very easy to manage the output and can save you a lot of time modifying your templates, by just adding a hook.'
        );

        buddyforms_get_addons($addon_args); ?>


    </div>

<?php
}

function buddyforms_get_addons($addon_args){

    foreach($addon_args as $key => $addon){ ?>

        <div class="bf-addon-half-col bf-left">
            <div class="bf-addon-col-content">
                <div class="addon-image">
                    <?php if($addon['plugin_image']){ ?>
                        <img width="250px" height="170px" height="170px" src="<?php echo $addon['plugin_image'] ?>">
                    <?php } ?>
                </div>
                <div class="addon-content">
                    <h4><?php echo $addon['plugin_name']; ?></h4>
                    <p><?php echo $addon['plugin_desc'] ?></p>
                </div>
                <div style="clear: left"></div>
                <?php
                $buddyforms_addons = new BuddyForms_Dependency( $key, $addon['plugin_url'] );

                if ( $buddyforms_addons->check_active() )
                    echo '<br><b><p style="color: #7AD03A">Installed and activated!</p></b>';
                else if ( $buddyforms_addons->check() )
                    echo '<br><b><p>Installed, but not activated. <a href="'.$buddyforms_addons->activate_link().'">Click here to activate the plugin.</a></p></b>';
                else if ( $install_link = $buddyforms_addons->install_link() )
                    echo '<br><b><p>Not installed. <a href="'.$install_link.'">Click here to install the plugin.</a></p></b>';
                else
                    echo '<br><b><p>Not installed and could not be found in the Plugin Directory. Please install this plugin manually.</p></b>';
                ?>
            </div>
        </div>
    <?php
    }

}

if (!class_exists('BuddyForms_Dependency')) {
    class BuddyForms_Dependency {
        // input information from the theme
        var $slug;
        var $uri;

        // installed plugins and uris of them
        private $plugins; // holds the list of plugins and their info
        private $uris; // holds just the URIs for quick and easy searching

        // both slug and PluginURI are required for checking things
        function __construct( $slug, $uri ) {
            $this->slug = $slug;
            $this->uri = $uri;
            if ( empty( $this->plugins ) )
                $this->plugins = get_plugins();
            if ( empty( $this->uris ) )
                $this->uris = wp_list_pluck($this->plugins, 'PluginURI');
        }

        // return true if installed, false if not
        function check() {
            return in_array($this->uri, $this->uris);
        }

        // return true if installed and activated, false if not
        function check_active() {
            $plugin_file = $this->get_plugin_file();
            if ($plugin_file) return is_plugin_active($plugin_file);
            return false;
        }

        // gives a link to activate the plugin
        function activate_link() {
            $plugin_file = $this->get_plugin_file();
            if ($plugin_file) return wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin='.$plugin_file), 'activate-plugin_'.$plugin_file);
            return false;
        }

        // return a nonced installation link for the plugin. checks wordpress.org to make sure it's there first.
        function install_link() {
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

            $info = plugins_api('plugin_information', array('slug' => $this->slug ));

            if ( is_wp_error( $info ) )
                return false; // plugin not available from wordpress.org

            return wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $this->slug), 'install-plugin_' . $this->slug);
        }

        // return array key of plugin if installed, false if not, private because this isn't needed for themes, generally
        private function get_plugin_file() {
            return array_search($this->uri, $this->uris);
        }
    }
}