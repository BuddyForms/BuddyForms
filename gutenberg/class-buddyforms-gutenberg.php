<?php
/**
 * @package WordPress
 * @subpackage BuddyForms
 * @author ThemeKraft Dev Team
 * @copyright 2018
 * @link http://www.themekraft.com
 * @license http://www.apache.org/licenses/
 */

/**
 * Class BuddyFormsGutenberg
 *
 * Handle the blocks for gutenberg integration
 */
class BuddyFormsGutenberg {
	public function __construct() {
		if ( function_exists( 'register_block_type' ) ) {
			add_action( 'init', array( $this, 'register_blocks' ) );
//			add_action( 'enqueue_block_assets', array( $this, 'register_block_assets' ) );
		}
	}

	public function register_block_assets() {
		wp_register_script(
			'buddyforms-gutenberg',
			path_join( BuddyForms::$gutenberg_url, 'hello/dist/bf-hello.js' ),
			array( 'wp-i18n', 'wp-blocks', 'wp-edit-post', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-api' ),
			filemtime( path_join( BuddyForms::$gutenberg_path, 'components/hello/dist/bf-hello.js' ) )
		);

		wp_register_style(
			'buddyforms-gutenberg',
			path_join( BuddyForms::$gutenberg_url, 'hello/bf-hello.css' ),
			array(),
			filemtime( path_join( BuddyForms::$gutenberg_path, 'components/hello/bf-hello.css' ) )
		);

		wp_register_style(
			'buddyforms-gutenberg-editor',
			path_join( BuddyForms::$gutenberg_url, 'hello/bf-hello-editor.css' ),
			array( 'wp-edit-blocks' ),
			filemtime( path_join( BuddyForms::$gutenberg_path, 'components/hello/bf-hello-editor.css' ) )
		);
	}

	public function register_blocks() {
		$this->register_block_assets();
		register_block_type( 'buddyforms/hello', array(
			'editor_script' => 'buddyforms-gutenberg',
			'editor_style'  => 'buddyforms-gutenberg-editor',
			'style'         => 'buddyforms-gutenberg'
		) );
	}
}