<?php

/**
 * Plugin Name: BuddyForms
 * Plugin URI:  https://themekraft.com/buddyforms/
 * Description: Contact Forms, Post Forms for User Generated Content and Registration Forms easily build in minutes. Step by step with an easy to use Form Wizard. Ideal for User Submitted Posts. Extendable with Addons!
 * Version: 2.2.9.1
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/buddyforms/
 * Licence: GPLv3
 * Network: false
 * Text Domain: buddyforms
 *
 * @fs_premium_only /includes/admin/form-metabox.php
 *
 * ****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 ****************************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'BuddyForms' ) ) {
	/**
	 * Class BuddyForms
	 */
	class BuddyForms {

		/**
		 * @var string
		 */
		public $version = '2.2.9.1';

		/**
		 * @var string Assets URL
		 */
		public static $assets;

		/**
		 * Initiate the class
		 *
		 * @package buddyforms
		 * @since 0.1-beta
		 */
		public function __construct() {
			global $wp_session;

			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );

			$this->load_constants();

			self::$assets  = plugin_dir_url( __FILE__ ) . 'assets/';

			//Load the necessary files to start the sessions
			require_once( BUDDYFORMS_INCLUDES_PATH . '/class-buddyforms-session.php' );

			add_action( 'init', array( $this, 'init_hook' ), 1, 1 );
			add_action( 'init', array( $this, 'includes' ), 4, 1 );
			add_action( 'init', array( $this, 'update_db_check' ), 10 );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ), 102, 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ), 102, 1 );
			add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );

			add_action( 'wp_enqueue_scripts', array( $this, 'front_js_loader' ), 102, 1 );

			register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
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

			/**
			 * Define the plugin version
			 */
			define( 'BUDDYFORMS_VERSION', $this->version );

			if ( ! defined( 'BUDDYFORMS_PLUGIN_URL' ) ) {
				/**
				 * Define the plugin url
				 */
				define( 'BUDDYFORMS_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
			}

			if ( ! defined( 'BUDDYFORMS_INSTALL_PATH' ) ) {
				/**
				 * Define the install path
				 */
				define( 'BUDDYFORMS_INSTALL_PATH', dirname( __FILE__ ) . '/' );
			}

			if ( ! defined( 'BUDDYFORMS_INCLUDES_PATH' ) ) {
				/**
				 * Define the include path
				 */
				define( 'BUDDYFORMS_INCLUDES_PATH', BUDDYFORMS_INSTALL_PATH . 'includes/' );
			}

			if ( ! defined( 'BUDDYFORMS_TEMPLATE_PATH' ) ) {
				/**
				 * Define the template path
				 */
				define( 'BUDDYFORMS_TEMPLATE_PATH', BUDDYFORMS_INSTALL_PATH . 'templates/' );
			}

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
			$this->set_globals();
			do_action( 'buddyforms_init' );
		}

		/**
		 * Setup all globals
		 *
		 * @package buddyforms
		 * @since 0.1-beta
		 */
		static function set_globals() {
			global $buddyforms;

			/*
			 * Get BuddyForms options
			 *
			 * @filter: buddyforms_set_globals
			 *
			 */
			$buddyforms = apply_filters( 'buddyforms_set_globals', get_option( 'buddyforms_forms' ) );

			return $buddyforms;
		}

		/**
		 * Include files needed by BuddyForms
		 *
		 * @package buddyforms
		 * @since 0.1-beta
		 */
		public function includes() {

			if ( ! function_exists( 'PFBC_Load' ) ) {
				require_once( BUDDYFORMS_INCLUDES_PATH . '/resources/pfbc/Form.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/resources/pfbc/FieldControl.php' );
				new FieldControl();
			}



			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/register-post-types.php' );

			require_once( BUDDYFORMS_INCLUDES_PATH . 'functions.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'gdpr.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'change-password.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'multisite.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'the-content.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'rewrite-roles.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'shortcodes.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'wp-mail.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'wp-insert-user.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'revisions.php' );

			require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-preview.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-render.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-ajax.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-elements.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-control.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-validation.php' );
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/user-meta.php' );

			if ( is_admin() ) {
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/form-builder-elements.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/form-templates.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/form-wizard.php' );

				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/admin-ajax.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/welcome-screen.php' );

				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/submissions.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/settings.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/password-strengh-settings.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/functions.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/deregister.php' );

				// GDPR
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/personal-data-exporter.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/personal-data-eraser.php' );

				if ( buddyforms_core_fs()->is__premium_only() ) {
					if ( buddyforms_core_fs()->is_plan( 'professional' ) || buddyforms_core_fs()->is_trial() ) {
						require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-metabox.php' );
					}
				}

				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/mce-editor-button.php' );

				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-mail-notification.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-permissions.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-layout.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-registration.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-shortcodes.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-select-form.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-form-elements.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-form-setup.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-form-header.php' );
				require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-form-footer.php' );
			}
		}

		/**
		 * Load the textdomain for the plugin
		 *
		 * @package buddyforms
		 * @since 0.1-beta
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'buddyforms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Enqueue the needed CSS for the admin screen
		 *
		 * @package buddyforms
		 * @since 0.1-beta
		 *
		 * @param $hook_suffix
		 */
		function admin_styles( $hook_suffix ) {
			global $post;

			if (
				( isset( $post ) && $post->post_type == 'buddyforms' && isset( $_GET['action'] ) && $_GET['action'] == 'edit'
				  || isset( $post ) && $post->post_type == 'buddyforms' && $hook_suffix == 'post-new.php' )
				|| $hook_suffix == 'buddyforms_page_buddyforms_submissions'
				|| $hook_suffix == 'buddyforms_page_buddyforms_settings'
			) {

				if ( is_rtl() ) {
					wp_enqueue_style( 'buddyforms-style-rtl', plugins_url( 'assets/admin/css/admin-rtl.css', __FILE__ ) );
				}

				wp_enqueue_style( 'buddyforms-admin-css', plugins_url( 'assets/admin/css/admin.css', __FILE__ ) );
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
				wp_enqueue_style( 'wp-color-picker' );

			} else {
				wp_enqueue_style( 'buddyforms-admin-post-metabox', plugins_url( 'assets/admin/css/admin-post-metabox.css', __FILE__ ) );
			}
			// load the tk_icons everywhere in the admin
			self::load_tk_font_icons();

		}

		/**
		 * Load TK icons
		 */
		static function load_tk_font_icons() {
			wp_enqueue_style( 'buddyforms-tk-icons', self::$assets . 'resources/tk_icons/style.css' );
		}

		/**
		 * Enqueue the needed JS for the admin screen
		 *
		 * @package buddyforms
		 * @since 0.1-beta
		 *
		 * @param $hook_suffix
		 */
		function admin_js( $hook_suffix ) {
			global $post;

			if (
				( isset( $post ) && $post->post_type == 'buddyforms' && isset( $_GET['action'] ) && $_GET['action'] == 'edit'
				  || isset( $post ) && $post->post_type == 'buddyforms' && $hook_suffix == 'post-new.php' )
				|| $hook_suffix == 'buddyforms_page_buddyforms_submissions'
				|| $hook_suffix == 'buddyforms_page_buddyforms_settings'
			) {
				wp_register_script( 'buddyforms-admin-js', plugins_url( 'assets/admin/js/admin.js', __FILE__ ), array(), $this->version );
				wp_register_script( 'buddyforms-admin-slugifies-js', plugins_url( 'assets/admin/js/slugifies.js', __FILE__ ), array(), $this->version );
				wp_register_script( 'buddyforms-admin-wizard-js', plugins_url( 'assets/admin/js/wizard.js', __FILE__ ), array(), $this->version );
				wp_register_script( 'buddyforms-admin-deprecated-js', plugins_url( 'assets/admin/js/deprecated.js', __FILE__ ), array(), $this->version );
				wp_register_script( 'buddyforms-admin-conditionals-js', plugins_url( 'assets/admin/js/conditionals.js', __FILE__ ), array(), $this->version );
				wp_register_script( 'buddyforms-admin-formbuilder-js', plugins_url( 'assets/admin/js/formbuilder.js', __FILE__ ), array(), $this->version );


				// GDPR Localisation
				$buddyforms_gdpr = get_option( 'buddyforms_gdpr' );
				$templates = isset( $buddyforms_gdpr['templates'] ) ? $buddyforms_gdpr['templates'] : array();

				$admin_text_array = array();
				$admin_text_array['check'] = __( 'Check all', 'buddyforms' );
				$admin_text_array['uncheck'] = __( 'Uncheck all', 'buddyforms' );
				$admin_text_array['uncheck'] = __( 'Uncheck all', 'buddyforms' );

				foreach( $templates as $key => $template ) {
					$admin_text_array[$key] = $template;
                 }
				wp_localize_script( 'buddyforms-admin-js', 'admin_text', $admin_text_array );
				wp_enqueue_script( 'buddyforms-admin-js' );

				wp_enqueue_script( 'buddyforms-admin-slugifies-js' );
				wp_enqueue_script( 'buddyforms-admin-wizard-js' );
				wp_enqueue_script( 'buddyforms-admin-deprecated-js' );
				wp_enqueue_script( 'buddyforms-admin-formbuilder-js' );
				wp_enqueue_script( 'buddyforms-admin-conditionals-js' );

				wp_enqueue_script( 'buddyforms-jquery-steps-js', plugins_url( 'assets/resources/jquery-steps/jquery.steps.min.js', __FILE__ ), array( 'jquery' ), $this->version );

				wp_enqueue_script( 'jQuery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'jquery-ui-accordion' );
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script( 'jquery-ui-tabs' );

				wp_enqueue_script( 'wp-color-picker' );

				wp_enqueue_script( 'buddyforms-select2-js', plugins_url( 'assets/resources/select2/dist/js/select2.min.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_style( 'buddyforms-select2-css', plugins_url( 'assets/resources/select2/dist/css/select2.min.css', __FILE__ ) );
			}
			wp_enqueue_script( 'tinymce' );
			wp_enqueue_script( 'buddyforms-admin-all-js', plugins_url( 'assets/admin/js/admin-all.js', __FILE__ ), array( 'jquery' ), $this->version );

			wp_enqueue_media();
			wp_enqueue_script( 'media-uploader-js', plugins_url( 'assets/js/media-uploader.js', __FILE__ ), array( 'jquery' ) );


            //DropZone
			wp_enqueue_script( 'buddyforms-dropzone', plugins_url( 'assets/resources/dropzone/dropzone.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'buddyforms_dropzone_initializer', plugins_url( 'assets/resources/dropzone/initializer.js', __FILE__ ), array( 'jquery' ), $this->version, true );
			wp_enqueue_style( 'buddyforms-dropzone-basic', plugins_url( 'assets/resources/dropzone/basic.css', __FILE__ ), array(), $this->version );
			wp_enqueue_style( 'buddyforms-dropzone', plugins_url( 'assets/resources/dropzone/dropzone.css', __FILE__ ), array(), $this->version );
            $params = array(
                'admin_url' => admin_url( 'admin-ajax.php' ),
                'ajaxnonce' => wp_create_nonce( 'fac_drop' )
            );
            wp_localize_script("buddyforms_dropzone_initializer", "dropParam", $params);

            //Featured image
            wp_enqueue_script( 'buddyforms_featured_image_initializer', plugins_url( 'assets/resources/featured-image/featured-image-initializer.js', __FILE__ ), array( 'jquery' ), $this->version );
			do_action( 'buddyforms_admin_js_css_enqueue' );
		}

		/**
		 * Check if a buddyforms view is displayed and load the needed styles and scripts
		 *
		 * @package buddyforms
		 * @since 1.0
		 */
		function front_js_loader() {
			global $post, $wp_query, $buddyforms;

			$found = false;

			// check the post content for the short code
			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'buddyforms_form' ) ) {
				$found = true;
			}

			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'buddyforms_list_all' ) ) {
				$found = true;
			}

			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'buddyforms_the_loop' ) ) {
				$found = true;
			}

			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'bf' ) ) {
				$found = true;
			}

			if ( isset( $wp_query->query['bf_action'] ) ) {
				$found = true;
			}

			$buddyforms_preview_page = get_option( 'buddyforms_preview_page', true );

			if ( isset( $post->ID ) && $post->ID == $buddyforms_preview_page ) {
				$found = true;
			}

			$found = apply_filters( 'buddyforms_front_js_css_loader', $found );

			if ( $found ) {
				BuddyForms::front_js_css();
				self::load_tk_font_icons();
			}

		}

		/**
		 * Enqueue the needed JS for the form in the frontend
		 *
		 * @package buddyforms
		 * @since 1.0
		 */
		function front_js_css() {
			global $wp_scripts;

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			do_action( 'buddyforms_front_js_css_enqueue' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			// wp_enqueue_style( 'buddyforms-jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );

			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-widgets' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'password-strength-meter' );


            $password_strength_settings = get_option( 'buddyforms_password_strength_settings' );
            wp_localize_script( 'password-strength-meter', 'pwsL10n', array(
                'empty' => isset( $password_strength_settings['hint_text']  ) && ! empty( $password_strength_settings['hint_text'] ) ? $password_strength_settings['hint_text'] : __( 'Strength indicator' ),
                'short' => isset( $password_strength_settings['lavel_1']  ) && ! empty( $password_strength_settings['lavel_1'] ) ? $password_strength_settings['lavel_1'] : __( 'Short: Your password is too short.' ),
                'bad' => isset( $password_strength_settings['lavel_2']  ) && ! empty( $password_strength_settings['lavel_2'] ) ? $password_strength_settings['lavel_2'] : __( 'Password Strength: Weak' ),
                'good' => isset( $password_strength_settings['lavel_3']  ) && ! empty( $password_strength_settings['lavel_3'] ) ? $password_strength_settings['lavel_3'] : _x( 'Password Strength: OK', 'password strength' ),
                'strong' => isset( $password_strength_settings['lavel_4']  ) && ! empty( $password_strength_settings['lavel_4'] ) ? $password_strength_settings['lavel_4'] : __( 'Password Strength: Strong' ),
                'mismatch' => isset( $password_strength_settings['mismatch']  ) && ! empty( $password_strength_settings['mismatch'] ) ? $password_strength_settings['mismatch'] : __( 'Mismatch' ),
                //'error' => isset( $password_strength_settings['error']  ) && ! empty( $password_strength_settings['error'] ) ? $password_strength_settings['error'] : __( 'Error' ),
                'hint_text' => isset( $password_strength_settings['hint_text']  ) && ! empty( $password_strength_settings['hint_text'] ) ? $password_strength_settings['hint_text'] : __( 'Hint: The password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! \" ? $ % ^ &amp; ).' ),
                'required_strength' => isset( $password_strength_settings['required_strength']  ) && ! empty( $password_strength_settings['required_strength'] ) ? $password_strength_settings['required_strength'] : '0',
            ) );

			wp_enqueue_script( 'mce-view' );
			// jQuery Validation http://jqueryvalidation.org/
			wp_enqueue_script( 'jquery-validation', plugins_url( 'assets/resources/jquery.validate.min.js', __FILE__ ), array( 'jquery' ) );

			// jQuery Local storage http://garlicjs.org/
			wp_enqueue_script( 'jquery-garlicjs', plugins_url( 'assets/resources/garlicjs/garlic.js', __FILE__ ), array( 'jquery' ) );

			//DropZone
			wp_enqueue_script( 'buddyforms-dropzone', plugins_url( 'assets/resources/dropzone/dropzone.js', __FILE__ ), array( 'jquery' ) );
            wp_enqueue_script( 'buddyforms_dropzone_initializer', plugins_url( 'assets/resources/dropzone/initializer.js', __FILE__ ), array( 'jquery' ), $this->version,  true);
			wp_enqueue_style( 'buddyforms-dropzone-basic', plugins_url( 'assets/resources/dropzone/basic.css', __FILE__ ) );
			wp_enqueue_style( 'buddyforms-dropzone', plugins_url( 'assets/resources/dropzone/dropzone.css', __FILE__ ) );
			$params = array(
				'admin_url' => admin_url( 'admin-ajax.php' ),
				'ajaxnonce' => wp_create_nonce( 'fac_drop' )
			);
            wp_localize_script("buddyforms_dropzone_initializer", "dropParam", $params);
            //Featured image
            wp_enqueue_script( 'buddyforms_featured_image_initializer', plugins_url( 'assets/resources/featured-image/featured-image-initializer.js', __FILE__ ), array( 'jquery' ), $this->version, true );
			// jQuery Select2 // https://select2.github.io/
			wp_enqueue_script( 'buddyforms-select2-js', plugins_url( 'assets/resources/select2/dist/js/select2.min.js', __FILE__ ), array( 'jquery' ), '4.0.3' );
			wp_enqueue_style( 'buddyforms-select2-css', plugins_url( 'assets/resources/select2/dist/css/select2.min.css', __FILE__ ) );

			wp_enqueue_script( 'buddyforms-jquery-ui-timepicker-addon-js', plugins_url( 'assets/resources/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js', __FILE__ ), array(
				'jquery-ui-core',
				'jquery-ui-datepicker',
				'jquery-ui-slider'
			), $this->version );
			wp_enqueue_style( 'buddyforms-jquery-ui-timepicker-addon-css', plugins_url( 'assets/resources/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.css', __FILE__ ) );

			wp_enqueue_script( 'buddyforms-js', plugins_url( 'assets/js/buddyforms.js', __FILE__ ), array(
				'jquery-ui-core',
				'jquery-ui-datepicker',
				'jquery-ui-slider'
			) );

			wp_enqueue_media();
			wp_enqueue_script( 'media-uploader-js', plugins_url( 'assets/js/media-uploader.js', __FILE__ ), array( 'jquery' ) );

			wp_enqueue_style( 'buddyforms-the-loop-css', plugins_url( 'assets/css/the-loop.css', __FILE__ ) );
			wp_enqueue_style( 'buddyforms-the-form-css', plugins_url( 'assets/css/the-form.css', __FILE__ ) );

			// load dashicons
			wp_enqueue_style( 'dashicons' );

			wp_enqueue_style( 'buddyforms-the-form-css', plugins_url( 'assets/css/the-form.css', __FILE__ ) );

			wp_enqueue_script( 'buddyforms-loadingoverlay', plugins_url( 'assets/resources/loadingoverlay/loadingoverlay.min.js', __FILE__ ), array( 'jquery' ) );

			wp_enqueue_style( 'wp_editor_css', includes_url( '/css/editor.css' ) );


			wp_enqueue_script( 'buddyforms-gdpr-js', plugins_url( 'assets/js/gdpr.js', __FILE__ ), array( 'jquery' ), $this->version, false  );
			$translations = array(
				'gdpr_ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'gdpr_success'  => __( 'Your enquiry have been submitted. Check your email to validate your data request.', 'gdpr-data-request-form' ),
				'gdpr_errors'   => __( 'Some errors occurred:', 'gdpr-data-request-form' ),
			);
			wp_localize_script( 'buddyforms-gdpr-js', 'buddyforms_gdpr_localize', $translations );


			add_action( 'wp_head', 'buddyforms_jquery_validation' );

		}

		/**
		 * Update form 1.x version
		 *
		 * @package buddyforms
		 * @since 2.0
		 */
		function update_db_check() {

			if ( ! is_admin() ) {
				return;
			}

			$buddyforms_old = get_option( 'buddyforms_options' );

			if ( ! $buddyforms_old ) {
				return;
			}

			update_option( 'buddyforms_options_old', $buddyforms_old );

			foreach ( $buddyforms_old['buddyforms'] as $key => $form ) {
				$bf_forms_args = array(
					'post_title'  => $form['name'],
					'post_type'   => 'buddyforms',
					'post_status' => 'publish',
				);

				// Insert the new form
				$post_id    = wp_insert_post( $bf_forms_args, true );
				$form['id'] = $post_id;

				update_post_meta( $post_id, '_buddyforms_options', $form );

				// Update the option _buddyforms_forms used to reduce queries
				$buddyforms_forms = get_option( 'buddyforms_forms' );

				$buddyforms_forms[ $form['slug'] ] = $form;
				update_option( 'buddyforms_forms', $buddyforms_forms );

			}

			update_option( 'buddyforms_version', BUDDYFORMS_VERSION );

			delete_option( 'buddyforms_options' );

			buddyforms_attached_page_rewrite_rules( true );
		}


		/**
		 * Change the admin footer text on BuddyForms admin pages.
		 *
		 * @since  1.6
		 *
		 * @param  string $footer_text
		 *
		 * @return string
		 */
		public function admin_footer_text( $footer_text ) {
			global $post;

			if ( ! current_user_can( 'manage_options' ) ) {
				return $footer_text;
			}

			$current_screen = get_current_screen();

			if ( ! isset( $current_screen->id ) ) {
				return $footer_text;
			}

			if ( $current_screen->id == 'edit-buddyforms'
			     || $current_screen->id == 'buddyforms'
			     || $current_screen->id == 'buddyforms_page_buddyforms_submissions'
			     || $current_screen->id == 'buddyforms_page_buddyforms_settings'
			     || $current_screen->id == 'buddyforms_page_bf_add_ons'
			) {

				// Change the footer text
				$footer_text = sprintf( __( 'If you like <strong>BuddyForms</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thank you from BuddyForms in advance!', 'buddyforms' ), '<a href="https://wordpress.org/support/view/plugin-reviews/buddyforms?filter=5#postform" target="_blank" class="wc-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'woocommerce' ) . '">', '</a>' );
			}

			return $footer_text;
		}

		/**
		 * Plugin activation
		 * @since  2.0
		 */
		function plugin_activation() {

			$title        = apply_filters( 'buddyforms_preview_page_title', 'BuddyForms Preview Page' );
			$preview_page = get_page_by_title( $title );
			if ( ! $preview_page ) {
				// Create preview page object
				$preview_post = array(
					'post_title'   => $title,
					'post_content' => 'This is a preview of how this form will appear on your website',
					'post_status'  => 'draft',
					'post_type'    => 'page'
				);

				// Insert the page into the database
				$page_id = wp_insert_post( $preview_post );
			} else {
				$page_id = $preview_page->ID;
			}

			update_option( 'buddyforms_preview_page', $page_id );

			$options = get_option( 'buddyforms_forms', true );

			update_option( 'buddyforms_first_path_after_install', is_array( $options ) && count( $options ) > 0 ? 'edit.php?post_type=buddyforms&page=buddyforms_welcome_screen' : 'post-new.php?post_type=buddyforms&wizard=1' );

			set_transient( '_buddyforms_welcome_screen_activation_redirect', true, 30 );

		}

		/**
		 * Plugin deactivation
		 * @since  2.0
		 */
		function plugin_deactivation() {
			$buddyforms_preview_page = get_option( 'buddyforms_preview_page', true );

			wp_delete_post( $buddyforms_preview_page, true );

			delete_option( 'buddyforms_preview_page' );
		}
	}

	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return Freemius
	 */
	function buddyforms_core_fs() {
		global $buddyforms_core_fs;

		$first_path = get_option( 'buddyforms_first_path_after_install' );

		if ( ! isset( $buddyforms_core_fs ) ) {

			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/includes/resources/freemius/start.php';

			$buddyforms_core_fs = fs_dynamic_init( array(
				'id'                  => '391',
				'slug'                => 'buddyforms',
				'type'                => 'plugin',
				'public_key'          => 'pk_dea3d8c1c831caf06cfea10c7114c',
				'is_premium'          => true,
				'has_addons'          => true,
				'has_paid_plans'      => true,
				'trial'               => array(
					'days'               => 14,
					'is_require_payment' => true,
				),
				'has_affiliation'     => 'all',
				'menu'                => array(
					'slug'           => 'edit.php?post_type=buddyforms',
					'first-path' => $first_path,
					'support'        => false,
					'contact'    => true,
					'addons'     => true,
				),
			) );
		}

		return $buddyforms_core_fs;
	}

	function buddyforms_php_version_admin_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'PHP Version Update Required!', 'buddyforms' ); ?></p>
			<p><?php _e( 'You are using PHP Version ' . PHP_VERSION, 'buddyforms' ); ?></p>
			<p><?php _e( 'Please make sure you have at least php version 5.3 installed.', 'buddyforms' ); ?></p>
		</div>
		<?php
	}

	function activate_buddyform_at_plugin_loader() {
		// BuddyForms requires php version 5.3 or higher.
		if ( PHP_VERSION < 5.3 ) {
			add_action( 'admin_notices', 'buddyforms_php_version_admin_notice' );
		} else {
			// Init BuddyForms.
			$GLOBALS['buddyforms_new'] = new BuddyForms();
			// Init Freemius.
			buddyforms_core_fs();
			// Signal that parent SDK was initiated.
			do_action( 'buddyforms_core_fs_loaded' );
			// GDPR Admin Notice
			buddyforms_core_fs()->add_filter( 'handle_gdpr_admin_notice', '__return_true');

			if ( buddyforms_core_fs()->is__premium_only() ) {
				define( 'BUDDYFORMS_PRO_VERSION', 'pro' );
			}
		}
	}

	activate_buddyform_at_plugin_loader();
}
