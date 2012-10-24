<?php
/*
 * Loader script for the themekraft framework
 * Just include this script to load the framework
 */

// If Framework in same version is not existing
if( !function_exists( 'tkf_init_011' ) ){
	global $tkf_version;
	
	$this_tkf_version = '0.1.1';
	
	// Initialize function of this version which have to be have hooked
	function tkf_init_011(){
		require( 'core.php' );
	}
	// If there is already a framework started check version
	if( $tkf_version != '' ){
			
		// If started framework version is older than this version, remove action from init actionhook
		if( version_compare( $tkf_version, $this_tkf_version, '<' ) ){

			// Add own action to actionhook
			$tkf_version = $this_tkf_version;
			$init_function_name = 'tkf_init_' . str_replace( '.', '', $this_tkf_version );
			call_user_func ( $init_function_name );
		}
	}else{
		$tkf_version = $this_tkf_version;
		$init_function_name = 'tkf_init_' . str_replace( '.', '', $this_tkf_version );
		call_user_func ( $init_function_name );
	}
}