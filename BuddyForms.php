<?php

/*
 Plugin Name: BuddyForms
 Plugin URI:  http://themekraft.com/store/wordpress-front-end-editor-and-form-builder-buddyforms/
 Description: Form Magic and Collaborative Publishing for WordPress. With Frontend Editing and Drag-and-Drop Form Builder.
 Version: 1.3
 Author: Sven Lehnert
 Author URI: http://themekraft.com/members/svenl77/
 Licence: GPLv3
 Network: false

 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

class BuddyForms {

	/**
	 * Self Upgrade Values
	 */
	// Base URL to the remote upgrade API server
	public $upgrade_url = 'http://themekraft.com/';

	/**
	 * @var string
	 */
	public $version = '1.3';

	/**
	 * @var string
	 */
	public $bf_version_name = 'bf_version';

	/**
	 * Initiate the class
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function __construct() {

		// Run the activation function
		register_activation_hook( __FILE__, array( $this, 'activation' ) );

        define('buddyforms', $this->version);
        define('BUDDYFORMS_VERSION', $this->version);

        add_action( 'init'					, array($this, 'includes')						, 4, 1);
		add_action( 'plugins_loaded'        , array($this, 'load_plugin_textdomain'));
		add_action( 'plugins_loaded'		, array($this, 'buddyforms_update_db_check'));
		add_action( 'wp_init'				, array($this, 'set_globals')					, 12, 1);
		add_action( 'admin_enqueue_scripts'	, array($this, 'buddyforms_admin_style')		, 1, 1);
		add_action( 'admin_enqueue_scripts'	, array($this, 'buddyforms_admin_js')			, 2, 1);
		add_action( 'template_redirect'		, array($this, 'buddyform_front_js_loader')		, 2, 1);


		$this->init_hook();
		$this->load_constants();

		/**
		 * Deletes all data if plugin deactivated
		 */
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );

	}

	/**
	 * Defines buddyforms_init action
	 *
	 * This action fires on WP's init action and provides a way for the rest of WP,
	 * as well as other dependent plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function init_hook() {
		global $buddyforms;
		$this->set_globals();
		do_action('buddyforms_init');
	}

	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * These constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function load_constants() {

		if (!defined('BUDDYFORMS_INSTALL_PATH'))
			define('BUDDYFORMS_INSTALL_PATH', dirname(__FILE__) . '/');

		if (!defined('BUDDYFORMS_INCLUDES_PATH'))
			define('BUDDYFORMS_INCLUDES_PATH', BUDDYFORMS_INSTALL_PATH . 'includes/');

		if (!defined('BUDDYFORMS_TEMPLATE_PATH'))
			define('BUDDYFORMS_TEMPLATE_PATH', BUDDYFORMS_INCLUDES_PATH . 'templates/');

	}

	/**
	 * Setup all globals
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	static function set_globals() {
		global $buddyforms;

		$buddyforms = get_option('buddyforms_options');

		$buddyforms = apply_filters('buddyforms_set_globals', $buddyforms);

	}

	/**
	 * Include files needed by BuddyForms
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function includes() {

		if(!function_exists('PFBC_Load'))
			require_once (BUDDYFORMS_INCLUDES_PATH . '/resources/pfbc/Form.php');

		require_once (BUDDYFORMS_INCLUDES_PATH . 'functions.php');

		require_once (BUDDYFORMS_INCLUDES_PATH . 'form.php');
		require_once(BUDDYFORMS_INCLUDES_PATH  . 'form-elements.php');
		require_once(BUDDYFORMS_INCLUDES_PATH  . 'form-control.php');
        require_once (BUDDYFORMS_INCLUDES_PATH . 'revisions.php');

        require_once (BUDDYFORMS_INCLUDES_PATH . 'shortcodes.php');
        require_once (BUDDYFORMS_INCLUDES_PATH . 'wp-mail.php');

		if (is_admin()){

			require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/form-builder.php');
			require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/form-builder-elements.php');
			require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/admin.php');
            require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/admin-ajax.php');
			require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/create-new-form.php');
            require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/meta-box.php');
            require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/add-ons.php');
            require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/mail-notification.php');
            require_once(BUDDYFORMS_INCLUDES_PATH . '/admin/manage-form-roles-and-capabilities.php');

            // License Key API Class
			require_once( plugin_dir_path( __FILE__ ) . 'includes/resources/api-manager/classes/class-bf-key-api.php');

			// Plugin Updater Class
			require_once( plugin_dir_path( __FILE__ ) . 'includes/resources/api-manager/classes/class-bf-plugin-update.php');

			// API License Key Registration Form
			require_once( plugin_dir_path( __FILE__ ) . 'includes/admin/license-registration.php');

			// Load update class to update $this plugin from for example toddlahman.com
			$this->load_plugin_self_updater();

			
		}
					
	}

	/**
	 * Load the textdomain for the plugin
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain('buddyforms', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	/**
	 * Enqueue the needed CSS for the admin screen
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	function buddyforms_admin_style($hook_suffix) {

     	if($hook_suffix == 'toplevel_page_buddyforms_options_page' || $hook_suffix == 'buddyforms_page_create-new-form' || $hook_suffix == 'buddyforms_page_bf_add_ons' || $hook_suffix == 'buddyforms_page_bf_mail_notification' || $hook_suffix == 'buddyforms_page_bf_manage_form_roles_and_capabilities') {
				
			wp_enqueue_style('buddyforms_admin_css', plugins_url('includes/admin/css/admin.css', __FILE__) );

			if ( is_rtl() ) {
				wp_enqueue_style(  'style-rtl',  plugins_url('includes/admin/css/admin-rtl.css', __FILE__) );
			}

			wp_enqueue_style('bootstrapcss', plugins_url('includes/admin/css/bootstrap.css', __FILE__) );
			wp_enqueue_style('buddyforms_zendesk_css', '//assets.zendesk.com/external/zenbox/v2.6/zenbox.css' );

            // load the tk_icons
            wp_enqueue_style( 'tk_icons', plugins_url('/includes/resources/tk_icons/style.css', __FILE__) );



		}
	}

	/**
	 * Enqueue the needed JS for the admin screen
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	function buddyforms_admin_js($hook_suffix) {

        if($hook_suffix == 'toplevel_page_buddyforms_options_page' || $hook_suffix == 'buddyforms_page_create-new-form' || $hook_suffix == 'buddyforms_page_bf_add_ons' || $hook_suffix == 'buddyforms_page_bf_mail_notification' || $hook_suffix == 'buddyforms_page_bf_manage_form_roles_and_capabilities') {

            wp_enqueue_script('buddyforms_admin_js', plugins_url('includes/admin/js/admin.js', __FILE__));
			wp_enqueue_script('bootstrapjs', plugins_url('includes/admin/js/bootstrap.js', __FILE__), array('jquery') );
		    wp_enqueue_script('jQuery');
		    wp_enqueue_script('jquery-ui-sortable'); 
			wp_enqueue_script('buddyforms_zendesk_js', '//assets.zendesk.com/external/zenbox/v2.6/zenbox.js');

	    }
	
	}
	
	/**
	 * Enqueue the needed JS for the form in the frontend
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	function buddyform_front_js_loader(){
		global $post, $wp_query, $buddyforms;

		$found = false;

		// check the post content for the short code
		if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'buddyforms_form') )
			$found = true;

		if(isset($wp_query->query['bf_action']))
			$found = true;

		$found = apply_filters('buddyforms_front_js_css_loader', $found);

		if($found)
			BuddyForms::buddyform_front_js();

	}
	function buddyform_front_js() {

		do_action('buddyforms_front_js_css_enqueue');

		wp_enqueue_script(	'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-datepicker' );

        wp_enqueue_script(	'buddyforms-select2-js',		                plugins_url('includes/resources/select2/select2.min.js', __FILE__) , array( 'jquery' ), '3.5.2' );
        wp_enqueue_style(	'buddyforms-select2-css',	                    plugins_url('includes/resources/select2/select2.css', __FILE__));

        wp_enqueue_script(	'buddyforms-jquery-ui-timepicker-addon-js',		plugins_url('includes/resources/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js', __FILE__), array('jquery-ui-core' ,'jquery-ui-datepicker', 'jquery-ui-slider') );
        wp_enqueue_style(	'buddyforms-jquery-ui-timepicker-addon-css',	plugins_url('includes/resources/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.css', __FILE__));

        wp_enqueue_script(	'buddyforms-js',					            plugins_url('includes/js/buddyforms.js', __FILE__),  array('jquery-ui-core' ,'jquery-ui-datepicker', 'jquery-ui-slider') );

        wp_enqueue_media();
        wp_enqueue_script(	'media-uploader-js',					            plugins_url('includes/js/media-uploader.js', __FILE__),  array('jquery') );

        wp_enqueue_style(   'buddyforms-the-loop-css',                     plugins_url('includes/css/the-loop.css', __FILE__));
        wp_enqueue_style(   'buddyforms-the-form-css',                     plugins_url('includes/css/the-form.css', __FILE__));

    }

	function buddyforms_update_db_check(){

		$curr_ver	= get_option( $this->bf_version_name );
		$buddyforms	= get_option('buddyforms_options');

		$buddyforms_options = $buddyforms;

		// checks if the current plugin version is lower than the version being updated
		if ( version_compare( $this->version, $curr_ver, '>' ) ) {
			foreach($buddyforms['buddyforms'] as $form_key => $buddyform){

				$needs_title = true;
				$needs_content = true;

				foreach($buddyform['form_fields'] as $field_key => $form_field){

					if($form_field['slug'] == 'editpost_title')
						$needs_title = false;

					if($form_field['slug'] == 'editpost_content')
						$needs_content = false;
				}

				if($needs_title){

					$field_id = $mod5 = substr(md5(time() * rand()), 0, 10);

					$buddyforms_options['buddyforms'][$form_key]['form_fields'][$field_id]['name']		= 'Title';
					$buddyforms_options['buddyforms'][$form_key]['form_fields'][$field_id]['slug']		= 'editpost_title';
					$buddyforms_options['buddyforms'][$form_key]['form_fields'][$field_id]['type']		= 'Title';
					$buddyforms_options['buddyforms'][$form_key]['form_fields'][$field_id]['order']		= '1';

				}

				if (version_compare($curr_ver, "1.1", "<=")) {
					if($needs_content){

						$field_id = $mod5 = substr(md5(time() * rand()), 0, 10);

						$buddyforms_options['buddyforms'][$form_key]['form_fields'][$field_id]['name']		= 'Content';
						$buddyforms_options['buddyforms'][$form_key]['form_fields'][$field_id]['slug']		= 'editpost_content';
						$buddyforms_options['buddyforms'][$form_key]['form_fields'][$field_id]['type']		= 'Content';
						$buddyforms_options['buddyforms'][$form_key]['form_fields'][$field_id]['order']		= '2';

					}
				}

			}
			update_option( $this->bf_version_name, $this->version );
			update_option("buddyforms_options", $buddyforms_options);
		}

	}


	/**
	 * Check for software updates
	 */
	public function load_plugin_self_updater() {
		$options = get_option( 'bf_license_manager' );

		// upgrade url must also be chaned in classes/class-bf-key-api.php
		$upgrade_url = 'http://themekraft.com/'; // URL to access the Update API Manager.
		$plugin_name = untrailingslashit( plugin_basename( __FILE__ ) ); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
		$product_id = get_option( 'buddyforms_product_id' ); // Software Title
		$api_key = $options['api_key']; // API License Key
		$activation_email = $options['activation_email']; // License Email
		$renew_license_url = 'http://themekraft.com/my-account/'; // URL to renew a license
		$instance = get_option( 'buddyforms_instance' ); // Instance ID (unique to each blog activation)
		$domain = site_url(); // blog domain name
		$software_version = get_option( $this->bf_version_name ); // The software version
		$plugin_or_theme = 'plugin'; // 'theme' or 'plugin'

		new Buddyforms_Plugin_Update_API_Check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme );
	}


	/**
	 * Generate the default data arrays
	 */
	public function activation() {

		$global_options = array(
			'api_key' 			=> '',
			'activation_email' 	=> '',
					);

		update_option( 'bf_license_manager', $global_options );

		// Password Management Class
		require_once( plugin_dir_path( __FILE__ ) . 'includes/resources/api-manager/classes/class-bf-passwords.php');

		$buddyforms_password_management = new Buddyforms_Password_Management();

		// Generate a unique installation $instance id
		$instance = $buddyforms_password_management->generate_password( 12, false );

		$single_options = array(
			'buddyforms_product_id' 			=> 'BuddyForms',
			'buddyforms_instance' 				=> $instance,
			'buddyforms_deactivate_checkbox' 	=> 'on',
			'buddyforms_activated' 				=> 'Deactivated',
			);

		foreach ( $single_options as $key => $value ) {
			update_option( $key, $value );
		}

		$curr_ver = get_option( $this->bf_version_name );

		// checks if the current plugin version is lower than the version being installed
		if ( version_compare( $this->version, $curr_ver, '>' ) ) {
			// update the version
			update_option( $this->bf_version_name, $this->version );
		}

	}

	/**
	 * Deletes all data if plugin deactivated
	 * @return void
	 */
	public function uninstall() {
		global $wpdb, $blog_id;

		$this->license_key_deactivation();

		// Remove options
		if ( is_multisite() ) {

			switch_to_blog( $blog_id );

			foreach ( array(
					'bf_license_manager',
					'buddyforms_product_id',
					'buddyforms_instance',
					'buddyforms_deactivate_checkbox',
					'buddyforms_activated',
					'bf_version'
					) as $option) {

					delete_option( $option );

					}

			restore_current_blog();

		} else {

			foreach ( array(
					'bf_license_manager',
					'buddyforms_product_id',
					'buddyforms_instance',
					'buddyforms_deactivate_checkbox',
					'buddyforms_activated'
					) as $option) {

					delete_option( $option );

					}

		}

	}

	/**
	 * Deactivates the license on the API server
	 * @return void
	 */
	public function license_key_deactivation() {

		$buddyforms_key = new Buddyforms_Key();

		$activation_status = get_option( 'buddyforms_activated' );

		$default_options = get_option( 'bf_license_manager' );

		$api_email = $default_options['activation_email'];
		$api_key = $default_options['api_key'];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);

		if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
			$buddyforms_key->deactivate( $args ); // reset license key activation
		}
	}

}

$GLOBALS['buddyforms_new'] = new BuddyForms();