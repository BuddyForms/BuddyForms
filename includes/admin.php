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
       // print_r($custom_field);
 	    register_setting( 'cgt_options', $custom_field.'_custom_fields' ); 	
    endforeach;       
 
	
	foreach($bp->bp_cgt->new_post_types as $new_group_type ) :
 	    register_setting( 'cgt_options', $new_group_type.'_name' ); 	
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
	
	//print_r($cgt_types);
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
	 				
	 			$checked = '';

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
		//echo $tab1;
		$TapArray[] = array(
				'id' => 1,
				'title' => 'General Settings',
				'content' => $tab1	
				);
                
		if(!empty($cgt_types)){
			foreach($cgt_types as $new_group_type ) :
				if($new_group_type != '') {
				    
		    	$new_group_type_id = str_replace(' ', '', $new_group_type);
		    	
		    	$accordion_lable .= '<div>';
				$accordion_lable .= '<div>Lable for ' .$new_group_type. ' display name: </div>';
				$accordion_lable .= '<div><input type="text" name="'.$new_group_type.'_name'.'" value="'.get_option($new_group_type.'_name').'" /></div>';
                $accordion_lable .= '</div>';
    		    
                $accordion_custom_fields .= '<table border="1" bordercolor="#ECECEC" style="background-color:#ECECEC" width="100%" cellpadding="0" cellspacing="0">';
                $accordion_custom_fields .= '<tbody>';
                $accordion_custom_fields .= '<tr>';
                    $accordion_custom_fields .= '<th>Field Name</th>';
                    $accordion_custom_fields .= '<th>form</th>';
                    $accordion_custom_fields .= '<th>multi select</th>';
                    $accordion_custom_fields .= '<th>required</th>';
                    $accordion_custom_fields .= '<th>display</th>';
                $accordion_custom_fields .= '</tr>';
   
				
				if(!empty($cgt_custom_fields[$new_group_type])){
					foreach($cgt_custom_fields[$new_group_type] as $custom_field ) {
					    if($custom_field)
                         $accordion_custom_fields .= '<tr>';
                          $accordion_custom_fields .= '<td><input type="text" name="cgt_custom_fields['.$new_group_type.'][]" value="'.$custom_field.'" /></td>';
                          $accordion_custom_fields .= '<td>' . tk_checkbox($taxonomy_form) . '</td>';
                          $accordion_custom_fields .= '<td>' . tk_checkbox($taxonomy_multy_select) . '</td>';
                          $accordion_custom_fields .= ' <td>' . tk_checkbox($taxonomy_required) . '</td>';
                          $accordion_custom_fields .= ' <td>' . tk_checkbox($taxonomy_display) . '</td>';
                        $accordion_custom_fields .= '</tr>';
                             
					}
				}
				$accordion_custom_fields .= ' </tbody></table>';	
                     
                $cgt_custom_field_type['value'] = 'text';
                $cgt_custom_field_type['option_name'] = 'Text';
                $cgt_custom_field_types[] = $cgt_custom_field_type;
                
                $cgt_custom_field_type['value'] = 'textarea';
                $cgt_custom_field_type['option_name'] = 'Textarea';
                $cgt_custom_field_types[] = $cgt_custom_field_type;
    
                $cgt_custom_field_type['value'] = 'dropdown';
                $cgt_custom_field_type['option_name'] = 'Dropdown';
                $cgt_custom_field_types[] = $cgt_custom_field_type;

                $cgt_custom_field_type['value'] = 'checkbox';
                $cgt_custom_field_type['option_name'] = 'Checkbox';
                $cgt_custom_field_types[] = $cgt_custom_field_type;
            
                $cgt_custom_field_type['value'] = 'radiobutton';
                $cgt_custom_field_type['option_name'] = 'Radiobutton';
                $cgt_custom_field_types[] = $cgt_custom_field_type;
  
                $cgt_custom_field_type['value'] = 'mail';
                $cgt_custom_field_type['option_name'] = 'Mail';
                $cgt_custom_field_types[] = $cgt_custom_field_type; 
                            
                $accordion_custom_fields .= 'Field Type'. tk_form_select( 'custom_fields', $cgt_custom_field_types, array( 'multi_index' => 0 ) ); 
                $accordion_custom_fields .= '<INPUT type="button" value="Add one more!" onclick="add_new_field(\''.$new_group_type.'\')"/>';
				
				
		    
                
                  $accordion_taxonomies .= '<table border="1" bordercolor="#ECECEC" style="background-color:#ECECEC" width="100%" cellpadding="0" cellspacing="0">';
                  $accordion_taxonomies .= '<tbody>';
                   $accordion_taxonomies .= '<tr>';
                      $accordion_taxonomies .= '<th>Name</th>';
                      $accordion_taxonomies .= '<th>form</th>';
                      $accordion_taxonomies .= '<th>multi select</th>';
                      $accordion_taxonomies .= '<th>required</th>';
                      $accordion_taxonomies .= '<th>display</th>';
                   $accordion_taxonomies .= '</tr>';
    			   
                   $args=array(
                      'object_type' => array($new_group_type),
                      'public'   => true,
                      '_builtin' => false
                      
                    ); 
                    $output = 'names'; // or objects
                    $operator = 'and'; // 'and' or 'or'
                    $taxonomies=get_taxonomies($args,$output,$operator); 
                              
                    if  ($taxonomies) {
                      foreach ($taxonomies  as $taxonomy ) {
                          
                        $accordion_taxonomies .= '<tr>';
                          $accordion_taxonomies .= '<td>' . $taxonomy . '</td>';
                          $accordion_taxonomies .= '<td>' . tk_checkbox($taxonomy_form) . '</td>';
                          $accordion_taxonomies .= '<td>' . tk_checkbox($taxonomy_multy_select) . '</td>';
                          $accordion_taxonomies .= '<td>' . tk_checkbox($taxonomy_required) . '</td>';
                          $accordion_taxonomies .= '<td>' . tk_checkbox($taxonomy_display) . '</td>';
                        $accordion_taxonomies .= '</tr>';
                       // $cgt_post_type_taxonomies_options[] = $taxonomy;
                      }
                    }
                    
                    $accordion_taxonomies .= '  </tbody></table>';
                        
                    //$cgt_post_type_taxonomies = tk_form_select( 'cgt_post_type_taxonomies_'.$new_group_type_id, $cgt_post_type_taxonomies_options, array(  'multiselect' => true ) );
                        
    			    $accordion_Array[] = array(
                            'id' => 'accordion_lable_'.$new_group_type_id,
                            'title' => $new_group_type.' Lable',
                            'content' => $accordion_lable
                            );
                    $accordion_Array[] = array(
                            'id' => 'accordion_taxonomies_'.$new_group_type_id,
                            'title' => $new_group_type. ' taxonomies',
                            'content' => $accordion_taxonomies
                            );  
                     $accordion_Array[] = array(
                            'id' => 'accordion_custom_fields_'.$new_group_type_id,
                            'title' => $new_group_type. ' custom Fields',
                            'content' => $accordion_custom_fields
                            );
                            
     
    				   
    			    $tabs = tk_accordion('cgt_accordion_'.$new_group_type_id , $accordion_Array );
                      
    			   $TapArray[] = array(
    					'id' => $new_group_type_id,
    					'title' => $new_group_type,
    					'content' => $tabs
    				);
    		    
    				$tabs = '';
                    $accordion_Array = '';
                    $accordion_lable = '';
                    $accordion_custom_fields = '';
                    $accordion_taxonomies = '';
                    $cgt_post_type_taxonomies_options = '';

				}
		   endforeach;     
		}
		// Creating the tabs
		echo tk_tabs( 'cgt_tabs', $TapArray );
		?>    
		<p class="submit"> <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
	</form>
	</div>
<?php } ?>