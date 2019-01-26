<?php

require_once( BUDDYFORMS_INCLUDES_PATH . 'gutenberg/shortcodes/shortcodes-to-blocks.php' );



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