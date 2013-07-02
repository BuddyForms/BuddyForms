<?php
class BuddyForms {
	
	/**
	 * Initiate the class
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function __construct() {
		
		add_action('init'					, array($this, 'includes')					, 4, 1);
		add_action('init'					, array($this, 'load_plugin_textdomain')	, 10, 1);
		add_action('wp_init'				, array($this, 'set_globals')				, 12, 1);
		add_action('admin_enqueue_scripts'	, array($this, 'buddyforms_admin_style')	, 1, 1);
		add_action('admin_enqueue_scripts'	, array($this, 'buddyforms_admin_js')		, 2, 1);
		//add_action('wp_enqueue_scripts'		, array($this, 'buddyform_front_js')		, 2, 1);
		$this->init_hook();
		$this->load_constants();
		
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
	public function set_globals() {
		global $buddyforms;

		$buddyforms = get_option('buddyforms_options');
		
		
		$buddyforms['hooks']['form_element'] = array('no','before_the_title','after_the_title','before_the_content','after_the_content');
		
		if (empty($buddyforms['buddyforms']))
			return;
		
		$buddyforms = apply_filters('buddyforms_set_globals', $buddyforms);	
		
		foreach ($buddyforms['buddyforms'] as $key => $buddyform) {
				
				$slug = sanitize_title($buddyforms['buddyforms'][$key]['slug']);
				if($slug != $key){
					$buddyforms['buddyforms'][$slug] = $buddyforms['buddyforms'][$key];
					unset($buddyforms['buddyforms'][$key]);
					$buddyforms = apply_filters('buddyforms_set_globals_new_slug', $buddyforms, $slug, $key);	
				}
				
		}
	
	}

	/**
	 * Include files needed by BuddyForms
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function includes() {
		
		require_once (BUDDYFORMS_INCLUDES_PATH . 'form-builder/Form.php');
		require_once (BUDDYFORMS_INCLUDES_PATH . 'functions.php');
		require_once (BUDDYFORMS_INCLUDES_PATH . 'the-form.php');
		require_once (BUDDYFORMS_INCLUDES_PATH . 'revisions.php');
		require_once (BUDDYFORMS_INCLUDES_PATH . 'shortcodes.php');
		
		if (is_admin()){
			require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/admin.php');
			require_once (BUDDYFORMS_INCLUDES_PATH . '/admin/admin-ajax.php');
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
			
		if($hook_suffix == 'toplevel_page_buddyforms_options_page') {
				
			wp_enqueue_style('buddyforms_admin_css', plugins_url('includes/admin/css/admin.css', __FILE__) );
			wp_enqueue_style('bootstrapcss', plugins_url('includes/admin/css/bootstrap.css', __FILE__));
			wp_enqueue_style('buddyforms_zendesk_css', '//assets.zendesk.com/external/zenbox/v2.6/zenbox.css' );
			
		
		}
		
	}

	/**
	 * Enqueue the needed JS for the admin screen
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	function buddyforms_admin_js($hook_suffix) {
	
		if($hook_suffix == 'toplevel_page_buddyforms_options_page') {

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
	function buddyform_front_js() {
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
	
		wp_enqueue_script( 'buddyforms-form',  plugins_url('includes/js/buddyforms.js', __FILE__), array('jquery') );
	
	}
}