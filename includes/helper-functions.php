<?php 

/**
 * Delete a product post
 * 
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta	
 */
function cpt4bp_delete_product_post( $group_id ){    
    $groups_post_id = groups_get_groupmeta( $group_id, 'group_post_id' );
    
    wp_delete_post( $groups_post_id );
}
add_action( 'groups_before_delete_group', 'cpt4bp_delete_product_post' );

/**
 * Locate a template
 * 
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta	
 */
function cpt4bp_locate_template( $file ) {
	if( locate_template( array( $file ), false ) ) {
		locate_template( array( $file ), true );
	} else {
		include( CPT4BP_TEMPLATE_PATH .$file );
	}
}

function cpt4bp_group_extension_link(){
	global $bp;
	echo bp_group_permalink().$bp->current_action.'/';
}

/**
 * Clean the input by type
 * 
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta	
 */
function cpt4bp_app_clean_input( $input, $type ) {
	global $allowedposttags;
	
    $cleanInput = false;
    
    switch( $type ) {
		case 'text':
			$cleanInput = wp_filter_nohtml_kses( $input );
	        break;
			
        case 'checkbox':
            $input === '1'? $cleanInput = '1' : $cleanInput = '';
        	break;
			
		case 'html':
            $cleanInput = wp_kses( $input, $allowedposttags );
        	break;
			
    	default:
        	$cleanInput = false;
        	break;
    }
	
    return $cleanInput;
}

?>