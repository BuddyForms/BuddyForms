<?php
/*
Plugin Name: PHP Block
Description: A sample PHP rendered block, showing how to convert a shortcode to a block.
Author: Gary Pendergast
Version: 0.1
Author URI: https://buddyforms.net/
License: GPLv2+
*/

/*
 * Here's a little sample plugin that shows how to easily convert an existing shortcode
 * to be a server-side rendered block. This lets you get your existing plugin functionality
 * running in the block editor as quickly as possible, you can always go back later and
 * improve the UX.
 *
 * In this case, we have an imaginary shortcode, [php_block], which accepts one argument, 'foo'.
 * This shortcode would be used like so:
 *
 * [php_block foo=abcde]
 *
 * Because the block editor uses the same function signature when doing server-side rendering, we
 * can reuse our entire shortcode logic when creating the block.
 */



/**
 * Register our block and shortcode.
 */
function php_block_init() {
	// Register our block editor script.
	wp_register_script(
		'php-block',
		plugins_url( 'shortcodes-to-blocks.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
	);

	// Register our block, and explicitly define the attributes we accept.
	register_block_type( 'buddyforms/php-block', array(
		'attributes'      => array(
			'foo' => array(
				'type' => 'string',
			),
		),
		'editor_script'   => 'php-block', // The script name we gave in the wp_register_script() call.
		'render_callback' => 'php_block_render',
	) );

	// Define our shortcode, too, using the same render function as the block.
	add_shortcode( 'php_block', 'php_block_render' );
}
add_action( 'init', 'php_block_init' );

/**
 * Our combined block and shortcode renderer.
 *
 * For more complex shortcodes, this would naturally be a much bigger function, but
 * I've kept it brief for the sake of focussing on how to use it for block rendering.
 *
 * @param array $attributes The attributes that were set on the block or shortcode.
 */
function php_block_render( $attributes ) {
	return '<p>Laver ' . print_r( $attributes, true ) . '</p>';
}
