<?php
 /**
  * Wrapper for checked() if value is an array
  * 
  * @package BuddyPress Custom Group Types
  * @since 0.1-beta	
  */
function filter_checked( $cat_name ){
 	if( isset( $_GET['filter'] ) ){
		$filters = (array) $_GET['filter'];

		foreach( $filters as $filter ){
			$check = checked( $filter, $cat_name, false );
		}
	}
	
	return $check;
}