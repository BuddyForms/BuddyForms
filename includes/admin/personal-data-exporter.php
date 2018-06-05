<?php
function buddyforms_exporter( $email_address, $page = 1 ) {
	global $buddyforms;

	$number = 500; // Limit us to avoid timing out
	$page = (int) $page;

	foreach ($buddyforms as $form_slug => $buddyform ){

		$query_args = array(
			'post_type'      => $buddyform['post_type'],
			'author_email'   => $email_address,
			'paged'          => $page,
		);

		$the_query = new WP_Query( $query_args );



		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$data[get_the_ID()] = get_the_title();

			}

			wp_reset_postdata();
		}

		$item_id = "buddyform-{$buddyform['slug']}";

		$export_items[] = array(
			'group_id' => $buddyform['slug'],
			'group_label' => $buddyform['name'],
			'item_id' => $item_id,
			'data' => $data,
		);
	}

	return array(
		'data' => $export_items,
		'done' => $done,
	);
}

function buddyforms_register_exporter( $exporters ) {
	$exporters['buddyforms'] = array(
		'exporter_friendly_name' => __( 'BuddyForms', 'buddyforms' ),
		'callback' => 'buddyforms_exporter',
	);
	return $exporters;
}

add_filter(
	'wp_privacy_personal_data_exporters',
	'buddyforms_register_exporter',
	10
);