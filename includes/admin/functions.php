<?php

/*
 * Freemius helper function to display individual go pro messages for the different arias of the admin ui
 */
function buddyforms_go_pro($h2 = '', $h4 = '', $pros = Array(), $link = true){
	echo buddyforms_get_go_pro( $h2, $h4, $pros, $link );
}
	function buddyforms_get_go_pro( $h2 = '', $h4 = '', $pros = Array(), $link = true ){
		if ( buddyforms_core_fs()->is_not_paying() ) {

			$tmp = '<div id="bf-gopro-sidebar">';
			$tmp .= !empty($h2) ? '<h2>' . $h2 . '</h2>' : '';
			$tmp .= '<div style="padding: 0 12px;">';
			$tmp .= !empty($h2) ? '<h4>' . $h4 . '</h4>' : '';
			$tmp .= '<ul>';
				foreach( $pros as $key => $pro ) {
					$tmp .= '<li>' . $pro . '</li>';
				}
			$tmp .= '</ul>';

			if($link)
				$tmp .= '<a class="buddyforms_get_pro button button-primary" href="' . buddyforms_core_fs()->get_upgrade_url() . '">' . __("Upgrade Now!", "buddyforms") . '</a>';

			$tmp .= '</div></div>';

			return $tmp;
		}
}

function buddyforms_version_type(){
	echo buddyforms_get_version_type();
}

function buddyforms_get_version_type(){
	if ( buddyforms_core_fs()->is__premium_only() ) {
		return  __('Pro', 'buddyforms');
	}
	return  __('Free', 'buddyforms');
}

function buddyforms_is_multisite(){
	if( is_multisite() ) {
		if ( apply_filters( 'buddyforms_enable_multisite', false ) ) {
			return true;
		}
	}
	return false;
}

function buddyforms_switch_to_form_blog($form_slug){
	global $buddyforms;

	// return if not a network install
	if( !buddyforms_is_multisite() ){
		return false;
	}

	// Check if the form has a blog id to switch to
	if ( isset( $buddyforms[$form_slug]['blog_id'] ) ) {
		switch_to_blog( $buddyforms[$form_slug]['blog_id'] );
		return true;
	}

	return false;
}
