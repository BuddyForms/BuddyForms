<?php

/*
 * Freemius helper function to display individual go pro messages for the different arias of the admin ui
 */
function buddyforms_go_pro($h2 = '', $h4 = '', $pros = Array(), $link = true){
	echo buddyforms_get_go_pro( $h2, $h4, $pros, $link );
}
	function buddyforms_get_go_pro( $h2 = '', $h4 = '', $pros = Array(), $link = true ){
		if ( buddyforms_core_fs()->is_not_paying() ) {

			$tmp = !empty($h2) ? '<h2>' . $h2 . '</h2>' : '';
			$tmp .= !empty($h2) ? '<h4>' . $h4 . '</h4>' : '';
			$tmp .= '<ul>';
				foreach( $pros as $key => $pro ) {
					$tmp .= '<li>' . $pro . '</li>';
				}
			$tmp .= '</ul>';

			if($link)
				$tmp .= '<a class="buddyforms_get_pro" href="' . buddyforms_core_fs()->get_upgrade_url() . '">' . __("Upgrade Now!", "buddyforms") . '</a>';

			return $tmp;
		}
}

function buddyforms_you_are_pro(){
	echo buddyforms_get_you_are_pro();
}

function buddyforms_get_you_are_pro(){
	if ( buddyforms_core_fs()->is__premium_only() ) {
		return '<span class="you-are-pro">' . __('You are a Pro user !', 'buddyforms') . '</span>';
	}
}
