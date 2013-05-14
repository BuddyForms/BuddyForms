<?php
class CPT4BP {
	public $post_type_name;
	public $associated_item_tax_name;

	/**
	 * Initiate the class
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */
	public function __construct() {
		$this->init_hook();
		$this->load_constants();

		add_action('bp_include'			, array($this, 'includes')					, 4, 1);
		add_action('init'				, array($this, 'load_plugin_textdomain')	, 10, 1);
		add_action('bp_setup_globals'	, array($this, 'set_globals')				, 12, 1);
		
	}

	/**
	 * Defines cpt4bp_init action
	 *
	 * This action fires on WP's init action and provides a way for the rest of WP,
	 * as well as other dependent plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */

	public function init_hook() {
		do_action('cpt4bp_init');
	}

	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * These constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */

	public function load_constants() {
		
		if (!defined('CPT4BP_INSTALL_PATH'))
			define('CPT4BP_INSTALL_PATH', dirname(__FILE__) . '/');

		if (!defined('CPT4BP_INCLUDES_PATH'))
			define('CPT4BP_INCLUDES_PATH', CPT4BP_INSTALL_PATH . 'includes/');

		if (!defined('CPT4BP_TEMPLATE_PATH'))
			define('CPT4BP_TEMPLATE_PATH', CPT4BP_INCLUDES_PATH . 'templates/');
		
	}



	/**
	 * Setup all globals
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */

	public function set_globals() {
		global $cpt4bp;

		$cpt4bp = get_option('cpt4bp_options');

		$form_element_hooks = array(
			'no'
		);
		
		$cpt4bp[hooks][form_element] = $form_element_hooks;
		
		if (empty($cpt4bp['selected_post_types']))
			return;

		foreach ($cpt4bp['selected_post_types'] as $key => $value) {

			$post_type_object = get_post_type_object($value);

			if (empty($cpt4bp['bp_post_types'][$value][name])) {
				$cpt4bp['bp_post_types'][$value][name] = $post_type_object->labels->name;
				$cpt4bp['bp_post_types'][$value][singular_name] = $post_type_object->labels->singular_name;
			}

			if (empty($cpt4bp['bp_post_types'][$value][name])) {
				$cpt4bp['bp_post_types'][$value][name] = $value;
				$cpt4bp['bp_post_types'][$value][singular_name] = $value;
			}

			if (empty($cpt4bp['bp_post_types'][$value][slug]))
				$cpt4bp['bp_post_types'][$value][slug] = $value;

		}

	}

	/**
	 * Includes files needed by CPT4BP
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */

	public function includes() {
		
		require_once (CPT4BP_INCLUDES_PATH . 'PFBC/Form.php');
		require_once (CPT4BP_INCLUDES_PATH . 'functions.php');
		require_once (CPT4BP_INCLUDES_PATH . 'the-form.php');
		require_once (CPT4BP_INCLUDES_PATH . 'member-extention.php');
		
		if (!class_exists('BP_Theme_Compat'))
			require_once (CPT4BP_INCLUDES_PATH . 'bp-backwards-compatibililty-functions.php');

		if (is_admin())
			require_once (CPT4BP_INCLUDES_PATH . 'admin.php');
		
	}

	/**
	 * Loads the textdomain for the plugin
	 *
	 * @package CPT4BP
	 * @since 0.1-beta
	 */

	public function load_plugin_textdomain() {
		load_plugin_textdomain('cpt4bp', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

}
