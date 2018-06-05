<?php

function my_plugin_exporter( $email_address, $page = 1 ) {
	$number = 500; // Limit us to avoid timing out
	$page = (int) $page;

	$export_items = array();

	$comments = get_comments(
		array(
			'author_email' => $email_address,
			'number' => $number,
			'paged' => $page,
			'order_by' => 'comment_ID',
			'order' => 'ASC',
		)
	);

	foreach ( (array) $comments as $comment ) {
		$latitude = get_comment_meta( $comment->comment_ID, 'latitude', true );
		$longitude = get_comment_meta( $comment->comment_ID, 'longitude', true );

		// Only add location data to the export if it is not empty
		if ( ! empty( $latitude ) ) {
			// Most item IDs should look like postType-postID
			// If you don't have a post, comment or other ID to work with,
			// use a unique value to avoid having this item's export
			// combined in the final report with other items of the same id
			$item_id = "comment-{$comment->comment_ID}";

			// Core group IDs include 'comments', 'posts', etc.
			// But you can add your own group IDs as needed
			$group_id = 'comments';

			// Optional group label. Core provides these for core groups.
			// If you define your own group, the first exporter to
			// include a label will be used as the group label in the
			// final exported report
			$group_label = __( 'Comments' );

			// Plugins can add as many items in the item data array as they want
			$data = array(
				array(
					'name' => __( 'Commenter Latitude' ),
					'value' => $latitude
				),
				array(
					'name' => __( 'Commenter Longitude' ),
					'value' => $longitude
				)
			);

			$export_items[] = array(
				'group_id' => $group_id,
				'group_label' => $group_label,
				'item_id' => $item_id,
				'data' => $data,
			);
		}
	}

	// Tell core if we have more comments to work on still
	$done = count( $comments ) < $number;
	return array(
		'data' => $export_items,
		'done' => $done,
	);
}


function register_my_plugin_exporter( $exporters ) {
	$exporters['my-plugin-slug'] = array(
		'exporter_friendly_name' => __( 'Comment Location Plugin' ),
		'callback' => 'my_plugin_exporter',
	);
	return $exporters;
}

add_filter(
	'wp_privacy_personal_data_exporters',
	'register_my_plugin_exporter',
	10
);