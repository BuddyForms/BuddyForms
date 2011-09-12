<?php
/*
Plugin Name: Custom Group Types
Plugin URI: http://themekraft.com
Description: Atache Groups to custom posts types and parcitipate from all the goodness wordpress brings to the custom post types and taxonomy sytem. This means you will have not only caching and benefite fromm planty of plugins available. With custom group types you will be able to use group categories, tags with custom taxonomies to sort and display you groups in there different ways.
Version: 0.1-beta
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
	global $bp_docs, $wpdb;
	
	if ( is_multisite() && BP_ROOT_BLOG != $wpdb->blogid )
		return;

	require( dirname( __FILE__ ) . '/bp-cgt.php' );
	$bp_cgt = new BP_CGT;
}
add_action( 'bp_include', 'bp_cgt_init' );
?>
