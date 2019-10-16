<?php

/**
 * Plugin Name: BuddyForms
 * Plugin URI:  https://themekraft.com/buddyforms/
 * Description: Contact Forms, Post Forms for User Generated Content and Registration Forms easily build in minutes. Step by step with an easy to use Form Wizard. Ideal for User Submitted Posts. Extendable with Addons!
 * Version: 2.5.8
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/buddyforms/
 * Licence: GPLv3
 * Network: false
 * Text Domain: buddyforms
 * Svn: buddyforms
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
		public $version = '2.5.8';

		/**
		 * @var array Frontend Global JS parameters
		 */
		private static $global_js_parameters;

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

			add_action( 'init', array( $this, 'init_hook' ), 1, 1 );
			require_once( BUDDYFORMS_INCLUDES_PATH . '/form/form-assets.php' );
			new BuddyFormsAssets();
			add_action( 'init', array( $this, 'includes' ), 4, 1 );
			add_action( 'init', array( $this, 'update_db_check' ), 10 );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

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

			if ( ! defined( 'BUDDYFORMS_ADMIN_VIEW' ) ) {
				/**
				 * Define the template path
				 */
				define( 'BUDDYFORMS_ADMIN_VIEW', BUDDYFORMS_INCLUDES_PATH . 'admin/view/' );
			}

			if ( ! defined( 'BUDDYFORMS_ASSETS' ) ) {
				/**
				 * Define the template path
				 */
				define( 'BUDDYFORMS_ASSETS', plugins_url( 'assets/', __FILE__ ) );
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
		 * Enable the localization of the fields adding the strings to js and hold the global localization
		 *
		 * @return array
		 */
		public static function localize_fields() {
			return apply_filters( 'buddyforms_field_localization', array(
				'error_strings' => array(
					'error_string_start'    => __( 'The following', 'buddyforms' ),
					'error_string_singular' => __( 'error was', 'buddyforms' ),
					'error_string_plural'   => __( 'errors were', 'buddyforms' ),
					'error_string_end'      => __( 'found: ', 'buddyforms' ),
				)
			) );
		}

		/**
		 * Hold the global variables to put in the frontend
		 *
		 * @param $array
		 *
		 * @return array
		 */
		public static function buddyforms_js_global_set_parameters( $array ) {
			if ( ! empty( self::$global_js_parameters ) ) {
				self::$global_js_parameters = array_merge( $array, self::$global_js_parameters );
			} else {
				self::$global_js_parameters = $array;
			}

			return self::$global_js_parameters;
		}

		/**
		 * Get the global variables to put in the frontend
		 *
		 * @param string $form_slug
		 *
		 * @return array
		 */
		public static function buddyforms_js_global_get_parameters( $form_slug = '' ) {
			return apply_filters( 'buddyforms_js_parameters', self::$global_js_parameters, $form_slug );
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

				$global_error = ErrorHandler::get_instance();
			}

			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/class-bf-admin-notices.php' );
			new BfAdminNotices();

			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/register-post-types.php' );

			//Compatibility
			require_once( BUDDYFORMS_INCLUDES_PATH . 'compatibility.php' );

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

			// Gutenberg
			require_once( BUDDYFORMS_INCLUDES_PATH . 'gutenberg/gutenberg.php' );

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
		 * Plugin activation
		 * @since  2.0
		 */
		function plugin_activation() {

			$title        = apply_filters( 'buddyforms_preview_page_title', __( 'BuddyForms Preview Page', 'buddyforms' ) );
			$preview_page = get_page_by_title( $title );
			if ( ! $preview_page ) {
				// Create preview page object
				$preview_post = array(
					'post_title'   => $title,
					'post_content' => __( 'This is a preview of how this form will appear on your website', 'buddyforms' ),
					'post_status'  => 'draft',
					'post_type'    => 'page',
					'post_name'    => sanitize_title( 'BuddyForms Preview Page' )
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
				'id'              => '391',
				'slug'            => 'buddyforms',
				'type'            => 'plugin',
				'public_key'      => 'pk_dea3d8c1c831caf06cfea10c7114c',
				'is_premium'      => true,
				'has_addons'      => true,
				'has_paid_plans'  => true,
				'trial'           => array(
					'days'               => 14,
					'is_require_payment' => true,
				),
				'has_affiliation' => false,
				'menu'            => array(
					'slug'       => 'edit.php?post_type=buddyforms',
					'first-path' => $first_path,
					'support'    => false,
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
			buddyforms_core_fs()->add_filter( 'handle_gdpr_admin_notice', '__return_true' );

			if ( buddyforms_core_fs()->is__premium_only() ) {
				define( 'BUDDYFORMS_PRO_VERSION', 'pro' );
			}
		}
	}

	activate_buddyform_at_plugin_loader();
}
