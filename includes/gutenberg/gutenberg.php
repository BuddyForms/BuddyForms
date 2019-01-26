<?php

require_once( BUDDYFORMS_INCLUDES_PATH . 'gutenberg/shortcodes/shortcodes-to-blocks.php' );


// Add Gutenberg block category "BuddyForms"
function buddyforms_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'buddyforms',
				'title' => __( 'BuddyForms', 'buddyforms' ),
			),
		)
	);
}
add_filter( 'block_categories', 'buddyforms_block_category', 10, 2);

// Load all the assets needed.
function buddyforms_editor_assets() {
	global $GLOBALS;
	$GLOBALS['buddyforms_new']->front_js_css();
};

add_action( 'enqueue_block_editor_assets', 'buddyforms_editor_assets');