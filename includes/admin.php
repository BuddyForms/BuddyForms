<?php

/**
 * Create "CGT Options" sub nav menu under the Buddypress main admin nav
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
add_action('admin_menu', 'cgt_create_menu');
function cgt_create_menu() {

	if ( !is_super_admin() )
		return false;
	
	//create new top-level menu
	add_submenu_page('bp-general-settings', 'CGT Options', 'CGT Options', 'manage_options', 'cgt-options', 'cgt_settings_page' ); 
	
	//call register settings function
	add_action( 'admin_init', 'cgt_register_settings' );
}

/**
 * Register the settings needed for the option page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cgt_register_settings() {
	global $bp;
	
	//register the settings
	register_setting( 'cgt_options', 'cgt_existing_types' );
	register_setting( 'cgt_options', 'cgt_new_types' );

	
	register_setting( 'cgt_options', 'cgt_custom_fields' );
	
	foreach($bp->bp_cgt->cgt_custom_fields as $custom_field ) :
 	    register_setting( 'cgt_options', $custom_field.'_custom_fields' ); 	
    endforeach;       
 
	
	foreach($bp->bp_cgt->new_post_types as $new_post_type ) :
 	    register_setting( 'cgt_options', $new_post_type.'_name' ); 	
    endforeach;       
 
    foreach($bp->bp_cgt->existing_post_types as $existing_post_type ) :
 	    register_setting( 'cgt_options', $existing_post_type.'_name' ); 	
    endforeach;       
 
}

/**
 * Create the option settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cgt_settings_page() {
	global $bp;
	
	// Create the Array for the jQuery Taps 
    $TapArray = array();
    
    // Get the plugin option arrays
    $cgt_existing_types = $bp->bp_cgt->existing_post_types;
    $cgt_new_types = $bp->bp_cgt->new_post_types;
	$cgt_custom_fields = $bp->bp_cgt->cgt_custom_fields;
	
	$cgt_types = array_merge($cgt_existing_types, $cgt_new_types);
	
	print_r($cgt_types);
	//print_r($cgt_custom_fields);
	
	// Get all post types
	$args=array(
	  '_builtin' => false
	); 
	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
	$post_types=get_post_types($args,$output,$operator); 
	
	// Unset all postipes created by CGT
	foreach($post_types as $key => $value) {
		if(array_key_exists($key, $cgt_new_types)) {
			unset($post_types[$key]);
		}
	}
	?>
	<div class="wrap">
	<h2>Custom Group Types Setup</h2>
	
	<SCRIPT language="javascript">
	function add_new_type() {
		 
	    //Create an input type dynamically.
	    var element = document.createElement("input");
	 
	    //Assign different attributes to the element.
	    element.setAttribute("type", 'text');
	    element.setAttribute("value", '');
	    element.setAttribute("name", 'cgt_new_types[]');
	 
	    var foo = document.getElementById("newtype");
	 
	    //Append the element in page (in span).
	    foo.appendChild(element);
	 
	}
	function add_new_field(new_post_type) {

		 alert(new_post_type);
	    //Create an input type dynamically.
	    var element = document.createElement("input");
	 
	    //Assign different attributes to the element.
	    element.setAttribute("type", 'text');
	    element.setAttribute("value", '');
	    element.setAttribute("name", 'cgt_custom_fields['+new_post_type+'][]');
	    element.id = 'newfield_'+new_post_type;
	    var foo = document.getElementById(element.id);
	 
	    //Append the element in page (in span).
	    foo.appendChild(element);
	 
	}
	</SCRIPT>
	
	<form method="post" action="options.php">
	    <?php settings_fields( 'cgt_options' ); ?>
	    <?php 
	    $tab1 .= '<table class="form-table">';

			$tab1 .= '<tr valign="top">';
		 	$tab1 .= '<th scope="row">Select Existing Posts Type to use as Custom Group Typeps.</th>';
		 	$tab1 .= '<th scope="row">Create new post types to use as Custom Group Typeps.</th>';
			$tab1 .= '</tr>';
		 	
			$tab1 .= '<tr><td style="vertical-align: top;">';
			
	 		foreach ($post_types  as $post_type ) {
	 		
				if( $post_type == $cgt_existing_types[$post_type])
					$checked = 'checked="checked"';
					
				$tab1 .= '<input type="checkbox" '.$checked.' name="cgt_existing_types['.$post_type.']" value="'.$post_type.'">'.$post_type.'<br>';
			}

			$tab1 .= '</td><td>';
			if(!empty($cgt_new_types)){
				foreach ($cgt_new_types  as $post_type ) {
					if($post_type != '')
						$tab1 .= '<input type="text" name="cgt_new_types['.$post_type.']" value="'. $post_type .'" /><br>';
				}
			}
			$tab1 .= '<span id="newtype">&nbsp;</span><br><INPUT type="button" value="Add one more!" onclick="add_new_type()"/>';

			$tab1 .= '</td></tr>';
		
		$tab1 .= '</table>';
		echo $tab1;
		$TapArray[] = array(
				'id' => 1,
				'title' => 'General Settings',
				'content' => $tab1	
				);
		if(!empty($cgt_types)){
			foreach($cgt_types as $new_post_type ) :
				if($new_post_type != '') {
				    $tabs .= '<table class="form-table">';
						$tabs .= '<tr valign="top">';
							$tabs .= '<th scope="row">Group Tap Name for '.$new_post_type.' </th>';
							$new_post_type_id = str_replace(' ', '', $new_post_type);
							$tabs .= '<td><input type="text" name="'.$new_post_type.'_name'.'" value="'.get_option($new_post_type.'_name').'" /></td>';
						$tabs .= '</tr>';
				    
				    	$tabs .= '<tr valign="top">';
							$tabs .= '<th scope="row">Custom fields '.$new_post_type.' </th><td>';
							$new_post_type_id = str_replace(' ', '', $new_post_type);
							if(!empty($cgt_custom_fields[$new_post_type])){
							foreach($cgt_custom_fields[$new_post_type] as $custom_field ) {
								$tabs .= '<input type="text" name="cgt_custom_fields['.$new_post_type.'][]" value="'.$custom_field.'" /><br>';
							}
							}
							$tabs .= '<span id="newfield_'.$new_post_type.'">&nbsp;</span><br><INPUT type="button" value="Add one more!" onclick="add_new_field(\''.$new_post_type.'\')"/>';
							
							$tabs .= '</td></tr>';
				    
				    
				    
				    $tabs .= '</table>';
				    
				    
				    
				   echo $tabs;
				    
				    
				    
				    
				//     $tabs = tk_wp_jquery_accordion( $tabs );
				    $TapArray[] = array(
							'id' => $new_post_type_id,
							'title' => $new_post_type,
							'content' => $tabs
						);
				    
					$tabs = '';
				}
		   endforeach;     
		}
		// Creating the tabs
	//	echo tk_jqueryui_tabs( $TapArray );
		?>    
		<p class="submit"> <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
	</form>
	</div>
<?php } ?>