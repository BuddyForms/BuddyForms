<?php
/*
Plugin Name: Custom Group Types
Plugin URI: http://themekraft.com
Description: Attach groups to custom posts types and benefit from all the goodness WordPress brings to the custom post type and taxonomy sytem. This means you will have not only caching and benefit from plenty of plugins available. With custom group types you will be able to use group categories, tags with custom taxonomies to sort and display your groups in their different ways.
Version: 0.1 alpha
Author: Sven Lehnert
Author URI: http://themekraft.com
Licence: GPLv3
Network: true
*/

define( 'BP_Custom_Group_Types_VERSION', '0.1' );

/**
 * Loads Custom Group Types files only if BuddyPress is present
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function bp_cgt_init() {
	global $wpdb;
	
	if( is_multisite() && BP_ROOT_BLOG != $wpdb->blogid )
		return;

	require( dirname( __FILE__ ) .'/bp-cgt.php' );
	$bp_cgt = new BP_CGT;    
}
add_action( 'bp_loaded', 'bp_cgt_init', -9999 );