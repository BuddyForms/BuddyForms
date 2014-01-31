<?php

/**
 *
 * @package Update API Manager
 * @author Todd Lahman LLC
 * @copyright   Copyright (c) 2011-2013, Todd Lahman LLC
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Buddyforms_Key_Registration_Menu {

	private $buddyforms_key;
	private $plugin_url;

	// Load admin menu
	public function __construct() {

		$this->buddyforms_key = new Buddyforms_Key();

		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'load_settings' ) );
	}

	public function plugin_url() {
		if ( isset( $this->plugin_url ) ) return $this->plugin_url;
		return $this->plugin_url = plugins_url( '/', dirname(__FILE__) );
	}

	// Add option page menu
	public function add_menu() {

		$page = add_submenu_page( 'buddyforms_options_page', 'BuddyForms', 'License Activation', 'manage_options', 'license_registration_dashboard', array( $this, 'config_page') );
		add_action( 'admin_print_styles-' . $page, array( $this, 'css_scripts' ) );
	}

	// Draw option page
	public function config_page() {
		$settings_tabs = array( 'license_registration_dashboard' => 'Activate License', 'buddyforms_deactivation' => 'License Deactivation' );
		$current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'license_registration_dashboard';
		$tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'license_registration_dashboard';
		?>
		<div class='wrap'>
			<?php screen_icon(); ?>
			<h2><?php _e( 'BuddyForms License Activation', 'buddyforms' ); ?></h2>

			<h2 class="nav-tab-wrapper">
			<?php
				foreach ( $settings_tabs as $tab_page => $tab_name ) {
					$active_tab = $current_tab == $tab_page ? 'nav-tab-active' : '';
					echo '<a class="nav-tab ' . $active_tab . '" href="?page=license_registration_dashboard&tab=' . $tab_page . '">' . $tab_name . '</a>';
				}
			?>
			</h2>
				<form action='options.php' method='post'>
				<div class="main">
			<?php
				if( $tab == 'license_registration_dashboard' ) {
						settings_fields( 'bf_license_manager' );
						do_settings_sections( 'license_registration_dashboard' );
							$save_changes = __( 'Save Changes', 'buddyforms' );
							submit_button( $save_changes );
				} else {
						settings_fields( 'buddyforms_deactivate_checkbox' );
						do_settings_sections( 'buddyforms_deactivation' );
							$save_changes_activation = __( 'Save Changes', 'buddyforms' );
							submit_button( $save_changes_activation );
				}

	}

	// Register settings
	public function load_settings() {
		register_setting( 'bf_license_manager', 'bf_license_manager', array( $this, 'validate_options' ) );

		// API Key
		add_settings_section( 'api_key', 'License Information', array( $this, 'bf_api_key_text' ), 'license_registration_dashboard' );
		add_settings_field( 'api_key', 'API License Key', array( $this, 'bf_api_key_field' ), 'license_registration_dashboard', 'api_key' );
		add_settings_field( 'api_email', 'License email', array( $this, 'bf_api_email_field' ), 'license_registration_dashboard', 'api_key' );

		// Activation settings
		register_setting( 'buddyforms_deactivate_checkbox', 'buddyforms_deactivate_checkbox', array( $this, 'bf_license_key_deactivation' ) );
		add_settings_section( 'deactivate_button', 'Plugin License Deactivation', array( $this, 'bf_deactivate_text' ), 'buddyforms_deactivation' );
		add_settings_field( 'deactivate_button', 'Deactivate Plugin License', array( $this, 'bf_deactivate_textarea' ), 'buddyforms_deactivation', 'deactivate_button' );

	}

	// Provides text for api key section
	public function bf_api_key_text() {
		//
	}

	// Outputs API License text field
	public function bf_api_key_field() {

		$options = get_option( 'bf_license_manager' );
		$api_key = $options['api_key'];
		echo "<input id='api_key' name='bf_license_manager[api_key]' size='25' type='text' value='{$options['api_key']}' />";
		if ( !empty( $options['api_key'] ) ) {
			echo "<span class='icon-pos'><img src='" . $this->plugin_url() . "resources/api-manager/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . $this->plugin_url() . "resources/api-manager/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Outputs API License email text field
	public function bf_api_email_field() {

		$options = get_option( 'bf_license_manager' );
		$activation_email = $options['activation_email'];
		echo "<input id='activation_email' name='bf_license_manager[activation_email]' size='25' type='text' value='{$options['activation_email']}' />";
		if ( !empty( $options['activation_email'] ) ) {
			echo "<span class='icon-pos'><img src='" . $this->plugin_url() . "resources/api-manager/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . $this->plugin_url() . "resources/api-manager/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Sanitizes and validates all input and output for Dashboard
	public function validate_options( $input ) {

		// Load existing options, validate, and update with changes from input before returning
		$options = get_option( 'bf_license_manager' );

		$options['api_key'] = trim( $input['api_key'] );
		$options['activation_email'] = trim( $input['activation_email'] );

		/**
		  * Plugin Activation
		  */
		$api_email = trim( $input['activation_email'] );
		$api_key = trim( $input['api_key'] );

		$activation_status = get_option( 'buddyforms_activated' );
		$checkbox_status = get_option( 'buddyforms_deactivate_checkbox' );

		$current_api_key = $this->get_key();

		if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key  ) {

			/**
			 * If this is a new key, and an existing key already exists in the database,
			 * deactivate the existing key before activating the new key.
			 */
			if ( $current_api_key != $api_key )
				$this->replace_license_key( $current_api_key );

			$args = array(
				'email' => $api_email,
				'licence_key' => $api_key,
				);

			$activate_results = $this->buddyforms_key->activate( $args );

			$activate_results = json_decode($activate_results, true);

			if ( $activate_results['activated'] == true ) {
				add_settings_error( 'activate_text', 'activate_msg', "Plugin activated. {$activate_results['message']}.", 'updated' );
				update_option( 'buddyforms_activated', 'Activated' );
				update_option( 'buddyforms_deactivate_checkbox', 'off' );
			}

			if ( $activate_results == false ) {
				add_settings_error( 'api_key_check_text', 'api_key_check_error', "Connection failed to the License Key API server. Try again later.", 'error' );
				$options['api_key'] = '';
				$options['activation_email'] = '';
				update_option( 'buddyforms_activated', 'Deactivated' );
			}

			if ( isset( $activate_results['code'] ) ) {

				switch ( $activate_results['code'] ) {
					case '100':
						add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options['activation_email'] = '';
						$options['api_key'] = '';
						update_option( 'buddyforms_activated', 'Deactivated' );
					break;
					case '101':
						add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options['api_key'] = '';
						$options['activation_email'] = '';
						update_option( 'buddyforms_activated', 'Deactivated' );
					break;
					case '102':
						add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options['api_key'] = '';
						$options['activation_email'] = '';
						update_option( 'buddyforms_activated', 'Deactivated' );
					break;
					case '103':
							add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'buddyforms_activated', 'Deactivated' );
					break;
					case '104':
							add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'buddyforms_activated', 'Deactivated' );
					break;
					case '105':
							add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}", 'error' );
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'buddyforms_activated', 'Deactivated' );
					break;
					case '106':
							add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'buddyforms_activated', 'Deactivated' );
					break;
				}

			}

		} // End Plugin Activation

		return $options;
	}

	public function get_key() {
		$bf_options = get_option('bf_license_manager');
		$api_key = $bf_options['api_key'];

		return $api_key;
	}

	// Deactivate the current license key before activating the new license key
	public function replace_license_key( $current_api_key ) {

		$default_options = get_option( 'bf_license_manager' );

		$api_email = $default_options['activation_email'];

		$args = array(
			'email' => $api_email,
			'licence_key' => $current_api_key,
			);

		$reset = $this->buddyforms_key->deactivate( $args ); // reset license key activation

		if ( $reset == true )
			return true;

		return add_settings_error( 'not_deactivated_text', 'not_deactivated_error', "The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.", 'updated' );;
	}

	// Deactivates the license key to allow key to be used on another blog
	public function bf_license_key_deactivation( $input ) {

		$activation_status = get_option( 'buddyforms_activated' );

		$default_options = get_option( 'bf_license_manager' );

		$api_email = $default_options['activation_email'];
		$api_key = $default_options['api_key'];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);

		$options = ( $input == 'on' ? 'on' : 'off' );

		if ( $options == 'on' && $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
			$reset = $this->buddyforms_key->deactivate( $args ); // reset license key activation

			if ( $reset == true ) {
				$update = array(
					'api_key' => '',
					'activation_email' => ''
					);
				$merge_options = array_merge( $default_options, $update );

				update_option( 'bf_license_manager', $merge_options );

				add_settings_error( 'bf_deactivate_text', 'deactivate_msg', "Plugin license deactivated.", 'updated' );

				return $options;
			}

		} else {

			return $options;
		}

	}

	public function bf_deactivate_text() {
	}

	public function bf_deactivate_textarea() {

		$activation_status = get_option( 'buddyforms_deactivate_checkbox' );

		?>
		<input type="checkbox" id="buddyforms_deactivate_checkbox" name="buddyforms_deactivate_checkbox" value="on" <?php checked( $activation_status, 'on' ); ?> />
		<span class="description"><?php _e( 'Deactivates plugin license so it can be used on another blog.', 'buddyforms' ); ?></span>
		<?php
	}

	// Loads admin style sheets
	public function css_scripts() {

		$curr_ver = get_option('bf_version');
		wp_register_style( 'bf-admin-css', $this->plugin_url() . 'resources/api-manager/assets/css/admin-settings.css', array(), $curr_ver, 'all');
		wp_enqueue_style( 'bf-admin-css' );
	}

}

$buddyforms_key_registration_menu = new Buddyforms_Key_Registration_Menu();
