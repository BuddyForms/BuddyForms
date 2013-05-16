<?php
class buddyforms {
	public $post_type_name;
	public $associated_item_tax_name;

	/**
	 * Initiate the class
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function __construct() {
		$this->init_hook();
		$this->load_constants();

		add_action('bp_include'				, array($this, 'includes')					, 4, 1);
		add_action('init'					, array($this, 'load_plugin_textdomain')	, 10, 1);
		add_action('bp_setup_globals'		, array($this, 'set_globals')				, 12, 1);
		add_action('admin_enqueue_scripts'	, array($this, 'buddyforms_admin_style')		, 1, 1);
		add_action('admin_enqueue_scripts'	, array($this, 'buddyforms_admin_js')			, 2, 1);
		
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

	public function set_globals() {
		global $buddyforms;

		$buddyforms = get_option('buddyforms_options');

		$form_element_hooks = array(
			'no'
		);
		
		$buddyforms[hooks][form_element] = $form_element_hooks;
		
		if (empty($buddyforms['selected_post_types']))
			return;

		foreach ($buddyforms['selected_post_types'] as $key => $value) {

			$post_type_object = get_post_type_object($value);

			if (empty($buddyforms['bp_post_types'][$value][name])) {
				$buddyforms['bp_post_types'][$value][name] = $post_type_object->labels->name;
				$buddyforms['bp_post_types'][$value][singular_name] = $post_type_object->labels->singular_name;
			}

			if (empty($buddyforms['bp_post_types'][$value][name])) {
				$buddyforms['bp_post_types'][$value][name] = $value;
				$buddyforms['bp_post_types'][$value][singular_name] = $value;
			}

			if (empty($buddyforms['bp_post_types'][$value][slug]))
				$buddyforms['bp_post_types'][$value][slug] = $value;

		}

	}

	/**
	 * Includes files needed by buddyforms
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */

	public function includes() {
		
		require_once (BUDDYFORMS_INCLUDES_PATH . 'PFBC/Form.php');
		require_once (BUDDYFORMS_INCLUDES_PATH . 'functions.php');
		require_once (BUDDYFORMS_INCLUDES_PATH . 'the-form.php');
		require_once (BUDDYFORMS_INCLUDES_PATH . 'member-extention.php');
		
		if (!class_exists('BP_Theme_Compat'))
			require_once (BUDDYFORMS_INCLUDES_PATH . 'bp-backwards-compatibililty-functions.php');

		if (is_admin())
			require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/admin.php');
		
	}

	/**
	 * Loads the textdomain for the plugin
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */

	public function load_plugin_textdomain() {
		load_plugin_textdomain('buddyforms', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	function buddyforms_admin_style($hook_suffix) {
			
		if($hook_suffix == 'toplevel_page_buddyforms_options_page') {
			
			wp_enqueue_style('buddyforms_admin_css', plugins_url('includes/admin/css/admin.css', __FILE__) );
			wp_enqueue_style('bootstrapcss', plugins_url('includes/PFBC/Resources/bootstrap/css/bootstrap.min.css', __FILE__));
			
		}
		
	}

	function buddyforms_admin_js($hook_suffix) {
	
		if($hook_suffix == 'toplevel_page_buddyforms_options_page') {
			
			wp_enqueue_script('buddyforms_admin_js', plugins_url('includes/admin/js/admin.js', __FILE__));
			wp_enqueue_script('bootstrapjs', plugins_url('includes/PFBC/Resources/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery') );
		    wp_enqueue_script('jQuery');
		    wp_enqueue_script('jquery-ui-sortable'); 
	
	    }
	
	}

}
