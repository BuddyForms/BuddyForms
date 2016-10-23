<?php

/*
 * Freemius helper function to display individual go pro messages for the different arias of the admin ui
 */
/**
 * @param string $h2
 * @param string $h4
 * @param array $pros
 * @param bool $link
 */
function buddyforms_go_pro( $h2 = '', $h4 = '', $pros = Array(), $link = true ) {
	echo buddyforms_get_go_pro( $h2, $h4, $pros, $link );
}

/**
 * @param string $h2
 * @param string $h4
 * @param array $pros
 * @param bool $link
 *
 * @return string
 */
function buddyforms_get_go_pro( $h2 = '', $h4 = '', $pros = Array(), $link = true ) {
	if ( buddyforms_core_fs()->is_not_paying() ) {

		$tmp = '<div id="bf-gopro-sidebar">';
		$tmp .= ! empty( $h2 ) ? '<h2>' . $h2 . '</h2>' : '';
		$tmp .= '<div style="padding: 0 12px;">';
		$tmp .= ! empty( $h2 ) ? '<h4>' . $h4 . '</h4>' : '';
		$tmp .= '<ul>';
		foreach ( $pros as $key => $pro ) {
			$tmp .= '<li>' . $pro . '</li>';
		}
		$tmp .= '</ul>';

		if ( $link ) {
			$tmp .= '<a class="buddyforms_get_pro button button-primary" href="' . buddyforms_core_fs()->get_upgrade_url() . '">' . __( "Upgrade Now!", "buddyforms" ) . '</a>';
		}

		$tmp .= '</div></div>';

		return $tmp;
	}
}

function buddyforms_version_type() {
	echo buddyforms_get_version_type();
}

/**
 * @return string|void
 */
function buddyforms_get_version_type() {
	if ( buddyforms_core_fs()->is__premium_only() ) {
		return __( 'Pro', 'buddyforms' );
	}

	return __( 'Free', 'buddyforms' );
}
